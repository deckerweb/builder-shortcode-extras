<?php
/** Copyright 2026 David Decker – DECKERWEB. SPDX-License-Identifier: GPL-2.0-or-later */
namespace Deckerweb\PluginLibrary\V0_8_1;
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Used both when rendering cards and immediately before install/activation. */
final class Requirements {
	/**
	 * Report platform, active dependency and network-scope requirements without installing prerequisites.
	 *
	 * @param array $entry Validated approved catalog entry and dependency metadata.
	 * @param array|null $plugins Installed plugin metadata; null reads the current installation.
	 * @param bool|null $network Network activation context; null derives it from the current admin scope.
	 * @param bool $activation Check the installed version for activation; false checks the offered release for package updates.
	 * @return array Localized unmet requirements; an empty list means the declared prerequisites are met.
	 */
	public static function check( array $entry, ?array $plugins = null, ?bool $network = null, bool $activation = true ): array {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$plugins = $plugins ?? get_plugins();
		$network = $network ?? ( is_multisite() && is_network_admin() );
		$issues = [];
		if ( ! empty( $entry['requires_multisite'] ) && ! is_multisite() ) { $issues[] = Library::t( 'Requires a WordPress Multisite network.' ); }
		if ( ! empty( $entry['network_only'] ) && is_multisite() && ! $network ) { $issues[] = Library::t( 'Install and activate this plugin in the network admin.' ); }
		if ( $network && isset( $entry['network_activation'] ) && ! $entry['network_activation'] && ! isset( $entry['network_activation_min_version'] ) ) { $issues[] = sprintf( Library::t( '%s supports activation per site only.' ), $entry['name'] ); }
		if ( $network && isset( $entry['network_activation_min_version'] ) ) {
			$installed_version = $activation && isset( $plugins[$entry['plugin_file']] ) ? ( $plugins[$entry['plugin_file']]['Version'] ?? '' ) : $entry['version'];
			if ( version_compare( $installed_version, $entry['network_activation_min_version'], '<' ) ) { $issues[] = sprintf( Library::t( 'Update %1$s to version %2$s or newer.' ), $entry['name'], $entry['network_activation_min_version'] ); }
		}
		global $wp_version;
		if ( version_compare( $wp_version, $entry['requires_wp'], '<' ) ) { $issues[] = sprintf( Library::t( 'Requires WordPress %s or newer.' ), $entry['requires_wp'] ); }
		if ( version_compare( PHP_VERSION, $entry['requires_php'], '<' ) ) { $issues[] = sprintf( Library::t( 'Requires PHP %s or newer.' ), $entry['requires_php'] ); }
  foreach ( self::dependencies_for( $entry, $plugins, $activation ) as $dep ) {
   $file = $dep['plugin_file'];
   $present = isset( $plugins[$file] );
   $active = $network ? is_plugin_active_for_network( $file ) : is_plugin_active( $file );
   $version = $plugins[$file]['Version'] ?? '';
   $detector = $dep['detector'] ?? '';
   if ( $detector === 'bricks' ) {
    if ( $network ) { $issues[] = Library::t( 'Activate Bricks QuickNav per site after selecting the Bricks parent or child theme.' ); continue; }
    $theme = wp_get_theme( get_template() );
    $present = $theme->exists() && $theme->get( 'Name' ) === 'Bricks';
    $active = $present && defined( 'BRICKS_VERSION' ) && function_exists( 'bricks_is_builder' );
    $version = $theme->get( 'Version' );
   }
   if ( in_array( $detector, [ 'breakdance', 'oxygen', 'advanced_scripts' ], true ) ) {
    $runtime_file = null; $loaded = false;
    if ( $detector === 'oxygen' || $detector === 'breakdance' ) {
     $mode = $detector === 'oxygen' ? 'oxygen' : 'breakdance';
     $loaded = defined( 'BREAKDANCE_MODE' ) && constant( 'BREAKDANCE_MODE' ) === $mode
      && defined( '__BREAKDANCE_VERSION' ) && function_exists( '\\Breakdance\\Admin\\get_builder_loader_url' );
     if ( $loaded && defined( '__BREAKDANCE_PLUGIN_FILE__' ) && is_string( constant( '__BREAKDANCE_PLUGIN_FILE__' ) ) ) {
      $runtime_file = plugin_basename( constant( '__BREAKDANCE_PLUGIN_FILE__' ) );
      $directory = realpath( dirname( constant( '__BREAKDANCE_PLUGIN_FILE__' ) ) );
      $helper = self::function_file( '\\Breakdance\\Admin\\get_builder_loader_url' );
      if ( ! $directory || ! $helper || ! str_starts_with( $helper, $directory . DIRECTORY_SEPARATOR ) ) { $loaded = false; }
     }
     if ( $loaded ) { $version = (string) constant( '__BREAKDANCE_VERSION' ); }
     if ( $detector === 'oxygen' && $loaded && version_compare( $version, '6.0.0', '<' ) ) { $loaded = false; }
    } else {
     $loaded = defined( 'EPXADVSC_VER' ) && function_exists( 'cpas_scripts_manager' );
     if ( $loaded ) {
      $path = self::function_file( 'cpas_scripts_manager' );
      foreach ( $plugins as $candidate => $info ) {
       $directory = realpath( WP_PLUGIN_DIR . '/' . dirname( $candidate ) );
       if ( $directory && $path && ( dirname( $candidate ) === '.' ? $path === realpath( WP_PLUGIN_DIR . '/' . $candidate ) : str_starts_with( $path, $directory . DIRECTORY_SEPARATOR ) ) ) {
        $present = true;
        if ( $network ? is_plugin_active_for_network( $candidate ) : is_plugin_active( $candidate ) ) { $runtime_file = $candidate; break; }
       }
      }
      $version = (string) constant( 'EPXADVSC_VER' );
     }
    }
    // A runtime marker must identify a genuinely active installed main file.
    // Plugin names alone never prove activation or network-wide availability.
    if ( $runtime_file !== null && isset( $plugins[$runtime_file] ) ) { $present = true; }
    $active = $loaded && $runtime_file !== null && isset( $plugins[$runtime_file] )
     && ( $network ? is_plugin_active_for_network( $runtime_file ) : is_plugin_active( $runtime_file ) );
   }
			if ( ! $present ) { $issues[] = sprintf( Library::t( '%s is missing. Install and activate it first.' ), $dep['name'] ); }
			elseif ( ! $active ) { $issues[] = sprintf( $network ? Library::t( '%s must be network activated first.' ) : Library::t( '%s is installed but inactive. Activate it first.' ), $dep['name'] ); }
			elseif ( $dep['min_version'] !== '' && ( $version === '' || version_compare( $version, $dep['min_version'], '<' ) ) ) { $issues[] = sprintf( Library::t( 'Update %1$s to version %2$s or newer.' ), $dep['name'], $dep['min_version'] ); }
		}
		return $issues;
	}
 /**
  * Resolve the effective dependencies for the installed or offered target version.
  * @param array $entry Validated approved plugin metadata.
  * @param array $plugins Installed plugin header inventory.
  * @param bool $activation Whether to evaluate the installed target instead of the offered package.
  * @return array Dependency metadata applicable to the selected target version.
  */
 public static function dependencies_for( array $entry, array $plugins, bool $activation = true ): array {
  $target_version = $activation && isset( $plugins[$entry['plugin_file']] ) ? ( $plugins[$entry['plugin_file']]['Version'] ?? '' ) : $entry['version'];
  // Explicit host contract: OQN 2.0 keeps settings available without a builder.
  // This never loosens the requirements of the offered 1.0.0 package.
  $optional_oxygen = $entry['plugin_file'] === 'oxygen-quicknav/oxygen-quicknav.php'
   && $entry['repository'] === 'deckerweb/oxygen-quicknav'
   && is_string( $target_version ) && strlen( $target_version ) <= 64 && preg_match( '/^\d+\.\d+\.\d+(?:-[a-zA-Z0-9.-]+)?$/D', $target_version ) && version_compare( $target_version, '2.0.0-rc.1', '>=' );
  $dependencies = [];
  foreach ( $entry['dependencies'] as $dep ) { if ( ! $optional_oxygen || ( $dep['detector'] ?? '' ) !== 'oxygen' ) { $dependencies[] = $dep; } }
  return $dependencies;
 }

 /**
  * Resolve a loaded function's source once per request; do not cache absent functions.
  * @param string $name Fully qualified function name.
  * @return string|false Canonical declaring file, or false for an unavailable/internal function.
  */
 private static function function_file( string $name ) {
  static $files = [];
  if ( isset( $files[$name] ) ) { return $files[$name]; }
  if ( ! function_exists( $name ) ) { return false; }
  $file = ( new \ReflectionFunction( $name ) )->getFileName();
  if ( ! is_string( $file ) ) { return false; }
  $real = realpath( $file );
  if ( $real !== false ) { $files[$name] = $real; }
  return $real;
 }
}
