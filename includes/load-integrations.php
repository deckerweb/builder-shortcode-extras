<?php

// includes/load-integrations

/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


/**
 * 1) Special Shortcodes for optional Integrations (Plugins):
 * @since 1.0.0
 * -----------------------------------------------------------------------------
 */

/**
 * Elementor specific Shortcode (for free version only)
 * @since 1.0.0
 */
if ( ddw_bse_is_elementor_active() && ! ddw_bse_is_elementor_pro_active() ) {
	require_once BSE_PLUGIN_DIR . 'includes/shortcodes/elementor.php';
}

/**
 * Genesis specific Shortcode (for 3.1.0+ only)
 * @since 1.0.0
 */
if ( ddw_bse_is_genesis_active() ) {
	require_once BSE_PLUGIN_DIR . 'includes/shortcodes/genesis.php';
}

/** Astra specific Shortcode (for Astra Pro only) */
if ( ddw_bse_is_astra_active() && ddw_bse_is_astra_custom_layouts_active() ) {
	require_once BSE_PLUGIN_DIR . 'includes/integrations/astra.php';
}



/**
 * 2) Post Type Integrations - Core, Plugins, Themes:
 * @since 1.0.0
 * -----------------------------------------------------------------------------
 */

/**
 * Block Editor (Gutenberg) - WP Core
 * @since 1.0.0
 */
if ( ddw_bse_is_block_editor_active() && ddw_bse_is_block_editor_wanted() ) {
	require_once BSE_PLUGIN_DIR . 'includes/integrations/block-editor.php';
}


/**
 * Astra Custom Layouts specific Shortcode (for Astra Pro only)
 * @since 1.0.0
 */
if ( ddw_bse_is_astra_active() && ddw_bse_is_astra_custom_layouts_active() ) {
	require_once BSE_PLUGIN_DIR . 'includes/integrations/astra.php';
}
