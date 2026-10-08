<?php
/**
 * Read-only release history from the documentation shipped with this installation.
 *
 * @package BuilderShortcodeExtras
 */

namespace Deckerweb\BuilderShortcodeExtras;

defined( 'ABSPATH' ) || exit;

/** Render trusted local filenames as escaped, structured changelog content. */
final class Changelog {
	/**
	 * Resolve the documentation language from the current WordPress admin locale.
	 *
	 * @return bool Whether to use the German documentation.
	 */
	public static function is_german(): bool {
		return 1 === preg_match( '/^de(?:_|$)/i', determine_locale() );
	}

	/**
	 * Return the localized local documentation URL.
	 *
	 * @return string URL to the bundled language document.
	 */
	public static function url(): string {
		return plugins_url( self::is_german() ? 'docs/changelog-de.txt' : 'docs/changelog.txt', BSE_PLUGIN_FILE );
	}

	/**
	 * Render bounded local documentation as escaped structural HTML.
	 *
	 * @return string Rendered document or an empty string when unavailable.
	 */
	public static function content(): string {
		$file = BSE_PLUGIN_DIR . ( self::is_german() ? 'docs/changelog-de.txt' : 'docs/changelog.txt' );
		if ( ! is_readable( $file ) || filesize( $file ) > 262144 ) {
			return '';
		}
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Read one of two fixed local documentation files; no user-supplied paths or remote requests.
		$text = file_get_contents( $file );
		if ( false === $text || ! preg_match( '/^== Changelog ==\s*\R(.*?)(?=^== [^\r\n]+ ==\s*$|\z)/ms', $text, $section ) ) {
			return '';
		}
		require_once BSE_PLUGIN_DIR . 'includes/deckerweb-changelog-v1.php';
		return \Deckerweb_Changelog_Renderer_V1::render( $text );
	}

	/**
	 * Output an accessible documentation dialog with a fallback link.
	 *
	 * @return void Emits escaped content and fixed dialog markup.
	 */
	public static function dialog(): void {
		$content = self::content();
		if ( '' === $content ) {
			return;
		}
		$close = __( 'Close', 'builder-shortcode-extras' );
		echo '<dialog id="bse-document-changelog" class="bse-document-dialog" aria-labelledby="bse-document-changelog-title"><header class="bse-document-header"><h2 id="bse-document-changelog-title">Builder Shortcode Extras · ' . esc_html__( 'Changelog', 'builder-shortcode-extras' ) . '</h2><button type="button" class="button" data-bse-close autofocus>' . esc_html( $close ) . '</button></header><div class="bse-document-content" tabindex="0">';
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- content() escapes every text node; only fixed heading, list and paragraph markup is emitted.
		echo '</div><p class="bse-document-source"><a href="' . esc_url( self::url() ) . '">' . esc_html( self::is_german() ? 'docs/changelog-de.txt' : 'docs/changelog.txt' ) . '</a></p></dialog>';
	}
}
