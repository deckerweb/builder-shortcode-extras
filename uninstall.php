<?php
/**
 * Remove only this plugin's temporary updater cache and release shared ownership.
 * Copyright © 2019–2026 David Decker – DECKERWEB.
 * SPDX-License-Identifier: GPL-2.0-or-later
 *
 * @package BuilderShortcodeExtras
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
// Core keeps the first plugin's uninstall constant during a batch. This file
// owns only the BSE cache; shared ownership is checked by Library protocol 3.
$key = 'ddw_ghru_' . substr( md5( 'https://github.com/deckerweb/builder-shortcode-extras' ), 0, 24 );
if ( is_multisite() ) {
	$network_ids = get_networks( array( 'fields' => 'ids', 'number' => 0 ) );
	foreach ( $network_ids as $network_id ) {
		delete_network_option( (int) $network_id, '_site_transient_' . $key );
		delete_network_option( (int) $network_id, '_site_transient_timeout_' . $key );
		wp_cache_delete( $key, 'site-transient' );
	}
} else {
	delete_site_transient( $key );
}
require_once __DIR__ . '/includes/deckerweb-plugin-library/lifecycle.php';
deckerweb_library_uninstall_v3( __DIR__ . '/builder-shortcode-extras.php' );
