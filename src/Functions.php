<?php
/**
 * Usefully functions.
 *
 * @since 1.0.0
 * @package UTMManager
 */

defined( 'ABSPATH' ) || exit;

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
		'orderby'        => 'title',
		'order'          => 'ASC',
	);
	$args     = wp_parse_args( $args, $defaults );

	var_dump( $args );
	// wp_die();

	$query = new WP_Query( $args );

	if ( $count ) {
		return $query->found_posts;
	}

	return array_map( 'utmm_get_lead', $query->posts );
}
