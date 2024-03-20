<?php
/**
 * Usefully functions.
 *
 * @since 1.0.0
 * @package UTMManager
 */

use UTMManager\Models\Lead;

defined( 'ABSPATH' ) || exit;

/**
 * Get lead.
 *
 * @param mixed $data The data.
 *
 * @since 1.0.0
 * @return Lead|false The lead, or false if not found.
 */
function utmm_get_lead( $data ) {
	if ( $data instanceof Lead ) {
		return $data;
	}

	if ( is_numeric( $data ) ) {
		$data = get_post( $data );
	}

	if ( $data instanceof WP_Post && 'utmm_lead' === $data->post_type ) {
		return new Lead( $data );
	}

	return false;
}

/**
 * Insert lead.
 *
 * @param array $data The data.
 * @param bool  $wp_error Optional. Whether to return a WP_Error object on failure. Default false.
 *
 * @since 1.0.0
 * @return Lead|WP_Error|false The lead object on success, WP_Error on failure. False if $wp_error is set to false.
 */
function utmm_create_lead( $data, $wp_error = true ) {
	$defaults = array(
		'ID' => 0,
	);
	$data     = wp_parse_args( $data, $defaults );
	$lead    = new Lead( $data['ID'] );
	$lead->set_data( $data );
	$saved = $lead->save();

	if ( is_wp_error( $saved ) ) {
		return $wp_error ? $saved : false;
	}

	return $lead;
}

/**
 * Get leads.
 *
 * @param array $args The args.
 * @param bool  $count Whether to return a count.
 *
 * @since 1.0.0
 * @return Lead[]|int The leads.
 */
function utmm_get_leads( $args = [], $count = false ) {
	$defaults = array(
		'post_type'      => 'utmm_lead',
		'posts_per_page' => - 1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);
	$args     = wp_parse_args( $args, $defaults );
	$query    = new WP_Query( $args );

	if ( $count ) {
		return $query->found_posts;
	}

	return $query->posts;

	return array_map( 'utmm_get_lead', $query->posts );
}
