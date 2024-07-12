<?php

namespace UTMManager;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Class Leads.
 *
 * @since 1.0.0
 * @package UTMManager
 */
class Leads {

	/**
	 * Leads constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'handle_leads' ) );
	}

	/**
	 * Create leads.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function handle_leads() {

		$ip = self::get_ip();

		if ( empty( $ip ) ) {
			return;
		}

		// Get UTM parameters.
		$utm_parameters = array();
		$utm_keys       = array(
			'utm_id',
			'utm_source',
			'utm_medium',
			'utm_campaign',
			'utm_term',
			'utm_content',
		);

		foreach ( $utm_keys as $utm_key ) {
			$is_utm_parameter = self::get_url_parameters( $utm_key );
			if ( $is_utm_parameter && 'yes' === get_option( 'utmm_' . $utm_key, 'yes' ) ) {
				$utm_parameters[ $utm_key ] = $is_utm_parameter;
			}
		}

		// Return if there are no UTM parameters are found.
		if ( empty( $utm_parameters ) ) {
			return;
		}

		// Create a lead.
		$post_args = array(
			'post_type'   => 'utmm_lead',
			'post_title'  => wp_strip_all_tags( $ip ),
			'post_name'   => sanitize_title( $ip ),
			'post_status' => 'publish',
		);

		$post_exists = utmm_get_post_by_title( $ip );
		if ( $post_exists ) {
			$post_exists_args = array(
				'ID' => intval( $post_exists ),
			);

			$post_args = wp_parse_args( $post_exists_args, $post_args );
		}

		// Create or update the post.
		$post = wp_insert_post( $post_args );

		// Update post meta.
		if ( $post && ! is_wp_error( $post ) && is_array( $utm_parameters ) ) {

			foreach ( $utm_parameters as $key => $utm_parameter ) {

				if ( 'utm_content' === $key ) {
					$the_post               = get_post( $post );
					$the_post->post_content = $utm_parameter;
					wp_update_post( $the_post );
					continue;
				}

				update_post_meta( $post, '_utmm_' . $key, $utm_parameter );
			}
		}
	}

	/**
	 * Get URL parameters.
	 *
	 * @param string $utm_key The UTM key.
	 *
	 * @since 1.0.0
	 * @return string|null
	 */
	private static function get_url_parameters( $utm_key ) {
		wp_verify_nonce( '_nonce' );

		if ( isset( $_GET[ $utm_key ] ) ) {
			return sanitize_text_field( wp_unslash( $_GET[ $utm_key ] ) );
		}

		return null;
	}

	/**
	 * Get the ip address.
	 *
	 * @since 1.0.0
	 * @return string|null
	 */
	public static function get_ip() {
		$keys = array(
			'REMOTE_ADDR',
			'SERVER_ADDR',
			'HTTP_CLIENT_IP',
			'HTTP_FORWARDED',
			'HTTP_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_CF_CONNECTING_IP',
		);

		foreach ( $keys as $key ) {
			if ( array_key_exists( $key, $_SERVER ) && ! empty( $_SERVER[ $key ] ) ) {
				$ip = filter_var( wp_unslash( $_SERVER[ $key ] ), FILTER_VALIDATE_IP );
				if ( $ip ) {
					return $ip;
				}
			}
		}

		return null;
	}
}
