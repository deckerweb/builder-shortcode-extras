<?php
/** Focused content metadata helpers. @package BuilderShortcodeExtras */
defined( 'ABSPATH' ) || exit;

/**
 * Render title, excerpt, featured image or estimated reading time without executing content.
 *
 * @since 1.3.0
 * @param array|string $atts Shortcode attributes.
 * @param string|null $content Enclosed content, unused.
 * @param string $tag Registered shortcode name.
 * @return string Escaped text or native responsive image markup; empty when unavailable.
 */
function ddw_bse_shortcode_metadata( $atts, $content = null, $tag = 'bse-post-title' ) {
	$atts = shortcode_atts( array( 'post_id' => get_the_ID(), 'words' => 30, 'size' => 'medium', 'wpm' => 200, 'before' => '', 'after' => '', 'class' => '', 'wrapper' => 'span' ), $atts, $tag );
	$id = absint( $atts['post_id'] );
	if ( ! $id ) { return ''; }
	$item = ddw_bse_readable_item( $id );
	if ( ! $item ) { return ''; }
	if ( 'bse-post-image' === $tag ) {
		$size = sanitize_key( $atts['size'] );
		if ( ! in_array( $size, array_merge( array( 'full' ), get_intermediate_image_sizes() ), true ) ) { $size = 'medium'; }
		$html = get_the_post_thumbnail( $item, $size );
		if ( '' === $html ) { return ''; }
	} else {
		if ( 'bse-post-title' === $tag ) {
			$value = get_the_title( $item );
		} elseif ( 'bse-post-excerpt' === $tag ) {
			$source = $item->post_excerpt ?: $item->post_content;
			$value = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $source ) ), max( 1, min( 500, absint( $atts['words'] ) ) ) );
		} else {
			// Count stored prose only: do not render blocks, builders or embedded shortcodes.
			$prose = trim( wp_strip_all_tags( strip_shortcodes( $item->post_content ) ) );
			if ( '' === $prose ) { return ''; }
			static $counts = array();
			$key = get_current_blog_id() . ':' . $id . ':' . md5( $prose );
			$count = $counts[$key] ?? preg_match_all( '/[\p{L}\p{N}]+(?:[’\x{0027}-][\p{L}\p{N}]+)*/u', $prose );
			if ( count( $counts ) < 100 ) { $counts[$key] = $count; }
			$minutes = max( 1, (int) ceil( $count / max( 50, min( 1000, absint( $atts['wpm'] ) ) ) ) );
			/* translators: %s: Estimated number of minutes. */
			$value = sprintf( _n( '%s minute', '%s minutes', $minutes, 'builder-shortcode-extras' ), number_format_i18n( $minutes ) );
		}
		if ( '' === trim( $value ) ) { return ''; }
		$html = esc_html( $value );
	}
	return ddw_bse_metadata_wrapper( $atts, $tag, $html );
}
foreach ( array( 'bse-post-title', 'bse-post-excerpt', 'bse-post-image', 'bse-reading-time' ) as $ddw_bse_metadata_tag ) {
	ddw_bse_register_shortcode( $ddw_bse_metadata_tag, 'ddw_bse_shortcode_metadata' );
}
unset( $ddw_bse_metadata_tag );
