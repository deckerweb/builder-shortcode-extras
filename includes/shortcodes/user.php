<?php

// includes/shortcodes/user

/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


if ( ! function_exists( 'ddw_bse_shortcode_user' ) ) :

	add_shortcode( 'bse-user', 'ddw_bse_shortcode_user' );
	/**
	 * ???
	 *
	 * @since 1.0.0
	 *
	 * @uses get_current_user_id()
	 * @uses get_user_by()
	 */
	function ddw_bse_shortcode_user( $atts = null, $content = null ) {

		/** Set default shortcode attributes */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/user',
			array(
				'user_id' => '',
				'field'   => 'display_name',
				'default' => '',
				'before'  => '',
				'after'   => '',
				'class'   => '',
				'wrapper' => 'span',
			)
		);

		/** Default shortcode attributes */
		$atts = shortcode_atts( $defaults, $atts, 'bse-user' );


		$output_text = '';

		/** Bail early if password gets requested - not allowed! */
		if ( 'user_pass' === $atts[ 'field' ] ) {

			return sprintf(
				'<span class="bse-error">%s: %s</span>',
				__( 'User', 'builder-shortcode-extras' ),
				__( 'The password field is not allowed', 'builder-shortcode-extras' )
			);

		}  // end if

		/** Optionally get current user ID */
		if ( ! $atts[ 'user_id' ] ) {
			$atts[ 'user_id' ] = get_current_user_id();
		}

		/** Check user ID - Bail early if no proper ID */
		if ( ! is_numeric( $atts[ 'user_id' ] ) || $atts[ 'user_id' ] < 0 ) {

			return sprintf(
				'<span class="bse-error">%s: %s</span>',
				__( 'User', 'builder-shortcode-extras' ),
				__( 'The user ID is incorrect', 'builder-shortcode-extras' )
			);

		}  // end if

		/** Get the user object, by ID */
		$user = get_user_by( 'id', $atts[ 'user_id' ] );

		/** Get the requested user data by 'field' if user was found */
		$user_data = ( $user && isset( $user->data->{ $atts[ 'field' ] } ) ) ? $user->data->{ $atts[ 'field' ] } : $atts[ 'default' ];

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-user%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			( $user_data ) ? $user_data : '',
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/user',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_user_id' ) ) :

	add_shortcode( 'bse-userid', 'ddw_bse_shortcode_user_id' );
	/**
	 * Shortcode to output a users ID.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $atts Array of Shortcode attributes.
	 * @param string $content Content of Shortcode.
	 * @return string Filterable text string of user's ID.
	 */
	function ddw_bse_shortcode_user_id( $atts, $content ) {

		/** Bail early if not logged in */
		if ( ! is_user_logged_in() ) {
			return;
		}

		/** Get current user */
		$user = wp_get_current_user();

		/** Output */
		return $user->ID;

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_user_email' ) ) :

	add_shortcode( 'bse-email', 'ddw_bse_shortcode_user_email' );
	/**
	 * Shortcode to output a users email.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $atts Array of Shortcode attributes.
	 * @param string $content Content of Shortcode.
	 * @return string Filterable text string of user's email.
	 */
	function ddw_bse_shortcode_user_email( $atts, $content ) {

		/** Bail early if not logged in */
		if ( ! is_user_logged_in() ) {
			return;
		}

		/** Get current user */
		$user = wp_get_current_user();

		/** Output */
		return $user->user_email;

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_user_login_name' ) ) :

	add_shortcode( 'bse-login', 'ddw_bse_shortcode_user_login_name' );
	/**
	 * Shortcode to output a users login handle/name.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $atts Array of Shortcode attributes.
	 * @param string $content Content of Shortcode.
	 * @return string Filterable text string of user's login name.
	 */
	function ddw_bse_shortcode_user_login_name( $atts, $content ) {

		/** Bail early if not logged in */
		if ( ! is_user_logged_in() ) {
			return;
		}

		/** Get current user */
		$user = wp_get_current_user();

		/** Output */
		return $user->user_login;

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_user_display_name' ) ) :

	add_shortcode( 'bse-displayname', 'ddw_bse_shortcode_user_display_name' );
	/**
	 * Shortcode to output a users display name.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $atts Array of Shortcode attributes.
	 * @param string $content Content of Shortcode.
	 * @return string Filterable text string of user's display name.
	 */
	function ddw_bse_shortcode_user_display_name( $atts, $content ) {

		/** Bail early if not logged in */
		if ( ! is_user_logged_in() ) {
			return;
		}

		/** Get current user */
		$user = wp_get_current_user();

		/** Output */
		return ! empty( $user->display_name ) ? esc_attr( $user->display_name ) : '';

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_user_firstname' ) ) :

	add_shortcode( 'bse-firstname', 'ddw_bse_shortcode_user_firstname' );
	/**
	 * Shortcode to output a users first name.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $atts Array of Shortcode attributes.
	 * @param string $content Content of Shortcode.
	 * @return string Filterable text string of user's first name.
	 */
	function ddw_bse_shortcode_user_firstname( $atts ) {

		/** Bail early if not logged in */
		if ( ! is_user_logged_in() ) {
			return;
		}

		/** Get current user */
		$user = wp_get_current_user();

		/** Output */
		return ! empty( $user->user_firstname ) ? esc_attr( $user->user_firstname ) : esc_attr( $user->display_name );

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_user_lastname' ) ) :

	add_shortcode( 'bse-lastname', 'ddw_bse_shortcode_user_lastname' );
	/**
	 * Shortcode to output a users last name.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $atts Array of Shortcode attributes.
	 * @param string $content Content of Shortcode.
	 * @return string Filterable text string of user's last name.
	 */
	function ddw_bse_shortcode_user_lastname( $atts ) {

		/** Bail early if not logged in */
		if ( ! is_user_logged_in() ) {
			return;
		}

		/** Get current user */
		$user = wp_get_current_user();

		/** Output */
		return ! empty( $user->user_lastname ) ? esc_attr( $user->user_lastname ) : '';

	}  // end function

endif;
