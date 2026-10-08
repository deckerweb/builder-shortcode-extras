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

	ddw_bse_register_shortcode( 'bse-genesis-footer', 'ddw_bse_shortcode_genesis_footer' );
	/**
	 * Render the configured Genesis footer text through its public theme API.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_genesis_footer( $atts ) {

		/** Bail early if not in needed Genesis context */
		if ( ! function_exists( 'genesis_get_option' ) || ! function_exists( 'genesis_strip_p_tags' ) ) {
			return '';
		}

		/**
		 * Filter shortcode defaults/genesis footer while preserving its value contract.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/genesis_footer',
			[
				'class'     => '',
				'wrapper'   => 'p',
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-genesis-footer' );

		$creds_text = wp_kses_post( genesis_get_option( 'footer_text' ) );

		$output = sprintf(
			'<%1$s class="bse-genesis-footer%2$s">%3$s</%1$s>',
			ddw_bse_wrapper_tag( $atts[ 'wrapper' ] ),
			( ! empty( $atts[ 'class' ] ) ) ? ' ' . ddw_bse_sanitize_html_classes( $atts[ 'class' ], 'string' ) : '',
			do_shortcode( genesis_strip_p_tags( $creds_text ) )
		);

		/**
		 * Filter shortcode output; callbacks are trusted developer code.
		 * @since 1.0.0
		 * @param string $output HTML/text value. Return the replacement output.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters(
			'bse/filter/shortcode/genesis_footer',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_genesis_breadcrumbs' ) ) :

	ddw_bse_register_shortcode( 'bse-genesis-breadcrumbs', 'ddw_bse_shortcode_genesis_breadcrumbs' );
	/**
	 * Capture Genesis breadcrumbs through its public theme API.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_genesis_breadcrumbs( $atts ) {

		/** Bail early if not in needed Genesis context */
		if ( ! function_exists( 'genesis_do_breadcrumbs' ) ) {
			return '';
		}

		/**
		 * Filter shortcode defaults/genesis breadcrumbs while preserving its value contract.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/genesis_breadcrumbs',
			[
				'class'     => '',
				'wrapper'   => 'div',
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-genesis-breadcrumbs' );

		/** Create the Edit link via Output Buffering (we have no chance...) */
		ob_start();
		genesis_do_breadcrumbs();
		$genesis_breadcrumbs = ob_get_clean();

		$output = sprintf(
			'<%1$s class="bse-genesis-breadcrumbs%2$s">%3$s</%1$s>',
			ddw_bse_wrapper_tag( $atts[ 'wrapper' ] ),
			( ! empty( $atts[ 'class' ] ) ) ? ' ' . ddw_bse_sanitize_html_classes( $atts[ 'class' ], 'string' ) : '',
			$genesis_breadcrumbs
		);

		/**
		 * Filter shortcode output; callbacks are trusted developer code.
		 * @since 1.0.0
		 * @param string $output HTML/text value. Return the replacement output.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters(
			'bse/filter/shortcode/genesis_breadcrumbs',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;
