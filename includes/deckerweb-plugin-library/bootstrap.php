<?php
/**
 * DECKERWEB Plugin Library bootstrap, protocol 1.
 * GPL-2.0-or-later. Include this file and call deckerweb_library_register().
 * Keep this neutral bootstrap compatible when shipping newer implementations.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'deckerweb_library_register' ) ) {
	/** Register an embedded copy; election happens after all active plugins load. */
	function deckerweb_library_register( string $plugin_file, array $config = [], ?string $library_dir = null ): void {
		$library_dir = $library_dir ?: __DIR__;
		$manifest = $library_dir . '/version.php';
		if ( ! is_file( $manifest ) ) { return; }
		$version = require $manifest;
		if ( ! is_string( $version ) || ! preg_match( '/^\d+\.\d+\.\d+$/D', $version ) ) { return; }
		$GLOBALS['deckerweb_library_candidates_v1'][] = [
			'version' => $version, 'dir' => $library_dir, 'host' => $plugin_file, 'config' => $config,
		];
		if ( empty( $GLOBALS['deckerweb_library_election_v1'] ) ) {
			$GLOBALS['deckerweb_library_election_v1'] = true;
			add_action( 'plugins_loaded', 'deckerweb_library_elect_v1', PHP_INT_MAX );
		}
	}

	/** One runtime per request, independent of host plugin load order. */
	function deckerweb_library_elect_v1(): void {
		if ( ! empty( $GLOBALS['deckerweb_library_runtime_v1'] ) ) { return; }
		$candidates = $GLOBALS['deckerweb_library_candidates_v1'] ?? [];
		if ( ! $candidates ) { return; }
		usort( $candidates, static function( array $a, array $b ): int {
			$comparison = version_compare( $b['version'], $a['version'] );
			return $comparison ?: strcmp( $a['host'], $b['host'] );
		} );
		$chosen = $candidates[0];
		$factory = require $chosen['dir'] . '/runtime.php';
		$GLOBALS['deckerweb_library_runtime_v1'] = $factory( $chosen, $candidates );
	}
}
