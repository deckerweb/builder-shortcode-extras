<?php

// includes/integrations/block-editor

/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


add_filter( 'bse/filter/integrations/all', 'ddw_bse_register_integration_wpblocks' );
/**
 * Register WordPress Reusable Blocks (post type: wp_block).
 *
 * @since 1.0.0
 *
 * @param array $integrations Holds array of all registered integrations.
 * @return array Tweaked array of registered integrations.
 */
function ddw_bse_register_integration_wpblocks( array $integrations ) {

	$integrations[ 'wp-reusable-blocks' ] = array(
		'label'         => __( 'WordPress Reusable Blocks', 'builder-shortcode-extras' ),
		'post_type'     => 'wp_block',
		'shortcode_tag' => 'wpblock',
	);

	return $integrations;

}  // end function


add_action( 'admin_menu', 'ddw_bse_maybe_add_menu_wpblock_posttype', 20 );
/**
 * Add the appropriate admin menu - using the post type list table page as
 *   the callback.
 *
 * @since 1.0.0
 *
 * @see WP Core /wp-includes/post.php for 'wp_block' capabilities
 *
 * @uses add_menu_page()
 * @uses add_submenu_page()
 *
 * @global string $GLOBALS[ 'admin_page_hooks' ]
 */
function ddw_bse_maybe_add_menu_wpblock_posttype() {
	
	/**
	 * Bail early if the same stuff as below was already added by other plugins.
	 */
	if ( ! empty( $GLOBALS[ 'admin_page_hooks' ][ 'edit.php?post_type=wp_block' ] ) ) {
		return;
	}

	/** Add "Blocks" top-level Admin menu, below "Comments" */
    add_menu_page(
        _x( 'Reusable Blocks', 'Admin page title', 'builder-shortcode-extras' ),
        _x( 'Blocks', 'Admin menu label', 'builder-shortcode-extras' ),
        'publish_posts',
        'edit.php?post_type=wp_block',
        '',
        'dashicons-screenoptions',
        '25.1'	// directly after comments
    );

    /** "Blocks" submenu: "Add New" (Reusable Block) */
    add_submenu_page(
    	'edit.php?post_type=wp_block',
        _x( 'Add New Reusable Block', 'Admin page title', 'builder-shortcode-extras' ),
        _x( 'Add New', 'Admin menu label', 'builder-shortcode-extras' ),
        'publish_posts',
        'post-new.php?post_type=wp_block'
    );

}  // end function
