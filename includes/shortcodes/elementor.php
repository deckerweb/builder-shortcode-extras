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

	add_action( 'manage_' . Source_Local::CPT . '_posts_columns', 'ddw_bse_admin_columns_headers_elementor_templates' );
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
	 * @return string Echoing column content.
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
				$shortcode
			);

		}  // end if

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_elementor_template' ) ) :

	add_shortcode( 'bse-elementor-template', 'ddw_bse_shortcode_elementor_template' );
	/**
	 * Shortcode to output an Elementor Saved Template by given ID.
	 *
	 * @since 1.0.0
	 *
	 * @uses \Elementor\Plugin::$instance
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `footer_home_link` shortcode.
	 */
	function ddw_bse_shortcode_elementor_template( $atts = [] ) {

		/** Set default shortcode attributes */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/elementor_template',
			array(
				'id'  => '',
				'css' => 'false',
			)
		);

		/** Default shortcode attributes */
		$atts = shortcode_atts( $defaults, $atts, 'bse-elementor-template' );

		if ( empty( $atts[ 'id' ] ) ) {
			return '';
		}

		$include_css = false;

		if ( isset( $atts[ 'css' ] ) && 'false' !== $atts[ 'css' ] ) {
			$include_css = (bool) $atts[ 'css' ];
		}

		$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $atts[ 'id' ], $include_css );

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/elementor_template',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;
