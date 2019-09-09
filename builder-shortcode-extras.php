<?php # -*- coding: utf-8 -*-
/**
 * Main plugin file.
 * @package           Builder Shortcode Extras
 * @author            David Decker
 * @copyright         Copyright (c) 2019, David Decker - DECKERWEB
 * @license           GPL-2.0-or-later
 * @link              https://deckerweb.de/twitter
 *
 * @wordpress-plugin
 * Plugin Name:       Builder Shortcode Extras
 * Plugin URI:        https://github.com/deckerweb/builder-shortcode-extras
 * Description:       A collection of totally useful extra Shortcodes to make the life of Site Builders more easy.
 * Version:           0.9.0
 * Author:            David Decker - DECKERWEB
 * Author URI:        https://deckerweb-plugins.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://opensource.org/licenses/GPL-2.0
 * Text Domain:       builder-shortcode-extras
 * Domain Path:       /languages/
 * Requires WP:       4.7
 * Requires PHP:      5.6
 * GitHub Plugin URI: https://github.com/deckerweb/builder-shortcode-extras
 * GitHub Branch:     master
 *
 * Copyright (c) 2019 David Decker - DECKERWEB
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
define( 'BSE_PLUGIN_VERSION', '0.9.0' );

/** Plugin directory */
define( 'BSE_PLUGIN_DIR', trailingslashit( dirname( __FILE__ ) ) );

/** Plugin base directory */
define( 'BSE_PLUGIN_BASEDIR', trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) );


add_action( 'init', 'ddw_bse_load_translations', 1 );
/**
 * Load the text domain for translation of the plugin.
 *
 * @since 1.0.0
 *
 * @uses get_user_locale()
 * @uses get_locale()
 * @uses load_textdomain() To load translations first from WP_LANG_DIR sub folder.
 * @uses load_plugin_textdomain() To additionally load default translations from plugin folder (default).
 */
function ddw_bse_load_translations() {

	/** Set unique textdomain string */
	$bse_textdomain = 'builder-shortcode-extras';

	/** The 'plugin_locale' filter is also used by default in load_plugin_textdomain() */
	$locale = esc_attr(
		apply_filters(
			'plugin_locale',
			get_user_locale(),	//is_admin() ? get_user_locale() : get_locale(),
			$bse_textdomain
		)
	);

	/**
	 * WordPress languages directory
	 *   Will default to: wp-content/languages/builder-shortcode-extras/builder-shortcode-extras-{locale}.mo
	 */
	$bse_wp_lang_dir = trailingslashit( WP_LANG_DIR ) . trailingslashit( $bse_textdomain ) . $bse_textdomain . '-' . $locale . '.mo';

	/** Translations: First, look in WordPress' "languages" folder = custom & update-safe! */
	load_textdomain(
		$bse_textdomain,
		$bse_wp_lang_dir
	);

	/** Translations: Secondly, look in 'wp-content/languages/plugins/' for the proper .mo file (= default) */
	load_plugin_textdomain(
		$bse_textdomain,
		FALSE,
		BSE_PLUGIN_BASEDIR . 'languages'
	);

}  // end function


/** Include global functions */
require_once BSE_PLUGIN_DIR . 'includes/functions-global.php';
require_once BSE_PLUGIN_DIR . 'includes/functions-conditionals.php';


add_action( 'init', 'ddw_bse_setup_plugin', 1 );
/**
 * Finally setup the plugin for the main tasks.
 *   Note: The setup fires after all activation checks and routines.
 *
 *   With the filter 'bse/filter/shortcodes_in_admin' the Shortcodes can be made
 *   available within the admin, for whatever purpose - help pages for clients,
 *   in admin footer, whereever you may need them. But use at your own risk!
 *
 * @since 1.0.0
 */
function ddw_bse_setup_plugin() {

	/** Include admin helper functions */
	if ( is_admin() ) {
		require_once BSE_PLUGIN_DIR . 'includes/admin/admin-extras.php';
	}

	/** All other Shortcodes as groups */
	$shortcode_groups = apply_filters(
		'bse/filter/registered_shortcode_groups',
		[
			'info',
			'post',
			'content',
			'integrations',
			'user',
			'version',
		]
	);

	foreach ( $shortcode_groups as $shortcode_group ) {

		if ( ! is_admin() || ! is_network_admin() ) {

			require_once BSE_PLUGIN_DIR . 'includes/shortcodes/' . sanitize_key( $shortcode_group ) . '.php';

		} elseif ( apply_filters( 'bse/filter/shortcodes_in_admin', FALSE ) ) {

			if ( 'content' === $shortcode_group ) {
				continue;
			}

			if ( is_admin() || is_network_admin() ) {
				require_once BSE_PLUGIN_DIR . 'includes/shortcodes/' . sanitize_key( $shortcode_group ) . '.php';
			}

		}  // end if

	}  // end foreach

	/** Load Integrations */
	require_once BSE_PLUGIN_DIR . 'includes/load-integrations.php';

	/** Enable Shortcodes in Widgets */
	add_filter( 'widget_text', 'do_shortcode' );

}  // end function
