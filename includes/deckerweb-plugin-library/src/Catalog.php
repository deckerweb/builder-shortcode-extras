<?php
/** Copyright 2026 David Decker – DECKERWEB. SPDX-License-Identifier: GPL-2.0-or-later */
namespace Deckerweb\PluginLibrary\V0_8_1;
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Strict metadata-only catalog. No remote PHP, JavaScript, CSS, icons or telemetry. */
final class Catalog {
 public const SERIES = [ 'quicknav' => 'QuickNav', 'builder' => 'Builder', 'purify' => 'Purify', 'manage-content' => 'Manage Content', 'connect' => 'Connect', 'tools' => 'Tools', 'shop' => 'Shop' ];
 public const LEGACY_SERIES = [ 'quicknav' => true, 'builder' => true, 'purify' => true, 'manage-content' => true, 'connect' => true ];
 public const DEFAULT_URL = 'https://raw.githubusercontent.com/deckerweb/deckerweb-plugin-library/main/catalog/catalog.json';
	private string $dir;
	public string $status = 'bundled';
	/**
	 * Initialize the component with validated host paths and shared runtime configuration.
	 *
	 * @param string $dir Absolute embedded Library directory.
	 * @return void No return value.
	 */
	public function __construct( string $dir ) { $this->dir = $dir; }

	/**
	 * Accept only approved first-party HTTPS catalog endpoint shapes.
	 *
	 * @param string $url Candidate HTTPS source or package URL.
	 * @return bool Whether the URL is an allowed first-party catalog endpoint.
	 */
	public static function trusted_source( string $url ): bool {
		$p = wp_parse_url( $url );
		if ( ! is_array( $p ) || ( $p['scheme'] ?? '' ) !== 'https' || isset( $p['user'], $p['pass'] ) || isset( $p['port'] ) || isset( $p['query'] ) || isset( $p['fragment'] ) ) { return false; }
		if ( isset( $p['user'] ) || isset( $p['pass'] ) ) { return false; }
		$host = strtolower( $p['host'] ?? '' );
		$path = $p['path'] ?? '';
		if ( strpos( $path, '..' ) !== false || strpos( $path, '%' ) !== false ) { return false; }
		return $host === 'raw.githubusercontent.com' && (bool) preg_match( '~^/deckerweb/[a-zA-Z0-9_.-]+/[a-zA-Z0-9_./-]+\.json$~D', $path );
	}

	/**
	 * Read explicit series memberships, falling back to a legacy primary series.
	 *
	 * @param array $entry Validated plugin metadata.
	 * @return array Ordered unique series identifiers; no memberships for independent plugins.
	 */
	public static function series( array $entry ): array {
		return $entry['series_memberships_v2'] ?? $entry['series_memberships'] ?? ( isset( $entry['series'] ) ? [ $entry['series'] ] : [] );
	}

	/**
	 * Match a release ZIP URL against its exact approved GitHub repository.
	 *
	 * @param string $url Candidate HTTPS source or package URL.
	 * @param string $repo Exact approved owner/repository identity.
	 * @return bool Whether the release URL belongs to the exact approved repository.
	 */
	public static function download_url( string $url, string $repo ): bool {
		return (bool) preg_match( '~^https://github\.com/' . preg_quote( $repo, '~' ) . '/releases/download/[a-zA-Z0-9_.-]+/[a-zA-Z0-9_.-]+\.zip$~D', $url );
	}

	/**
	 * Validate the whole catalog atomically before displaying or caching its entries.
	 *
	 * @param mixed $data Untrusted decoded catalog document.
	 * @return array|\WP_Error Approved entries keyed by slug, or WP_Error when any metadata is invalid.
	 */
	public static function validate( $data ) {
		if ( ! is_array( $data ) || ( $data['schema_version'] ?? null ) !== 1 || ! isset( $data['plugins'] ) || ! is_array( $data['plugins'] ) || count( $data['plugins'] ) > 100 ) {
			return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid catalog schema.' ) );
		}
  if ( isset( $data['catalog_revision'] ) && ( ! is_string( $data['catalog_revision'] ) || ! preg_match( '/^[a-zA-Z0-9][a-zA-Z0-9._-]{0,63}$/D', $data['catalog_revision'] ) ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid catalog revision.' ) ); }
  if ( isset( $data['requires_library'] ) && ( ! is_string( $data['requires_library'] ) || ! preg_match( '/^\d+\.\d+\.\d+$/D', $data['requires_library'] ) || version_compare( Library::VERSION, $data['requires_library'], '<' ) ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'This catalog requires a newer Library.' ) ); }
  if ( isset( $data['generated_at'] ) && ( ! is_string( $data['generated_at'] ) || ! preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/D', $data['generated_at'] ) ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid catalog timestamp.' ) ); }
		$result = [];
		foreach ( $data['plugins'] as $entry ) {
			if ( ! is_array( $entry ) || ! isset( $entry['approved'] ) || ! is_bool( $entry['approved'] ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid approval flag.' ) ); }
			if ( ! $entry['approved'] ) { continue; }
			foreach ( [ 'slug', 'name', 'description', 'version', 'repository', 'plugin_file', 'download_url', 'sha256', 'requires_wp', 'requires_php' ] as $field ) {
				if ( ! isset( $entry[$field] ) || ! is_string( $entry[$field] ) || strlen( $entry[$field] ) > 2000 ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid catalog field: ' ) . $field ); }
			}
			if ( isset( $entry['series'] ) && ( ! is_string( $entry['series'] ) || ! isset( self::LEGACY_SERIES[$entry['series']] ) ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid plugin series.' ) ); }
			if ( isset( $entry['series_memberships'] ) ) {
				$memberships = $entry['series_memberships'];
				if ( ! is_array( $memberships ) || array_keys( $memberships ) !== array_keys( array_values( $memberships ) ) || count( $memberships ) > count( self::LEGACY_SERIES ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid plugin series.' ) ); }
				$seen_series = [];
				foreach ( $memberships as $membership ) {
					if ( ! is_string( $membership ) || ! isset( self::LEGACY_SERIES[$membership] ) || isset( $seen_series[$membership] ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid plugin series.' ) ); }
					$seen_series[$membership] = true;
				}
				if ( isset( $entry['series'] ) && ! isset( $seen_series[$entry['series']] ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid plugin series.' ) ); }
			}

   if ( isset( $entry['series_memberships_v2'] ) ) {
    $extended = $entry['series_memberships_v2'];
    if ( ! is_array( $extended ) || array_keys( $extended ) !== array_keys( array_values( $extended ) ) || count( $extended ) > count( self::SERIES ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid plugin series.' ) ); }
    $seen = [];
    foreach ( $extended as $membership ) {
     if ( ! is_string( $membership ) || ! isset( self::SERIES[$membership] ) || isset( $seen[$membership] ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid plugin series.' ) ); }
     $seen[$membership] = true;
    }
    // Older readers see exactly the supported projection and ignore this additive field.
    $projection = array_values( array_intersect( $extended, array_keys( self::LEGACY_SERIES ) ) );
    $legacy = $entry['series_memberships'] ?? ( isset( $entry['series'] ) ? [ $entry['series'] ] : [] );
    if ( $projection !== $legacy ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid plugin series.' ) ); }
   }
			$slug = $entry['slug'];
			if ( ! preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $slug ) || isset( $result[$slug] )
				|| ! preg_match( '~^deckerweb/[a-zA-Z0-9_.-]+$~D', $entry['repository'] )
				|| ! preg_match( '~^' . preg_quote( $slug, '~' ) . '/[a-zA-Z0-9_-]+\.php$~D', $entry['plugin_file'] )
				|| ! self::download_url( $entry['download_url'], $entry['repository'] )
				|| ! preg_match( '/^[a-f0-9]{64}$/D', $entry['sha256'] )
				|| ! preg_match( '/^\d+\.\d+(?:\.\d+)?(?:-[a-zA-Z0-9.-]+)?$/D', $entry['version'] ) ) {
				return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid identity or release metadata.' ) );
			}
			foreach ( [ 'requires_wp', 'requires_php' ] as $field ) {
				if ( ! preg_match( '/^\d+\.\d+(?:\.\d+)?$/D', $entry[$field] ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid platform version.' ) ); }
			}
			if ( trim( $entry['name'] ) === '' || trim( $entry['description'] ) === '' ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Missing display text.' ) ); }
			if ( isset( $entry['dependencies_optional_since_version'] ) && ( ! is_string( $entry['dependencies_optional_since_version'] ) || ! preg_match( '/^\d+\.\d+\.\d+(?:-[a-zA-Z0-9.-]+)?$/D', $entry['dependencies_optional_since_version'] ) || $entry['dependencies_optional_since_version'] !== '2.0.0-rc.1' || $entry['plugin_file'] !== 'oxygen-quicknav/oxygen-quicknav.php' || $entry['repository'] !== 'deckerweb/oxygen-quicknav' ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid dependency metadata.' ) ); }
			$dependencies = $entry['dependencies'] ?? [];
			if ( ! is_array( $dependencies ) || count( $dependencies ) > 10 ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid dependencies.' ) ); }
			foreach ( $dependencies as $d ) {
				if ( ! is_array( $d ) || ! is_string( $d['name'] ?? null ) || strlen( $d['name'] ) > 150
					|| ! is_string( $d['plugin_file'] ?? null ) || ! preg_match( '~^[a-z0-9-]+/[a-zA-Z0-9_-]+\.php$~D', $d['plugin_file'] )
					|| ! is_string( $d['min_version'] ?? null ) || ! preg_match( '/^(?:\d+\.\d+(?:\.\d+)?)?$/D', $d['min_version'] )
					|| ! in_array( $d['detector'] ?? '', [ '', 'breakdance', 'bricks', 'oxygen', 'advanced_scripts' ], true ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid dependency metadata.' ) ); }
				if ( isset( $d['url'] ) && ( ! is_string( $d['url'] ) || ! preg_match( '~^https://(?:breakdance\.com|bricksbuilder\.io|oxygenbuilder\.com|cleanplugins\.com|github\.com/deckerweb|deckerweb\.de)/[a-zA-Z0-9_./-]*$~D', $d['url'] ) ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid dependency URL.' ) ); }
			}
			foreach ( [ 'name_de', 'description_de', 'category' ] as $field ) {
				if ( isset( $entry[$field] ) && ( ! is_string( $entry[$field] ) || strlen( $entry[$field] ) > 2000 ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid display field.' ) ); }
			}

			if ( isset( $entry['icon'] ) && ( ! is_string( $entry['icon'] ) || ! preg_match( '~^assets/icons/[a-z0-9-]+\.png$~D', $entry['icon'] ) ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid local icon.' ) ); }
			if ( isset( $entry['icon_de'] ) && ( ! is_string( $entry['icon_de'] ) || ! preg_match( '~^assets/icons/[a-z0-9-]+\.png$~D', $entry['icon_de'] ) ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid local icon.' ) ); }
			if ( isset( $entry['icon_label'] ) && ( ! is_string( $entry['icon_label'] ) || ! preg_match( '/^[A-Z0-9]{1,3}$/D', $entry['icon_label'] ) ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid icon label.' ) ); }
			if ( isset( $entry['icon_background'] ) && ( ! is_string( $entry['icon_background'] ) || ! preg_match( '/^#[a-f0-9]{6}$/D', $entry['icon_background'] ) ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid icon color.' ) ); }
			if ( isset( $entry['github_stars'] ) && ( ! is_int( $entry['github_stars'] ) || $entry['github_stars'] < 0 ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid star count.' ) ); }
			if ( isset( $entry['stars_checked_at'] ) && ( ! is_string( $entry['stars_checked_at'] ) || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/D', $entry['stars_checked_at'] ) ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid stars date.' ) ); }
			if ( isset( $entry['network_activation_min_version'] ) && ( ! is_string( $entry['network_activation_min_version'] ) || ! preg_match( '/^\d+\.\d+\.\d+$/D', $entry['network_activation_min_version'] ) ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid network policy.' ) ); }
			if ( isset( $entry['network_activation'] ) && ! is_bool( $entry['network_activation'] ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid network policy.' ) ); }
			foreach ( [ 'requires_multisite', 'network_only' ] as $field ) { if ( isset( $entry[$field] ) && ! is_bool( $entry[$field] ) ) { return new \WP_Error( 'dwl_catalog', Library::t( 'Invalid network requirement.' ) ); } }
			$entry['dependencies'] = $dependencies;
			$result[$slug] = $entry;
		}
		return $result;
	}

	/**
	 * Return bundled display-only entries; the stable catalog has no pending previews.
	 *
	 * @return array Empty list; all catalog plugins now have approved stable releases.
	 */
	public function previews(): array { return []; }

	/**
	 * Read approved metadata with independent display and updater caches or mandatory fresh verification.
	 *
	 * @param bool $fresh Require a successful uncached source read.
	 * @param bool $updates Use the independent updater cache instead of the 24-hour display cache.
	 * @return array|\WP_Error Validated catalog entries, or WP_Error if a required fresh approval fails.
	 * May read or change component-owned shared storage; foreign plugin data is preserved.
	 */
	public function entries( bool $fresh = false, bool $updates = false ) {
		$settings = Library::settings();
		$url = $settings['catalog_url'];
		if ( $settings['online'] && ! self::trusted_source( $url ) ) {
			if ( $fresh ) { return new \WP_Error( 'dwl_source', Library::t( 'The online catalog source is not trusted.' ) ); }
			$this->status = 'offline';
		}
		if ( $settings['online'] && self::trusted_source( $url ) ) {
			$base_key = 'dwl_catalog_' . md5( $url ) . '_061';
   $key = $base_key . ( $updates ? '_updates' : '' );
   $keys = get_site_option( 'deckerweb_library_cache_keys_v2', [] ); $keys = is_array( $keys ) ? $keys : [];
   if ( ! in_array( $base_key, $keys, true ) ) { $keys[] = $base_key; update_site_option( 'deckerweb_library_cache_keys_v2', $keys ); }
			$cache = get_site_transient( $key );
			if ( ! $fresh && is_array( $cache ) && isset( $cache['data'] ) ) {
				$this->status = 'online';
				return self::validate( $cache['data'] );
			}
			if ( $fresh || ! get_site_transient( $key . '_retry' ) ) {
				$response = wp_safe_remote_get( $url, [ 'user-agent' => 'deckerweb-plugin-library/' . Library::VERSION, 'timeout' => 6, 'redirection' => 0, 'limit_response_size' => 262145, 'headers' => ( $fresh ? [ 'Accept' => 'application/json', 'Cache-Control' => 'no-cache', 'Pragma' => 'no-cache' ] : [ 'Accept' => 'application/json' ] ) ] );
				$body = is_wp_error( $response ) ? '' : wp_remote_retrieve_body( $response );
				$data = json_decode( $body, true );
				$valid = self::validate( $data );
				if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 && strlen( $body ) <= 262144 && ! is_wp_error( $valid ) ) {
					set_site_transient( $key, [ 'data' => $data, 'checked_at' => time() ], ( $updates ? 12 : 24 ) * HOUR_IN_SECONDS );
					set_site_transient( $key . '_last', [ 'data' => $data, 'checked_at' => time() ], 48 * HOUR_IN_SECONDS );
					delete_site_transient( $key . '_retry' );
					$this->status = 'online';
					return $valid;
				}
				set_site_transient( $key . '_retry', true, 15 * MINUTE_IN_SECONDS );
			}
			if ( $fresh ) { return new \WP_Error( 'dwl_offline', Library::t( 'The catalog could not be verified. Please try again later.' ) ); }
			$last = get_site_transient( $key . '_last' );
			$this->status = 'offline';
			if ( is_array( $last ) && isset( $last['data'] ) ) { return self::validate( $last['data'] ); }
		}
		$data = json_decode( (string) ( is_file( $this->dir . '/catalog.json' ) ? file_get_contents( $this->dir . '/catalog.json' ) : '' ), true );
		return self::validate( $data );
	}
}
