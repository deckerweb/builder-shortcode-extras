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

	ddw_bse_register_shortcode( 'bse-user', 'ddw_bse_shortcode_user' );
	/**
	 * Render an allowed user-table field or escaped fallback; password fields are refused.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @param string|null $content Optional enclosed text; unused by this callback.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_user( $atts = null, $content = null ) {

		/**
		 * Filter shortcode defaults/user while preserving its value contract.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
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

		$atts = shortcode_atts( $defaults, $atts, 'bse-user' );


		$output_text = '';

		/** Bail early if password gets requested - not allowed! */
		if ( in_array( $atts['field'], array( 'user_pass', 'user_activation_key' ), true ) ) {

			return sprintf(
				'<span class="bse-error">%s: %s</span>',
				__( 'User', 'builder-shortcode-extras' ),
				__( 'This user field is not allowed', 'builder-shortcode-extras' )
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

		$output = sprintf(
			'<%1$s class="bse-user%2$s">%3$s%4$s%5$s</%1$s>',
			ddw_bse_wrapper_tag( $atts[ 'wrapper' ] ),
			! empty( $atts[ 'class' ] ) ? ' ' . esc_attr( ddw_bse_sanitize_html_classes( $atts[ 'class' ], 'string' ) ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html( $atts[ 'before' ] ) . ' ' : '',
			esc_html( (string) $user_data ),
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html( $atts[ 'after' ] ) : ''
		);

		/**
		 * Filter shortcode output; callbacks are trusted developer code.
		 * @since 1.0.0
		 * @param string $output HTML/text value. Return the replacement output.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters(
			'bse/filter/shortcode/user',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_user_id' ) ) :

	ddw_bse_register_shortcode( 'bse-userid', 'ddw_bse_shortcode_user_id' );
	/**
	 * Return the logged-in user’s numeric ID.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @param string|null $content Optional enclosed text; unused by this callback.
	 * @return int|null Current user ID, or null for logged-out visitors.
	 */
	function ddw_bse_shortcode_user_id( $atts, $content ) {

		/** Bail early if not logged in */
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user = wp_get_current_user();

		return $user->ID;

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_user_email' ) ) :

	ddw_bse_register_shortcode( 'bse-email', 'ddw_bse_shortcode_user_email' );
	/**
	 * Render the logged-in user’s escaped email address.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @param string|null $content Optional enclosed text; unused by this callback.
	 * @return string|null Escaped current-user value, or null for logged-out visitors.
	 */
	function ddw_bse_shortcode_user_email( $atts, $content ) {

		/** Bail early if not logged in */
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user = wp_get_current_user();

		return esc_html( $user->user_email );

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_user_login_name' ) ) :

	ddw_bse_register_shortcode( 'bse-login', 'ddw_bse_shortcode_user_login_name' );
	/**
	 * Render the logged-in user’s escaped login name.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @param string|null $content Optional enclosed text; unused by this callback.
	 * @return string|null Escaped current-user value, or null for logged-out visitors.
	 */
	function ddw_bse_shortcode_user_login_name( $atts, $content ) {

		/** Bail early if not logged in */
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user = wp_get_current_user();

		return esc_html( $user->user_login );

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_user_display_name' ) ) :

	ddw_bse_register_shortcode( 'bse-displayname', 'ddw_bse_shortcode_user_display_name' );
	/**
	 * Render the logged-in user’s escaped display name.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @param string|null $content Optional enclosed text; unused by this callback.
	 * @return string|null Escaped current-user value, or null for logged-out visitors.
	 */
	function ddw_bse_shortcode_user_display_name( $atts, $content ) {

		/** Bail early if not logged in */
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user = wp_get_current_user();

		return ! empty( $user->display_name ) ? esc_attr( $user->display_name ) : '';

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_user_firstname' ) ) :

	ddw_bse_register_shortcode( 'bse-firstname', 'ddw_bse_shortcode_user_firstname' );
	/**
	 * Render the logged-in user’s first name, falling back to the display name.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string|null Escaped current-user value, or null for logged-out visitors.
	 */
	function ddw_bse_shortcode_user_firstname( $atts ) {

		/** Bail early if not logged in */
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user = wp_get_current_user();

		return ! empty( $user->user_firstname ) ? esc_attr( $user->user_firstname ) : esc_attr( $user->display_name );

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_user_lastname' ) ) :

	ddw_bse_register_shortcode( 'bse-lastname', 'ddw_bse_shortcode_user_lastname' );
	/**
	 * Render the logged-in user’s last name, or an empty value.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string|null Escaped current-user value, or null for logged-out visitors.
	 */
	function ddw_bse_shortcode_user_lastname( $atts ) {

		/** Bail early if not logged in */
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user = wp_get_current_user();

		return ! empty( $user->user_lastname ) ? esc_attr( $user->user_lastname ) : '';

	}  // end function

endif;
