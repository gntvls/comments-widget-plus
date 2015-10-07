<?php
/**
 * Various functions used by the plugin.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Sets up the default arguments.
 */
function cwp_get_default_args() {

	$defaults = array(
		'title'         => esc_attr__( 'Recent Comments', 'comments-widget-plus' ),
		'title_url'     => '',
		'post_type'     => 'post',
		'limit'         => 5,
		'offset'        => '',
		'order'         => 'DESC',
		'avatar'        => 0,
		'avatar_size'   => 55,
		'excerpt'       => 0,
		'excerpt_limit' => 50,
		'css_class'     => '',
	);

	// Allow plugins/themes developer to filter the default arguments.
	return apply_filters( 'cwp_default_args', $defaults );

}

/**
 * Generates the recent comments markup.
 */
function cwp_get_recent_comments( $args = array() ) {

	// Set up a default, empty variable.
	$html = '';

	// Merge the input arguments and the defaults.
	$args = wp_parse_args( $args, cwp_get_default_args() );

	// Extract the array to allow easy use of variables.
	extract( $args );

	// Allow devs to hook in stuff before the recent comments.
	do_action( 'cwp_before_loop' );

	// Recent comments query.
	$comments = cwp_get_comments( $args );

	if ( is_array( $comments ) && $comments ) :

		$html = '<ul class="cwp-ul ' . ( ! empty( $args['css_class'] ) ? '' . sanitize_html_class( $args['css_class'] ) . '' : '' ) . '">';

			foreach( $comments as $comment ) :

				$html .= '<li class="recentcomments cwp-li">';

					if ( $args['avatar'] ) :
						$html .= '<a class="comment-link cwp-comment-link" href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">';
							$html .= '<span class="comment-avatar cwp-avatar">' . get_avatar( $comment->comment_author_email, $args['avatar_size'] ) . '</span>';
						$html .= '</a>';
					endif;

					$html .= '<span class="cwp-comment-title">';
						/* translators: comments widget: 1: comment author, 4: post link */
						$html .= sprintf( _x( '%1$s %2$son%3$s %4$s', 'widgets', 'comments-widget-plus' ),
							'<span class="comment-author-link cwp-author-link">' . get_comment_author_link( $comment->comment_ID ) . '</span>',
							'<span class="cwp-on-text">',
							'</span>',
							'<a class="comment-link cwp-comment-link" href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">' . get_the_title( $comment->comment_post_ID ) . '</a>'
						);
					$html .= '</span>';

					if ( $args['excerpt'] ) :
						$html .= '<span class="comment-excerpt cwp-comment-excerpt">' . wp_html_excerpt( $comment->comment_content, $args['excerpt_limit'], '&hellip;' ) . '</span>';
					endif;

				$html .= '</li>';

			endforeach;

		$html .= '</ul>';

	endif;

	// Allow devs to hook in stuff after the loop.
	do_action( 'cwp_after_loop' );

	// Return the  posts markup.
	return $html;

}

/**
 * The recent comments query.
 */
function cwp_get_comments( $args = array() ) {

	// Arguments
	$query = array(
		'number'      => $args['limit'],
		'offset'      => $args['offset'],
		'order'       => $args['order'],
		'post_status' => 'publish',
		'post_type'   => $args['post_type'],
		'status'      => 'approve',
	);

	// Allow plugins/themes developer to filter the default query.
	$query = apply_filters( 'cwp_default_query_args', $query );

	// Get the comments.
	$comments = get_comments( $query );

	return $comments;

}