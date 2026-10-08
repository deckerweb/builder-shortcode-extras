<?php
/** Shared deckerweb footer standard: escaped, structured release history. GPL-2.0-or-later. */
defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'Deckerweb_Changelog_Renderer_V1', false ) ) {
	/** Render local release history as escaped headings and categorized entries. */
	final class Deckerweb_Changelog_Renderer_V1 {
		/**
		 * Parse bounded release headings and list items; source HTML is always escaped.
		 * @param string $text Local changelog text, optionally with readme section markers.
		 * @return string Structured release HTML, or an empty string for oversized input.
		 */
		public static function render( string $text ): string {
			if ( strlen( $text ) > 262144 ) { return ''; }
			if ( preg_match( '/^== Changelog ==\s*\R(.*?)(?=^== [^\r\n]+ ==\s*$|\z)/ms', $text, $match ) ) { $text = $match[1]; }
			$releases = []; $current = null;
			foreach ( preg_split( '/\R/', $text ) as $line ) {
				$line = trim( $line );
				if ( preg_match( '/^= (.+) =$/', $line, $heading ) ) {
					$releases[] = [ 'heading' => $heading[1], 'items' => [] ]; $current = count( $releases ) - 1;
				} elseif ( null !== $current && preg_match( '/^[*\-] (.+)$/', $line, $item ) ) { $releases[$current]['items'][] = $item[1]; }
			}
			$html = '';
			$categories = [ 'New' => 'new', 'Neu' => 'new', 'Improved' => 'improved', 'Verbessert' => 'improved', 'Fixed' => 'fixed', 'Behoben' => 'fixed', 'Misc' => 'misc', 'Sonstiges' => 'misc' ];
			foreach ( $releases as $release ) {
				$parts = array_map( 'trim', preg_split( '/[·—]/u', $release['heading'], 2 ) );
				$html .= '<section class="ddw-changelog-release"><header class="ddw-changelog-version"><h3>' . esc_html( $parts[0] ) . '</h3>' . ( isset( $parts[1] ) ? '<span>' . esc_html( $parts[1] ) . '</span>' : '' ) . '</header><ul class="ddw-changelog-entries">';
				foreach ( $release['items'] as $item ) {
					$prefix = ''; $category = 'misc'; $body = $item;
					if ( preg_match( '/^([^:]+):\s*(.*)$/', $item, $entry ) && isset( $categories[$entry[1]] ) ) { $prefix = $entry[1]; $category = $categories[$prefix]; $body = $entry[2]; }
					$html .= '<li>' . ( $prefix ? '<span class="ddw-changelog-badge ddw-changelog-' . $category . '">' . esc_html( $prefix ) . '</span>' : '' ) . '<span>' . self::inline( $body ) . '</span></li>';
				}
				$html .= '</ul></section>';
			}
			return $html;
		}
		/**
		 * Escape one entry and allow only fixed inline code-span markup.
		 * @param string $text Untrusted local entry text.
		 * @return string Escaped inline HTML.
		 */
		private static function inline( string $text ): string {
			// Escape first; only literal code spans become fixed markup.
			return preg_replace( '/`([^`]+)`/', '<code>$1</code>', esc_html( $text ) );
		}
	}
}
