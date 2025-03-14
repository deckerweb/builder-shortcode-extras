<?php

// includes/admin/admin-extras

/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


/**
 * Remove unethical Jetpack search results Ads as no one needs these anyway.
 *   Additionally remove other promotions and Ads from Jetpack.
 *
 * @link https://wptavern.com/jetpack-7-1-adds-feature-suggestions-to-plugin-search-results#comment-284531
 *
 * @since 1.0.0
 */
add_filter( 'jetpack_show_promotions', '__return_false', 20 );
add_filter( 'can_display_jetpack_manage_notice', '__return_false', 20 );
add_filter( 'jetpack_just_in_time_msgs', '__return_false', 20 );


/**
 * Remove unethical WooCommerce Ads injections.
 *
 * @since 1.6.0
 */
add_filter( 'woocommerce_allow_marketplace_suggestions', '__return_false' );


add_filter( 'plugin_row_meta', 'ddw_bse_plugin_links', 10, 2 );
/**
 * Add various support links to Plugins page.
 *
 * @since 1.0.0
 *
 * @uses ddw_bse_get_info_link()
 *
 * @param array  $bse_links (Default) Array of plugin meta links
 * @param string $bse_file  Path of base plugin file
 * @return array $bse_links Array of plugin link strings to build HTML markup.
 */
function ddw_bse_plugin_links( $bse_links, $bse_file ) {

	/** Capability check */
	if ( ! current_user_can( 'install_plugins' ) ) {
		return $bse_links;
	}

	/** List additional links only for this plugin */
	if ( $bse_file === BSE_PLUGIN_BASEDIR . 'builder-shortcode-extras.php' ) {

		/* translators: Plugins page listing */
		$bse_links[] = ddw_bse_get_info_link(
			'url_github_issues',
			esc_html_x( 'Support', 'Plugins page listing', 'builder-shortcode-extras' ),
			'dashicons-before dashicons-sos'
		);

		/* translators: Plugins page listing */
		$bse_links[] = ddw_bse_get_info_link(
			'url_snippets',
			esc_html_x( 'Code Snippets', 'Plugins page listing', 'builder-shortcode-extras' ),
			'dashicons-before dashicons-editor-code'
		);

		/* translators: Plugins page listing */
		$bse_links[] = ddw_bse_get_info_link(
			'url_donate',
			esc_html_x( 'Donate', 'Plugins page listing', 'builder-shortcode-extras' ),
			'button dashicons-before dashicons-thumbs-up'
		);

		/* translators: Plugins page listing */
		$bse_links[] = ddw_bse_get_info_link(
			'url_newsletter',
			esc_html_x( 'Join our Newsletter', 'Plugins page listing', 'builder-shortcode-extras' ),
			'button-primary dashicons-before dashicons-awards'
		);

	}  // end if plugin links

	/** Output the links */
	return apply_filters(
		'bse/filter/plugins_page/more_links',
		$bse_links
	);

}  // end function


add_action( 'admin_enqueue_scripts', 'ddw_bse_inline_styles_plugins_page', 20 );
/**
 * Add additional inline styles on the admin Plugins page.
 *
 * @since 1.0.0
 *
 * @uses wp_add_inline_style()
 *
 * @global string $GLOBALS[ 'pagenow' ]
 */
function ddw_bse_inline_styles_plugins_page() {

	/** Bail early if not on plugins.php admin page */
	if ( 'plugins.php' !== $GLOBALS[ 'pagenow' ] ) {
		return;
	}

	$bse_file = BSE_PLUGIN_BASEDIR . 'builder-shortcode-extras.php';

    /**
     * For WordPress Admin Bar
     *   Style handle: 'admin-bar' (WordPress Core)
     */
    $inline_css = sprintf(
    	'
        tr[data-plugin="%s"] .plugin-version-author-uri a.dashicons-before:before {
			font-size: 17px;
			margin-right: 2px;
			opacity: .85;
			vertical-align: sub;
		}
		',
		$bse_file
	);

    wp_add_inline_style( 'wp-admin', $inline_css );

}  // end function


add_filter( 'debug_information', 'ddw_bse_site_health_add_debug_info', 7 );
/**
 * Add additional plugin related info to the Site Health Debug Info section.
 *   (Only relevant for WordPress 5.2 or higher)
 *
 * @link https://make.wordpress.org/core/2019/04/25/site-health-check-in-5-2/
 *
 * @since 1.0.0
 *
 * @uses ddw_bse_is_elementor_active()
 * @uses ddw_bse_is_elementor_pro_active()
 *
 * @param array $debug_info Array holding all Debug Info items.
 * @return array Modified array of Debug Info.
 */
function ddw_bse_site_health_add_debug_info( $debug_info ) {

	/** Setup strings */
	$string_enabled   = __( 'Enabled', 'builder-shortcode-extras' );
	$string_disabled  = __( 'Disabled', 'builder-shortcode-extras' );

	/** Add our Debug info */
	$debug_info[ 'builder-shortcode-extras' ] = [
		'label'  => esc_html__( 'Builder Shortcode Extras', 'builder-shortcode-extras' ) . ' (' . esc_html__( 'Plugin', 'builder-shortcode-extras' ) . ')',
		'fields' => [
			'bse_plugin_version' => [
				'label' => __( 'Plugin version', 'builder-shortcode-extras' ),
				'value' => BSE_PLUGIN_VERSION,
			],
			'bse_elementor_free' => [
				'label' => __( 'Elementor free', 'builder-shortcode-extras' ),
				'value' => ddw_bse_is_elementor_active() ? $string_enabled : $string_disabled,
			],
			'bse_elementor_pro' => [
				'label' => __( 'Elementor Pro', 'builder-shortcode-extras' ),
				'value' => ddw_bse_is_elementor_pro_active() ? $string_enabled : $string_disabled,
			],
		],
	];

	/** Return modified Debug Info array */
	return $debug_info;

}  // end function


if ( ! function_exists( 'ddw_wp_site_health_remove_percentage' ) ) :

	add_action( 'admin_head', 'ddw_wp_site_health_remove_percentage', 100 );
	/**
	 * Remove the "Percentage Progress" display in Site Health feature as this will
	 *   get users obsessed with fullfilling a 100% where there are non-problems!
	 *
	 * @link https://make.wordpress.org/core/2019/04/25/site-health-check-in-5-2/
	 *
	 * @since 1.0.0
	 */
	function ddw_wp_site_health_remove_percentage() {

		/** Bail early if not on WP 5.2+ */
		if ( version_compare( $GLOBALS[ 'wp_version' ], '5.2-beta', '<' ) ) {
			return;
		}

		?>
			<style type="text/css">
				.site-health-progress {
					display: none;
				}
			</style>
		<?php

	}  // end function

endif;