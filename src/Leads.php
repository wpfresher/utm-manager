<?php

namespace UTMManager;

defined( 'ABSPATH' ) || exit;

/**
 * Class Leads.
 *
 * @since 1.0.0
 *
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

		$post_args = array(
			'post_type'     => 'utmm_lead',
			'post_title'    => wp_strip_all_tags( $ip ),
			'post_status'   => 'publish',
		);

		$post_exists = self::get_post_by_title( $ip );

		if ( $post_exists ) {
			$post_exists_args = array(
				'ID' => intval( $post_exists ),
			);

			$post_args = wp_parse_args( $post_exists_args, $post_args );
		}

		// Create or update the post.
		$post = wp_insert_post( $post_args );

		// Update post meta.
		if ( $post && ! is_wp_error( $post ) ) {

			$utm_id = self::get_url_parameters( 'utm_id' );
			if ( $utm_id && 'yes' === get_option( 'utmm_utm_id', 'yes' ) ) {
				update_post_meta( $post, '_utmm_utm_id', $utm_id );
			}

			$utm_source = self::get_url_parameters( 'utm_source' );
			if ( $utm_source && 'yes' === get_option( 'utmm_utm_source', 'yes' ) ) {
				update_post_meta( $post, '_utmm_utm_source', $utm_source );
			}

			$utm_medium = self::get_url_parameters( 'utm_medium' );
			if ( $utm_medium && 'yes' === get_option( 'utmm_utm_medium', 'yes' ) ) {
				update_post_meta( $post, '_utmm_utm_medium', $utm_medium );
			}

			$utm_campaign = self::get_url_parameters( 'utm_campaign' );
			if ( $utm_campaign && 'yes' === get_option( 'utmm_utm_campaign', 'yes' ) ) {
				update_post_meta( $post, '_utmm_utm_campaign', $utm_campaign );
			}

			$utm_term = self::get_url_parameters( 'utm_term' );
			if ( $utm_term && 'yes' === get_option( 'utmm_utm_term', 'yes' ) ) {
				update_post_meta( $post, '_utmm_utm_term', $utm_term );
			}

			$utm_content = self::get_url_parameters( 'utm_content' );
			if ( $utm_content && 'yes' === get_option( 'utmm_utm_content', 'yes' ) ) {
				var_dump('Hello');
				$the_post = get_post( $post );
				$the_post->post_content = $utm_content;
				wp_update_post( $the_post );
			}
		}
	}

	private static function get_url_parameters( $utm_id ) {

		if ( isset( $_GET[$utm_id] ) ) {
			return wp_unslash( $_GET[$utm_id] );
		}

		return null;
	}

	/**
	 * Get the post by title.
	 *
	 * @param string $post_title The post title.
	 *
	 * @since 1.0.0
	 * @return string|null
	 */
	public static function get_post_by_title( $post_title ) {
		global $wpdb;

		// Query posts by title.
		$post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='utmm_lead'", $post_title ));

		if ( $post ) {
			return $post;
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

		if ( array_key_exists('HTTP_CF_CONNECTING_IP', $_SERVER ) ) {
			$client = @$_SERVER['HTTP_CF_CONNECTING_IP'];
			if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {
				return $client;
			}
		}

		if ( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER ) ) {
			$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
			if ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
				return $forward;
			}
		}

		if ( array_key_exists('HTTP_X_FORWARDED', $_SERVER ) ) {
			$forward = @$_SERVER['HTTP_X_FORWARDED'];
			if ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
				return $forward;
			}
		}

		if ( array_key_exists('HTTP_FORWARDED_FOR', $_SERVER ) ) {
			$forward = @$_SERVER['HTTP_FORWARDED_FOR'];
			if ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
				return $forward;
			}
		}

		if ( array_key_exists('HTTP_FORWARDED', $_SERVER ) ) {
			$forward = @$_SERVER['HTTP_FORWARDED'];
			if ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
				return $forward;
			}
		}

		if ( array_key_exists('HTTP_CLIENT_IP', $_SERVER ) ) {
			$client_ip = @$_SERVER['HTTP_CLIENT_IP'];
			if ( filter_var( $client_ip, FILTER_VALIDATE_IP ) ) {
				return $client_ip;
			}
		}

		if ( array_key_exists('REMOTE_ADDR', $_SERVER ) ) {
			$remote_addr = @$_SERVER['REMOTE_ADDR'];
			if ( filter_var( $remote_addr, FILTER_VALIDATE_IP ) ) {
				return $remote_addr;
			}
		}

		return null;
	}
}
