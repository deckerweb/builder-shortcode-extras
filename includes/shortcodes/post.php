<?php

// includes/shortcodes/post

/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


if ( ! function_exists( 'ddw_bse_shortcode_post_count' ) ) :

	add_shortcode( 'bse-post-count', 'ddw_bse_shortcode_post_count' );
	/**
	 * Shortcode to output the current number of all items of a given post type
	 *   for a given post status.
	 *
	 * @since 1.0.0
	 *
	 * @uses wp_count_posts()
	 *
	 * @param array  $atts Array of Shortcode attributes.
	 * @param string $content Content of Shortcode.
	 * @return string Filterable text string of user's ID.
	 */
	function ddw_bse_shortcode_post_count( $atts, $content ) {

		/** Set default shortcode attributes */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/post_count',
			[
				'post_type' => 'post',
				'status'    => 'publish',
				'before'    => '',
				'after'     => '',
				'class'     => '',
				'wrapper'   => 'span',
			]
		);

		/** Default shortcode attributes */
		$atts = shortcode_atts( $defaults, $atts, 'bse-post-count' );

		$post_type   = sanitize_key( $atts[ 'post_type' ] );
		$post_status = sanitize_key( $atts[ 'status' ] );

		$post_count = wp_count_posts( $post_type );

		$post_count_result = $post_count->$post_status;

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-post-count%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			absint( $post_count_result ),
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/post_count',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_date' ) ) :

	add_shortcode( 'bse-post-date', 'ddw_bse_shortcode_post_date' );
	/**
	 * Shortcode to output the date of post publication (or for any post type).
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @uses ddw_bse_human_time_diff()
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `post_date` shortcode.
	 */
	function ddw_bse_shortcode_post_date( $atts ) {

		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/post_date',
			[
				'after'          => '',
				'before'         => '',
				'post_id'        => get_the_ID(),
				'format'         => get_option( 'date_format' ),
				'label'          => '',
				'relative_depth' => 2,
				'class'          => '',
				'wrapper'        => 'span',
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-post-date' );

		$post_id = ( ! is_null( $atts[ 'post_id' ] ) ) ? absint( $atts[ 'post_id' ] ) : null;

		if ( 'relative' === $atts[ 'format' ] ) {

			$display  = ddw_bse_human_time_diff( get_the_time( 'U', $post_id ), current_time( 'timestamp' ), $atts[ 'relative_depth' ] );
			$display .= ' ' . __( 'ago', 'builder-shortcode-extras' );

		} else {

			$display = get_the_time( $atts[ 'format' ] );

		}  // end if

		$date_string = sprintf(
			'<time %s>%s</time>',
			ddw_bse_attr( 'entry-time' ),
			$atts[ 'label' ] . $display
		);

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-post-time%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			$date_string,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/post_date',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_time' ) ) :

	add_shortcode( 'bse-post-time', 'ddw_bse_shortcode_post_time' );
	/**
	 * Shortcode to output the time of post publication (or for any post type).
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `post_time` shortcode`.
	 */
	function ddw_bse_shortcode_post_time( $atts ) {

		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/post_time',
			[
				'after'   => '',
				'before'  => '',
				'post_id' => get_the_ID(),
				'format'  => get_option( 'time_format' ),
				'label'   => '',
				'class'   => '',
				'wrapper' => 'span',
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-post-time' );

		$post_id = ( ! is_null( $atts[ 'post_id' ] ) ) ? absint( $atts[ 'post_id' ] ) : null;

		$time_string = sprintf(
			'<time %s>%s</time>',
			ddw_bse_attr( 'entry-time' ),
			$atts[ 'label' ] . get_the_time( $atts[ 'format' ], $post_id )
		);

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-post-time%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			$time_string,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/post_time',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_modified_date' ) ) :

	add_shortcode( 'bse-post-modified-date', 'ddw_bse_shortcode_post_modified_date' );
	/**
	 * Shortcode to output the post last modified date (or for any post type).
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @uses ddw_bse_human_time_diff()
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `post_modified_date` shortcode.
	 */
	function ddw_bse_shortcode_post_modified_date( $atts ) {

		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/post_modified_date',
			[
				'after'          => '',
				'before'         => '',
				'post_id'        => get_the_ID(),
				'format'         => get_option( 'date_format' ),
				'label'          => '',
				'relative_depth' => 2,
				'class'          => '',
				'wrapper'        => 'span',
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-post-modified-date' );

		$post_id = ( ! is_null( $atts[ 'post_id' ] ) ) ? absint( $atts[ 'post_id' ] ) : null;

		if ( 'relative' === $atts[ 'format' ] ) {

			$display  = ddw_bse_human_time_diff( get_the_modified_time( 'U', $post_id ), current_time( 'timestamp' ), $atts[ 'relative_depth' ] );
			$display .= ' ' . __( 'ago', 'builder-shortcode-extras' );

		} else {

			$display = get_the_modified_time( $atts[ 'format' ], $post_id );

		}  // end if

		$date_string = sprintf(
			'<time %s>%s</time>',
			ddw_bse_attr( 'entry-modified-time' ),
			$atts[ 'label' ] . $display
		);

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-post-modified-date%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			$date_string,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/post_modified_date',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_item_last_updated' ) ) :

	add_shortcode( 'bse-item-last-updated', 'ddw_bse_shortcode_item_last_updated' );
	/**
	 * Shortcode to output the date when a post type item was last updated.
	 *
	 * @since 1.0.0
	 *
	 * @uses ddw_bse_shortcode_post_modified_date()
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `bse-item-last-updated` shortcode.
	 */
	function ddw_bse_shortcode_item_last_updated( $atts ) {

		return ddw_bse_shortcode_post_modified_date( $atts );

	}  // end function
	
endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_modified_time' ) ) :

	add_shortcode( 'bse-post-modified-time', 'ddw_bse_shortcode_post_modified_time' );
	/**
	 * Shortcode to output the post last modified time (or for any post type).
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `bse-post-modified-time` shortcode.
	 */
	function ddw_bse_shortcode_post_modified_time( $atts ) {

		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/post_modified_time',
			[
				'after'   => '',
				'before'  => '',
				'post_id' => get_the_ID(),
				'format'  => get_option( 'time_format' ),
				'label'   => '',
				'class'   => '',
				'wrapper' => 'span',
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-post-modified-time' );

		$post_id = ( ! is_null( $atts[ 'post_id' ] ) ) ? absint( $atts[ 'post_id' ] ) : null;

		$time_string = sprintf(
			'<time %s>%s</time>',
			ddw_bse_attr( 'entry-modified-time' ),
			$atts[ 'label' ] . get_the_modified_time( $atts[ 'format' ], $post_id )
		);

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-post-modified-time%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			$time_string,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/post_modified_time',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_author' ) ) :

	add_shortcode( 'bse-post-author', 'ddw_bse_shortcode_post_author' );
	/**
	 * Shortcode to output the author of the post/ post type item (unlinked
	 *   display name).
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @uses ddw_bse_attr()
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Return empty string if post type does not support `author`, or has no author assigned.
	 *                Otherwise, output for `bse-post-author` shortcode.
	 */
	function ddw_bse_shortcode_post_author( $atts ) {

		/** Bail early if post type supports no author */
		if ( ! post_type_supports( get_post_type(), 'author' ) ) {
			return '';
		}

		$author = get_the_author();

		/** Bail early if there is no author */
		if ( ! $author ) {
			return '';
		}

		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/post_author',
			[
				'after'   => '',
				'before'  => '',
				'class'   => '',
				'wrapper' => 'span',
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-post-author' );

		/** Author string */
		$author_string = sprintf(
			'<span %s>%s</span>',
			ddw_bse_attr( 'entry-author-name' ),
			esc_html( $author )
		);

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="entry-author bse-post-author%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			$author_string,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/post_author',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_author_link' ) ) :

	add_shortcode( 'bse-post-author-link', 'ddw_bse_shortcode_post_author_link' );
	/**
	 * Shortcode to output the author of the post/ post type item (link to
	 *   author URL).
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @uses ddw_bse_shortcode_post_author()
	 * @uses ddw_bse_attr()
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Return empty string if post type does not support `author` or post has no author assigned.
	 *                Return `genesis_post_author_shortcode()` if author has no URL.
	 *                Otherwise, output for `post_author_link` shortcode.
	 */
	function ddw_bse_shortcode_post_author_link( $atts ) {

		/** Bail early if post type supports no author */
		if ( ! post_type_supports( get_post_type(), 'author' ) ) {
			return '';
		}

		$url = get_the_author_meta( 'url' );

		/** If no URL, use post author shortcode function. */
		if ( ! $url ) {
			return ddw_bse_shortcode_post_author( $atts );
		}

		$author = get_the_author();

		/** Bail early if there is no author */
		if ( ! $author ) {
			return '';
		}

		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/post_author_link',
			[
				'after'   => '',
				'before'  => '',
				'target'  => '',
				'rel'     => '',
				'class'   => '',
				'wrapper' => 'span',
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-post-author-link' );

		$target = sprintf(
			'target="%s"',
			sanitize_key( $atts[ 'target' ] )
		);

		$rel = sprintf(
			'rel="%s"',
			strtolower( esc_attr( $atts[ 'rel' ] ) )
		);

		/** Author link */
		$author_link = sprintf(
			'<a href="%1$s" %2$s %3$s %4$s><span %5$s>%6$s</span></a>',
			esc_url( $url ),
			( ! empty( $atts[ 'target' ] ) ) ? $target : '',
			( ! empty( $atts[ 'target' ] ) && ! empty( $atts[ 'rel' ] ) ) ? $rel : '',
			ddw_bse_attr( 'entry-author-link' ),
			ddw_bse_attr( 'entry-author-name' ),
			esc_html( $author )
		);

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="entry-author bse-post-author-link%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			( ! empty( $atts[ 'class' ] ) ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			$author_link,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/post_author_link',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_author_posts_link' ) ) :

	add_shortcode( 'bse-post-author-posts-link', 'ddw_bse_shortcode_post_author_posts_link' );
	/**
	 * Shortcode to output the author of the post/ post type item (link to
	 *   author archive).
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @uses ddw_bse_attr()
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Return empty string if post type does not support `author` or post has no author assigned.
	 *                Otherwise, output for `post_author_posts_link` shortcode.
	 */
	function ddw_bse_shortcode_post_author_posts_link( $atts ) {

		/** Bail early if post type supports no author */
		if ( ! post_type_supports( get_post_type(), 'author' ) ) {
			return '';
		}

		$author = get_the_author();

		/** Bail early if there is no author */
		if ( ! $author ) {
			return '';
		}

		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/post_author_posts_link',
			[
				'after'   => '',
				'before'  => '',
				'target'  => '',
				'rel'     => '',
				'class'   => '',
				'wrapper' => 'span',
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'post_author_posts_link' );

		$url = get_author_posts_url( get_the_author_meta( 'ID' ) );

		$target = sprintf(
			'target="%s"',
			sanitize_key( $atts[ 'target' ] )
		);

		$rel = sprintf(
			'rel="%s"',
			strtolower( esc_attr( $atts[ 'rel' ] ) )
		);

		/** Author link */
		$author_link = sprintf(
			'<a href="%1$s" %2$s %3$s %4$s><span %5$s>%6$s</span></a>',
			esc_url( $url ),
			( ! empty( $atts[ 'target' ] ) ) ? $target : '',
			( ! empty( $atts[ 'target' ] ) && ! empty( $atts[ 'rel' ] ) ) ? $rel : '',
			ddw_bse_attr( 'entry-author-link' ),
			ddw_bse_attr( 'entry-author-name' ),
			esc_html( $author )
		);

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="entry-author bse-post-author-posts-link%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			$author_link,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/post_author_posts_link',
			$output,
			$atts
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_tags' ) ) :

	add_shortcode( 'bse-post-tags', 'ddw_bse_shortcode_post_tags' );
	/**
	 * Shortcode to output the tag links list (Post Tags).
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @uses ddw_bse_attr()
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Return empty string if the `post_tag` taxonomy is not associated with the current post type
	 *                or if the post has no tags. Otherwise, output for `post_tags` shortcode.
	 */
	function ddw_bse_shortcode_post_tags( $atts ) {

		if ( ! is_object_in_taxonomy( get_post_type(), 'post_tag' ) ) {
			return '';
		}

		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/post_tags',
			[
				'after'   => '',
				'before'  => __( 'Tagged With:', 'builder-shortcode-extras' ),
				'sep'     => ', ',
				'class'   => '',
				'wrapper' => 'span',
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-post-tags' );

		$tags = get_the_tag_list(
			$atts[ 'before' ] . ' ',
			trim( $atts[ 'sep' ] ) . ' ',
			' ' . $atts[ 'after' ]
		);

		/** Do nothing if no tags. */
		if ( ! $tags ) {
			return '';
		}

		//$output = sprintf( '<span %s>', ddw_bse_attr( 'entry-tags' ) ) . $tags . '</span>';

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="entry-tags bse-post-tags%2$s">%3$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			$tags
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/post_tags',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_categories' ) ) :

	add_shortcode( 'bse-post-categories', 'ddw_bse_shortcode_post_categories' );
	/**
	 * Shortcode to output the category links list (Post Categories).
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @uses ddw_bse_attr()
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Return empty string if the `category` taxonomy is not associated with the current post type
	 *                or if the post has no categories. Otherwise, output for `post_categories` shortcode.
	 */
	function ddw_bse_shortcode_post_categories( $atts ) {

		if ( ! is_object_in_taxonomy( get_post_type(), 'category' ) ) {
			return '';
		}

		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/post_categories',
			[
				'sep'    => ', ',
				'before'  => __( 'Filed Under:', 'builder-shortcode-extras' ),
				'after'   => '',
				'class'   => '',
				'wrapper' => 'span',
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-post-categories' );

		$cats = get_the_category_list( trim( $atts[ 'sep' ] ) . ' ' );

		/** Do nothing if there are no categories. */
		if ( ! $cats ) {
			return '';
		}

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="entry-categories bse-post-categories%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			$cats,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/post_categories',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_terms' ) ) :

	add_shortcode( 'bse-post-terms', 'ddw_bse_shortcode_post_terms' );
	/**
	 * Shortcode to output the linked post taxonomy terms list (for any (custom)
	 *   taxonomy).
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `post_terms` shortcode, or empty string on failure to retrieve terms.
	 */
	function ddw_bse_shortcode_post_terms( $atts ) {

		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/post_terms',
			[
				'after'    => '',
				'before'   => __( 'Filed Under:', 'builder-shortcode-extras' ),
				'sep'      => ', ',
				'taxonomy' => 'category',
				'class'    => '',
				'wrapper'  => 'span',
			]
		);

		/**
		 * Post terms shortcode defaults. Allows the default args in the post
		 *   terms shortcode function to be filtered.
		 *
		 * @since 1.0.0
		 *
		 * @param array $defaults The default args array.
		 */
		$defaults = apply_filters( 'bse/filter/shortcode_defaults/post_terms', $defaults );

		$atts = shortcode_atts( $defaults, $atts, 'bse-post-terms' );

		$terms = get_the_term_list(
			get_the_ID(),
			$atts[ 'taxonomy' ],
			$atts[ 'before' ] . ' ',
			trim( $atts[ 'sep' ] ) . ' ',
			$atts[ 'after' ]
		);

		/** Do nothing if WP_Error object is true */
		if ( is_wp_error( $terms ) ) {
				return '';
		}

		/** Do nothing if terms list is empty */
		if ( empty( $terms ) ) {
				return '';
		}

		/*
		$output = sprintf(
			'<span %s>%s</span>',
			ddw_bse_attr( 'entry-terms' ),
			$terms
		);
		*/

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="entry-terms bse-post-terms%2$s">%3$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			$terms
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/post_terms',
			$output,
			$terms, 		// additional param
			$atts 			// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_edit' ) ) :

	add_shortcode( 'bse-post-edit', 'ddw_bse_shortcode_post_edit' );
	/**
	 * Shortcode to output the edit post link for logged in users.
	 *
	 * Function code based on: Genesis Framework by StudioPress (studiopress.com)
	 * @link    https://toolbarextras.com/go/genesis/
	 * @license GPLv2-or-later
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
	 * @return string Output for `post_edit` shortcode, or empty string if `genesis_edit_post_link` filter returns `false`.
	 */
	function ddw_bse_shortcode_post_edit( $atts ) {

		/** Bail early if deactivated via our filter */
		if ( ! apply_filters( 'bse/filter/edit_post_link', TRUE ) ) {
			return '';
		}

		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/post_edit',
			[
				'after'   => '',
				'before'  => '',
				'label'   => __( '(Edit)', 'builder-shortcode-extras' ),
				'class'   => '',
				'wrapper' => 'span',
			]
		);

		$atts = shortcode_atts( $defaults, $atts, 'bse-post-edit' );

		/** Create the Edit link via Output Buffering (we have no chance...) */
		ob_start();
		edit_post_link( $atts[ 'label' ], $atts[ 'before' ], $atts[ 'after' ] );
		$edit = ob_get_clean();

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-post-edit%2$s">%3$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			$edit
		);

		//$output = $edit;

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/post_edit',
			$output,
			$atts 			// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_link' ) ) :

	add_shortcode( 'bse-post-link', 'ddw_bse_shortcode_post_link' );
	/**
	 * Shortcode to output the current number of all items of a given post type
	 *   for a given post status.
	 *
	 * @since 1.0.0
	 *
	 * @uses ddw_bse_get_post_id_by_slug()
	 *
	 * @param array  $atts Array of Shortcode attributes.
	 * @param string $content Content of Shortcode.
	 * @return string Filterable text string of user's ID.
	 */
	function ddw_bse_shortcode_post_link( $atts, $content ) {

		/** Set default shortcode attributes */
		$defaults = apply_filters(
			'bse/filter/shortcode_defaults/post_link',
			[
				'id'        => '',
				'slug'      => '',
				'post_type' => '',
				'privacy'   => '',
				'text'      => '',
				'target'    => '',
				'rel'       => '',
				'before'    => '',
				'after'     => '',
				'class'     => '',
				'wrapper'   => 'span',
			]
		);

		/** Default shortcode attributes */
		$atts = shortcode_atts( $defaults, $atts, 'bse-post-link' );

		$target = sprintf(
			'target="%s"',
			sanitize_key( $atts[ 'target' ] )
		);

		$rel = sprintf(
			'rel="%s"',
			strtolower( esc_attr( $atts[ 'rel' ] ) )
		);

		$bool_array = array( 'yes', '1', 'true' );
		$privacy    = ( in_array( strtolower( $atts[ 'privacy' ] ), $bool_array ) ) ? get_option( 'wp_page_for_privacy_policy' ) : '';
		$post_id    = ( '' !== $atts[ 'privacy' ] && '' !== $privacy ) ? absint( $privacy ) : absint( $atts[ 'id' ] );
		$permalink  = null;
		$title      = null;

		if ( '' !== $post_id ) {

			$permalink = get_permalink( $post_id );
			$title     = get_the_title( $post_id );

		} elseif ( '' === $post_id && '' !== $atts[ 'slug' ] ) {

			$permalink = get_permalink( ddw_bse_get_post_id_by_slug( $atts[ 'slug' ], $atts[ 'post_type' ] ) );
			$title     = get_the_title( ddw_bse_get_post_id_by_slug( $atts[ 'slug' ], $atts[ 'post_type' ] ) );

		}  // end if

		$post_link = sprintf(
			'<a href="%1$s" %2$s %3$s>%4$s</a>',
			esc_url( $permalink ),
			( ! empty( $atts[ 'target' ] ) ) ? $target : '',
			( ! empty( $atts[ 'target' ] ) && ! empty( $atts[ 'rel' ] ) ) ? $rel : '',
			( empty( $atts[ 'text' ] ) ) ? $title : $atts[ 'text' ]
		);

		/** Prepare output */
		$output = sprintf(
			'<%1$s class="bse-post-link%2$s">%3$s%4$s%5$s</%1$s>',
			strtolower( sanitize_html_class( $atts[ 'wrapper' ] ) ),
			! empty( $atts[ 'class' ] ) ? ' ' . sanitize_html_class( $atts[ 'class' ] ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html__( $atts[ 'before' ] ) . ' ' : '',
			$post_link,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html__( $atts[ 'after' ] ) : ''
		);

		/** Return the output - filterable */
		return apply_filters(
			'bse/filter/shortcode/post_link',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;
