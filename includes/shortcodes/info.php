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

	add_shortcode( 'bse-copyright', 'ddw_bse_shortcode_copyright' );
	/**
	 * Shortcode to output a typical copyright intro string.
	 *
	 * If the 'first' attribute is not empty, and not equal to the current year,
	 *   then output will be formatted as first-current year (e.g. 1998-2020).
	 *   Otherwise, output is just given as the current year.
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `footer_copyright` shortcode.
	 */
	function ddw_bse_shortcode_copyright( $atts ) {

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

		$date_first = '';

		if ( '' !== $atts[ 'first' ] && date( 'Y' ) !== $atts[ 'first' ] ) {
			$date_first = $atts[ 'first' ] . '&#x02013;';
		}

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-copyright%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			$atts[ 'copyright' ] . '&nbsp;' . $date_first . date( 'Y' ),
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/bse_copyright',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_site_title' ) ) :

	add_shortcode( 'bse-site-title', 'ddw_bse_shortcode_site_title' );
	/**
	 * Shortcode to output the site title.
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `footer_site_title` shortcode.
	 */
	function ddw_bse_shortcode_site_title( $atts ) {

		/** Bail early if site title is empty */
		if ( empty( get_bloginfo( 'name' ) ) ) {
			return '';
		}

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

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-site-title%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			get_bloginfo( 'name' ),
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/site_title',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_site_slogan' ) ) :

	add_shortcode( 'bse-site-slogan', 'ddw_bse_shortcode_site_slogan' );
	/**
	 * Shortcode to output the site slogan (aka Description/ Tag line).
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `footer_site_slogan` shortcode.
	 */
	function ddw_bse_shortcode_site_slogan( $atts ) {

		/** Bail early if site slogan is empty */
		if ( empty( get_bloginfo( 'description' ) ) ) {
			return '';
		}

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

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-site-slogan%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			get_bloginfo( 'description' ),
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/site_slogan',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_home_link' ) ) :

	add_shortcode( 'bse-home-link', 'ddw_bse_shortcode_home_link' );
	/**
	 * Shortcode to output a link to the home URL.
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `footer_home_link` shortcode.
	 */
	function ddw_bse_shortcode_home_link( $atts ) {

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

		$target = sprintf(
			'target="%s"',
			sanitize_key( $atts[ 'target' ] )
		);

		$rel = sprintf(
			'rel="%s"',
			strtolower( esc_attr( $atts[ 'rel' ] ) )
		);

		$home_link = sprintf(
			'<a href="%1$s" %2$s %3$s>%4$s</a>',
			esc_url( home_url() ),
			( ! empty( $atts[ 'target' ] ) ) ? $target : '',
			( ! empty( $atts[ 'target' ] ) && ! empty( $atts[ 'rel' ] ) ) ? $rel : '',
			$atts[ 'text' ]
		);

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-home-link%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			$home_link,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/home_link',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_loginout' ) ) :

	add_shortcode( 'bse-loginout', 'ddw_bse_shortcode_loginout' );
	/**
	 * Shortcode to output an admin login/ logout link.
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `footer_loginout` shortcode.
	 */
	function ddw_bse_shortcode_loginout( $atts ) {

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

		$target_login = sprintf(
			'target="%s"',
			sanitize_key( $atts[ 'login_target' ] )
		);

		$target_logout = sprintf(
			'target="%s"',
			sanitize_key( $atts[ 'logout_target' ] )
		);

		if ( ! is_user_logged_in() ) {

			$link = sprintf(
				'<a href="%1$s" %2$s rel="nofollow noopener noreferrer">%3$s</a>',
				esc_url( wp_login_url( $atts[ 'login_redirect' ] ) ),
				( ! empty( $atts[ 'login_target' ] ) ) ? $target_login : '',
				$atts[ 'login_text' ]
			);

		} else {

			$link = sprintf(
				'<a href="%1$s" %2$s rel="nofollow noopener noreferrer">%3$s</a>',
				esc_url( wp_logout_url( $atts[ 'logout_redirect' ] ) ),
				( ! empty( $atts[ 'logout_target' ] ) ) ? $target_logout : '',
				$atts[ 'logout_text' ]
			);

		}  // end if

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-loginout%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			apply_filters( 'loginout', $link ),	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core WP hook.
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/loginout',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_site_updated' ) ) :

	add_shortcode( 'bse-site-updated', 'ddw_bse_shortcode_site_updated' );
	/**
	 * Returns the date value when the content on the site was first published
	 *   or last updated.
	 *
	 *   Note:
	 *   - both date types are taken from the "posts" DB table
	 *   - "first" = the first published post type item (post status: publish)
	 *   - "last" = the last published post type item (post status: publish)
	 *
	 * @since 1.0.0
	 *
	 * @uses ddw_bse_site_updated()
	 *
	 * @param array $atts Shortcode attributes
	 * @return string Shortcode output.
	 */
	function ddw_bse_shortcode_site_updated( $atts ) {

		/** Set default shortcode attributes */
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

		/** Default shortcode attributes */
		$atts = shortcode_atts( $defaults, $atts, 'bse-site-updated' );

		$type = sanitize_key( $atts[ 'type' ] );
		$type = ( 'first' === $type ) ? $type : 'last';

		/** For "first": Get date & time format */
		$site_first_updated_date = date_i18n(
			$atts[ 'date_format' ],
			strtotime( ddw_bse_site_updated() )                                                                                                                                                            
		);

		$site_first_updated_time = date_i18n(
			$atts[ 'time_format' ],
			strtotime( ddw_bse_site_updated() )
		);

		/** For "last": Get date & time format */
		$site_last_updated_date = date_i18n(
			$atts[ 'date_format' ],
			strtotime( ddw_bse_site_updated( 'MAX' ) )
		);

		$site_last_updated_time = date_i18n(
			$atts[ 'time_format' ],
			strtotime( ddw_bse_site_updated( 'MAX' ) )
		);

		/** For "first": Date & time display logic */
		$show_updated_date = $site_first_updated_date ? ' <span class="date published">' . $site_first_updated_date . '</span>' : '';

		$show_updated_time = $site_first_updated_time ? ' ' . sprintf(
			_x( '%s', 'Translators: shortcode, first published content time string', 'builder-shortcode-extras' ),
			'<span class="time published">' . $site_first_updated_time . '</span>'
		) : '';

		/** For "last": Date & time display logic */
		if ( 'last' === $type ) {

			$show_updated_date = $site_last_updated_date ? ' <span class="date published">' . $site_last_updated_date . '</span>' : '';

			$show_updated_time = $site_last_updated_time ? ' ' . sprintf(
				_x( '%s', 'Translators: shortcode, last updated time string', 'builder-shortcode-extras' ),
				'<span class="time published">' . $site_last_updated_time . '</span>'
			) : '';

		}  // end if

		$date_time_string = sprintf(
			'%1$s %2$s %3$s %4$s',
			$atts[ 'label_date' ],
			$show_updated_date,
			$atts[ 'label_time' ],
			$show_updated_time
		);

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-site-%2$s-updated%3$s"%4$s>%5$s%6$s%7$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			$type,
			( ! empty( $atts[ 'class' ] ) ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'tooltip' ] ) ) ? ' title="' . esc_html( $atts[ 'tooltip' ] ) . '"' : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			$date_time_string,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/site_updated',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;
