<?php
/** Shared registration and rendering boundaries. @package BuilderShortcodeExtras */
defined( 'ABSPATH' ) || exit;

/** Register frontend shortcodes, or explicitly enabled text helpers in wp-admin. */
function ddw_bse_register_shortcode( $tag, $callback ) {
	$admin = is_admin() || is_network_admin();
	if ( $admin ) {
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

/** Keep markup wrappers predictable; arbitrary executable/void tags are not wrappers. */
function ddw_bse_wrapper_tag( $tag ) {
	$tag = strtolower( (string) $tag );
	$allowed = array( 'span', 'p', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'article', 'section', 'aside', 'header', 'footer', 'main', 'nav', 'strong', 'em', 'small', 'b', 'i', 'u', 's', 'mark', 'time', 'code', 'pre', 'blockquote', 'figure', 'figcaption', 'li', 'ul', 'ol', 'dl', 'dt', 'dd' );
	return in_array( $tag, $allowed, true ) ? $tag : 'span';
}

/** Parse shortcode booleans; the strings "false", "0", "off" and "no" are false. */
function ddw_bse_boolean( $value ) {
	return true === filter_var( $value, FILTER_VALIDATE_BOOLEAN );
}

/** Return a readable content item, including explicitly requested published templates. */
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

/** Render content without echoing; protect both direct and indirect recursive embeds. */
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
