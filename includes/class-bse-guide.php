<?php
/** Read-only local guide, usable before GitHub documentation is published. @package BuilderShortcodeExtras */
namespace Deckerweb\BuilderShortcodeExtras;
defined( 'ABSPATH' ) || exit;
final class Guide {
	public static function url(): string {
		return plugins_url( Changelog::is_german() ? 'docs/Deutsch.md' : 'docs/English.md', BSE_PLUGIN_FILE );
	}
	private static function text( string $line ): string {
		$line = esc_html( $line );
		$line = preg_replace_callback( '/\[([^\]]+)\]\(([^)\s]+)\)/', static function( $match ) {
			$url = html_entity_decode( $match[2], ENT_QUOTES, 'UTF-8' );
			if ( str_starts_with( $url, '#' ) ) {
				$href = esc_attr( $url );
			} elseif ( preg_match( '~^https?://~', $url ) ) {
				$href = esc_url( $url );
			} elseif ( preg_match( '/^[A-Za-z0-9_.-]+\.md$/D', $url ) ) {
				$href = esc_url( plugins_url( 'docs/' . $url, BSE_PLUGIN_FILE ) );
			} else { return $match[1]; }
			return '<a href="' . $href . '">' . $match[1] . '</a>';
		}, $line );
		$line = preg_replace( '/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $line );
		return preg_replace( '/`([^`]+)`/', '<code>$1</code>', $line );
	}
	public static function content(): string {
		$file = BSE_PLUGIN_DIR . ( Changelog::is_german() ? 'docs/Deutsch.md' : 'docs/English.md' );
		if ( ! is_readable( $file ) || filesize( $file ) > 262144 ) { return ''; }
		$html = ''; $code = false; $table = false; $list = false;
		foreach ( preg_split( '/\R/', (string) file_get_contents( $file ) ) as $line ) {
			if ( str_starts_with( $line, '```' ) ) {
				$html .= $code ? '</code></pre>' : ( $list ? '</ul>' : '' ) . '<pre><code>';
				$code = ! $code; $list = false; continue;
			}
			if ( $code ) { $html .= esc_html( $line ) . "\n"; continue; }
			if ( str_starts_with( $line, '|' ) ) {
				if ( preg_match( '/^\|[\s|:\-]+\|$/', $line ) ) { continue; }
				$cell = $table ? 'td' : 'th';
				if ( ! $table ) { $html .= '<div class="bse-guide-table"><table>'; $table = true; }
				$html .= '<tr>';
				foreach ( explode( '|', trim( $line, '| ' ) ) as $value ) { $html .= '<' . $cell . '>' . self::text( trim( $value ) ) . '</' . $cell . '>'; }
				$html .= '</tr>'; continue;
			}
			if ( $table ) { $html .= '</table></div>'; $table = false; }
			if ( str_starts_with( $line, '- ' ) ) {
				$html .= ( $list ? '' : '<ul>' ) . '<li>' . self::text( substr( $line, 2 ) ) . '</li>'; $list = true; continue;
			}
			if ( $list ) { $html .= '</ul>'; $list = false; }
			if ( preg_match( '/^(#{1,4}) (.+)$/', $line, $heading ) ) {
				$level = min( 4, strlen( $heading[1] ) + 1 ); $html .= '<h' . $level . ' id="' . esc_attr( sanitize_title( $heading[2] ) ) . '">' . self::text( $heading[2] ) . '</h' . $level . '>';
			} elseif ( '' !== trim( $line ) ) { $html .= '<p>' . self::text( $line ) . '</p>'; }
		}
		return $html . ( $code ? '</code></pre>' : '' ) . ( $table ? '</table></div>' : '' ) . ( $list ? '</ul>' : '' );
	}
	public static function dialog(): void {
		$content = self::content(); if ( '' === $content ) { return; }
		echo '<dialog id="bse-document-guide" class="bse-document-dialog" aria-labelledby="bse-document-guide-title"><header class="bse-document-header"><h2 id="bse-document-guide-title">Builder Shortcode Extras · ' . esc_html__( 'Documentation', 'builder-shortcode-extras' ) . '</h2><button type="button" class="button" data-bse-close autofocus>' . esc_html__( 'Close', 'builder-shortcode-extras' ) . '</button></header><div class="bse-document-content" tabindex="0">';
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed markup and escaped local text nodes from content().
		echo '</div><p class="bse-document-source"><a href="' . esc_url( self::url() ) . '">' . esc_html__( 'Documentation', 'builder-shortcode-extras' ) . '</a></p></dialog>';
	}
}
