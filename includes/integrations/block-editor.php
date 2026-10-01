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
 * Register WordPress Synced Patterns (post type: wp_block).
 *
 * @since 1.0.0
 *
 * @param array $integrations Holds array of all registered integrations.
 * @return array Tweaked array of registered integrations.
 */
function ddw_bse_register_integration_wpblocks( array $integrations ) {

	$integrations[ 'wp-reusable-blocks' ] = array(
		'label'         => __( 'WordPress Synced Patterns', 'builder-shortcode-extras' ),
		'post_type'     => 'wp_block',
		'shortcode_tag' => 'wpblock',
	);

	return $integrations;

}  // end function
