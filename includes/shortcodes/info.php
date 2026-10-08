<?php

// includes/shortcodes/info

/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


if ( ! function_exists( 'ddw_bse_shortcode_copyright' ) ) :

	ddw_bse_register_shortcode( 'bse-copyright', 'ddw_bse_shortcode_copyright' );
	/**
	 * Render a copyright symbol and the current year or a year range.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_copyright( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/copyright',
			[
				'after'     => '',
				'before'    => '',
				'copyright' => '&#x000A9;',
				'first'     => '',
				'class'     => '',
				'wrapper'   => 'span',
			]
		);
		$atts = shortcode_atts( $defaults, $atts, 'bse-copyright' );
		$year = wp_date( 'Y' );
		$first = (string) $atts['first'];
		$years = $first !== '' && $first !== $year ? esc_html( $first ) . '–' . $year : $year;
		$html = esc_html( html_entity_decode( $atts['copyright'], ENT_QUOTES, 'UTF-8' ) ) . '&nbsp;' . $years;
		$output = ddw_bse_metadata_wrapper( $atts, 'bse-copyright', $html, true );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/bse_copyright', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_site_title' ) ) :

	ddw_bse_register_shortcode( 'bse-site-title', 'ddw_bse_shortcode_site_title' );
	/**
	 * Render the current website title as escaped text.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_site_title( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/site_title',
			[
				'after'   => '',
				'before'  => '',
				'class'   => '',
				'wrapper' => 'span',
			]
		);
		$atts = shortcode_atts( $defaults, $atts, 'bse-site-title' );
		$text = get_bloginfo( 'name' );
		if ( $text === '' ) {
			return '';
		}
		$html = esc_html( $text );
		$output = ddw_bse_metadata_wrapper( $atts, 'bse-site-title', $html, true );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/site_title', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_site_slogan' ) ) :

	ddw_bse_register_shortcode( 'bse-site-slogan', 'ddw_bse_shortcode_site_slogan' );
	/**
	 * Render the current website description as escaped text.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_site_slogan( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/site_slogan',
			[
				'after'   => '',
				'before'  => '',
				'class'   => '',
				'wrapper' => 'span',
			]
		);
		$atts = shortcode_atts( $defaults, $atts, 'bse-site-slogan' );
		$text = get_bloginfo( 'description' );
		if ( $text === '' ) {
			return '';
		}
		$html = esc_html( $text );
		$output = ddw_bse_metadata_wrapper( $atts, 'bse-site-slogan', $html, true );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/site_slogan', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_home_link' ) ) :

	ddw_bse_register_shortcode( 'bse-home-link', 'ddw_bse_shortcode_home_link' );
	/**
	 * Render an escaped link to the current website homepage.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_home_link( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/home_link',
			[
				'after'   => '',
				'before'  => '',
				'text'    => get_bloginfo( 'name' ),
				'target'  => '',
				'rel'     => '',
				'class'   => '',
				'wrapper' => 'span',
			]
		);
		$atts = shortcode_atts( $defaults, $atts, 'bse-home-link' );
		$html = '<a href="' . esc_url( home_url( '/' ) ) . '"' . ddw_bse_metadata_link_attributes( $atts ) . '>' . esc_html( $atts['text'] ) . '</a>';
		$output = ddw_bse_metadata_wrapper( $atts, 'bse-home-link', $html, true );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/home_link', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_loginout' ) ) :

	ddw_bse_register_shortcode( 'bse-loginout', 'ddw_bse_shortcode_loginout' );
	/**
	 * Render the current user’s login or logout link with optional redirects.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_loginout( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/loginout',
			[
				'after'           => '',
				'before'          => '',
				'login_text'      => __( 'Log in', 'builder-shortcode-extras' ),
				'logout_text'     => __( 'Log out', 'builder-shortcode-extras' ),
				'login_target'    => '',
				'logout_target'   => '',
				'login_redirect'  => '',
				'logout_redirect' => '',
				'class'           => '',
				'wrapper'         => 'span',
			]
		);
		$atts = shortcode_atts( $defaults, $atts, 'bse-loginout' );
		$mode = is_user_logged_in() ? 'logout' : 'login';
		$url = $mode === 'logout' ? wp_logout_url( $atts['logout_redirect'] ) : wp_login_url( $atts['login_redirect'] );
		$target = $atts[$mode . '_target'];
		$link = '<a href="' . esc_url( $url ) . '"' . ( $target !== '' ? ' target="' . esc_attr( sanitize_key( $target ) ) . '"' : '' );
		$link .= ' rel="nofollow noopener noreferrer">' . esc_html( $atts[$mode . '_text'] ) . '</a>';
		$html = apply_filters( 'loginout', $link ); // WordPress core link filter.
		$output = ddw_bse_metadata_wrapper( $atts, 'bse-loginout', $html, true );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/loginout', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_site_updated' ) ) :

	ddw_bse_register_shortcode( 'bse-site-updated', 'ddw_bse_shortcode_site_updated' );
	/**
	 * Render a published-content modification date in the WordPress timezone.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_site_updated( $atts ) {

		/**
		 * Filter shortcode defaults/site updated while preserving its value contract.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/site_updated',
			[
				'before'      => '',
				'after'       => '',
				'type'        => 'last',
				'label_date'  => '',
				'date_format' => get_option( 'date_format' ),
				'label_time'  => '',
				'time_format' => get_option( 'time_format' ),
				'tooltip'     => '',
				'class'       => '',
				'wrapper'     => 'span',
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-site-updated' );

		$type = sanitize_key( $atts[ 'type' ] );
		$type = ( 'first' === $type ) ? $type : 'last';

		$modified = ddw_bse_site_updated( 'first' === $type ? 'MIN' : 'MAX' );
		if ( ! $modified ) {
			return '';
		}
		$timestamp = ( new DateTimeImmutable( $modified, wp_timezone() ) )->getTimestamp();
		$show_updated_date = ' <span class="date published">' . esc_html( wp_date( $atts['date_format'], $timestamp ) ) . '</span>';
		$show_updated_time = ' <span class="time published">' . esc_html( wp_date( $atts['time_format'], $timestamp ) ) . '</span>';
		$date_time_string = esc_html( $atts['label_date'] ) . $show_updated_date . ' ' . esc_html( $atts['label_time'] ) . $show_updated_time;

		$output = sprintf(
			'<%1$s class="bse-site-%2$s-updated%3$s"%4$s>%5$s%6$s%7$s</%1$s>',
			ddw_bse_wrapper_tag( $atts[ 'wrapper' ] ),
			$type,
			( ! empty( $atts[ 'class' ] ) ) ? ' ' . esc_attr( ddw_bse_sanitize_html_classes( $atts[ 'class' ], 'string' ) ) : '',
			( ! empty( $atts[ 'tooltip' ] ) ) ? ' title="' . esc_attr( $atts[ 'tooltip' ] ) . '"' : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html( $atts[ 'before' ] ) . ' ' : '',
			$date_time_string,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html( $atts[ 'after' ] ) : ''
		);

		/**
		 * Filter shortcode output; callbacks are trusted developer code.
		 * @since 1.0.0
		 * @param string $output HTML/text value. Return the replacement output.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters(
			'bse/filter/shortcode/site_updated',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;
