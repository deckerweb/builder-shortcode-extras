<?php
/** Searchable help and a focused shortcode generator. @package BuilderShortcodeExtras */
namespace Deckerweb\BuilderShortcodeExtras;
defined( 'ABSPATH' ) || exit;

/** Own the read-only admin tools screen, its scoped assets and product footer. */
final class Tools {
	/**
	 * Register the shortcode tools menu, assets and plugin action links.
	 *
	 * @return void Attaches admin-only hooks.
	 */
	public static function register(): void {
		add_action( 'wp_ajax_bse_content_search', array( __CLASS__, 'search' ) );
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'network_admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( BSE_PLUGIN_FILE ), array( __CLASS__, 'links' ) );
		add_filter( 'network_admin_plugin_action_links_' . plugin_basename( BSE_PLUGIN_FILE ), array( __CLASS__, 'links' ) );
	}
	/**
	 * Search readable content for authorized tools users in the current website.
	 *
	 * @since 1.3.0
	 * @return void Sends at most ten title/ID pairs as JSON; never renders content.
	 */
	public static function search(): void {
		check_ajax_referer( 'bse-content-search', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_network_options' ) ) { wp_send_json_error( array(), 403 ); }
		$term = isset( $_GET['q'] ) && is_string( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
		if ( strlen( $term ) < 2 || strlen( $term ) > 100 ) { wp_send_json_success( array() ); }
		$types = array_values( get_post_types( array( 'public' => true ) ) );
		foreach ( array( 'wp_block', 'elementor_library', 'bricks_template', 'fl-builder-template' ) as $type ) { if ( post_type_exists( $type ) ) { $types[] = $type; } }
		$query = new \WP_Query( array( 'post_type' => array_unique( $types ), 'post_status' => array( 'publish', 'private', 'draft' ), 's' => $term, 'posts_per_page' => 10, 'no_found_rows' => true, 'update_post_meta_cache' => false, 'update_post_term_cache' => false ) );
		$items = array();
		foreach ( $query->posts as $item ) {
			if ( ! current_user_can( 'read_post', $item->ID ) ) { continue; }
			$items[] = array( 'id' => $item->ID, 'title' => wp_strip_all_tags( get_the_title( $item ) ), 'type' => $item->post_type, 'status' => $item->post_status );
		}
		wp_send_json_success( $items );
	}
	/**
	 * Register the representative tools screen for the current admin scope.
	 *
	 * @return void Adds a management or network settings page.
	 */
	public static function menu(): void {
		if ( is_network_admin() ) {
			add_submenu_page( 'settings.php', 'Builder Shortcode Extras', 'Builder Shortcode Extras', 'manage_network_options', 'builder-shortcode-extras', array( __CLASS__, 'page' ) );
			return;
		}
		add_management_page( 'Builder Shortcode Extras', __( 'Builder Shortcode Extras', 'builder-shortcode-extras' ), 'manage_options', 'builder-shortcode-extras', array( __CLASS__, 'page' ) );
	}
	/**
	 * Prepend the representative Settings link when permitted in this admin scope.
	 *
	 * @param array $links Existing plugin action links.
	 * @return array Action links with the permitted Settings link first.
	 */
	public static function links( array $links ): array {
		if ( current_user_can( is_network_admin() ? 'manage_network_options' : 'manage_options' ) ) {
			$links = array( 'bse_tools' => '<a href="' . esc_url( is_network_admin() ? network_admin_url( 'settings.php?page=builder-shortcode-extras' ) : admin_url( 'tools.php?page=builder-shortcode-extras' ) ) . '">' . esc_html__( 'Settings', 'builder-shortcode-extras' ) . '</a>' ) + $links;
		}
		return $links;
	}
	/**
	 * Enqueue help assets and catalog data only on the tools screen.
	 *
	 * @param string $hook Current admin page hook suffix.
	 * @return void Enqueues local styles, JavaScript and escaped catalog JSON.
	 */
	public static function assets( $hook ): void {
		if ( ! in_array( $hook, array( 'tools_page_builder-shortcode-extras', 'settings_page_builder-shortcode-extras' ), true ) ) {
			return;
		}
		wp_enqueue_style( 'bse-tools', plugins_url( 'assets/admin/tools.css', BSE_PLUGIN_FILE ), array(), BSE_PLUGIN_VERSION );
		wp_enqueue_script( 'bse-tools', plugins_url( 'assets/admin/tools.js', BSE_PLUGIN_FILE ), array(), BSE_PLUGIN_VERSION, true );
		wp_localize_script( 'bse-tools', 'BSE_TOOLS', array(
			'catalog' => Catalog::all(),
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'bse-content-search' ),
			'i18n' => array(
				'copied' => __( 'Copied. Paste this shortcode into a shortcode-enabled field.', 'builder-shortcode-extras' ),
				'manual' => __( 'Copy the selected shortcode manually.', 'builder-shortcode-extras' ),
				'required' => __( 'Enter a valid content ID greater than zero.', 'builder-shortcode-extras' ),
				'invalid' => __( 'Use a whole number greater than zero.', 'builder-shortcode-extras' ),
				'searching' => __( 'Searching…', 'builder-shortcode-extras' ),
				'noContent' => __( 'No readable content found.', 'builder-shortcode-extras' ),
				'searchFailed' => __( 'Search unavailable. Enter the content ID manually.', 'builder-shortcode-extras' ),
				'currentItem' => __( 'Current frontend item', 'builder-shortcode-extras' ),
				'selectedItem' => __( 'Selected content ID:', 'builder-shortcode-extras' ),
				'quote' => __( 'Attribute values cannot contain quotation marks, square brackets or line breaks.', 'builder-shortcode-extras' ),
			),
		) );
	}
	/**
	 * Render help and the generator for authorized site or network administrators.
	 *
	 * @return void Outputs the read-only tools interface; no settings are written.
	 */
	public static function page(): void {
		if ( ! current_user_can( is_network_admin() ? 'manage_network_options' : 'manage_options' ) ) {
			return;
		}
		$catalog = Catalog::all();
		?>
		<div class="wrap bse-root">
			<header class="bse-page-heading"><img src="<?php echo esc_url( plugins_url( 'assets/icon.svg', BSE_PLUGIN_FILE ) ); ?>" alt="" width="56" height="56"><div><h1>Builder Shortcode Extras</h1><p><?php esc_html_e( 'Small dynamic helpers. Ready when you need them.', 'builder-shortcode-extras' ); ?></p></div></header>
			<hr class="wp-header-end">
			<p class="bse-intro"><?php esc_html_e( 'Find a helper, copy its shortcode and paste it into a Shortcode block or a shortcode-enabled builder field. No settings to save.', 'builder-shortcode-extras' ); ?></p>
			<div class="bse-layout">
			<section class="bse-panel bse-generator" aria-labelledby="bse-generator-heading">
				<h2 id="bse-generator-heading"><?php esc_html_e( 'Build your shortcode', 'builder-shortcode-extras' ); ?></h2>
				<label for="bse-use-case"><?php esc_html_e( 'What would you like to add?', 'builder-shortcode-extras' ); ?></label>
				<select id="bse-use-case">
				<?php foreach ( $catalog as $tag => $entry ) : if ( ! $entry['fields'] ) { continue; } ?>
					<option value="<?php echo esc_attr( $tag ); ?>"><?php echo esc_html( $entry['label'] ); ?></option>
				<?php endforeach; ?>
				</select>
				<p id="bse-case-description"></p><div id="bse-fields"></div>
				<div id="bse-content-picker" hidden><label for="bse-content-query"><?php esc_html_e( 'Find a page, post or template', 'builder-shortcode-extras' ); ?></label><input type="search" id="bse-content-query" maxlength="100"><p id="bse-content-status" role="status"></p><div id="bse-content-results"></div></div>
				<p class="description"><?php esc_html_e( 'An empty content ID uses the current item on the frontend. Builder previews can show a different item. Embedded content and links require an explicit ID. Drafts and private content remain subject to access permissions.', 'builder-shortcode-extras' ); ?></p>
				<label for="bse-generated"><?php esc_html_e( 'Your shortcode', 'builder-shortcode-extras' ); ?></label>
				<textarea id="bse-generated" rows="3" readonly spellcheck="false">[bse-copyright]</textarea>
				<p id="bse-generator-error" class="bse-error" role="alert" hidden></p>
				<button type="button" class="button button-primary" id="bse-copy-generated"><?php esc_html_e( 'Copy shortcode', 'builder-shortcode-extras' ); ?></button>
				<p class="description"><?php esc_html_e( 'For dates, leave the post ID empty to use the current post. Insert generated shortcodes on your website to see their output.', 'builder-shortcode-extras' ); ?></p>
				<noscript><p><?php esc_html_e( 'The generator needs JavaScript. You can still select and copy examples from the help below.', 'builder-shortcode-extras' ); ?></p></noscript>
			</section>
			<section class="bse-panel" aria-labelledby="bse-help-heading">
				<h2 id="bse-help-heading"><?php esc_html_e( 'Find a shortcode', 'builder-shortcode-extras' ); ?></h2>
				<label for="bse-search"><?php esc_html_e( 'Search by task, name or attribute', 'builder-shortcode-extras' ); ?></label>
				<input type="search" id="bse-search" placeholder="<?php esc_attr_e( 'Try: copyright, date, author…', 'builder-shortcode-extras' ); ?>" aria-controls="bse-catalog">
				<p id="bse-no-results" hidden><?php esc_html_e( 'No matching shortcode. Try another search term.', 'builder-shortcode-extras' ); ?></p>
				<div id="bse-catalog">
				<?php $groups = array(); foreach ( $catalog as $tag => $entry ) { $groups[$entry['group']][$tag] = $entry; } ?>
				<?php foreach ( $groups as $group => $entries ) : ?>
				<section class="bse-group"><h3><?php echo esc_html( $group ); ?></h3>
				<?php foreach ( $entries as $tag => $entry ) : ?>
				<article class="bse-card">
					<h4><?php echo esc_html( $entry['label'] ); ?></h4><p><?php echo esc_html( $entry['description'] ); ?></p>
					<?php if ( $entry['integration'] ) : ?><p class="bse-badge"><?php echo esc_html( $entry['integration'] ); ?></p><?php endif; ?>
					<div class="bse-example"><code tabindex="0"><?php echo esc_html( $entry['example'] ); ?></code><button type="button" class="button bse-copy" aria-label="<?php echo esc_attr( __( 'Copy shortcode', 'builder-shortcode-extras' ) . ': ' . $entry['label'] ); ?>"><?php esc_html_e( 'Copy', 'builder-shortcode-extras' ); ?></button></div>
					<?php if ( $entry['fields'] ) : ?><button type="button" class="button-link bse-configure" data-tag="<?php echo esc_attr( $tag ); ?>"><?php esc_html_e( 'Configure in generator', 'builder-shortcode-extras' ); ?></button><?php endif; ?>
					<?php if ( $entry['attributes'] ) : ?><details><summary><?php esc_html_e( 'Available attributes', 'builder-shortcode-extras' ); ?></summary><p><code><?php echo esc_html( implode( ', ', $entry['attributes'] ) ); ?></code></p></details><?php endif; ?>
				</article>
				<?php endforeach; ?></section><?php endforeach; ?>
				</div>
			</section>
			</div>
			<p id="bse-copy-status" role="status" aria-live="polite"></p>
			<?php self::footer(); ?>
		</div>
		<?php
	}
	/**
	 * Render product information and localized documentation dialogs.
	 *
	 * @return void Emits the tools-page footer and its dialogs.
	 */
	private static function footer(): void {
		$repo = 'https://github.com/deckerweb/builder-shortcode-extras';
		$guide = Guide::url();
		echo '<footer class="bse-footer" aria-label="' . esc_attr__( 'Plugin information', 'builder-shortcode-extras' ) . '"><div class="bse-footer-product"><div><strong>Builder Shortcode Extras</strong> <span>' . esc_html__( 'Version', 'builder-shortcode-extras' ) . ' ' . esc_html( BSE_PLUGIN_VERSION ) . '</span> · <a href="' . esc_url( Changelog::url() ) . '" data-bse-document="changelog">' . esc_html__( 'Changelog', 'builder-shortcode-extras' ) . '</a> · <a href="' . esc_url( $guide ) . '" data-bse-document="guide">' . esc_html__( 'Documentation', 'builder-shortcode-extras' ) . '</a><p>' . esc_html__( 'Small dynamic helpers. Ready when you need them.', 'builder-shortcode-extras' ) . '</p></div></div><div><span>© 2019–2026 <a href="https://github.com/deckerweb" target="_blank" rel="noopener noreferrer">David Decker – DECKERWEB</a></span><a href="' . esc_url( $repo ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Plugin website', 'builder-shortcode-extras' ) . '</a></div></footer>';
		Changelog::dialog();
		Guide::dialog();
	}
}
