<?php
/** Shared registration and rendering boundaries. @package BuilderShortcodeExtras */
defined( 'ABSPATH' ) || exit;

/**
 * Register a callback under the frontend or opted-in admin policy.
 *
 * @param string $tag Public shortcode tag.
 * @param callable|string $callback Registered shortcode handler.
 * @return void Registers a WordPress shortcode when allowed.
 */
function ddw_bse_register_shortcode( $tag, $callback ) {
	$admin = is_admin() || is_network_admin();
	if ( $admin ) {
		/**
		 * Filter shortcodes in admin while preserving its value contract.
		 * @since 1.0.0
		 * @param mixed $value Current value. Return the replacement value.
		 */
		if ( ! apply_filters( 'bse/filter/shortcodes_in_admin', false ) ) {
			return;
		}
		$embedding = array( 'ddw_bse_shortcode_item_content', 'ddw_bse_shortcode_nav_menu', 'ddw_bse_shortcode_comment_form', 'ddw_bse_shortcode_elementor_template', 'ddw_bse_shortcode_genesis_footer', 'ddw_bse_shortcode_genesis_breadcrumbs' );
		if ( in_array( $callback, $embedding, true ) ) {
			return;
		}
	}
	add_shortcode( $tag, $callback );
}

/**
 * Select a supported non-executable wrapper element.
 *
 * @param mixed $tag Requested element name.
 * @return string Supported tag or span.
 */
function ddw_bse_wrapper_tag( $tag ) {
	$tag = strtolower( (string) $tag );
	$allowed = array( 'span', 'p', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'article', 'section', 'aside', 'header', 'footer', 'main', 'nav', 'strong', 'em', 'small', 'b', 'i', 'u', 's', 'mark', 'time', 'code', 'pre', 'blockquote', 'figure', 'figcaption', 'li', 'ul', 'ol', 'dl', 'dt', 'dd' );
	return in_array( $tag, $allowed, true ) ? $tag : 'span';
}

/**
 * Interpret scalar shortcode boolean values.
 *
 * @param mixed $value Boolean or a textual shortcode value.
 * @return bool True only for an affirmative value.
 */
function ddw_bse_boolean( $value ) {
	return true === filter_var( $value, FILTER_VALIDATE_BOOLEAN );
}

/**
 * Resolve a content item and enforce its status and password access.
 *
 * @param int $id Requested content ID.
 * @return WP_Post|null Readable item, or null when access is denied.
 */
function ddw_bse_readable_item( $id ) {
	$post = get_post( $id );
	if ( ! $post || post_password_required( $post ) || in_array( $post->post_status, array( 'trash', 'auto-draft' ), true ) ) {
		return null;
	}
	if ( 'publish' === $post->post_status ) {
		return $post;
	}
	$capability = 'private' === $post->post_status ? 'read_post' : 'edit_post';
	return current_user_can( $capability, $post->ID ) ? $post : null;
}

/**
 * Render native or supported builder content with a request-local recursion guard.
 *
 * @param int $id Requested content ID.
 * @param bool $include_css Whether Elementor may include its CSS.
 * @param string $renderer auto or elementor selection.
 * @return string Rendered content; empty when unavailable or recursively referenced.
 */
function ddw_bse_render_item_content( $id, $include_css = false, $renderer = 'auto' ) {
	static $stack = array();
	$id = absint( $id );
	if ( ! $id || isset( $stack[$id] ) || count( $stack ) >= 20 ) {
		return '';
	}
	$post = ddw_bse_readable_item( $id );
	if ( ! $post ) {
		return '';
	}
	$stack[$id] = true;
	try {
		$elementor = get_post_meta( $id, '_elementor_edit_mode', true );
		if ( 'elementor' === $renderer || ( $elementor && ddw_bse_is_elementor_active() ) ) {
			if ( ! ddw_bse_is_elementor_active() || ! class_exists( '\Elementor\Plugin' ) ) {
				return '';
			}
			return (string) \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $id, $include_css );
		}
		if ( class_exists( 'FLBuilder' ) && get_post_meta( $id, '_fl_builder_enabled', true ) ) {
			return do_shortcode( sprintf( '[fl_builder_insert_layout id="%d"]', $id ) );
		}
		// Render native blocks too; do not run the_content, which may embed this item again.
		return do_shortcode( do_blocks( $post->post_content ) );
	} finally {
		unset( $stack[$id] );
	}
}

/**
 * Compose a shortcode wrapper from trusted inner HTML and escaped labels.
 *
 * @param array $attributes Resolved attributes.
 * @param string $classes Existing public CSS classes.
 * @param string $html Trusted WordPress or plugin generated markup.
 * @param bool $labels Include the before and after labels.
 * @return string Safe wrapper markup.
 */
function ddw_bse_metadata_wrapper( $attributes, $classes, $html, $labels = true ) {
	$tag = ddw_bse_wrapper_tag( $attributes['wrapper'] );
	$custom = ddw_bse_sanitize_html_classes( $attributes['class'], 'string' );
	$classes .= $custom ? ' ' . $custom : '';
	if ( $labels ) {
		$html = ( $attributes['before'] !== '' ? esc_html( $attributes['before'] ) . ' ' : '' ) . $html;
		$html .= $attributes['after'] !== '' ? ' ' . esc_html( $attributes['after'] ) : '';
	}
	return '<' . $tag . ' class="' . esc_attr( $classes ) . '">' . $html . '</' . $tag . '>';
}

/**
 * Serialize optional link attributes using the existing target and rel policy.
 *
 * @param array $attributes Resolved target and rel attributes.
 * @return string Escaped optional anchor attributes.
 */
function ddw_bse_metadata_link_attributes( $attributes ) {
	$result = '';
	if ( ! empty( $attributes['target'] ) ) {
		$result = ' target="' . esc_attr( sanitize_key( $attributes['target'] ) ) . '"';
	}
	if ( ! empty( $attributes['rel'] ) ) {
		$result .= ' rel="' . esc_attr( strtolower( $attributes['rel'] ) ) . '"';
	}
	return $result;
}

/**
 * Apply opt-in presentation attributes to BSE shortcode output after existing callbacks.
 *
 * @since 1.3.0
 * @param string $output Existing filtered callback output.
 * @param string $tag Registered shortcode name.
 * @param array|string $atts Raw shortcode attributes.
 * @param array $match Core shortcode match, unused.
 * @return string Output with escaped fallback, optional time markup or outer-wrapper removal.
 */
function ddw_bse_shortcode_presentation( $output, $tag, $atts, $match ) {
	if ( 0 !== strpos( $tag, 'bse-' ) || ! is_array( $atts ) ) { return $output; }
	if ( '' === trim( (string) $output ) || preg_match( '/^<([a-z][a-z0-9]*)\b[^>]*class="bse-[^"]*"[^>]*>\s*<\/\1>$/s', $output ) ) {
		return isset( $atts['fallback'] ) && is_scalar( $atts['fallback'] ) ? esc_html( (string) $atts['fallback'] ) : $output;
	}
	$handler = $GLOBALS['shortcode_tags'][$tag] ?? '';
	$embedding = is_string( $handler ) && in_array( $handler, array( 'ddw_bse_shortcode_item_content', 'ddw_bse_shortcode_elementor_template', 'ddw_bse_shortcode_nav_menu', 'ddw_bse_shortcode_comment_form', 'ddw_bse_shortcode_genesis_footer', 'ddw_bse_shortcode_genesis_breadcrumbs' ), true );
	$excluded = array( 'bse-nav-menu', 'bse-comment-form', 'bse-item-content', 'bse-elementor-template', 'bse-genesis-footer', 'bse-genesis-breadcrumbs', 'bse-post-image' );
	if ( isset( $atts['wrapper'] ) && 'none' === $atts['wrapper'] && ! $embedding && ! in_array( $tag, $excluded, true ) && ! in_array( $tag, array( 'bse-wpblock', 'bse-astra-layout', 'bse-beaver-template', 'bse-bricks-template' ), true ) ) {
		$output = preg_replace( '/^<([a-z][a-z0-9]*)\b[^>]*class="bse-[^"]*"[^>]*>(.*)<\/\1>$/s', '$2', $output );
	}
	$dates = array( 'bse-post-date' => 'date', 'bse-post-time' => 'date', 'bse-post-modified-date' => 'modified', 'bse-post-modified-time' => 'modified', 'bse-item-last-updated' => 'modified' );
	if ( ( isset( $dates[$tag] ) || 'bse-site-updated' === $tag ) && isset( $atts['semantic'] ) && ddw_bse_boolean( $atts['semantic'] ) ) {
		$id = isset( $atts['post_id'] ) ? absint( $atts['post_id'] ) : get_the_ID();
		$item = ddw_bse_readable_item( $id );
		$date = isset( $dates[$tag] ) && $item ? get_post_datetime( $item, $dates[$tag] ) : false;
		if ( 'bse-site-updated' === $tag ) {
			$modified = ddw_bse_site_updated( isset( $atts['type'] ) && 'first' === $atts['type'] ? 'MIN' : 'MAX' );
			$date = $modified ? new DateTimeImmutable( $modified, wp_timezone() ) : false;
		}
		if ( $date ) {
			$datetime = esc_attr( $date->format( DATE_W3C ) );
			if ( preg_match( '/<time\b[^>]*>/i', $output ) ) {
				/**
				 * Add the selected item date to native time markup.
				 * @param array $time Opening tag match.
				 * @return string Opening tag with escaped datetime.
				 */
				$output = preg_replace_callback( '/<time\b[^>]*>/i', static function ( $time ) use ( $datetime ) {
					$open = preg_replace( '/\sdatetime="[^"]*"/i', '', $time[0] );
					return substr( $open, 0, -1 ) . ' datetime="' . $datetime . '">';
				}, $output, 1 );
			} else { $output = '<time datetime="' . $datetime . '">' . $output . '</time>'; }
		}
	}
	return $output;
}
add_filter( 'do_shortcode_tag', 'ddw_bse_shortcode_presentation', 10, 4 );
