<?php

// includes/shortcodes/integrations

/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


add_action( 'init', 'ddw_bse_run_integrations' );
/**
 * Run all currently registered and active integrations.
 *
 * @since 1.0.0
 *
 * @uses ddw_bse_get_integrations()
 * @uses ddw_bse_admin_columns_headers_integrations_post_type()
 * @uses ddw_bse_admin_columns_content_integrations_post_type()
 * @uses ddw_bse_shortcode_item_content()
 */
function ddw_bse_run_integrations() {

	$integrations = ddw_bse_get_integrations();

	foreach ( $integrations as $integration ) {
		
		if ( 'default-none' === $integration ) {
			continue;
		}

		$pt_string = sanitize_key( str_replace( '-', '_' , $integration[ 'post_type' ] ) );

		add_action( "manage_{$integration[ 'post_type' ]}_posts_columns", 'ddw_bse_admin_columns_headers_integrations_post_type' );

		add_action( "manage_{$integration[ 'post_type' ]}_posts_custom_column", "ddw_bse_admin_columns_content_integrations_post_type", 10, 2 );


		/** Add the actual Shortcode for this integration */
		if ( function_exists( 'ddw_bse_shortcode_item_content' ) ) {
			add_shortcode( "bse-{$integration[ 'shortcode_tag' ]}", 'ddw_bse_shortcode_item_content' );
		}

	}  // end foreach

}  // end function


/**
 * Add post type list table column for "Shortcode" for the given, supported and
 *   active integration.
 *
 * @since 1.0.0
 *
 * @uses ddw_bse_string_shortcode()
 *
 * @param array $columns Holds all post type list table columns.
 * @return array Modified array of post type list table columns.
 */
function ddw_bse_admin_columns_headers_integrations_post_type( $columns ) {

	$columns[ 'shortcode' ] = ddw_bse_string_shortcode();

	return $columns;

}  // end function


/**
 * Add the "Shortcode" column content to the post type list table for the given,
 *   supported and active integration.
 *
 * @since 1.0.0
 *
 * @uses ddw_bse_get_integrations()
 *
 * @param string $column_name Name of the column the content gets added for.
 * @param string $post_id     Id of the current post type item, for table row.
 * @return string Echoing column content.
 */
function ddw_bse_admin_columns_content_integrations_post_type( $column_name, $post_id ) {

	$integrations = ddw_bse_get_integrations();

	foreach ( $integrations as $integration ) {
			
		if ( 'shortcode' === $column_name && 'no-shortcode-tag' !== $integration[ 'shortcode_tag' ] ) {

			// %s = shortcode, %d = post_id
			$shortcode = esc_attr( sprintf(
				'[%s id="%d"]',
				'bse-' . $integration[ 'shortcode_tag' ],
				$post_id
			) );

			printf(
				'<input class="bse-%s-shortcode-input" type="text" readonly onfocus="this.select()" value="%s" />',
				$integration[ 'shortcode_tag' ],
				$shortcode
			);

		}  // end if

	}  // end foreach

}  // end function
