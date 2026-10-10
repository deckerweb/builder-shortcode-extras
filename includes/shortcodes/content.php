<?php

// includes/shortcodes/content

/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


if ( ! function_exists( 'ddw_bse_shortcode_nav_menu' ) ) :

	ddw_bse_register_shortcode( 'bse-nav-menu', 'ddw_bse_shortcode_nav_menu' );
	/**
	 * Render a native WordPress navigation menu with a bounded callback policy.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @param string|null $content Optional enclosed text; unused by this callback.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_nav_menu( $atts, $content = null ) {

		/**
		 * Filter shortcode defaults/nav menu while preserving its value contract.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/nav_menu',
			[
				/** For the wrapper */
				'before'          => '',				// before menu container
				'after'           => '',				// after menu container
				'class'           => '',				// class for wrapper
				'wrapper'         => 'div',				// wrapper HTML tag

				/** For the Nav Menu */
				'menu'            => '',				// name, ID, slug, object
				'container'       => 'div', 			// string, bool ("false")
				'container_class' => '',
				'container_id'    => '',
				'menu_class'      => 'menu',
				'menu_id'         => '',
				'fallback_cb'     => 'wp_page_menu',	// function, bool ("false")
				'item_before'     => '',
				'item_after'      => '',
				'link_before'     => '',
				'link_after'      => '',
				'depth'           => 0,					// int
				'walker'          => '',
				'theme_location'  => '',
				'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        		'item_spacing'    => 'preserve',		// string "preserve" or "discard" only
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-nav-menu' );

		// A content author cannot select arbitrary PHP callbacks or walker classes.
		$fallback = $atts['fallback_cb'];
		if ( in_array( $fallback, array( false, 'false', '0', '' ), true ) ) {
			$fallback = false;
		} elseif ( 'wp_page_menu' !== $fallback && $fallback !== $defaults['fallback_cb'] ) {
			$fallback = 'wp_page_menu';
		}
		$walker = $atts['walker'] instanceof \Walker ? $atts['walker'] : '';
		$container = in_array( $atts['container'], array( false, 'false', '0', '' ), true ) ? false : sanitize_key( $atts['container'] );

		/**
		 * Escape unsupported printf directives in a content-supplied menu wrapper.
		 * @param array $match A percent token or supported WordPress placeholder.
		 * @return string Safe literal percent or an unchanged supported placeholder.
		 */
		$items_wrap = preg_replace_callback( '/%%|%[123]\\$s|%/', static function ( array $match ): string { return '%' === $match[0] ? '%%' : $match[0]; }, wp_kses_post( $atts['items_wrap'] ) );
		$nav_menu = wp_nav_menu(
			[
				'echo'            => FALSE,
				'menu'            => $atts[ 'menu' ],
				'container'       => $container,
				'container_class' => $atts[ 'container_class' ],
				'container_id'    => sanitize_html_class( $atts[ 'container_id' ] ),
				'menu_class'      => $atts[ 'menu_class' ],
				'menu_id'         => $atts[ 'menu_id' ],
				'fallback_cb'     => $fallback,
				'before'          => wp_kses_post( $atts[ 'item_before' ] ),
				'after'           => wp_kses_post( $atts[ 'item_after' ] ),
				'link_before'     => wp_kses_post( $atts[ 'link_before' ] ),
				'link_after'      => wp_kses_post( $atts[ 'link_after' ] ),
				'depth'           => (int) $atts[ 'depth' ],
				'walker'          => $walker,
				'theme_location'  => $atts[ 'theme_location' ],
				'items_wrap'      => $items_wrap,
				'item_spacing'    => sanitize_key( $atts[ 'item_spacing' ] ),
			]
		);

		$output = sprintf(
			'<%1$s class="bse-nav-menu%2$s">%3$s%4$s%5$s</%1$s>',
			ddw_bse_wrapper_tag( $atts[ 'wrapper' ] ),
			! empty( $atts[ 'class' ] ) ? ' ' . esc_attr( ddw_bse_sanitize_html_classes( $atts[ 'class' ], 'string' ) ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html( $atts[ 'before' ] ) . ' ' : '',
			$nav_menu,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html( $atts[ 'after' ] ) : ''
		);

		/**
		 * Filter shortcode output; callbacks are trusted developer code.
		 * @since 1.0.0
		 * @param string $output HTML/text value. Return the replacement output.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters(
			'bse/filter/shortcode/nav_menu',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_item_content' ) ) :

	ddw_bse_register_shortcode( 'bse-item-content', 'ddw_bse_shortcode_item_content' );
	/**
	 * Render readable native or supported builder content with recursion protection.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @param string|null $content Optional enclosed text; unused by this callback.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_item_content( $atts, $content = null ) {

		/**
		 * Filter shortcode defaults/item content while preserving its value contract.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/item_content',
			[
				'id'  => '',
				'css' => 'false',
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-item-content' );

		return ddw_bse_render_item_content( absint( $atts['id'] ), ddw_bse_boolean( $atts['css'] ) );

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_comment_form' ) ) :

	ddw_bse_register_shortcode( 'bse-comment-form', 'ddw_bse_shortcode_comment_form' );
	/**
	 * Capture the native comment form for a readable content item.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @param string|null $content Optional enclosed text; unused by this callback.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_comment_form( $atts, $content = null ) {

		/**
		 * Filter shortcode defaults/comment form while preserving its value contract.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/comment_form',
			[
				/** For the wrapper */
				'before'            => '',
				'after'             => '',
				'class'             => '',
				'wrapper'           => 'div',

				/** For the comment form */
				'post_id'           => FALSE,
				'id_form'           => 'commentform',
				'class_form'        => 'comment-form',
				'title_reply'       => _x( 'Leave a Reply', 'Label for Comment Form', 'builder-shortcode-extras' ),
				/* translators: %s - name of the author of the comment being replied to */
				'title_reply_to'    => _x( 'Leave a Reply to %s', 'Label for Comment Form', 'builder-shortcode-extras' ),
				'cancel_reply_link' => _x( 'Cancel reply', 'Label for Comment Form', 'builder-shortcode-extras' ),
				'label_submit'      => _x( 'Post Comment', 'Label for Comment Form', 'builder-shortcode-extras' ),
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-comment-form' );

		$post_id = ( empty( $atts[ 'post_id' ] ) ) ? get_the_ID() : absint( $atts[ 'post_id' ] );

		if ( ! ddw_bse_readable_item( $post_id ) ) { return ''; }

		$args = [
			'id_form'           => sanitize_html_class( $atts[ 'id_form' ], 'commentform' ),
			'class_form'        => sanitize_html_class( $atts[ 'class_form' ], 'comment-form' ),
			'title_reply'       => esc_html( $atts[ 'title_reply' ] ),
			'title_reply_to'    => esc_html( $atts[ 'title_reply_to' ] ),
			'cancel_reply_link' => esc_html( $atts[ 'cancel_reply_link' ] ),
			'label_submit'      => esc_html( $atts[ 'label_submit' ] ),
		];

		ob_start();
		comment_form( $args, $post_id );
		$comment_form = ob_get_clean();

		$output = sprintf(
			'<%1$s class="bse-comment-form%2$s">%3$s%4$s%5$s</%1$s>',
			ddw_bse_wrapper_tag( $atts[ 'wrapper' ] ),
			! empty( $atts[ 'class' ] ) ? ' ' . esc_attr( ddw_bse_sanitize_html_classes( $atts[ 'class' ], 'string' ) ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html( $atts[ 'before' ] ) . ' ' : '',
			$comment_form,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html( $atts[ 'after' ] ) : ''
		);

		/**
		 * Filter shortcode output; callbacks are trusted developer code.
		 * @since 1.0.0
		 * @param string $output HTML/text value. Return the replacement output.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters(
			'bse/filter/shortcode/comment_form',
			$output,
			$atts 		// additional param
		);

	}   // end function

endif;
