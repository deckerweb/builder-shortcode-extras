<?php

// includes/functions-conditionals

/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


/**
 * Is Elementor (free) plugin active or not?
 *
 * @since 1.0.0
 *
 * @return bool TRUE if Elementor is active, FALSE otherwise.
 */
function ddw_bse_is_elementor_active() {

	return defined( 'ELEMENTOR_VERSION' );

}  // end function


/**
 * Is Elementor Pro plugin active or not?
 *
 * @since 1.0.0
 *
 * @return bool TRUE if Elementor Pro active, FALSE otherwise.
 */
function ddw_bse_is_elementor_pro_active() {

	return defined( 'ELEMENTOR_PRO_VERSION' );

}  // end function


/**
 * Is Genesis Framework plugin active or not?
 *   (A Premium theme by StudioPress/ Rainmaker Digital, LLC)
 *   NOTE: Usage of Genesis without Child Theme is absolutely NOT recommended,
 *         therefore Toolbar Extras plugin does not support that!
 *
 * @since 1.0.0
 *
 * @return bool TRUE if Genesis is active, FALSE otherwise.
 */
function ddw_bse_is_genesis_active() {

	return ( 'genesis' === wp_basename( get_template_directory() )
		&& ( defined( 'PARENT_THEME_VERSION' ) && version_compare( PARENT_THEME_VERSION, '3.1.0', '>=' ) )
	);

}  // end function


/**
 * Check if Astra theme is active or not.
 *
 * @since 1.0.0
 *
 * @return bool TRUE if constant defined, FALSE otherwise.
 */
function ddw_bse_is_astra_active() {

	return ( defined( 'ASTRA_THEME_VERSION' ) );

}  // end function


/**
 * Check if "Astra Custom Layouts" module is active or not.
 *   Note: Only via Astra Pro Add-On plugin.
 *
 * @since 1.0.0
 *
 * @return bool TRUE if module is active, FALSE otherwise.
 */
function ddw_bse_is_astra_custom_layouts_active() {

	//return ( defined( 'ASTRA_EXT_VER' ) || class_exists( 'Astra_Addon_Update' ) );
	return ( class_exists( 'Astra_Ext_Extension' ) && Astra_Ext_Extension::is_active( 'advanced-hooks' ) );

}  // end function


/**
 * Check for various conditions (admin screens etc.) if the display of Admin
 *   Notice for plugin review is allowed/ wanted.
 *
 * @since 1.0.0
 *
 * @param object $current_screen This global (via get_current_screen()) holds
 *                               the current screen object.
 * @return bool If current screen matches the conditions return TRUE, FALSE
 *              otherwise.
 */
function ddw_bse_is_notice_review_allowed( $current_screen ) {

	$needle_mainwp = 'mainwp';

	/** For specific cases & admin screens don't show the notice */
	if ( ( 'edit' == $current_screen->base || 'post' == $current_screen->base || 'post-new' == $current_screen->base )
		|| is_network_admin()
		|| ( strpos( $needle_mainwp, $current_screen->base ) !== FALSE )
	) {
		return FALSE;
	}

	/** By default let return TRUE (notice will appear) */
	return TRUE;

}  // end function





/**
 * 5) Block Editor (Gutenberg) integrations - WP Core, plugins etc.:
 * @since 1.0.0
 * -----------------------------------------------------------------------------
 */

/**
 * Check if the "Blocks Editor" is available or not. This can currently mean:
 *   1) WordPress is in version 5.0.0+ (will contain blocks editor by default)
 *   2) or, the "Gutenberg" plugin is active (it is the blocks editor)
 *
 * @since 1.0.0
 *
 * @global string $GLOBALS[ 'wp_version' ] 
 * @return bool TRUE if blocks editor available, FALSE otherwise.
 */
function ddw_bse_is_block_editor_active() {

	if ( version_compare( $GLOBALS[ 'wp_version' ], '5.0-beta1', '>=' )
		|| defined( 'GUTENBERG_VERSION' )
	) {
		return TRUE;
	}

	return FALSE;

}  // end function


/**
 * Is the Classic Editor plugin active or not?
 *
 * @since 1.0.0
 *
 * @return bool TRUE if plugin is active, FALSE otherwise.
 */
function ddw_bse_is_classic_editor_plugin_active() {

	return class_exists( 'Classic_Editor' );

}  // end function


/**
 * Is the Classic Editor Add-On plugin active or not?
 *
 * @since 1.0.0
 *
 * @return bool TRUE if Add-On plugin is active, FALSE otherwise.
 */
function ddw_bse_is_classic_editor_addon_active() {

	return function_exists( 'classic_editor_addon_post_init' );

}  // end function


/**
 * Is the Disable Gutenberg plugin active or not?
 *
 * @since 1.0.0
 *
 * @return bool TRUE if plugin is active, FALSE otherwise.
 */
function ddw_bse_is_disable_gutenberg_active() {

	return class_exists( 'DisableGutenberg' );

}  // end function


/**
 * Is the Gutenberg Ramp plugin active or not?
 *
 * @since 1.0.0
 *
 * @return bool TRUE if plugin is active, FALSE otherwise.
 */
function ddw_bse_is_gutenberg_ramp_active() {

	return class_exists( 'Gutenberg_Ramp' );

}  // end function


/**
 * Is the Gutenberg Manager plugin active or not?
 *
 * @since 1.0.0
 *
 * @return bool TRUE if plugin is active, FALSE otherwise.
 */
function ddw_bse_is_gutenberg_manager_active() {

	return function_exists( 'gm_check_gutenberg' );

}  // end function


/**
 * Is the No Gutenberg plugin active or not?
 *
 * @since 1.0.0
 *
 * @return bool TRUE if plugin is active, FALSE otherwise.
 */
function ddw_bse_is_nogutenberg_plugin_active() {

	return function_exists( 'no_gutenberg_init' );

}  // end function


/**
 * Is the Disable WP Block Editor plugin active or not?
 *
 * @since 1.0.0
 *
 * @return bool TRUE if plugin is active, FALSE otherwise.
 */
function ddw_bse_is_disable_wp_block_editor_active() {

	return function_exists( 'dwgu_disable_update' );

}  // end function


/**
 * Is the Disable WordPress "Gutenberg" Update plugin active or not?
 *
 * @since 1.0.0
 *
 * @return bool TRUE if plugin is active, FALSE otherwise.
 */
function ddw_bse_is_disable_wpgutenberg_update_active() {

	return function_exists( 'dwgu_disable_update' );

}  // end function


/**
 * When Block Editor is active, check if any external plugin is deactiving the
 *   Block Editor.
 *
 * @since 1.0.0
 *
 * @uses ddw_bse_is_block_editor_active()
 * @uses ddw_bse_is_classic_editor_plugin_active()
 * @uses ddw_bse_is_classic_editor_addon_active()
 * @uses ddw_bse_is_disable_gutenberg_active()
 * @uses ddw_bse_is_gutenberg_ramp_active()
 * @uses ddw_bse_is_nogutenberg_plugin_active()
 * @uses ddw_bse_is_disable_wp_block_editor_active()
 * @uses ddw_bse_is_disable_wpgutenberg_update_active()
 * @uses ddw_bse_is_gutenberg_manager_active()
 *
 * @return bool TRUE if certain popular plugins are NOT globally disabling the
 *              Block Editor.
 */
function ddw_bse_is_block_editor_wanted() {

	/** Bail early if Block Editor isn't active at all */
	if ( ! ddw_bse_is_block_editor_active() ) {
		return FALSE;
	}

	/**
	 * For: "Classic Editor Add-On" plugin (it deactivates Block Editor
	 *   completely, automatically).
	 *   FALSE when plugin is active.
	 */
	if ( ddw_bse_is_classic_editor_addon_active() ) {
		return FALSE;
	}


	/**
	 * For: "No Gutenberg" plugin (deactivates Block Editor completely).
	 *   FALSE when plugin is active.
	 */
	if ( ddw_bse_is_nogutenberg_plugin_active() ) {
		return FALSE;
	}


	/**
	 * For: "No Gutenberg" plugin (deactivates Block Editor completely).
	 *   FALSE when plugin is active.
	 */
	if ( ddw_bse_is_disable_wp_block_editor_active() ) {
		return FALSE;
	}


	/**
	 * For: "Disable WordPress 'Gutenberg' Update" plugin (keeps WordPress on
	 *   the legacy 4.9 branch - no updates to 5.0+).
	 *   FALSE when plugin is active.
	 */
	if ( ddw_bse_is_disable_wpgutenberg_update_active() ) {
		return FALSE;
	}


	/**
	 * For: "Classic Editor" plugin (there are various options we need to check).
	 *   FALSE when "Classic Editor" is set and users cannot change editor.
	 */
	$classic_type = get_option( 'classic-editor-replace' );
	$classic_user = get_option( 'classic-editor-allow-users' );

	if ( ddw_bse_is_classic_editor_plugin_active()
		&& ( isset( $classic_type ) && 'classic' === $classic_type )
		&& ( isset( $classic_user ) && 'disallow' === $classic_user )
	) {
		return FALSE;
	}


	/**
	 * For: "Disable Gutenberg" plugin (there are various options we need to
	 *   check).
	 *   FALSE when option 'disable for all' is set.
	 */
	$g7g_options = get_option( 'disable_gutenberg_options', 'default_disabled_all_not_saved' );

	if ( ddw_bse_is_disable_gutenberg_active()
		&& (
			( 'default_disabled_all_not_saved' === $g7g_options )
			|| ( isset( $g7g_options[ 'disable-all' ] ) && 1 === $g7g_options[ 'disable-all' ] )
		)
	) {
		return FALSE;
	}


	/**
	 * For: "Gutenberg Ramp" plugin (there are various options we need to check).
	 */
	if ( ddw_bse_is_gutenberg_ramp_active() ) {

		$gutenberg_ramp = Gutenberg_Ramp::get_instance();
		$gbramp_types   = get_option( 'gutenberg_ramp_post_types' );

		/**
		 * FALSE when no $criteria set & no post types in settings are set
		 */
		if ( FALSE === $gutenberg_ramp->active && empty( $gbramp_types ) ) {
			return FALSE;
		}

	}  // end if


	/**
	 * For: "Gutenberg Manager" plugin.
	 */
	if ( ddw_bse_is_gutenberg_manager_active() ) {

		$gb_manager = get_option( 'gm-global-disable' );

		/**
		 * FALSE when option is set to global disable Gutenberg
		 */
		if ( 1 === absint( $gb_manager ) ) {
			return FALSE;
		}

	}  // end if

	/** For: Default - TRUE */
	return TRUE;

}  // end function
