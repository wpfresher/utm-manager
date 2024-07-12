<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Get lead.
 *
 * @param mixed $data The data.
 *
 * @since 1.0.0
 * @return WP_Post|false The lead object, or false if not found.
 */
function utmm_get_lead( $data ) {

	if ( is_numeric( $data ) ) {
		$data = get_post( $data );
	}

	if ( $data instanceof WP_Post && 'utmm_lead' === $data->post_type ) {
		return $data;
	}

	return false;
}

/**
 * Get leads.
 *
 * @param array $args The args.
 * @param bool  $count Whether to return a count.
 *
 * @since 1.0.0
 * @return array|int The leads.
 */
function utmm_get_leads( $args = array(), $count = false ) {
	$defaults = array(
		'post_type'      => 'utmm_lead',
		'posts_per_page' => - 1,
		'orderby'        => 'date',
		'order'          => 'ASC',
	);

	$args  = wp_parse_args( $args, $defaults );
	$query = new WP_Query( $args );

	if ( $count ) {
		return $query->found_posts;
	}

	return array_map( 'utmm_get_lead', $query->posts );
}

/**
 * Get the post by title.
 *
 * @param string $post_title The post title.
 *
 * @since 1.0.0
 * @return string|null
 */
function utmm_get_post_by_title( $post_title ) {

	$query = new WP_Query(
		array(
			'title'          => $post_title,
			'post_type'      => 'utmm_lead',
			'posts_per_page' => 1,
		)
	);

	$post = $query->post;

	if ( $post instanceof WP_Post && 'utmm_lead' === $post->post_type ) {
		return $post->ID;
	}

	return null;
}
