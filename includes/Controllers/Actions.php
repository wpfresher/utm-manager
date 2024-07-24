<?php

namespace UTMManager\Controllers;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Actions Class.
 *
 * @since 1.0.0
 * @package UTMManager\Controllers
 */
class Actions {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_post_utmm_update_settings', array( __CLASS__, 'handle_settings' ) );
	}

	/**
	 * Updating settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function handle_settings() {
		wp_verify_nonce( '_nonce' );
		$referer = wp_get_referer();

		if ( ! current_user_can( 'manage_options' ) ) {
			utm_manager()->add_flash_notice( esc_html__( 'You do not have permission to perform this action.', 'utm-manager' ), 'error' );
			wp_safe_redirect( $referer );
			exit();
		}

		$utm_id       = isset( $_POST['utmm_utm_id'] ) ? sanitize_key( wp_unslash( $_POST['utmm_utm_id'] ) ) : '';
		$utm_source   = isset( $_POST['utmm_utm_source'] ) ? sanitize_key( wp_unslash( $_POST['utmm_utm_source'] ) ) : '';
		$utm_medium   = isset( $_POST['utmm_utm_medium'] ) ? sanitize_key( wp_unslash( $_POST['utmm_utm_medium'] ) ) : '';
		$utm_campaign = isset( $_POST['utmm_utm_campaign'] ) ? sanitize_key( wp_unslash( $_POST['utmm_utm_campaign'] ) ) : '';
		$utm_term     = isset( $_POST['utmm_utm_term'] ) ? sanitize_key( wp_unslash( $_POST['utmm_utm_term'] ) ) : '';
		$utm_content  = isset( $_POST['utmm_utm_content'] ) ? sanitize_key( wp_unslash( $_POST['utmm_utm_content'] ) ) : '';

		update_option( 'utmm_utm_id', $utm_id );
		update_option( 'utmm_utm_source', $utm_source );
		update_option( 'utmm_utm_medium', $utm_medium );
		update_option( 'utmm_utm_campaign', $utm_campaign );
		update_option( 'utmm_utm_term', $utm_term );
		update_option( 'utmm_utm_content', $utm_content );

		utm_manager()->add_flash_notice( esc_html__( 'Settings saved successfully.', 'utm-manager' ) );
		wp_safe_redirect( $referer );
		exit();
	}
}
