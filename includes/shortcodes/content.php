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

	add_shortcode( 'bse-nav-menu', 'ddw_bse_shortcode_nav_menu' );
	/**
	 * Shortcode to output a full nav menu (registered as regular WP Nav Menu)
	 *   by a given menu name/ id/ slug/ object.
	 *
	 * @since 1.0.0
	 *
	 * @uses wp_nav_menu()
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `footer_home_link` shortcode.
	 */
	function ddw_bse_shortcode_nav_menu( $atts, $content = null ) {

		/** Set default shortcode attributes */
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

		/** Default shortcode attributes */
		$atts = shortcode_atts( $defaults, $atts, 'bse-nav-menu' );

		$nav_menu = wp_nav_menu(
			[
				'echo'            => FALSE,
				'menu'            => $atts[ 'menu' ],
				'container'       => sanitize_key( $atts[ 'container' ] ),
				'container_class' => $atts[ 'container_class' ],
				'menu_class'      => $atts[ 'menu_class' ],
				'menu_id'         => $atts[ 'menu_id' ],
				'fallback_cb'     => $atts[ 'fallback_cb' ],
				'before'          => $atts[ 'item_before' ],
				'after'           => $atts[ 'item_after' ],
				'link_before'     => $atts[ 'link_before' ],
				'link_after'      => $atts[ 'link_after' ],
				'depth'           => (int) $atts[ 'depth' ],
				'walker'          => $atts[ 'walker' ],
				'theme_location'  => $atts[ 'theme_location' ],
				'items_wrap'      => $atts[ 'items_wrap' ],
				'item_spacing'    => sanitize_key( $atts[ 'item_spacing' ] ),
			]
		);

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-nav-menu%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			$nav_menu,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/nav_menu',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_item_content' ) ) :

	add_shortcode( 'bse-item-content', 'ddw_bse_shortcode_item_content' );
	/**
	 * Shortcode to output the content of a singular post type item by given ID.
	 *
	 * @since 1.0.0
	 *
	 * @uses ddw_bse_is_elementor_active()
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `footer_home_link` shortcode.
	 */
	function ddw_bse_shortcode_item_content( $atts, $content = null ) {

		/** Set default shortcode attributes */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/item_content',
			[
				'id'  => '',
				'css' => 'false',
			]
		);

		/** Default shortcode attributes */
		$atts = shortcode_atts( $defaults, $atts, 'bse-item-content' );

		/** Start output buffering (we have no chance...) */
		ob_start();
		
		if ( $atts[ 'id' ] ) {

			/** Check if the post type item was created with Elementor */
			$elementor = get_post_meta( $atts[ 'id' ], '_elementor_edit_mode', TRUE );

			$include_css = false;

			if ( isset( $atts[ 'css' ] ) && 'false' !== $atts[ 'css' ] ) {
				$include_css = (bool) $atts[ 'css' ];
			}

		    /** Case: Elementor */
		    if ( ddw_bse_is_elementor_active() && $elementor ) {

		        echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $atts[ 'id' ], $include_css );

		    }

		    /** Case: Beaver Builder */
		    elseif ( class_exists( 'FLBuilder' ) && ! empty( $atts[ 'id' ] ) ) {

		        echo do_shortcode( '[fl_builder_insert_layout id="' . $atts[ 'id' ] . '"]' );

		    }

		    /** Else: Embed the regular post item content */
		    else {

		    	/** Get item content */
		    	$content = $atts[ 'id' ];

				if ( ! empty( $content ) ) {

					$template = get_post( $content );

					if ( $template && ! is_wp_error( $template ) ) {
						$content = $template->post_content;
					}

				}

		        /** Display item content */
		        echo do_shortcode( $content );

		    }  // end if

		}  // end if
		
		/** Return the output - from the WordPress output buffer */
		return ob_get_clean();

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_comment_form' ) ) :

	add_shortcode( 'bse-comment-form', 'ddw_bse_shortcode_comment_form' );
	/**
	 * Shortcode to output a comment form for a specific Post ID (or the current
	 *   post).
	 *
	 * @since 1.0.0
	 *
	 * @uses comment_form()
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `footer_home_link` shortcode.
	 */
	function ddw_bse_shortcode_comment_form( $atts, $content = null ) {

		/** Set default shortcode attributes */
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

		/** Default shortcode attributes */
		$atts = shortcode_atts( $defaults, $atts, 'bse-comment-form' );

		$post_id = ( empty( $atts[ 'post_id' ] ) ) ? get_the_ID() : absint( $atts[ 'post_id' ] );

		$args = [
			'id_form'           => sanitize_html_class( $atts[ 'id_form' ], 'commentform' ),
			'class_form'        => sanitize_html_class( $atts[ 'class_form' ], 'comment-form' ),
			'title_reply'       => $atts[ 'title_reply' ],
			'title_reply_to'    => $atts[ 'title_reply_to' ],
			'cancel_reply_link' => $atts[ 'cancel_reply_link' ],
			'label_submit'      => $atts[ 'label_submit' ],
		];

		ob_start();
		comment_form( $args, $post_id );
		$comment_form = ob_get_clean();		//ob_get_contents();
		//ob_end_clean();

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-comment-form%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			$comment_form,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/comment_form',
			$output,
			$atts 		// additional param
		);

	}   // end function

endif;
