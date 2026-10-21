<?php

// includes/functions-global

/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


/**
 * Return array of registered integrations and their arguments.
 *
 * Plugins and themes can hook into the 'btc_filter_get_integrations' filter to
 *   register their own integration.
 *
 * @since 1.0.0
 *
 * @return array Filterable array of registered integrations.
 */
function ddw_bse_get_integrations() {

	/** Set integrations array */
	$integrations = array(
		'default-none' => array(
			'label'         => esc_attr__( 'No Integration registered', 'builder-shortcode-extras' ),
			'post_type'     => 'bse-custom-post-type',
			'shortcode_tag' => 'no-shortcode-tag',
		),
	);

	/** Allow the array to be filtered (= adding more integrations) */
	/**
	 * Filter integrations/all while preserving its value contract.
	 * @since 1.0.0
	 * @param array $integrations Integration definitions; return the amended map.
	 * @param array $context Reserved extension context.
	 */
	$integrations = (array) apply_filters(
		'bse/filter/integrations/all',
		$integrations,
		array()
	);

	$clean = array();
	foreach ( $integrations as $key => $data ) {
		if ( 'default-none' === $key || ! is_array( $data ) ) {
			continue;
		}
		if ( ! isset( $data['label'], $data['post_type'], $data['shortcode_tag'] )
			|| ! is_string( $data['label'] ) || ! is_string( $data['post_type'] ) || ! is_string( $data['shortcode_tag'] ) ) {
			continue;
		}
		$key = sanitize_key( (string) $key );
		$data['label'] = sanitize_text_field( $data['label'] );
		$data['post_type'] = sanitize_key( $data['post_type'] );
		$data['shortcode_tag'] = sanitize_key( $data['shortcode_tag'] );
		if ( $key && $data['post_type'] && $data['shortcode_tag'] ) {
			$clean[$key] = $data;
		}
	}
	return $clean;

}  // end function


/**
 * Setting internal plugin helper values.
 *
 * @since 1.0.0
 *
 * @uses ddw_bse_get_db_version()
 * @uses genesis_get_theme_version()
 *
 * @return array $bse_info Array of info values.
 */
function ddw_bse_info_values() {

	$bse_info = [

		'url_snippets'        => 'https://github.com/deckerweb/builder-shortcode-extras/wiki/English',

		'author'              => __( 'David Decker - DECKERWEB', 'builder-shortcode-extras' ),
		'author_uri'          => 'https://github.com/deckerweb',
		'license'             => 'GPL-2.0-or-later',
		'url_license'         => 'https://www.gnu.org/licenses/gpl-2.0.html',
		'first_code'          => '2019',
		'plugin_icon_png'     => plugins_url( '/assets/images/bse-icon.png', dirname( __FILE__ ) ),

		'url_donate'          => 'https://paypal.me/deckerweb',
		'url_patreon'         => 'https://www.patreon.com/deckerweb',
		'url_newsletter'      => 'https://eepurl.com/gbAUUn',
		'url_plugin'          => 'https://github.com/deckerweb/builder-shortcode-extras',
		'url_plugin_docs'     => 'https://github.com/deckerweb/builder-shortcode-extras/wiki',
		'url_github'          => 'https://github.com/deckerweb/builder-shortcode-extras',
		'url_github_issues'   => 'https://github.com/deckerweb/builder-shortcode-extras/issues',
		'url_github_follow'   => 'https://github.com/deckerweb',

		/** For our Shortcodes specifically */
		'php_current'         => phpversion(),
		'php_minimum'         => '8.1',
		'php_recommended'     => '8.2',
		'wp_current'          => $GLOBALS[ 'wp_version' ],
		'wp_minimum'          => '6.7',
		'wp_recommended'      => '7.1.3',
		//'db_current'          => esc_attr( ddw_bse_get_db_version() ),
		'server_software'     => sanitize_text_field( wp_unslash( $_SERVER[ 'SERVER_SOFTWARE' ] ?? '' ) ),
		'mysql_minimum'       => '5.0',
		'mysql_recommended'   => '5.6',
		'mariadb_minimum'     => '10.0',
		'mariadb_recommended' => '10.1',
		'elementor_free'      => defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '',
		'elementor_pro'       => defined( 'ELEMENTOR_PRO_VERSION' ) ? ELEMENTOR_PRO_VERSION : '',
		'genesis_parent'      => defined( 'PARENT_THEME_VERSION' ) ? PARENT_THEME_VERSION : '',
		'genesis_child'       => function_exists( 'genesis_get_theme_version' ) ? genesis_get_theme_version() : '',
		'astra_theme'         => defined( 'ASTRA_THEME_VERSION' ) ? ASTRA_THEME_VERSION : '',
		'astra_pro'           => defined( 'ASTRA_EXT_VER' ) ? ASTRA_EXT_VER : '',

	];  // end of array

	return $bse_info;

}  // end function


/**
 * Get URL of specific BTC info value.
 *
 * @since 1.0.0
 *
 * @uses ddw_bse_info_values()
 *
 * @param string $url_key String of value key from array of ddw_bse_info_values()
 * @param bool   $raw     If raw escaping or regular escaping of URL gets used
 * @return string URL for info value.
 */
function ddw_bse_get_info_url( $url_key = '', $raw = FALSE ) {

	$bse_info = (array) ddw_bse_info_values();

	$output = esc_url( $bse_info[ sanitize_key( $url_key ) ] ?? '' );

	if ( TRUE === $raw ) {
		$output = esc_url_raw( $bse_info[ sanitize_key( $url_key ) ] ?? '' );
	}

	return $output;

}  // end function


/**
 * Get link with complete markup for a specific BSE info value.
 *
 * @since 1.0.0
 *
 * @uses ddw_bse_get_info_url()
 *
 * @param string $url_key String of value key
 * @param string $text    String of text and link attribute
 * @param string $class   String of CSS class
 * @return string HTML markup for linked URL.
 */
function ddw_bse_get_info_link( $url_key = '', $text = '', $class = '' ) {

	$link = sprintf(
		'<a class="%1$s" href="%2$s" target="_blank" rel="nofollow noopener noreferrer" title="%3$s">%3$s</a>',
		esc_attr( ddw_bse_sanitize_html_classes( $class, 'string' ) ),	//sanitize_html_class( $class ),
		ddw_bse_get_info_url( $url_key ),
		esc_html( $text )
	);

	return $link;

}  // end function


/**
 * Get timespan of coding years for this plugin.
 *
 * @since 1.0.0
 *
 * @uses ddw_bse_info_values()
 *
 * @param int $first_year Integer number of first year
 * @return string Timespan of years.
 */
function ddw_bse_coding_years( $first_year = '' ) {

	$bse_info = (array) ddw_bse_info_values();

	$first_year = ( empty( $first_year ) ) ? absint( $bse_info[ 'first_code' ] ) : absint( $first_year );

	/** Set year of first released code */
	$code_first_year = ( wp_date( 'Y' ) == $first_year || 0 === $first_year ) ? '' : $first_year . '&#x02013;';

	return $code_first_year . wp_date( 'Y' );

}  // end function


/**
 * Build string "Shortcode" for reusage.
 *
 * @since 1.0.0
 *
 * @return string Translateable string.
 */
function ddw_bse_string_shortcode() {

	return __( 'Shortcode', 'builder-shortcode-extras' );

}  // end function


/**
 * Format an elapsed interval using bounded approximate calendar units.
 *
 * @since 1.0.0
 * @param int $older_date Older Unix timestamp; other input returns an empty string.
 * @param int|false $newer_date Comparison timestamp, or false for the current time.
 * @param int $relative_depth Maximum number of nonzero units, between one and seven.
 * @return string Localized interval, without HTML.
 */
function ddw_bse_human_time_diff( $older_date, $newer_date = false, $relative_depth = 2 ) {
	if ( ! is_int( $older_date ) ) { return ''; }
	$remaining = absint( ( $newer_date ?: time() ) - $older_date );
	$limit = max( 1, min( 7, (int) $relative_depth ?: 2 ) );
	if ( ! $remaining ) { return '0 ' . _x( 'seconds', 'time difference', 'builder-shortcode-extras' ); }
	$units = array(
		/* translators: %s: Number of elapsed units. */
		31536000 => _nx_noop( '%s year', '%s years', 'Time difference', 'builder-shortcode-extras' ),
		/* translators: %s: Number of elapsed units. */
		2592000 => _nx_noop( '%s month', '%s months', 'Time difference', 'builder-shortcode-extras' ),
		/* translators: %s: Number of elapsed units. */
		604800 => _nx_noop( '%s week', '%s weeks', 'Time difference', 'builder-shortcode-extras' ),
		/* translators: %s: Number of elapsed units. */
		86400 => _nx_noop( '%s day', '%s days', 'Time difference', 'builder-shortcode-extras' ),
		/* translators: %s: Number of elapsed units. */
		3600 => _nx_noop( '%s hour', '%s hours', 'Time difference', 'builder-shortcode-extras' ),
		/* translators: %s: Number of elapsed units. */
		60 => _nx_noop( '%s minute', '%s minutes', 'Time difference', 'builder-shortcode-extras' ),
		/* translators: %s: Number of elapsed units. */
		1 => _nx_noop( '%s second', '%s seconds', 'Time difference', 'builder-shortcode-extras' ),
	);
	$parts = array();
	foreach ( $units as $seconds => $labels ) {
		$amount = intdiv( $remaining, $seconds );
		$remaining %= $seconds;
		if ( $amount ) { $parts[] = sprintf( translate_nooped_plural( $labels, $amount, 'builder-shortcode-extras' ), $amount ); }
		if ( count( $parts ) >= $limit ) { break; }
	}
	if ( count( $parts ) < 2 ) { return implode( '', $parts ); }
	$last = array_pop( $parts );
	return implode( ', ', $parts ) . ' ' . _x( 'and', 'Separator in time difference', 'builder-shortcode-extras' ) . ' ' . $last;
}

/**
 * Parse attributes with a default context class and the existing extension filter.
 *
 * @since 1.0.0
 * @param string $context Attribute context used in the filter name.
 * @param array|string $attributes Values accepted by wp_parse_args().
 * @param array $args Additional developer data passed unchanged to the filter.
 * @return array Filtered attribute map.
 */
function ddw_bse_parse_attr( $context, $attributes = array(), $args = array() ) {
	$parsed = wp_parse_args( $attributes, array( 'class' => sanitize_html_class( $context ) ) );
	/**
	 * Filter attr {$context} while preserving its value contract.
	 * @since 1.0.0
	 * @param array $attributes Parsed attribute map; return its replacement.
	 * @param string $context Attribute context.
	 * @param array $args Additional developer context.
	 */
	return apply_filters( "bse/filter/attr_{$context}", $parsed, $context, $args );
}

/**
 * Serialize safe attribute names and scalar values through the existing output filter.
 *
 * @since 1.0.0
 * @param string $context Attribute context used in the filter name.
 * @param array|string $attributes Attributes merged with the default context class.
 * @param array $args Additional developer data passed unchanged to the filter.
 * @return string Escaped attributes, without a surrounding element.
 */
function ddw_bse_attr( $context, $attributes = array(), $args = array() ) {
	$map = ddw_bse_parse_attr( $context, $attributes, $args );
	$parts = array();
	foreach ( (array) $map as $name => $value ) {
		if ( ! preg_match( '/^[a-zA-Z][a-zA-Z0-9_:.-]*$/D', (string) $name ) || ! $value || ! is_scalar( $value ) ) { continue; }
		$parts[] = true === $value ? $name : $name . '="' . esc_attr( (string) $value ) . '"';
	}
	/**
	 * Filter attr {$context} output while preserving its value contract.
	 * @since 1.0.0
	 * @param string $output Escaped attribute string; return its replacement.
	 * @param array $attributes Resolved attribute map.
	 * @param string $context Attribute context.
	 * @param array $args Additional developer context.
	 */
	return trim( apply_filters( "bse/filter/attr_{$context}_output", implode( ' ', $parts ), $map, $context, $args ) );
}

/**
 * Read the earliest or latest modification date among published items.
 *
 * @param string $value MIN selects the earliest date; other values select MAX.
 * @return string|null Database-local date string, or null without published content.
 */
function ddw_bse_site_updated( $value = 'MIN' ) {

	global $wpdb;

	/** Sanitize $value */
	$value = ( 'MIN' === $value ) ? $value : 'MAX';

	static $dates = array();
	$key = get_current_blog_id() . ':' . $value . ':' . wp_cache_get_last_changed( 'posts' );
	if ( ! array_key_exists( $key, $dates ) ) {
		if ( 'MIN' === $value ) {
			$dates[$key] = $wpdb->get_var( $wpdb->prepare( 'SELECT MIN(post_modified) FROM %i WHERE post_status = %s', $wpdb->posts, 'publish' ) );
		} else {
			$dates[$key] = $wpdb->get_var( $wpdb->prepare( 'SELECT MAX(post_modified) FROM %i WHERE post_status = %s', $wpdb->posts, 'publish' ) );
		}
	}
	return $dates[$key];

}  // end function


/**
 * Get the current database version - this works for MySQL and MariaDB.
 *
 * @since 1.0.0
 *
 * @global object $GLOBALS[ 'wpdb' ]
 *
 * @return string Current database version.
 */
function ddw_bse_get_db_version() {

	//global $wpdb;

	return $GLOBALS[ 'wpdb' ]->get_var( "SELECT VERSION();" );

}  // end function


/**
 * Sanitize multiple HTML classes in one pass.
 *
 * Accepts either an array of `$classes`, or a space separated string of classes
 *   and sanitizes them using the `sanitize_html_class()` WordPress function.
 *
 * @since 1.0.0
 *
 * @param array|string $classes       Classes to be sanitized.
 * @param string       $return_format Optional. The return format, 'input', 'string', or 'array'. Default is 'input'.
 * @return array|string String of multiple sanitized classes.
 */
function ddw_bse_sanitize_html_classes( $classes, $return_format = 'input' ) {

	if ( 'input' === $return_format ) {
		$return_format = is_array( $classes ) ? 'array' : 'string';
	}

	$classes = is_array( $classes ) ? $classes : explode( ' ', $classes );

	$sanitized_classes = array_map( 'sanitize_html_class', $classes );

	if ( 'array' === $return_format ) {
		return $sanitized_classes;
	}

	return implode( ' ', $sanitized_classes );

}  // end function


/**
 * Resolve one published post without changing the global loop.
 *
 * @param string $slug Requested post slug.
 * @param string $post_type Optional post type; defaults to post.
 * @return int|null Matching post ID, or null when absent.
 */
function ddw_bse_get_post_id_by_slug( $slug = '', $post_type = '' ) {
	$query = new WP_Query( array(
		'name' => sanitize_title( $slug ),
		'post_type' => $post_type ? sanitize_key( $post_type ) : 'post',
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'fields' => 'ids',
		'no_found_rows' => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	) );
	return $query->posts ? (int) $query->posts[0] : null;
}
