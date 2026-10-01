<?php
/** Searchable help and a five-use-case generator. @package BuilderShortcodeExtras */
namespace Deckerweb\BuilderShortcodeExtras;
defined( 'ABSPATH' ) || exit;

final class Tools {
	public static function register(): void {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( BSE_PLUGIN_FILE ), array( __CLASS__, 'links' ) );
	}
	public static function menu(): void {
		add_management_page( 'Builder Shortcode Extras', __( 'Builder Shortcode Extras', 'builder-shortcode-extras' ), 'manage_options', 'builder-shortcode-extras', array( __CLASS__, 'page' ) );
	}
	public static function links( array $links ): array {
		if ( current_user_can( 'manage_options' ) ) {
			$links = array( 'bse_tools' => '<a href="' . esc_url( admin_url( 'tools.php?page=builder-shortcode-extras' ) ) . '">' . esc_html__( 'Shortcodes', 'builder-shortcode-extras' ) . '</a>' ) + $links;
		}
		return $links;
	}
	public static function assets( $hook ): void {
		if ( 'tools_page_builder-shortcode-extras' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'bse-tools', plugins_url( 'assets/admin/tools.css', BSE_PLUGIN_FILE ), array(), BSE_PLUGIN_VERSION );
		wp_enqueue_script( 'bse-tools', plugins_url( 'assets/admin/tools.js', BSE_PLUGIN_FILE ), array(), BSE_PLUGIN_VERSION, true );
		wp_add_inline_script( 'bse-tools', 'window.BSE_TOOLS=' . wp_json_encode( array(
			'catalog' => Catalog::all(),
			'i18n' => array(
				'copied' => __( 'Copied. Paste this shortcode into a shortcode-enabled field.', 'builder-shortcode-extras' ),
				'manual' => __( 'Copy the selected shortcode manually.', 'builder-shortcode-extras' ),
				'required' => __( 'Enter a valid content ID greater than zero.', 'builder-shortcode-extras' ),
				'invalid' => __( 'Use a whole number greater than zero.', 'builder-shortcode-extras' ),
				'quote' => __( 'Attribute values cannot contain quotation marks, square brackets or line breaks.', 'builder-shortcode-extras' ),
			),
		) ) . ';', 'before' );
	}
	public static function page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$catalog = Catalog::all();
		?>
		<div class="wrap bse-root">
			<header class="bse-page-heading"><img src="<?php echo esc_url( plugins_url( 'assets/icon.svg', BSE_PLUGIN_FILE ) ); ?>" alt="" width="56" height="56"><div><h1>Builder Shortcode Extras</h1><p><?php esc_html_e( 'Small dynamic helpers. Ready when you need them.', 'builder-shortcode-extras' ); ?></p></div></header>
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
	private static function footer(): void {
		$repo = 'https://github.com/deckerweb/builder-shortcode-extras';
		$guide = Guide::url();
		echo '<footer class="bse-footer" aria-label="' . esc_attr__( 'Plugin information', 'builder-shortcode-extras' ) . '"><div><strong>Builder Shortcode Extras</strong> <span>' . esc_html__( 'Version', 'builder-shortcode-extras' ) . ' ' . esc_html( BSE_PLUGIN_VERSION ) . '</span> · <a href="' . esc_url( Changelog::url() ) . '" data-bse-document="changelog">' . esc_html__( 'Changelog', 'builder-shortcode-extras' ) . '</a> · <a href="' . esc_url( $guide ) . '" data-bse-document="guide">' . esc_html__( 'Documentation', 'builder-shortcode-extras' ) . '</a><p>' . esc_html__( 'Small dynamic helpers. Ready when you need them.', 'builder-shortcode-extras' ) . '</p></div><div><span>© 2019–2026 <a href="https://github.com/deckerweb" target="_blank" rel="noopener noreferrer">David Decker – DECKERWEB</a></span><a href="' . esc_url( $repo ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Plugin website', 'builder-shortcode-extras' ) . '</a></div></footer>';
		Changelog::dialog();
		Guide::dialog();
	}
}
