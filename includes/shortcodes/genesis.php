<?php

// includes/shortcodes/genesis

/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


if ( ! function_exists( 'ddw_bse_shortcode_genesis_footer' ) ) :

	add_shortcode( 'bse-genesis-footer', 'ddw_bse_shortcode_genesis_footer' );
	/**
	 * Shortcode to output the complete Genesis footer text, taken from the
	 *   Genesis Customizer settings since Genesis 3.1.0 or higher.
	 *
	 * @since 1.0.0
	 *
	 * @uses genesis_get_option()
	 * @uses ddw_bse_sanitize_html_classes()
	 * @uses genesis_strip_p_tags()
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `footer_copyright` shortcode.
	 */
	function ddw_bse_shortcode_genesis_footer( $atts ) {

		/** Bail early if not in needed Genesis context */
		if ( ! function_exists( 'genesis_get_option' ) || ! function_exists( 'genesis_strip_p_tags' ) ) {
			return '';
		}

		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/genesis_footer',
			[
				'class'     => '',
				'wrapper'   => 'p',
			]
		);

		/** Default shortcode attributes */
		$atts = shortcode_atts( $defaults, $atts, 'bse-genesis-footer' );

		$creds_text = wp_kses_post( genesis_get_option( 'footer_text' ) );

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-genesis-footer%2$s">%3$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			( ! empty( $atts[ 'class' ] ) ) ? ' ' . ddw_bse_sanitize_html_classes( $atts[ 'class' ], 'string' ) : '',
			do_shortcode( genesis_strip_p_tags( $creds_text ) )
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/genesis_footer',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_genesis_breadcrumbs' ) ) :

	add_shortcode( 'bse-genesis-breadcrumbs', 'ddw_bse_shortcode_genesis_breadcrumbs' );
	/**
	 * Shortcode to output the complete Genesis Breadcrumb container.
	 *
	 * @since 1.0.0
	 *
	 * @uses genesis_do_breadcrumbs()
	 * @uses ddw_bse_sanitize_html_classes()
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `breadcrumbs_copyright` shortcode.
	 */
	function ddw_bse_shortcode_genesis_breadcrumbs( $atts ) {

		/** Bail early if not in needed Genesis context */
		if ( ! function_exists( 'genesis_do_breadcrumbs' ) ) {
			return '';
		}

		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/genesis_breadcrumbs',
			[
				'class'     => '',
				'wrapper'   => 'div',
			]
		);

		/** Default shortcode attributes */
		$atts = shortcode_atts( $defaults, $atts, 'bse-genesis-breadcrumbs' );

		/** Create the Edit link via Output Buffering (we have no chance...) */
		ob_start();
		genesis_do_breadcrumbs();
		$genesis_breadcrumbs = ob_get_clean();

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-genesis-breadcrumbs%2$s">%3$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			( ! empty( $atts[ 'class' ] ) ) ? ' ' . ddw_bse_sanitize_html_classes( $atts[ 'class' ], 'string' ) : '',
			$genesis_breadcrumbs
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/genesis_breadcrumbs',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;
