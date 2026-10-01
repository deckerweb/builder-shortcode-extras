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
	 * Local documentation URL, also used when dialogs or JavaScript are unavailable.
	 *
	 * @return string Changelog URL.
	 */
	public static function url(): string {
		return plugins_url( self::is_german() ? 'docs/changelog-de.txt' : 'docs/changelog.txt', BSE_PLUGIN_FILE );
	}

	/**
	 * Extract only the changelog section; never interpret readme text as HTML.
	 *
	 * @return string Escaped headings and lists, or an empty string if unavailable.
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
		$html = '';
		$list = false;
		foreach ( preg_split( '/\R/', trim( $section[1] ) ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			if ( preg_match( '/^= (.+) =$/', $line, $heading ) ) {
				$html .= ( $list ? '</ul>' : '' ) . '<h3>' . esc_html( $heading[1] ) . '</h3>';
				$list  = false;
			} elseif ( str_starts_with( $line, '* ' ) ) {
				$html .= ( $list ? '' : '<ul>' ) . '<li>' . esc_html( substr( $line, 2 ) ) . '</li>';
				$list  = true;
			} else {
				$html .= ( $list ? '</ul>' : '' ) . '<p>' . esc_html( $line ) . '</p>';
				$list  = false;
			}
		}
		return $html . ( $list ? '</ul>' : '' );
	}

	/**
	 * Dialog markup; the footer link remains usable if content cannot be read.
	 *
	 * @return void
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
