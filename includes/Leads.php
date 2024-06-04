<?php

namespace WpFreshers\UTMManager;

defined( 'ABSPATH' ) || exit;

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
			return filter_var( wp_unslash( $_GET[ $utm_key ] ), FILTER_SANITIZE_STRING );
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

		if ( array_key_exists( 'REMOTE_ADDR', $_SERVER ) && ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			return filter_var( wp_unslash( $_SERVER['REMOTE_ADDR'] ), FILTER_VALIDATE_IP );
		}

		if ( array_key_exists( 'SERVER_ADDR', $_SERVER ) && ! empty( $_SERVER['SERVER_ADDR'] ) ) {
			return filter_var( wp_unslash( $_SERVER['SERVER_ADDR'] ), FILTER_VALIDATE_IP );
		}

		if ( array_key_exists( 'HTTP_CLIENT_IP', $_SERVER ) && ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			return filter_var( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ), FILTER_VALIDATE_IP );
		}

		if ( array_key_exists( 'HTTP_FORWARDED', $_SERVER ) && ! empty( $_SERVER['HTTP_FORWARDED'] ) ) {
			return filter_var( wp_unslash( $_SERVER['HTTP_FORWARDED'] ), FILTER_VALIDATE_IP );
		}

		if ( array_key_exists( 'HTTP_FORWARDED_FOR', $_SERVER ) && ! empty( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			return filter_var( wp_unslash( $_SERVER['HTTP_FORWARDED_FOR'] ), FILTER_VALIDATE_IP );
		}

		if ( array_key_exists( 'HTTP_X_FORWARDED', $_SERVER ) && ! empty( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			return filter_var( wp_unslash( $_SERVER['HTTP_X_FORWARDED'] ), FILTER_VALIDATE_IP );
		}

		if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			return filter_var( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ), FILTER_VALIDATE_IP );
		}

		if ( array_key_exists( 'HTTP_CF_CONNECTING_IP', $_SERVER ) && ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
			return filter_var( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ), FILTER_VALIDATE_IP );
		}

		return null;
	}
}
