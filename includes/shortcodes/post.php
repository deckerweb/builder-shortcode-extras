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

	ddw_bse_register_shortcode( 'bse-post-count', 'ddw_bse_shortcode_post_count' );
	/**
	 * Render a count for the selected post type and status.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @param string|null $content Optional enclosed text; unused by this callback.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_post_count( $atts, $content ) {

		/**
		 * Filter shortcode defaults/post count while preserving its value contract.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
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

		$atts = shortcode_atts( $defaults, $atts, 'bse-post-count' );

		$post_type   = sanitize_key( $atts[ 'post_type' ] );
		$post_status = sanitize_key( $atts[ 'status' ] );

		$post_count = wp_count_posts( $post_type );

		$post_count_result = $post_count->$post_status ?? 0;

		$output = sprintf(
			'<%1$s class="bse-post-count%2$s">%3$s%4$s%5$s</%1$s>',
			ddw_bse_wrapper_tag( $atts[ 'wrapper' ] ),
			! empty( $atts[ 'class' ] ) ? ' ' . esc_attr( ddw_bse_sanitize_html_classes( $atts[ 'class' ], 'string' ) ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html( $atts[ 'before' ] ) . ' ' : '',
			absint( $post_count_result ),
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html( $atts[ 'after' ] ) : ''
		);

		/**
		 * Filter shortcode output; callbacks are trusted developer code.
		 * @since 1.0.0
		 * @param string $output HTML/text value. Return the replacement output.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters(
			'bse/filter/shortcode/post_count',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_date' ) ) :

	ddw_bse_register_shortcode( 'bse-post-date', 'ddw_bse_shortcode_post_date' );
	/**
	 * Render the publication date of a readable content item.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_post_date( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
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
		$item = ddw_bse_readable_item( absint( $atts['post_id'] ) );
		if ( ! $item ) {
			return '';
		}
		if ( $atts['format'] === 'relative' ) {
			$timestamp = get_post_timestamp( $item, 'date' );
			if ( $timestamp === false ) {
				return '';
			}
			$text = ddw_bse_human_time_diff( $timestamp, time(), $atts['relative_depth'] ) . ' ' . __( 'ago', 'builder-shortcode-extras' );
		} else {
			$text = get_the_time( $atts['format'], $item );
		}
		$html = '<time ' . ddw_bse_attr( 'entry-time' ) . '>' . esc_html( $atts['label'] . $text ) . '</time>';
		$output = ddw_bse_metadata_wrapper( $atts, 'bse-post-time', $html, true );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/post_date', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_time' ) ) :

	ddw_bse_register_shortcode( 'bse-post-time', 'ddw_bse_shortcode_post_time' );
	/**
	 * Render the publication time of a readable content item.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_post_time( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
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
		$item = ddw_bse_readable_item( absint( $atts['post_id'] ) );
		if ( ! $item ) {
			return '';
		}
		$text = get_the_time( $atts['format'], $item );
		$html = '<time ' . ddw_bse_attr( 'entry-time' ) . '>' . esc_html( $atts['label'] . $text ) . '</time>';
		$output = ddw_bse_metadata_wrapper( $atts, 'bse-post-time', $html, true );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/post_time', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_modified_date' ) ) :

	ddw_bse_register_shortcode( 'bse-post-modified-date', 'ddw_bse_shortcode_post_modified_date' );
	/**
	 * Render the modification date of a readable content item.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_post_modified_date( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
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
		$item = ddw_bse_readable_item( absint( $atts['post_id'] ) );
		if ( ! $item ) {
			return '';
		}
		if ( $atts['format'] === 'relative' ) {
			$timestamp = get_post_timestamp( $item, 'modified' );
			if ( $timestamp === false ) {
				return '';
			}
			$text = ddw_bse_human_time_diff( $timestamp, time(), $atts['relative_depth'] ) . ' ' . __( 'ago', 'builder-shortcode-extras' );
		} else {
			$text = get_the_modified_time( $atts['format'], $item );
		}
		$html = '<time ' . ddw_bse_attr( 'entry-modified-time' ) . '>' . esc_html( $atts['label'] . $text ) . '</time>';
		$output = ddw_bse_metadata_wrapper( $atts, 'bse-post-modified-date', $html, true );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/post_modified_date', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_item_last_updated' ) ) :

	ddw_bse_register_shortcode( 'bse-item-last-updated', 'ddw_bse_shortcode_item_last_updated' );
	/**
	 * Delegate the existing last-updated alias to the modification-date helper.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_item_last_updated( $atts ) {

		return ddw_bse_shortcode_post_modified_date( $atts );

	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_modified_time' ) ) :

	ddw_bse_register_shortcode( 'bse-post-modified-time', 'ddw_bse_shortcode_post_modified_time' );
	/**
	 * Render the modification time of a readable content item.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_post_modified_time( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
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
		$item = ddw_bse_readable_item( absint( $atts['post_id'] ) );
		if ( ! $item ) {
			return '';
		}
		$text = get_the_modified_time( $atts['format'], $item );
		$html = '<time ' . ddw_bse_attr( 'entry-modified-time' ) . '>' . esc_html( $atts['label'] . $text ) . '</time>';
		$output = ddw_bse_metadata_wrapper( $atts, 'bse-post-modified-time', $html, true );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/post_modified_time', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_author' ) ) :

	ddw_bse_register_shortcode( 'bse-post-author', 'ddw_bse_shortcode_post_author' );
	/**
	 * Render the current post author’s display name.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_post_author( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
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
		if ( ! post_type_supports( get_post_type(), 'author' ) || ! get_the_author() ) {
			return '';
		}
		$html = '<span ' . ddw_bse_attr( 'entry-author-name' ) . '>' . esc_html( get_the_author() ) . '</span>';
		$output = ddw_bse_metadata_wrapper( $atts, 'entry-author bse-post-author', $html, true );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/post_author', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_author_link' ) ) :

	ddw_bse_register_shortcode( 'bse-post-author-link', 'ddw_bse_shortcode_post_author_link' );
	/**
	 * Link the current author’s display name to their website, or show the name alone.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_post_author_link( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
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
		if ( ! post_type_supports( get_post_type(), 'author' ) || ! get_the_author() ) {
			return '';
		}
		$html = '<span ' . ddw_bse_attr( 'entry-author-name' ) . '>' . esc_html( get_the_author() ) . '</span>';
		$url = get_the_author_meta( 'url' );
		if ( ! $url ) {
			return ddw_bse_shortcode_post_author( $atts );
		}
		$html = '<a href="' . esc_url( $url ) . '"' . ddw_bse_metadata_link_attributes( $atts ) . ' ' . ddw_bse_attr( 'entry-author-link' ) . '>' . $html . '</a>';
		$output = ddw_bse_metadata_wrapper( $atts, 'entry-author bse-post-author-link', $html, true );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/post_author_link', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_author_posts_link' ) ) :

	ddw_bse_register_shortcode( 'bse-post-author-posts-link', 'ddw_bse_shortcode_post_author_posts_link' );
	/**
	 * Link the current author’s display name to their post archive.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_post_author_posts_link( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
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
		if ( ! post_type_supports( get_post_type(), 'author' ) || ! get_the_author() ) {
			return '';
		}
		$html = '<span ' . ddw_bse_attr( 'entry-author-name' ) . '>' . esc_html( get_the_author() ) . '</span>';
		$url = get_author_posts_url( get_the_author_meta( 'ID' ) );
		$html = '<a href="' . esc_url( $url ) . '"' . ddw_bse_metadata_link_attributes( $atts ) . ' ' . ddw_bse_attr( 'entry-author-link' ) . '>' . $html . '</a>';
		$output = ddw_bse_metadata_wrapper( $atts, 'entry-author bse-post-author-posts-link', $html, true );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/post_author_posts_link', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_tags' ) ) :

	ddw_bse_register_shortcode( 'bse-post-tags', 'ddw_bse_shortcode_post_tags' );
	/**
	 * Render the current post’s linked tags with escaped labels and separators.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_post_tags( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
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
		$taxonomy = 'post_tag';
		if ( ! is_object_in_taxonomy( get_post_type(), $taxonomy ) ) {
			return '';
		}
		$terms = get_the_term_list( get_the_ID(), $taxonomy, esc_html( $atts['before'] ) . ' ', esc_html( $atts['sep'] ), esc_html( $atts['after'] ) );
		$html = $terms;
		if ( is_wp_error( $terms ) || ! $terms ) {
			return '';
		}
		$output = ddw_bse_metadata_wrapper( $atts, 'entry-tags bse-post-tags', $html, false );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/post_tags', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_categories' ) ) :

	ddw_bse_register_shortcode( 'bse-post-categories', 'ddw_bse_shortcode_post_categories' );
	/**
	 * Render the current post’s linked categories with escaped labels and separators.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_post_categories( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
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
		$taxonomy = 'category';
		if ( ! is_object_in_taxonomy( get_post_type(), $taxonomy ) ) {
			return '';
		}
		$terms = get_the_category_list( esc_html( trim( $atts['sep'] ) ) . ' ' );
		$html = esc_html( $atts['before'] ) . ' ' . $terms . ' ' . esc_html( $atts['after'] );
		if ( is_wp_error( $terms ) || ! $terms ) {
			return '';
		}
		$output = ddw_bse_metadata_wrapper( $atts, 'entry-categories bse-post-categories', $html, false );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/post_categories', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_terms' ) ) :

	ddw_bse_register_shortcode( 'bse-post-terms', 'ddw_bse_shortcode_post_terms' );
	/**
	 * Render linked terms from the requested taxonomy for the current post.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_post_terms( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
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
		$atts = shortcode_atts( $defaults, $atts, 'bse-post-terms' );
		$taxonomy = $atts['taxonomy'];
		if ( ! is_object_in_taxonomy( get_post_type(), $taxonomy ) ) {
			return '';
		}
		$terms = get_the_term_list( get_the_ID(), $taxonomy, esc_html( $atts['before'] ) . ' ', esc_html( $atts['sep'] ), esc_html( $atts['after'] ) );
		$html = $terms;
		if ( is_wp_error( $terms ) || ! $terms ) {
			return '';
		}
		$output = ddw_bse_metadata_wrapper( $atts, 'entry-terms bse-post-terms', $html, false );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/post_terms', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_edit' ) ) :

	ddw_bse_register_shortcode( 'bse-post-edit', 'ddw_bse_shortcode_post_edit' );
	/**
	 * Capture WordPress’s edit link when permitted by capabilities and the existing filter.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_post_edit( $atts ) {
		/**
		 * Resolve the existing shortcode default attributes.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
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
		/**
		 * Enable the edit link when the current user can edit the item.
		 * @since 1.0.0
		 * @param bool $enabled Whether the link is enabled.
		 */
		if ( ! apply_filters( 'bse/filter/edit_post_link', true ) ) {
			return '';
		}
		ob_start();
		try {
			edit_post_link( esc_html( $atts['label'] ), esc_html( $atts['before'] ), esc_html( $atts['after'] ) );
			$html = ob_get_contents();
		} finally {
			ob_end_clean();
		}
		$output = ddw_bse_metadata_wrapper( $atts, 'bse-post-edit', $html, false );
		/**
		 * Filter rendered shortcode output for trusted PHP integrations.
		 * @since 1.0.0
		 * @param string $output Rendered markup. Return replacement markup.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters( 'bse/filter/shortcode/post_edit', $output, $atts );
	}  // end function

endif;


if ( ! function_exists( 'ddw_bse_shortcode_post_link' ) ) :

	ddw_bse_register_shortcode( 'bse-post-link', 'ddw_bse_shortcode_post_link' );
	/**
	 * Render a link to readable content selected by ID or published slug.
	 *
	 * @since 1.0.0
	 * @param array|string|null $atts Shortcode attributes; empty string or null when omitted.
	 * @param string|null $content Optional enclosed text; unused by this callback.
	 * @return string Rendered output; empty when unavailable.
	 */
	function ddw_bse_shortcode_post_link( $atts, $content ) {

		/**
		 * Filter shortcode defaults/post link while preserving its value contract.
		 * @since 1.0.0
		 * @param array $defaults Default attribute map. Return the replacement map.
		 */
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

		if ( $post_id > 0 ) {

			if ( ! ddw_bse_readable_item( $post_id ) ) { return ''; }
			$permalink = get_permalink( $post_id );
			$title     = get_the_title( $post_id );

		} elseif ( 0 === $post_id && '' !== $atts[ 'slug' ] ) {

			$resolved_id = ddw_bse_get_post_id_by_slug( $atts[ 'slug' ], $atts[ 'post_type' ] );
			if ( ! $resolved_id ) { return ''; }
			$permalink = get_permalink( $resolved_id );
			$title = get_the_title( $resolved_id );

		}  // end if

		if ( ! $permalink ) {
			return '';
		}

		$post_link = sprintf(
			'<a href="%1$s" %2$s %3$s>%4$s</a>',
			esc_url( $permalink ),
			( ! empty( $atts[ 'target' ] ) ) ? $target : '',
			( ! empty( $atts[ 'target' ] ) && ! empty( $atts[ 'rel' ] ) ) ? $rel : '',
			esc_html( ( empty( $atts[ 'text' ] ) ) ? (string) $title : $atts[ 'text' ] )
		);

		$output = sprintf(
			'<%1$s class="bse-post-link%2$s">%3$s%4$s%5$s</%1$s>',
			ddw_bse_wrapper_tag( $atts[ 'wrapper' ] ),
			! empty( $atts[ 'class' ] ) ? ' ' . esc_attr( ddw_bse_sanitize_html_classes( $atts[ 'class' ], 'string' ) ) : '',
			( ! empty( $atts[ 'before' ] ) ) ? esc_html( $atts[ 'before' ] ) . ' ' : '',
			$post_link,
			( ! empty( $atts[ 'after' ] ) ) ? ' ' . esc_html( $atts[ 'after' ] ) : ''
		);

		/**
		 * Filter shortcode output; callbacks are trusted developer code.
		 * @since 1.0.0
		 * @param string $output HTML/text value. Return the replacement output.
		 * @param array $atts Resolved shortcode attributes.
		 */
		return apply_filters(
			'bse/filter/shortcode/post_link',
			$output,
			$atts 		// additional param
		);

	}  // end function

endif;
