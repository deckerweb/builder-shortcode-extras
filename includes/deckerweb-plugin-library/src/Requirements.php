<?php
namespace Deckerweb\PluginLibrary\V0_2_0;
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Used both when rendering cards and immediately before install/activation. */
final class Requirements {
	public static function check( array $entry, ?array $plugins = null, ?bool $network = null ): array {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$plugins = $plugins ?? get_plugins();
		$network = $network ?? ( is_multisite() && is_network_admin() );
		$issues = [];
		if ( $network && isset( $entry['network_activation'] ) && ! $entry['network_activation'] ) { $issues[] = sprintf( Library::t( '%s supports activation per site only.', '%s unterstützt nur die Aktivierung je Website.' ), $entry['name'] ); }
		global $wp_version;
		if ( version_compare( $wp_version, $entry['requires_wp'], '<' ) ) { $issues[] = sprintf( Library::t( 'Requires WordPress %s or newer.', 'Benötigt WordPress %s oder neuer.' ), $entry['requires_wp'] ); }
		if ( version_compare( PHP_VERSION, $entry['requires_php'], '<' ) ) { $issues[] = sprintf( Library::t( 'Requires PHP %s or newer.', 'Benötigt PHP %s oder neuer.' ), $entry['requires_php'] ); }
		foreach ( $entry['dependencies'] as $dep ) {
			$file = $dep['plugin_file'];
			$present = isset( $plugins[$file] );
			$active = $network ? is_plugin_active_for_network( $file ) : is_plugin_active( $file );
			$version = $plugins[$file]['Version'] ?? '';
			// Official Breakdance constant also detects installations in a renamed folder.
			// It is only sufficient for site activation, never for a network-wide activation.
			if ( ! $network && ( $dep['detector'] ?? '' ) === 'breakdance' && defined( '__BREAKDANCE_VERSION' ) ) {
				$present = true; $active = true; $version = (string) constant( '__BREAKDANCE_VERSION' );
			}

			$detector = $dep['detector'] ?? '';
			if ( $detector === 'bricks' ) {
				if ( $network ) {
					$issues[] = Library::t( 'Activate Bricks QuickNav per site after selecting the Bricks parent or child theme.', 'Bricks QuickNav bitte je Website aktivieren, nachdem das Bricks-Theme oder ein Child-Theme ausgewählt wurde.' );
					continue;
				}
				$theme = wp_get_theme( get_template() );
				$present = $theme->exists() && $theme->get( 'Name' ) === 'Bricks';
				$active = $present && defined( 'BRICKS_VERSION' ) && function_exists( 'bricks_is_builder' );
				$version = $theme->get( 'Version' );
			}
			if ( $detector === 'oxygen' || $detector === 'advanced_scripts' ) {
				$loaded = $detector === 'oxygen'
					? ( defined( 'BREAKDANCE_MODE' ) && constant( 'BREAKDANCE_MODE' ) === 'oxygen' && defined( '__BREAKDANCE_VERSION' ) )
					: ( defined( 'EPXADVSC_VER' ) && function_exists( 'cpas_scripts_manager' ) );
				// Runtime markers also support renamed plugin directories. On a network,
				// a matching active network plugin is required in addition to the marker.
				foreach ( $plugins as $candidate => $info ) {
					$expected = $detector === 'oxygen' ? 'Oxygen' : 'Advanced Scripts';
					if ( isset( $info['Name'] ) && $info['Name'] === $expected ) {
						$present = true;
						if ( $network && is_plugin_active_for_network( $candidate ) ) { $active = true; }
					}
				}
				if ( ! $network && $loaded ) { $present = true; $active = true; }
				$active = $active && $loaded;
				if ( $loaded ) { $version = (string) constant( $detector === 'oxygen' ? '__BREAKDANCE_VERSION' : 'EPXADVSC_VER' ); }
			}
			if ( ! $present ) { $issues[] = sprintf( Library::t( '%s is missing. Install and activate it first.', '%s fehlt. Bitte zuerst installieren und aktivieren.' ), $dep['name'] ); }
			elseif ( ! $active ) { $issues[] = sprintf( $network ? Library::t( '%s must be network activated first.', '%s muss zuerst netzwerkweit aktiviert werden.' ) : Library::t( '%s is installed but inactive. Activate it first.', '%s ist installiert, aber inaktiv. Bitte zuerst aktivieren.' ), $dep['name'] ); }
			elseif ( $dep['min_version'] !== '' && ( $version === '' || version_compare( $version, $dep['min_version'], '<' ) ) ) { $issues[] = sprintf( Library::t( 'Update %1$s to version %2$s or newer.', '%1$s bitte auf Version %2$s oder neuer aktualisieren.' ), $dep['name'], $dep['min_version'] ); }
		}
		return $issues;
	}
}
