<?php
/**
 * DECKERWEB GitHub Release Updater, API v2.
 * Copyright © 2026 David Decker – DECKERWEB.
 * SPDX-License-Identifier: GPL-2.0-or-later
 *
 * @package DeckerwebGitHubReleaseUpdater
 */

namespace Deckerweb\GitHubReleaseUpdater\V2;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Connects one public or private GitHub repository to WordPress plugin update APIs.
 *
 * API v2 is intended to be copied into several plugins without class collisions.
 * It never turns on automatic updates. Private credentials come from an optional provider.
 */
interface AuthProvider {
	/** Return a runtime credential for this exact repository, or null. Never log it. */
	public function token( string $repository ): ?string;
}

/** Read an installation-managed secret; the distributable contains only its variable name. */
final class EnvironmentAuthProvider implements AuthProvider {
	private string $repository;
	private string $variable;
	public function __construct( string $repository, string $variable ) {
		if ( ! preg_match( '/^[A-Z][A-Z0-9_]*$/D', $variable ) ) {
			throw new \InvalidArgumentException( 'Invalid secret variable name.' );
		}
		$this->repository = rtrim( $repository, '/' );
		$this->variable = $variable;
	}
	public function token( string $repository ): ?string {
		if ( $repository !== $this->repository ) { return null; }
		$value = getenv( $this->variable );
		return is_string( $value ) && '' !== $value ? $value : null;
	}
	public function __debugInfo(): array { return array( 'provider' => 'environment' ); }
}

final class Updater {
	public const IMPLEMENTATION_VERSION = '2.1.0';
	public const SUPPORTS_PRIVATE_REPOSITORIES = true;
	public const SUPPORTS_HOST_TRANSLATIONS = true;
	/** Host-owned translator, evaluated in the current locale at output time. */
	private ?\Closure $translate;
	private bool $private;
	private ?AuthProvider $auth;

	/**
	 * WordPress plugin basename, for example slug/slug.php.
	 *
	 * @var string
	 */
	private string $file;
	/**
	 * Stable installed directory name.
	 *
	 * @var string
	 */
	private string $slug;
	/**
	 * Normalized public GitHub repository URL.
	 *
	 * @var string
	 */
	private string $repo;
	/**
	 * GitHub REST API repository URL.
	 *
	 * @var string
	 */
	private string $api;
	/**
	 * Repository-specific site transient key.
	 *
	 * @var string
	 */
	private string $cache;
	/**
	 * Display name for the WordPress plugin information dialog.
	 *
	 * @var string
	 */
	private string $name;
	/**
	 * Short description shown in plugin information.
	 *
	 * @var string
	 */
	private string $description;
	/**
	 * Validated icon URLs, keyed by WordPress resolution.
	 *
	 * @var array<string,string>
	 */
	private array $icons;
	/**
	 * Validated banner URLs, keyed by low/high resolution.
	 *
	 * @var array<string,string>
	 */
	private array $banners;
	/**
	 * Validate the installed plugin basename and repository identity.
	 *
	 * @param string $main_file Absolute path to the main plugin file.
	 * @param string $repository_url https://github.com/owner/repository URL.
	 * @param string $name Plugin display name.
	 * @param string $description Short description for update details.
	 * @param array  $artwork Optional icons (svg, 1x, 2x, default) and banners (low, high) URL maps.
	 * @param array  $options Optional private boolean, auth AuthProvider and translate callable(string):string. Public defaults remain compatible.
	 * @throws \InvalidArgumentException If the slug or repository URL is invalid.
	 */
	public function __construct( string $main_file, string $repository_url, string $name, string $description, array $artwork = array(), array $options = array() ) {
		if ( isset( $options['translate'] ) && ! is_callable( $options['translate'] ) ) { throw new \InvalidArgumentException( 'Invalid host translator.' ); }
		$this->translate = isset( $options['translate'] ) ? \Closure::fromCallable( $options['translate'] ) : null;
		if ( isset( $options['private'] ) && ! is_bool( $options['private'] ) ) { throw new \InvalidArgumentException( $this->text( 'Private mode must be boolean.' ) ); }
		if ( isset( $options['auth'] ) && ! $options['auth'] instanceof AuthProvider ) { throw new \InvalidArgumentException( $this->text( 'Invalid authentication provider.' ) ); }
		$this->private = $options['private'] ?? false;
		$this->auth = $options['auth'] ?? null;
		$this->file = \plugin_basename( $main_file );
		$this->slug = \dirname( $this->file );
		if ( '.' === $this->slug || ! \preg_match( '/^[a-z0-9_-]+$/D', $this->slug ) ) {
			throw new \InvalidArgumentException( $this->text( 'The plugin must be installed in a stable slug directory.' ) );
		}
		if ( ! \preg_match( '~^https://github\.com/([A-Za-z0-9-]+)/([A-Za-z0-9._-]+)/?$~D', $repository_url, $matches ) ) {
			throw new \InvalidArgumentException( $this->text( 'Invalid GitHub repository URL.' ) );
		}
		$this->repo        = 'https://github.com/' . $matches[1] . '/' . $matches[2];
		$this->api         = 'https://api.github.com/repos/' . $matches[1] . '/' . $matches[2];
		$this->cache       = 'ddw_ghru_' . \substr( \md5( $this->repo ), 0, 24 );
		if ( $this->private ) { $this->cache .= '_private'; }
		$this->name        = $name;
		$this->description = $description;
		$this->icons       = $this->artwork_urls( $artwork['icons'] ?? array(), array( 'svg', '1x', '2x', 'default' ) );
		$this->banners     = $this->artwork_urls( $artwork['banners'] ?? array(), array( 'low', 'high' ) );
	}

	/**
	 * Accept only known artwork keys and absolute HTTP(S) URLs without credentials.
	 * Values come from the integrating plugin, never from release metadata.
	 * No image fetch, remote probing or filesystem access is performed here.
	 *
	 * @param mixed $urls Optional URL map.
	 * @param array $keys Supported WordPress artwork keys.
	 * @return array<string,string> Safe URLs.
	 */
	private function artwork_urls( $urls, array $keys ): array {
		$result = array();
		if ( ! is_array( $urls ) ) {
			return $result;
		}
		foreach ( $keys as $key ) {
			if ( ! isset( $urls[ $key ] ) || ! is_string( $urls[ $key ] ) ) {
				continue;
			}
			$url   = $urls[ $key ];
			$parts = \wp_parse_url( $url );
			if ( ! is_array( $parts ) || ! in_array( $parts['scheme'] ?? '', array( 'https', 'http' ), true ) || empty( $parts['host'] ) || isset( $parts['user'] ) || isset( $parts['pass'] ) || preg_match( '/[\x00-\x20\x7f]/', $url ) ) {
				continue;
			}
			$clean = \esc_url_raw( $url, array( 'https', 'http' ) );
			if ( '' !== $clean ) {
				$result[ $key ] = $clean;
			}
		}
		return $result;
	}

	/**
	 * Add icons to an existing cached offer for this plugin only.
	 * Preserve cache timestamps, package fields and unrelated plugin objects.
	 *
	 * @param mixed $transient WordPress update transient.
	 * @return mixed Decorated copy or original value.
	 */
	public function cached_icons( $transient ) {
		if ( ! $this->icons || ! is_object( $transient ) || ! isset( $transient->response[ $this->file ] ) || ! is_object( $transient->response[ $this->file ] ) ) {
			return $transient;
		}
		$transient                                 = clone $transient;
		$transient->response[ $this->file ]        = clone $transient->response[ $this->file ];
		$transient->response[ $this->file ]->icons = $this->icons;
		return $transient;
	}

	/**
	 * Register the update, information, and extracted archive hooks.
	 */
	public function register(): void {
		\add_filter( 'update_plugins_github.com', array( $this, 'update' ), 10, 4 );
		\add_filter( 'site_transient_update_plugins', array( $this, 'cached_icons' ) );
		\add_filter( 'plugins_api', array( $this, 'information' ), 20, 3 );
		if ( $this->private ) { \add_filter( 'upgrader_pre_download', array( $this, 'download' ), 10, 4 ); }
		\add_filter( 'upgrader_source_selection', array( $this, 'select_source' ), 20, 4 );
	}

	/**
	 * Fetch the latest published release and choose its installable ZIP.
	 * Caches successful responses for 30 minutes and failures for 10 minutes.
	 *
	 * @return array{version:string,package:string,notes:string,published:string}|null
	 */
	private function release(): ?array {
		$auth_headers = $this->auth_headers();
		if ( $this->private && null === $auth_headers ) { return null; }
		$cached = \get_site_transient( $this->cache );
		if ( \is_array( $cached ) ) {
			return ! empty( $cached['failed'] ) ? null : $cached;
		}
		$response = \wp_remote_get(
			$this->api . '/releases/latest',
			array(
				'timeout'     => 6,
				'redirection' => $this->private ? 0 : 2,
				'sslverify' => true,
				'reject_unsafe_urls' => true,
				'limit_response_size' => 512 * 1024,
				'headers'     => array_merge( $auth_headers ?? array(), array(
					'Accept'               => 'application/vnd.github+json',
					'User-Agent'           => 'DECKERWEB-WordPress-GitHub-Release-Updater',
					'X-GitHub-Api-Version' => '2022-11-28',
				) ),
			)
		);
		if ( \is_wp_error( $response ) || \wp_remote_retrieve_response_code( $response ) !== 200 ) {
			\set_site_transient( $this->cache, array( 'failed' => true ), 10 * MINUTE_IN_SECONDS );
			return null;
		}
		$data = \json_decode( \wp_remote_retrieve_body( $response ), true );
		if ( ! \is_array( $data ) || ! empty( $data['draft'] ) || ! empty( $data['prerelease'] ) || ! \is_string( $data['tag_name'] ?? null ) || ! \preg_match( '/^v?(\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?)$/D', $data['tag_name'], $match ) ) {
			\set_site_transient( $this->cache, array( 'failed' => true ), 10 * MINUTE_IN_SECONDS );
			return null;
		}
		// Prefer a release asset with the stable slug; otherwise use the source archive.
		$version = $match[1];
		$package = '';
		foreach ( ( \is_array( $data['assets'] ?? null ) ? $data['assets'] : array() ) as $asset ) {
			if ( ! \is_array( $asset ) || ( $asset['state'] ?? 'uploaded' ) !== 'uploaded' ) {
				continue;
			}
			$asset_name = \is_string( $asset['name'] ?? null ) ? $asset['name'] : '';
			if ( $asset_name !== $this->slug . '.zip' && $asset_name !== $this->slug . '-' . $version . '.zip' ) {
				continue;
			}
			if ( $this->private ) {
				$url = $asset['url'] ?? '';
				if ( is_string( $url ) && preg_match( '~^' . preg_quote( $this->api, '~' ) . '/releases/assets/[1-9][0-9]*$~D', $url ) ) { $package = $url; break; }
				continue;
			}
			$url = \is_string( $asset['browser_download_url'] ?? null ) ? $asset['browser_download_url'] : '';
			if ( \strpos( $url, $this->repo . '/releases/download/' ) === 0 ) {
				$package = $url;
				break;
			}
		}
		if ( ! $package ) {
			$url = \is_string( $data['zipball_url'] ?? null ) ? $data['zipball_url'] : '';
			if ( \strpos( $url, $this->api . '/zipball/' ) === 0 && ( ! $this->private || $url === $this->api . '/zipball/' . rawurlencode( $data['tag_name'] ) ) ) {
				$package = $url;
			}
		}
		if ( ! $package ) {
			\set_site_transient( $this->cache, array( 'failed' => true ), 10 * MINUTE_IN_SECONDS );
			return null;
		}
		$release = array(
			'version'   => $version,
			'package'   => $package,
			'notes'     => \is_string( $data['body'] ?? null ) ? $data['body'] : '',
			'published' => \is_string( $data['published_at'] ?? null ) ? $data['published_at'] : '',
		);
		\set_site_transient( $this->cache, $release, 30 * MINUTE_IN_SECONDS );
		return $release;
	}

	/** The host owns extraction, catalogs and the sole textdomain; never cache translated strings. */
	private function text( string $message ): string {
		if ( null === $this->translate ) { return $message; }
		try { $translated = ( $this->translate )( $message ); }
		catch ( \Throwable $error ) { return $message; }
		return is_string( $translated ) && '' !== trim( $translated ) ? $translated : $message;
	}

	/** Keep provider objects out of ordinary diagnostic object dumps. */
	public function __debugInfo(): array { return array( 'repository' => $this->repo, 'private' => $this->private, 'version' => self::IMPLEMENTATION_VERSION ); }

	/** Remove metadata after credential rotation; never derive cache names from secrets. */
	public function clear_cache(): void { \delete_site_transient( $this->cache ); }

	private function auth_headers(): ?array {
		if ( ! $this->private ) { return array(); }
		try { $token = $this->auth ? $this->auth->token( $this->repo ) : null; }
		catch ( \Throwable $error ) { return null; }
		if ( ! is_string( $token ) || ! preg_match( '/^[A-Za-z0-9_]+$/D', $token ) ) { return null; }
		return array( 'Authorization' => 'Bearer ' . $token );
	}

	/** Core bulk plugin updates omit type/action per item; reject any explicit conflicting context. */
	private function is_update_context( $upgrader, array $context ): bool {
		if ( ( $context['plugin'] ?? '' ) !== $this->file || ( isset( $context['type'] ) && 'plugin' !== $context['type'] ) || ( isset( $context['action'] ) && 'update' !== $context['action'] ) ) { return false; }
		if ( isset( $context['type'], $context['action'] ) ) { return true; }
		return $upgrader instanceof \Plugin_Upgrader && true === $upgrader->bulk;
	}

	/** Stream only this plugin's private offered package. Credentials never follow redirects. */
	public function download( $reply, $package, $upgrader, $hook_extra ) {
		if ( ! $this->private || false !== $reply || ! $this->is_update_context( $upgrader, $hook_extra ) ) { return $reply; }
		$release = $this->release();
		$headers = $this->auth_headers();
		if ( ! $release || null === $headers || $package !== $release['package'] || ! preg_match( '~^' . preg_quote( $this->api, '~' ) . '/(?:releases/assets/[1-9][0-9]*|zipball/v?[0-9A-Za-z.-]+)$~D', $package ) ) {
			return new \WP_Error( 'ddw_ghru_private', $this->text( 'The private update could not be authorized. Check the repository credentials and refresh updates.' ) );
		}
		$file = \wp_tempnam( $this->slug . '.zip' );
		if ( ! $file ) { return new \WP_Error( 'ddw_ghru_temp', $this->text( 'Could not create the update download file.' ) ); }
		$headers['Accept'] = strpos( $package, $this->api . '/zipball/' ) === 0 ? 'application/vnd.github+json' : 'application/octet-stream';
		$headers['User-Agent'] = 'DECKERWEB-WordPress-GitHub-Release-Updater';
		$headers['X-GitHub-Api-Version'] = '2022-11-28';
		$url = $package;
		try {
			for ( $hop = 0; $hop < 4; ++$hop ) {
				$response = \wp_safe_remote_get( $url, array( 'timeout' => 300, 'redirection' => 0, 'sslverify' => true, 'reject_unsafe_urls' => true, 'stream' => true, 'filename' => $file, 'headers' => $headers ) );
				if ( \is_wp_error( $response ) ) { break; }
				$code = \wp_remote_retrieve_response_code( $response );
				clearstatcache( true, $file );
				if ( 200 === $code && is_file( $file ) && filesize( $file ) > 0 ) { return $file; }
				if ( ! in_array( $code, array( 301, 302, 303, 307, 308 ), true ) ) { break; }
				$next = \wp_remote_retrieve_header( $response, 'location' );
				$parts = is_string( $next ) ? \wp_parse_url( $next ) : false;
				if ( ! is_array( $parts ) || ( $parts['scheme'] ?? '' ) !== 'https' || ! in_array( $parts['host'] ?? '', array( 'codeload.github.com', 'release-assets.githubusercontent.com', 'objects.githubusercontent.com' ), true ) || isset( $parts['user'] ) || isset( $parts['pass'] ) || isset( $parts['port'] ) || isset( $parts['fragment'] ) || preg_match( '/[\x00-\x20\x7f]/', $next ) ) { break; }
				$url = $next;
				$headers = array( 'Accept' => 'application/octet-stream', 'User-Agent' => 'DECKERWEB-WordPress-GitHub-Release-Updater' );
				file_put_contents( $file, '' );
			}
		} catch ( \Throwable $error ) { /* Do not return HTTP/provider exception data. */ }
		\wp_delete_file( $file );
		return new \WP_Error( 'ddw_ghru_download', $this->text( 'The private update download failed. Check credentials and try again.' ) );
	}

	/**
	 * Provide an update only for this plugin and its matching Update URI.
	 *
	 * @param mixed               $response Current WordPress update response.
	 * @param array<string,mixed> $plugin_data Installed plugin headers.
	 * @param string              $plugin_file Plugin basename supplied by WordPress.
	 * @param mixed               $locales Requested locales, unused.
	 * @return mixed Existing response or WordPress update data.
	 */
	public function update( $response, $plugin_data, $plugin_file, $locales ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- WordPress hostname filter signature.
		// The hostname hook may run for multiple GitHub plugins on the same site.
		if ( $plugin_file !== $this->file || ( $plugin_data['UpdateURI'] ?? '' ) !== $this->repo ) {
			return $response;
		}
		$release = $this->release();
		if ( ! $release || \version_compare( $release['version'], (string) ( $plugin_data['Version'] ?? '0' ), '<=' ) ) {
			return $response;
		}
		return array(
			'id'           => $this->repo,
			'slug'         => $this->slug,
			'version'      => $release['version'],
			'url'          => $this->repo . '/releases',
			'package'      => $release['package'],
			'requires'     => (string) ( $plugin_data['RequiresWP'] ?? '' ),
			'requires_php' => (string) ( $plugin_data['RequiresPHP'] ?? '' ),
			'icons'        => $this->icons,
		);
	}

	/**
	 * Populate the WordPress plugin details modal from release metadata.
	 *
	 * @param mixed  $response Current plugin information response.
	 * @param string $action WordPress plugins API action.
	 * @param mixed  $args Request arguments.
	 * @return mixed Existing response or plugin information object.
	 */
	public function information( $response, $action, $args ) {
		if ( 'plugin_information' !== $action || ! \is_object( $args ) || ( $args->slug ?? '' ) !== $this->slug ) {
			return $response;
		}
		$release = $this->release();
		if ( ! $release ) {
			return $response;
		}
		return (object) array(
			'name'           => $this->name,
			'slug'           => $this->slug,
			'version'        => $release['version'],
			'author'         => 'David Decker – DECKERWEB',
			'author_profile' => 'https://github.com/deckerweb',
			'homepage'       => $this->repo,
			'last_updated'   => $release['published'],
			'download_link'  => $release['package'],
			'icons'          => $this->icons,
			'banners'        => $this->banners,
			'sections'       => array(
				'description' => \esc_html( $this->description ),
				'changelog'   => \wpautop( \esc_html( ! empty( $release['notes'] ) ? $release['notes'] : $this->text( 'See the release on GitHub.' ) ) ),
			),
		);
	}

	/**
	 * Verify and normalize the extracted plugin directory before installation.
	 *
	 * @param mixed               $source Path to extracted archive, or a WP_Error.
	 * @param mixed               $remote_source Remote source path, unused.
	 * @param mixed               $upgrader WordPress upgrader instance, unused.
	 * @param array<string,mixed> $hook_extra Upgrade context.
	 * @return mixed Valid source directory or WP_Error.
	 */
	public function select_source( $source, $remote_source, $upgrader, $hook_extra ) {
		if ( \is_wp_error( $source ) || ! \is_string( $source ) || ! $this->is_update_context( $upgrader, $hook_extra ) ) {
			return $source;
		}
		global $wp_filesystem;
		if ( ! $wp_filesystem ) {
			return new \WP_Error( 'ddw_ghru_filesystem', $this->text( 'Could not access the update filesystem.' ) );
		}
		$root = \untrailingslashit( $source );
		$main = \basename( $this->file );
		// Accept an asset's slug folder; a GitHub source ZIP needs renaming.
		// Refuse archives missing the expected main file before WordPress replaces anything.
		if ( $wp_filesystem->is_file( $root . '/' . $this->slug . '/' . $main ) ) {
			return \trailingslashit( $root . '/' . $this->slug );
		}
		if ( ! $wp_filesystem->is_file( $root . '/' . $main ) ) {
			return new \WP_Error( 'ddw_ghru_archive', $this->text( 'GitHub release does not contain the plugin main file.' ) );
		}
		$target = \dirname( $root ) . '/' . $this->slug;
		if ( $root === $target ) {
			return $source;
		}
		if ( $wp_filesystem->exists( $target ) || ! $wp_filesystem->move( $root, $target, false ) ) {
			return new \WP_Error( 'ddw_ghru_rename', $this->text( 'Could not prepare the GitHub release package.' ) );
		}
		return \trailingslashit( $target );
	}
}

