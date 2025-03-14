<?php

// includes/shortcodes/version

/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


if ( ! function_exists( 'ddw_bse_shortcode_version' ) ) {

	add_shortcode( 'bse-version', 'ddw_bse_shortcode_version' );
	/**
	 * Shortcode to output a version number for various types and occeasions,
	 *   like WordPress, PHP, MySQL/ MariaDB, Database, Server, Elementor,
	 *   Genesis Framework, Astra Theme amongst others.
	 *
	 * @since 1.0.0
	 *
	 * @uses ddw_bse_info_values()
	 *
	 * @param array $atts Array of Shortcode attributes.
	 * @return string Filterable text string of user's last name.
	 */
	function ddw_bse_shortcode_version( $atts ) {

		/** Set default shortcode attributes */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/version',
			[
				'type'     => 'version',
				'plugin'   => 'bse',
				'constant' => '',
				'custom'   => '',
				'before'   => '',
				'after'    => '',
				'class'    => '',
				'wrapper'  => 'span',
			]
		);

		/** Default shortcode attributes */
		$atts = shortcode_atts( $defaults, $atts, 'bse-version' );

		$type      = sanitize_key( $atts[ 'type' ] );
		$plugin    = sanitize_key( $atts[ 'plugin' ] );
		$version   = '';
		$defined   = '';
		$constant  = '';
		$bse       = defined( 'BSE_PLUGIN_VERSION' ) ? BSE_PLUGIN_VERSION : '';
		$sys_types = [
			'php_current', 'php_minimum', 'php_recommended',
			'wp_current', 'wp_minimum', 'wp_recommended',
			//'db_current',
			'server_software',
			'mysql_minimum', 'mysql_recommended',
			'mariadb_minimum', 'mariadb_recommended',
			'elementor_free', 'elementor_pro',
			'genesis_parent', 'genesis_child',
			'astra_theme', 'astra_pro',
		];

		if ( 'bse' === $plugin && 'version' === $type ) {

			$version = $bse;

		} elseif ( 'required' === $type ) {

			$plugin  = strtoupper( $plugin );
			$defined = constant( $plugin . '_REQUIRED_BASE_PLUGIN_VERSION' );
			$version = $defined ? $defined : $bse;

		} elseif ( 'custom' === $type ) {

			$version = wp_filter_nohtml_kses( $atts[ 'custom' ] );

		} elseif ( 'constant' === $type ) {

			$constant = strtoupper( sanitize_key( $atts[ 'constant' ] ) );
			$constant = constant( $constant );
			$version  = $constant ? $constant : __( '(Not defined)', 'builder-shortcode-extras' );

		} elseif ( 'db_current' === $type ) {

			if ( method_exists( $wpdb, 'db_version' ) ) {
		        $version = preg_replace( '/[^0-9.].*/', '', $wpdb->db_version() );
		    } else {
		        $version = 'N/A';
		    }

		} elseif ( in_array( $type, $sys_types ) ) {

			$bse_info = ddw_bse_info_values();
			$version  = $bse_info[ $type ];

		}  // end if
		
		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-version%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			( ! is_null( $version ) ) ? $version : '',
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/version',
			$output,
			$atts 		// additional param
		);

	}  // end function

}  // end if
