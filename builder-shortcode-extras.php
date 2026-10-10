<?php # -*- coding: utf-8 -*-
/**
 * Plugin Name:       Builder Shortcode Extras
 * Plugin URI:        https://github.com/deckerweb/builder-shortcode-extras
 * Description:       Small dynamic helpers for your WordPress website. Find, configure and copy useful shortcodes.
 * Version:           1.3.0
 * Requires at least: 6.7
 * Requires PHP:      8.0
 * Author:            David Decker – DECKERWEB
 * Author URI:        https://github.com/deckerweb
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       builder-shortcode-extras
 * Domain Path:       /languages/
 * Update URI:        https://github.com/deckerweb/builder-shortcode-extras
 * GitHub Plugin URI: https://github.com/deckerweb/builder-shortcode-extras
 *
 * Copyright © 2019–2026 David Decker – DECKERWEB.
 * SPDX-License-Identifier: GPL-2.0-or-later
 */

/**
 * Exit if called directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


/**
 * Setting constants.
 *
 * @since 1.0.0
 */
/** Plugin version */
define( 'BSE_PLUGIN_VERSION', '1.3.0' );

/** Plugin directory */
define( 'BSE_PLUGIN_FILE', __FILE__ );

define( 'BSE_PLUGIN_DIR', trailingslashit( dirname( __FILE__ ) ) );

/** Plugin base directory */
define( 'BSE_PLUGIN_BASEDIR', trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) );


/** Include global functions */
require_once BSE_PLUGIN_DIR . 'includes/functions-global.php';
require_once BSE_PLUGIN_DIR . 'includes/functions-conditionals.php';
require_once BSE_PLUGIN_DIR . 'includes/functions-rendering.php';
require_once BSE_PLUGIN_DIR . 'includes/class-bse-shortcode-catalog.php';
require_once BSE_PLUGIN_DIR . 'includes/admin/class-bse-tools.php';
require_once BSE_PLUGIN_DIR . 'includes/class-bse-changelog.php';
require_once BSE_PLUGIN_DIR . 'includes/class-bse-guide.php';
require_once BSE_PLUGIN_DIR . 'includes/class-bse-github-updates.php';
\Deckerweb\BuilderShortcodeExtras\Tools::register();
( new \Deckerweb\BuilderShortcodeExtras\GitHubUpdates() )->register();
require_once BSE_PLUGIN_DIR . 'includes/deckerweb-plugin-library/bootstrap.php';
deckerweb_library_register_v2( __FILE__, [], BSE_PLUGIN_DIR . 'includes/deckerweb-plugin-library' );


add_action( 'init', 'ddw_bse_setup_plugin', 1 );
/**
 * Load shortcode callbacks, optional integrations and host translations.
 *
 * @return void Registers site-context hooks and shortcodes.
 */
function ddw_bse_setup_plugin() {

	load_plugin_textdomain( 'builder-shortcode-extras', false, dirname( plugin_basename( BSE_PLUGIN_FILE ) ) . '/languages/' );

	/** Include admin helper functions */
	if ( is_admin() ) {
		require_once BSE_PLUGIN_DIR . 'includes/admin/admin-extras.php';
	}

	/** All other Shortcodes as groups */
	/**
	 * Filter registered shortcode groups while preserving its value contract.
	 * @since 1.0.0
	 * @param mixed $value Current value. Return the replacement value.
	 */
	$shortcode_groups = apply_filters(
		'bse/filter/registered_shortcode_groups',
		[
			'info',
			'post',
			'metadata',
			'content',
			'integrations',
			'user',
			'version',
		]
	);

	// Load callbacks in every context; registration policy is handled separately.
	foreach ( (array) $shortcode_groups as $shortcode_group ) {
		if ( is_string( $shortcode_group ) && preg_match( '/^[a-z0-9_-]+$/D', $shortcode_group ) && is_readable( BSE_PLUGIN_DIR . 'includes/shortcodes/' . $shortcode_group . '.php' ) ) {
			require_once BSE_PLUGIN_DIR . 'includes/shortcodes/' . $shortcode_group . '.php';
		}
	}

	/** Load Integrations */
	require_once BSE_PLUGIN_DIR . 'includes/load-integrations.php';

	/** Enable Shortcodes in Widgets */
	add_filter( 'widget_text', 'do_shortcode' );

}  // end function
