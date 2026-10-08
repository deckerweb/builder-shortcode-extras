<?php
/**
 * Integrate the shared deckerweb updater with plugin-scoped safeguards.
 *
 * @package BuilderShortcodeExtras
 */

namespace Deckerweb\BuilderShortcodeExtras;

defined( 'ABSPATH' ) || exit;

/** Configure shared update artwork and isolate plugin-specific safeguards. */
final class GitHubUpdates {
	/** Public release repository; never taken from user input. */
	private const REPOSITORY = 'https://github.com/deckerweb/builder-shortcode-extras';

	/** Register after WordPress initializes translations. @return void */
	public function register(): void {
		add_action( 'init', array( $this, 'boot' ) );
	}

	/** Load the shared versioned class once, including beside other deckerweb plugins. @return void */
	public function boot(): void {
		if ( ! is_admin() && ! wp_doing_cron() && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) { return; }
		if ( ! class_exists( '\Deckerweb\GitHubReleaseUpdater\V2\Updater' ) ) {
			require_once BSE_PLUGIN_DIR . 'includes/deckerweb-github-release-updater-v2.php';
		}
		if ( ! defined( 'Deckerweb\\GitHubReleaseUpdater\\V2\\Updater::SUPPORTS_HOST_TRANSLATIONS' ) || ! \Deckerweb\GitHubReleaseUpdater\V2\Updater::SUPPORTS_HOST_TRANSLATIONS ) {
			add_action( 'admin_notices', array( $this, 'compatibility_notice' ) );
			add_action( 'network_admin_notices', array( $this, 'compatibility_notice' ) );
			return;
		}
		try {
			$updater = new \Deckerweb\GitHubReleaseUpdater\V2\Updater(
				BSE_PLUGIN_FILE,
				self::REPOSITORY,
				'Builder Shortcode Extras',
				__( 'Small dynamic helpers for your WordPress website. Find, configure and copy useful shortcodes.', 'builder-shortcode-extras' ),
				$this->artwork(),
				array( 'translate' => require BSE_PLUGIN_DIR . 'includes/updater-translations.php' )
			);
		} catch ( \InvalidArgumentException $error ) {
			add_action( 'admin_notices', array( $this, 'compatibility_notice' ) );
			add_action( 'network_admin_notices', array( $this, 'compatibility_notice' ) );
			return;
		}
		$updater->register();
		add_filter( 'http_request_args', array( $this, 'request_limits' ), 20, 2 );
		add_filter( 'upgrader_source_selection', array( $this, 'validate_source' ), 30, 4 );
	}

	/**
	 * Explain a shared updater conflict without exposing exception details.
	 *
	 * @return void Outputs a notice only to administrators allowed to update plugins.
	 */
	public function compatibility_notice(): void {
		if ( ! current_user_can( 'update_plugins' ) ) { return; }
		echo '<div class="notice notice-warning"><p>' . esc_html__( 'Builder Shortcode Extras cannot use the shared updater loaded by another plugin. Update the other deckerweb plugins or install the latest plugin ZIP manually.', 'builder-shortcode-extras' ) . '</p></div>';
	}

	/**
	 * Provide bundled artwork matching the current administrator's language.
	 *
	 * @return array<string,array<string,string>> WordPress icon and banner maps.
	 */
	public function artwork(): array {
		$language = 1 === preg_match( '/^de(?:_|$)/i', determine_locale() ) ? 'de-' : '';
		return array(
			'icons'   => array(
				'svg' => plugins_url( 'assets/icon.svg', BSE_PLUGIN_FILE ),
				'1x'  => plugins_url( 'assets/icon-128x128.png', BSE_PLUGIN_FILE ),
				'2x'  => plugins_url( 'assets/icon-256x256.png', BSE_PLUGIN_FILE ),
			),
			'banners' => array(
				'low'  => plugins_url( 'assets/banner-' . $language . '772x250.png', BSE_PLUGIN_FILE ),
				'high' => plugins_url( 'assets/banner-' . $language . '1544x500.png', BSE_PLUGIN_FILE ),
			),
		);
	}

	/**
	 * Bound only this repository's metadata requests; do not change package downloads.
	 *
	 * @param array  $args WordPress HTTP arguments.
	 * @param string $url Requested URL.
	 * @return array Bounded repository metadata arguments; other requests remain unchanged.
	 */
	public function request_limits( array $args, string $url ): array {
		if ( 'https://api.github.com/repos/deckerweb/builder-shortcode-extras/releases/latest' === $url ) {
			$args['limit_response_size'] = 512 * 1024;
			$args['timeout']             = 6;
			$args['redirection']         = 0;
			$args['sslverify']           = true;
			$args['reject_unsafe_urls']  = true;
		}
		return $args;
	}

	/**
	 * Validate the actual candidate before WordPress removes the installed plugin.
	 * Requirements in installed headers cannot describe a future release reliably.
	 *
	 * @param mixed $source Normalized directory or WP_Error from the shared updater.
	 * @param mixed $remote_source Unused extraction root.
	 * @param mixed $upgrader WordPress upgrader; identifies its native bulk-update context.
	 * @param array $hook_extra Upgrade context.
	 * @return mixed Original source or localized WP_Error.
	 */
	public function validate_source( $source, $remote_source, $upgrader, array $hook_extra ) {
		if ( ( $hook_extra['plugin'] ?? '' ) !== plugin_basename( BSE_PLUGIN_FILE )
			|| ( isset( $hook_extra['type'] ) && 'plugin' !== $hook_extra['type'] )
			|| ( isset( $hook_extra['action'] ) && 'update' !== $hook_extra['action'] )
			|| ( ! isset( $hook_extra['type'], $hook_extra['action'] ) && ! ( $upgrader instanceof \Plugin_Upgrader && true === $upgrader->bulk ) )
		) {
			return $source;
		}
		if ( is_wp_error( $source ) ) {
			$messages = array(
				'ddw_ghru_filesystem' => __( 'The update filesystem is unavailable. Please try again.', 'builder-shortcode-extras' ),
				'ddw_ghru_archive'    => __( 'The GitHub package does not contain the Builder Shortcode Extras plugin file.', 'builder-shortcode-extras' ),
				'ddw_ghru_rename'     => __( 'The GitHub package could not be prepared. The installed version has been kept.', 'builder-shortcode-extras' ),
			);
			$code     = $source->get_error_code();
			return isset( $messages[ $code ] ) ? new \WP_Error( $code, $messages[ $code ], $source->get_error_data( $code ) ) : $source;
		}
		global $wp_filesystem;
		if ( ! is_string( $source ) || ! $wp_filesystem ) {
			return new \WP_Error( 'bse_update_source', __( 'The update package could not be checked.', 'builder-shortcode-extras' ) );
		}
		$main = trailingslashit( $source ) . basename( BSE_PLUGIN_FILE );
		if ( ! $wp_filesystem->is_file( $main ) || $wp_filesystem->size( $main ) > 1024 * 1024 ) {
			return new \WP_Error( 'bse_update_source', __( 'The update package could not be checked.', 'builder-shortcode-extras' ) );
		}
		$text = $wp_filesystem->get_contents( $main );
		if ( ! is_string( $text ) ) {
			return new \WP_Error( 'bse_update_source', __( 'The update package could not be checked.', 'builder-shortcode-extras' ) );
		}
		$headers = array();
		foreach ( array( 'Plugin Name', 'Version', 'Update URI', 'Requires PHP', 'Requires at least' ) as $header ) {
			$headers[ $header ] = preg_match( '/^[ \t\/*#@]*' . preg_quote( $header, '/' ) . ':(.*)$/mi', str_replace( "\r", "\n", substr( $text, 0, 8192 ) ), $match ) ? trim( $match[1] ) : '';
		}
		if ( 'Builder Shortcode Extras' !== $headers['Plugin Name'] || self::REPOSITORY !== $headers['Update URI'] || ! preg_match( '/^\d+\.\d+\.\d+$/D', $headers['Version'] ) || version_compare( $headers['Version'], BSE_PLUGIN_VERSION, '<=' ) ) {
			return new \WP_Error( 'bse_update_identity', __( 'The package identity or version does not match a newer Builder Shortcode Extras release.', 'builder-shortcode-extras' ) );
		}
		$current  = get_site_transient( 'update_plugins' );
		$expected = is_object( $current ) ? ( $current->response[ plugin_basename( BSE_PLUGIN_FILE ) ]->new_version ?? '' ) : '';
		if ( ! is_string( $expected ) || '' === $expected || $headers['Version'] !== $expected ) {
			return new \WP_Error( 'bse_update_version', __( 'The package version differs from the offered update. Please check for updates again.', 'builder-shortcode-extras' ) );
		}
		foreach ( array( 'Requires PHP', 'Requires at least' ) as $header ) {
			if ( ! preg_match( '/^\d+\.\d+(?:\.\d+)?$/D', $headers[ $header ] ) ) {
				return new \WP_Error( 'bse_update_requirements', __( 'The update package has missing or invalid WordPress/PHP requirements.', 'builder-shortcode-extras' ) );
			}
		}
		if ( ! is_php_version_compatible( $headers['Requires PHP'] ) || ! is_wp_version_compatible( $headers['Requires at least'] ) ) {
			return new \WP_Error( 'bse_update_compatibility', __( 'This release requires a newer WordPress or PHP version. The installed version has been kept.', 'builder-shortcode-extras' ) );
		}
		return $source;
	}
}
