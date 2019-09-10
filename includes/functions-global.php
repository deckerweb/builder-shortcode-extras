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
	$integrations = (array) apply_filters(
		'bse/filter/integrations/all',
		$integrations,
		array()
	);

	/** Escape the values of the array */
	foreach ( $integrations as $integration => $integration_data ) {

		$integration                         = sanitize_key( $integration );
		$integration_data[ 'label' ]         = esc_attr( $integration_data[ 'label' ] );
		$integration_data[ 'post_type' ]     = sanitize_key( $integration_data[ 'post_type' ] );
		$integration_data[ 'shortcode_tag' ] = sanitize_key( $integration_data[ 'shortcode_tag' ] );

	}  // end foreach

	/** Return registered integrations */
	return (array) $integrations;

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

	/** Get current user */
	$user = wp_get_current_user();

	/** Build Newsletter URL */
	$url_nl = sprintf(
		'https://deckerweb.us2.list-manage.com/subscribe?u=e09bef034abf80704e5ff9809&amp;id=380976af88&amp;MERGE0=%1$s&amp;MERGE1=%2$s',
		esc_attr( $user->user_email ),
		esc_attr( $user->user_firstname )
	);

	$bse_info = [

		'url_translate'       => 'https://translate.wordpress.org/projects/wp-plugins/builder-shortcode-extras',
		'url_wporg_faq'       => 'https://wordpress.org/plugins/builder-shortcode-extras/#faq',
		'url_wporg_forum'     => 'https://wordpress.org/support/plugin/builder-shortcode-extras',
		'url_wporg_review'    => 'https://wordpress.org/support/plugin/builder-shortcode-extras/reviews/?filter=5/#new-post',
		'url_wporg_profile'   => 'https://profiles.wordpress.org/daveshine/',
		'url_fb_group'        => 'https://www.facebook.com/groups/deckerweb.wordpress.plugins/',
		'url_snippets'        => 'https://github.com/deckerweb/builder-shortcode-extras/wiki/Code-Snippets',

		'author'              => __( 'David Decker - DECKERWEB', 'builder-shortcode-extras' ),
		'author_uri'          => 'https://deckerweb.de/',
		'license'             => 'GPL-2.0-or-later',
		'url_license'         => 'https://opensource.org/licenses/GPL-2.0',
		'first_code'          => '2019',
		'plugin_icon_png'     => plugins_url( '/assets/images/bse-icon.png', dirname( __FILE__ ) ),

		'url_donate'          => 'https://www.paypal.me/deckerweb',
		'url_patreon'         => 'https://www.patreon.com/deckerweb',
		'url_newsletter'      => $url_nl,
		'url_plugin'          => 'https://github.com/deckerweb/builder-shortcode-extras',
		'url_plugin_docs'     => 'https://github.com/deckerweb/builder-shortcode-extras/wiki',
		'url_plugin_faq'      => 'https://wordpress.org/plugins/builder-shortcode-extras/#faq',
		'url_github'          => 'https://github.com/deckerweb/builder-shortcode-extras',
		'url_github_issues'   => 'https://github.com/deckerweb/builder-shortcode-extras/issues',
		'url_github_follow'   => 'https://github.com/deckerweb',

		/** For our Shortcodes specifically */
		'php_current'         => phpversion(),
		'php_minimum'         => '5.6.20',
		'php_recommended'     => '7.2',
		'wp_current'          => $GLOBALS[ 'wp_version' ],
		'wp_minimum'          => '4.7',
		'wp_recommended'      => '5.2',
		'db_current'          => esc_attr( ddw_bse_get_db_version() ),
		'server_software'     => esc_attr( wp_unslash( $_SERVER[ 'SERVER_SOFTWARE' ] ) ),
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

	$output = esc_url( $bse_info[ sanitize_key( $url_key ) ] );

	if ( TRUE === $raw ) {
		$output = esc_url_raw( $bse_info[ esc_attr( $url_key ) ] );
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
		strtolower( esc_attr( $class ) ),	//sanitize_html_class( $class ),
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
	$code_first_year = ( date( 'Y' ) == $first_year || 0 === $first_year ) ? '' : $first_year . '&#x02013;';

	return $code_first_year . date( 'Y' );

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
 * Calculate the time difference - a replacement for `human_time_diff()` until
 *   it is improved.
 *
 * Based on BuddyPress function `bp_core_time_since()`, which in turn is based
 *   on functions created by Dunstan Orchard - http://1976design.com
 *
 * This function will return an text representation of the time elapsed since a
 *   given date, giving the two largest units e.g.:
 *
 *  - 2 hours and 50 minutes
 *  - 4 days
 *  - 4 weeks and 6 days
 *
 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
 * @link    https://toolbarextras.com/go/genesis/
 * @license GPLv2-or-later
 *
 * @since 1.0.0
 *
 * @param int      $older_date     Unix timestamp of date you want to calculate
 *                                 the time since for`.
 * @param int|bool $newer_date     Optional. Unix timestamp of date to compare
 *                                 older date to. Default false (current time).
 * @param int      $relative_depth Optional, how many units to include in
 *                                 relative date. Default 2.
 * @return string The time difference between two dates.
 */
function ddw_bse_human_time_diff( $older_date, $newer_date = false, $relative_depth = 2 ) {

	if ( ! is_int( $older_date ) ) {
		return '';
	}

	// If no newer date is given, assume now.
	$newer_date = $newer_date ?: time();

	// Difference in seconds.
	$since = absint( $newer_date - $older_date );

	if ( ! $since ) {
		return '0 ' . _x( 'seconds', 'time difference', 'builder-shortcode-extras' );
	}

	// Hold units of time in seconds, and their pluralised strings (not translated yet).
	$units = [
		/* translators: %s: Number of year(s). */
		[ 31536000, _nx_noop( '%s year', '%s years', 'Time difference', 'builder-shortcode-extras' ) ],  // 60 * 60 * 24 * 365
		/* translators: %s: Number of month(s). */
		[ 2592000, _nx_noop( '%s month', '%s months', 'Time difference', 'builder-shortcode-extras' ) ], // 60 * 60 * 24 * 30
		/* translators: %s: Number of week(s). */
		[ 604800, _nx_noop( '%s week', '%s weeks', 'Time difference', 'builder-shortcode-extras' ) ],    // 60 * 60 * 24 * 7
		/* translators: %s: Number of day(s). */
		[ 86400, _nx_noop( '%s day', '%s days', 'Time difference', 'builder-shortcode-extras' ) ],       // 60 * 60 * 24
		/* translators: %s: Number of hour(s). */
		[ 3600, _nx_noop( '%s hour', '%s hours', 'Time difference', 'builder-shortcode-extras' ) ],      // 60 * 60
		/* translators: %s: Number of minute(s). */
		[ 60, _nx_noop( '%s minute', '%s minutes', 'Time difference', 'builder-shortcode-extras' ) ],
		/* translators: %s: Number of second(s). */
		[ 1, _nx_noop( '%s second', '%s seconds', 'Time difference', 'builder-shortcode-extras' ) ],
	];

	// Build output with as many units as specified in $relative_depth.
	$relative_depth = (int) $relative_depth ?: 2;

	$i = 0;

	$counted_seconds = 0;

	$date_partials        = [];
	$amount_date_partials = 0;
	$amount_units         = count( $units );

	while ( $amount_date_partials < $relative_depth && $i < $amount_units ) {

		$seconds = $units[ $i ][0];
		$count   = (int) floor( ( $since - $counted_seconds ) / $seconds );

		if ( 0 !== $count ) {

			$date_partials[]      = sprintf(
				translate_nooped_plural( $units[ $i ][1], $count, 'builder-shortcode-extras' ),
				$count
			);
			$counted_seconds     += $count * $seconds;
			$amount_date_partials = count( $date_partials );

		}  // end if

		$i++;

	}  // end while

	if ( empty( $date_partials ) ) {

		$output = '';

	} elseif ( 1 === count( $date_partials ) ) {

		$output = $date_partials[0];

	} else {

		/** Combine all but last partial using commas. */
		$output = implode( ', ', array_slice( $date_partials, 0, -1 ) );

		/** Add 'and' separator. */
		$output .= ' ' . _x( 'and', 'Separator in time difference', 'builder-shortcode-extras' ) . ' ';

		/** Add last partial. */
		$output .= end( $date_partials );

	}  // end if

	return $output;

}  // end function


/**
 * Merge array of attributes with defaults, and apply contextual filter on array.
 *
 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
 * @link    https://toolbarextras.com/go/genesis/
 * @license GPLv2-or-later
 *
 * @since 1.0.0
 *
 * @param string $context    The context, to build filter name.
 * @param array  $attributes Optional. Extra attributes to merge with defaults.
 * @param array  $args       Optional. Custom data to pass to filter.
 * @return array Merged and filtered attributes.
 */
function ddw_bse_parse_attr( $context, $attributes = [], $args = [] ) {

	$defaults = [
		'class' => sanitize_html_class( $context ),
	];

	$attributes = wp_parse_args( $attributes, $defaults );

	/** Contextual filter. */
	return apply_filters(
		"bse/filter/attr_{$context}",
		$attributes,
		$context,
		$args
	);

}  // end function


/**
 * Build list of attributes into a string and apply contextual filter on string.
 *
 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
 * @link    https://toolbarextras.com/go/genesis/
 * @license GPLv2-or-later
 *
 * @since 1.0.0
 *
 * @uses ddw_bse_parse_attr()
 *
 * @param string $context    The context, to build filter name.
 * @param array  $attributes Optional. Extra attributes to merge with defaults.
 * @param array  $args       Optional. Custom data to pass to filter.
 * @return string String of HTML attributes and values.
 */
function ddw_bse_attr( $context, $attributes = [], $args = [] ) {

	$attributes = ddw_bse_parse_attr( $context, $attributes, $args );

	$output = '';

	// Cycle through attributes, build tag attribute string.
	foreach ( $attributes as $key => $value ) {

		if ( ! $value ) {
			continue;
		}

		if ( TRUE === $value ) {

			$output .= esc_html( $key ) . ' ';

		} else {

			$output .= sprintf(
				'%s="%s" ',
				esc_html( $key ),
				esc_attr( $value )
			);

		}  // end if

	}  // end foreach

	$output = apply_filters(
		"bse/filter/attr_{$context}_output",
		$output,
		$attributes,
		$context,
		$args
	);

	return trim( $output );

}  // end function


/**
 * Helper function: This will check the modified_date of all posts and give you
 *   the most/ least recent date.
 *
 * @since 1.0.0
 *
 * @global mixed $wpdb
 *
 * @param string $val string Defaults to MIN, only accepts MAX as alternative.
 * @return string UNIX Date Time Stamp, e.g. 2014-12-05 01:34:25.
 */
function ddw_bse_site_updated( $value = 'MIN' ) {

	global $wpdb;
	
	/** Sanitize $value */
	$value = ( 'MIN' === $value ) ? $value : 'MAX';

	/** Return date time stamp */
	return $wpdb->get_var( "SELECT $value( $wpdb->posts.post_modified ) FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish'" );

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
 * Returns the ID of a post type item based on the incoming slug.
 *
 * @link https://tommcfarlin.com/permalink-by-slug/
 * @link https://wordpress.stackexchange.com/questions/4999/is-it-possible-to-get-a-page-link-from-its-slug
 *
 * @since 1.0.0
 *
 * @param string $slug The post ID of the post type item to which we're going to link.
 * @return string      The post ID of the post type item.
 */
function ddw_bse_get_post_id_by_slug( $slug = '', $post_type = '' ) {

    /** Initialize the post_id value */
    $post_id = null;

    /** Set the arguments for WP_Query */
    $args = array(
        'name'          => sanitize_key( $slug ),
        'max_num_posts' => 1
    );

    /** If the optional argument is set, add it to the arguments array */
    if ( '' !== $post_type ) {
        $args = array_merge( $args, array( 'post_type' => sanitize_key( $post_type ) ) );
    }

    /** Run the query (and afterwards reset it) */
    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {

        $query->the_post();
        $post_id = get_the_ID();
        wp_reset_postdata();

    }  // end if

    /** Return the determined post_id */
    return $post_id;

}  // end function
