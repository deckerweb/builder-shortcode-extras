<?php

use Elementor\TemplateLibrary\Source_Local;

// includes/shortcodes/elementor

/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


/**
 * Add post type list table column only for Elementor free version:
 * @since 1.0.0
 */
if ( ! ddw_bse_is_elementor_pro_active() ) :

	add_filter( 'manage_' . Source_Local::CPT . '_posts_columns', 'ddw_bse_admin_columns_headers_elementor_templates' );
	/**
	 * Add post type list table column for "Saved Templates" post type.
	 *
	 * @since 1.0.0
	 *
	 * @uses ddw_bse_string_shortcode()
	 *
	 * @param array $columns Holds all post type list table columns.
	 * @return array Modified array of post type list table columns.
	 */
	function ddw_bse_admin_columns_headers_elementor_templates( $columns ) {

		$columns[ 'shortcode' ] = ddw_bse_string_shortcode();

		return $columns;

	}  // end function


	add_action( 'manage_' . Source_Local::CPT . '_posts_custom_column', 'ddw_bse_admin_columns_content_elementor_templates', 10, 2 );
	/**
	 * Add the "Shortcode" content for the post type list table for "Saved
	 *   Templates" post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name Name of the column the content gets added for.
	 * @param string $post_id     Id of the current post type item, for table row.
	 * @return void Emits the escaped shortcode input for the selected column.
	 */
	function ddw_bse_admin_columns_content_elementor_templates( $column_name, $post_id ) {

		if ( 'shortcode' === $column_name ) {

			/** %s = shortcode tag, %d = post_id */
			$shortcode = esc_attr(
				sprintf(
					'[%s id="%d"]',
					'bse-elementor-template',
					$post_id
				)
			);

			printf(
				'<input class="bse-elementor-shortcode-input widefat" type="text" readonly onfocus="this.select()" value="%s" />',
				esc_attr( $shortcode )
			);

		}  // end if

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_elementor_template' ) ) :

	ddw_bse_register_shortcode( 'bse-elementor-template', 'ddw_bse_shortcode_elementor_template' );
	/**
	 * Render a readable saved Elementor template through the supported builder API.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_elementor_template( $atts = [] ) {

		/**
		 * Filter shortcode defaults/elementor template while preserving its value contract.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/elementor_template',
			array(
				'id'  => '',
				'css' => 'false',
			)
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-elementor-template' );

		if ( empty( $atts[ 'id' ] ) ) {
			return '';
		}

		$output = ddw_bse_render_item_content( absint( $atts['id'] ), ddw_bse_boolean( $atts['css'] ), 'elementor' );

		/**
		 * Filter shortcode output; callbacks are trusted developer code.
		 * @since 1.0.0
		 * @param string $output HTML/text value. Return the replacement output.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters(
			'bse/filter/shortcode/elementor_template',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;
