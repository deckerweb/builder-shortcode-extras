<?php
namespace Deckerweb\PluginLibrary\V0_2_0;
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Strict metadata-only catalog. No remote PHP, JavaScript, CSS, icons or telemetry. */
final class Catalog {
	private string $dir;
	public string $status = 'bundled';
	public function __construct( string $dir ) { $this->dir = $dir; }

	/** Only first-party catalog endpoints; HTTP redirects are deliberately disabled. */
	public static function trusted_source( string $url ): bool {
		$p = wp_parse_url( $url );
		if ( ! is_array( $p ) || ( $p['scheme'] ?? '' ) !== 'https' || isset( $p['user'], $p['pass'] ) || isset( $p['port'] ) || isset( $p['query'] ) || isset( $p['fragment'] ) ) { return false; }
		if ( isset( $p['user'] ) || isset( $p['pass'] ) ) { return false; }
		$host = strtolower( $p['host'] ?? '' );
		$path = $p['path'] ?? '';
		if ( strpos( $path, '..' ) !== false || strpos( $path, '%' ) !== false ) { return false; }
		return ( in_array( $host, [ 'deckerweb.de', 'www.deckerweb.de' ], true ) && preg_match( '~^/[a-zA-Z0-9_./-]+\.json$~D', $path ) )
			|| ( $host === 'raw.githubusercontent.com' && preg_match( '~^/deckerweb/[a-zA-Z0-9_.-]+/[a-zA-Z0-9_./-]+\.json$~D', $path ) );
	}

	public static function download_url( string $url, string $repo ): bool {
		return (bool) preg_match( '~^https://github\.com/' . preg_quote( $repo, '~' ) . '/releases/download/[a-zA-Z0-9_.-]+/[a-zA-Z0-9_.-]+\.zip$~D', $url );
	}

	/** Validate the whole document atomically, including every dependency. */
	public static function validate( $data ) {
		if ( ! is_array( $data ) || ( $data['schema_version'] ?? null ) !== 1 || ! isset( $data['plugins'] ) || ! is_array( $data['plugins'] ) || count( $data['plugins'] ) > 100 ) {
			return new \WP_Error( 'dwl_catalog', 'Invalid catalog schema.' );
		}
		$result = [];
		foreach ( $data['plugins'] as $entry ) {
			if ( ! is_array( $entry ) || ! isset( $entry['approved'] ) || ! is_bool( $entry['approved'] ) ) { return new \WP_Error( 'dwl_catalog', 'Invalid approval flag.' ); }
			if ( ! $entry['approved'] ) { continue; }
			foreach ( [ 'slug', 'name', 'description', 'version', 'repository', 'plugin_file', 'download_url', 'sha256', 'requires_wp', 'requires_php' ] as $field ) {
				if ( ! isset( $entry[$field] ) || ! is_string( $entry[$field] ) || strlen( $entry[$field] ) > 2000 ) { return new \WP_Error( 'dwl_catalog', 'Invalid catalog field: ' . $field ); }
			}
			$slug = $entry['slug'];
			if ( ! preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $slug ) || isset( $result[$slug] )
				|| ! preg_match( '~^deckerweb/[a-zA-Z0-9_.-]+$~D', $entry['repository'] )
				|| ! preg_match( '~^' . preg_quote( $slug, '~' ) . '/[a-zA-Z0-9_-]+\.php$~D', $entry['plugin_file'] )
				|| ! self::download_url( $entry['download_url'], $entry['repository'] )
				|| ! preg_match( '/^[a-f0-9]{64}$/D', $entry['sha256'] )
				|| ! preg_match( '/^\d+\.\d+(?:\.\d+)?(?:-[a-zA-Z0-9.-]+)?$/D', $entry['version'] ) ) {
				return new \WP_Error( 'dwl_catalog', 'Invalid identity or release metadata.' );
			}
			foreach ( [ 'requires_wp', 'requires_php' ] as $field ) {
				if ( ! preg_match( '/^\d+\.\d+(?:\.\d+)?$/D', $entry[$field] ) ) { return new \WP_Error( 'dwl_catalog', 'Invalid platform version.' ); }
			}
			if ( trim( $entry['name'] ) === '' || trim( $entry['description'] ) === '' ) { return new \WP_Error( 'dwl_catalog', 'Missing display text.' ); }
			$dependencies = $entry['dependencies'] ?? [];
			if ( ! is_array( $dependencies ) || count( $dependencies ) > 10 ) { return new \WP_Error( 'dwl_catalog', 'Invalid dependencies.' ); }
			foreach ( $dependencies as $d ) {
				if ( ! is_array( $d ) || ! is_string( $d['name'] ?? null ) || strlen( $d['name'] ) > 150
					|| ! is_string( $d['plugin_file'] ?? null ) || ! preg_match( '~^[a-z0-9-]+/[a-zA-Z0-9_-]+\.php$~D', $d['plugin_file'] )
					|| ! is_string( $d['min_version'] ?? null ) || ! preg_match( '/^(?:\d+\.\d+(?:\.\d+)?)?$/D', $d['min_version'] )
					|| ! in_array( $d['detector'] ?? '', [ '', 'breakdance', 'bricks', 'oxygen', 'advanced_scripts' ], true ) ) { return new \WP_Error( 'dwl_catalog', 'Invalid dependency metadata.' ); }
				if ( isset( $d['url'] ) && ( ! is_string( $d['url'] ) || ! preg_match( '~^https://(?:breakdance\.com|bricksbuilder\.io|oxygenbuilder\.com|cleanplugins\.com|github\.com/deckerweb|deckerweb\.de)/[a-zA-Z0-9_./-]*$~D', $d['url'] ) ) ) { return new \WP_Error( 'dwl_catalog', 'Invalid dependency URL.' ); }
			}
			foreach ( [ 'name_de', 'description_de', 'category' ] as $field ) {
				if ( isset( $entry[$field] ) && ( ! is_string( $entry[$field] ) || strlen( $entry[$field] ) > 2000 ) ) { return new \WP_Error( 'dwl_catalog', 'Invalid display field.' ); }
			}

			if ( isset( $entry['icon'] ) && ( ! is_string( $entry['icon'] ) || ! preg_match( '~^assets/icons/[a-z0-9-]+\.png$~D', $entry['icon'] ) ) ) { return new \WP_Error( 'dwl_catalog', 'Invalid local icon.' ); }
			if ( isset( $entry['icon_label'] ) && ( ! is_string( $entry['icon_label'] ) || ! preg_match( '/^[A-Z0-9]{1,3}$/D', $entry['icon_label'] ) ) ) { return new \WP_Error( 'dwl_catalog', 'Invalid icon label.' ); }
			if ( isset( $entry['icon_background'] ) && ( ! is_string( $entry['icon_background'] ) || ! preg_match( '/^#[a-f0-9]{6}$/D', $entry['icon_background'] ) ) ) { return new \WP_Error( 'dwl_catalog', 'Invalid icon color.' ); }
			if ( isset( $entry['github_stars'] ) && ( ! is_int( $entry['github_stars'] ) || $entry['github_stars'] < 0 ) ) { return new \WP_Error( 'dwl_catalog', 'Invalid star count.' ); }
			if ( isset( $entry['stars_checked_at'] ) && ( ! is_string( $entry['stars_checked_at'] ) || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/D', $entry['stars_checked_at'] ) ) ) { return new \WP_Error( 'dwl_catalog', 'Invalid stars date.' ); }
			if ( isset( $entry['network_activation'] ) && ! is_bool( $entry['network_activation'] ) ) { return new \WP_Error( 'dwl_catalog', 'Invalid network policy.' ); }
			$entry['dependencies'] = $dependencies;
			$result[$slug] = $entry;
		}
		return $result;
	}

	/** Cached reads are cheap. Mutations can require a successful fresh remote read. */
	public function entries( bool $fresh = false ) {
		$settings = Library::settings();
		$url = $settings['catalog_url'];
		if ( $settings['online'] && ! self::trusted_source( $url ) ) {
			if ( $fresh ) { return new \WP_Error( 'dwl_source', Library::t( 'The online catalog source is not trusted.', 'Die Online-Katalogquelle ist nicht vertrauenswürdig.' ) ); }
			$this->status = 'offline';
		}
		if ( $settings['online'] && self::trusted_source( $url ) ) {
			$key = 'dwl_catalog_' . md5( $url );
			$cache = get_site_transient( $key );
			if ( ! $fresh && is_array( $cache ) && isset( $cache['data'] ) ) {
				$this->status = 'online';
				return self::validate( $cache['data'] );
			}
			if ( $fresh || ! get_site_transient( $key . '_retry' ) ) {
				$response = wp_safe_remote_get( $url, [ 'timeout' => 6, 'redirection' => 0, 'limit_response_size' => 262145, 'headers' => [ 'Accept' => 'application/json' ] ] );
				$body = is_wp_error( $response ) ? '' : wp_remote_retrieve_body( $response );
				$data = json_decode( $body, true );
				$valid = self::validate( $data );
				if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 && strlen( $body ) <= 262144 && ! is_wp_error( $valid ) ) {
					set_site_transient( $key, [ 'data' => $data ], 12 * HOUR_IN_SECONDS );
					set_site_transient( $key . '_last', [ 'data' => $data ], 48 * HOUR_IN_SECONDS );
					delete_site_transient( $key . '_retry' );
					$this->status = 'online';
					return $valid;
				}
				set_site_transient( $key . '_retry', true, 15 * MINUTE_IN_SECONDS );
			}
			if ( $fresh ) { return new \WP_Error( 'dwl_offline', Library::t( 'The catalog could not be verified. Please try again later.', 'Der Katalog konnte nicht geprüft werden. Bitte später erneut versuchen.' ) ); }
			$last = get_site_transient( $key . '_last' );
			$this->status = 'offline';
			if ( is_array( $last ) && isset( $last['data'] ) ) { return self::validate( $last['data'] ); }
		}
		$data = json_decode( (string) file_get_contents( $this->dir . '/catalog.json' ), true );
		return self::validate( $data );
	}
}
