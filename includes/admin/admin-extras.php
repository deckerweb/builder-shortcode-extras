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


/**
 * Add plugin review info notice within admin - dismissible!
 *   Gets displayed and once dismissed will never show again.
 *
 * @since 1.0.0
 */
require_once BSE_PLUGIN_DIR . 'includes/admin/views/notice-plugin-review.php';


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
			'url_wporg_forum',
			esc_html_x( 'Support', 'Plugins page listing', 'builder-shortcode-extras' ),
			'dashicons-before dashicons-sos'
		);

		/* translators: Plugins page listing */
		$bse_links[] = ddw_bse_get_info_link(
			'url_fb_group',
			esc_html_x( 'Facebook Group', 'Plugins page listing', 'builder-shortcode-extras' ),
			'dashicons-before dashicons-facebook'
		);

		/* translators: Plugins page listing */
		$bse_links[] = ddw_bse_get_info_link(
			'url_translate',
			esc_html_x( 'Translations', 'Plugins page listing', 'builder-shortcode-extras' ),
			'dashicons-before dashicons-translation'
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
 */
function ddw_bse_inline_styles_plugins_page() {

	/** Bail early if not on plugins.php admin page */
	if ( 'plugins' !== get_current_screen()->id ) {
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

		.bse-update-message p:before,
		.update-message.notice p:empty {
			display: none !important;
		}',
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


add_action( 'in_plugin_update_message-' . BSE_PLUGIN_BASEDIR . 'builder-shortcode-extras.php', 'ddw_bse_plugin_update_message', 10, 2 );
/**
 * On Plugins page add visible upgrade/update notice in the overview table.
 *   Note: This action fires for regular single site installs, and for Multisite
 *         installs where the plugin is activated Network-wide.
 *
 * @since 1.0.0
 *
 * @param object $data
 * @param object $response
 * @return string Echoed string and markup for the plugin's upgrade/update
 *                notice.
 */
function ddw_bse_plugin_update_message( $data, $response ) {

	if ( isset( $data[ 'upgrade_notice' ] ) ) {

		printf(
			'<div class="update-message bse-update-message">%s</div>',
			wpautop( $data[ 'upgrade_notice' ] )
		);

	}  // end if

}  // end function


add_action( 'after_plugin_row_wp-' . BSE_PLUGIN_BASEDIR . 'builder-shortcode-extras.php', 'ddw_bse_multisite_subsite_plugin_update_message', 10, 2 );
/**
 * On Plugins page add visible upgrade/update notice in the overview table.
 *   Note: This action fires for Multisite installs where the plugin is
 *         activated on a per site basis.
 *
 * @since 1.0.0
 *
 * @param string $file
 * @param object $plugin
 * @return string Echoed string and markup for the plugin's upgrade/update
 *                notice.
 */
function ddw_bse_multisite_subsite_plugin_update_message( $file, $plugin ) {

	if ( is_multisite() && version_compare( $plugin[ 'Version' ], $plugin[ 'new_version' ], '<' ) ) {

		$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );

		printf(
			'<tr class="plugin-update-tr"><td colspan="%s" class="plugin-update update-message notice inline notice-warning notice-alt"><div class="update-message bse-update-message"><h4 style="margin: 0; font-size: 14px;">%s</h4>%s</div></td></tr>',
			$wp_list_table->get_column_count(),
			$plugin[ 'Name' ],
			wpautop( $plugin[ 'upgrade_notice' ] )
		);

	}  // end if

}  // end function


/**
 * Optionally tweaking Plugin API results to make more useful recommendations to
 *   the user.
 *
 * @since 1.0.0
 */

add_filter( 'ddwlib_plir/filter/plugins', 'ddw_bse_register_extra_plugin_recommendations' );
/**
 * Register specific plugins for the class "DDWlib Plugin Installer
 *   Recommendations".
 *   Note: The top-level array keys are plugin slugs from the WordPress.org
 *         Plugin Directory.
 *
 * @since 1.0.0
 *
 * @param array $plugins Array holding all plugin recommendations, coming from
 *                       the class and the filter.
 * @return array Filtered array of all plugin recommendations.
 */
function ddw_bse_register_extra_plugin_recommendations( array $plugins ) {

	/** Remove our own slug when we are already active :) */
	if ( isset( $plugins[ 'builder-shortcode-extras' ] ) ) {
		$plugins[ 'builder-shortcode-extras' ] = null;
	}

	/** Merge with the existing recommendations and return */
	return $plugins;
  
}  // end function

/** Optionally add string translations for the library */
if ( ! function_exists( 'ddwlib_plir_strings_plugin_installer' ) ) :

	add_filter( 'ddwlib_plir/filter/strings/plugin_installer', 'ddwlib_plir_strings_plugin_installer' );
	/**
	 * Optionally, make strings translateable for included library "DDWlib Plugin
	 *   Installer Recommendations".
	 *   Strings:
	 *    - "Newest" --> tab in plugin installer toolbar
	 *    - "Version:" --> label in plugin installer plugin card
	 *
	 * @since 1.0.0
	 *
	 * @param array $strings Holds all filterable strings of the library.
	 * @return array Array of tweaked translateable strings.
	 */
	function ddwlib_plir_strings_plugin_installer( $strings ) {

		$strings[ 'newest' ] = _x(
			'Newest',
			'Plugin installer: Tab name in installer toolbar',
			'builder-shortcode-extras'
		);

		$strings[ 'version' ] = _x(
			'Version:',
			'Plugin card: plugin version',
			'builder-shortcode-extras'
		);

		$strings[ 'ddwplugins_tab' ] = _x(
			'deckerweb Plugins',
			'Plugin installer: Tab name in installer toolbar',
			'builder-shortcode-extras'
		);

		$strings[ 'tab_title' ] = _x(
			'deckerweb Plugins',
			'Plugin installer: Page title',
			'builder-shortcode-extras'
		);

		$strings[ 'tab_slogan' ] = __( 'Great helper tools for Site Builders to save time and get more productive', 'builder-shortcode-extras' );

		$strings[ 'tab_info' ] = sprintf(
			__( 'You can use any of our free plugins or premium plugins from %s', 'builder-shortcode-extras' ),
			'<a href="https://deckerweb-plugins.com/" target="_blank" rel="nofollow noopener noreferrer">' . $strings[ 'tab_title' ] . '</a>'
		);

		$strings[ 'tab_newsletter' ] = __( 'Join our Newsletter', 'builder-shortcode-extras' );

		$strings[ 'tab_fbgroup' ] = __( 'Facebook User Group', 'builder-shortcode-extras' );

		return $strings;

	}  // end function

endif;  // function check

/** Include class DDWlib Plugin Installer Recommendations */
require_once BSE_PLUGIN_DIR . 'includes/admin/classes/ddwlib-plir/ddwlib-plugin-installer-recommendations.php';
