<?php

namespace WpFreshers\UTMManager\Controllers;

defined( 'ABSPATH' ) || exit;

/**
 * Actions Class.
 *
 * @since 1.0.0
 * @package WpFreshers\UTMManager\Controllers
 */
class Actions {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_post_utmm_edit_lead', array( __CLASS__, 'handle_edit_lead' ) );
		add_action( 'admin_post_utmm_update_settings', array( __CLASS__, 'handle_settings' ) );
	}

	/**
	 * Edit lead.
	 */
	public static function handle_edit_lead() {
		wp_verify_nonce( '_nonce' );
		$lead_id      = isset( $_POST['id'] ) ? intval( wp_unslash( $_POST['id'] ) ) : '';
		$lead_name    = isset( $_POST['lead_name'] ) ? sanitize_text_field( wp_unslash( $_POST['lead_name'] ) ) : '';
		$lead_content = isset( $_POST['lead_content'] ) ? sanitize_textarea_field( wp_unslash( $_POST['lead_content'] ) ) : '';

		if ( ! $lead_id ) {
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// Lead args.
		$args = array(
			'ID'           => $lead_id,
			'post_type'    => 'utmm_lead',
			'post_title'   => $lead_name,
			'post_content' => $lead_content,
		);

		// Update lead.
		$lead = wp_update_post( $args );

		if ( ! is_wp_error( $lead ) ) {
			utm_manager()->add_flash_notice( __( 'Lead updated successfully.', 'utm-manager' ) );
		} else {
			utm_manager()->add_flash_notice( __( 'There has been an issue while updating the lead.', 'utm-manager' ) );
		}

		$redirect_to = admin_url( 'admin.php?page=utm-manager&edit=' . intval( $lead ) );
		wp_safe_redirect( $redirect_to );
		exit;
	}

	/**
	 * Updating settings.
	 */
	public static function handle_settings() {
		wp_verify_nonce( '_nonce' );

		$utm_id = $_POST['utmm_utm_id'];
		// TODO: Do something.

		utm_manager()->add_flash_notice( __( 'Settings saved successfully.', 'utm-manager' ) );
		wp_safe_redirect( wp_get_referer() );
		exit();
	}
}
