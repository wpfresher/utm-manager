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
		add_action( 'wp_ajax_utmm_export_csv', array( __CLASS__, 'handle_export_csv' ) );
		add_action( 'admin_post_utmm_download_exported_csv', array( __CLASS__, 'download_exported_file' ) );
	}

	/**
	 * Updating settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function handle_settings() {
		check_admin_referer( 'utmm_update_settings' );
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

	/**
	 * Export UTM Leads to CSV file.
	 * This function handles the export of UTM leads to a CSV file in multiple steps to avoid timeouts.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public static function handle_export_csv() {
		check_admin_referer( 'utmm_export_csv' );
		$referer = wp_get_referer();

		// Check current user can manage options.
		if ( ! current_user_can( 'manage_options' ) ) {
			utm_manager()->add_flash_notice( __( 'You do not have permission to perform this action.', 'utm-manager' ), 'error' );
			wp_safe_redirect( $referer );
			exit;
		}

		$start_date = isset( $_POST['lead_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['lead_start_date'] ) ) : '';
		$end_date   = isset( $_POST['lead_end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['lead_end_date'] ) ) : '';
		$fields     = isset( $_POST['fields'] ) ? array_map( 'sanitize_key', $_POST['fields'] ) : array();
		$filename   = isset( $_POST['filename'] ) ? sanitize_text_field( wp_unslash( $_POST['filename'] ) ) : '';
		$step       = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : 1;

		// Remove all invalid fields.
		$fields            = array_unique( $fields );
		$exportable_fields = array(
			'ip_address'   => __( 'IP Address', 'utm-manager' ),
			'utm_id'       => __( 'UTM ID', 'utm-manager' ),
			'utm_source'   => __( 'UTM Source', 'utm-manager' ),
			'utm_medium'   => __( 'UTM Medium', 'utm-manager' ),
			'utm_campaign' => __( 'UTM Campaign', 'utm-manager' ),
			'utm_term'     => __( 'UTM Term', 'utm-manager' ),
			'utm_content'  => __( 'UTM Content', 'utm-manager' ),
			'date'         => __( 'Date', 'utm-manager' ),
		);
		$fields            = array_intersect( $fields, array_keys( $exportable_fields ) );

		// Check if fields are selected.
		if ( empty( $fields ) ) {
			utm_manager()->add_flash_notice( __( 'Error: Please select at least one field to export.', 'utm-manager' ), 'error' );
			wp_safe_redirect( $referer );
			exit;
		}

		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		// Create a CSV file in uploads directory.
		$upload_dir = wp_upload_dir();
		$file_path  = trailingslashit( $upload_dir['path'] ) . $filename . '.csv';

		// If step is 1 then delete the file and add headers.
		if ( 1 === $step ) {
			// If file exists then delete it.
			if ( file_exists( $file_path ) ) {
				$wp_filesystem->delete( $file_path );
			}

			// Open the file and Add headers to the file.
			$fp = fopen( $file_path, 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
			fputcsv( $fp, $fields );
			fclose( $fp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		}

		// Get UTM leads.
		$args = array(
			'post_type' => 'utmm_lead',
			'per_page'  => 30,
			'status'    => 'publish',
			'fields'    => 'ids',
			'paged'     => $step,
		);

		// Filter by post modified date range.
		if ( ! empty( $start_date ) ) {
			// Normalize $start_date to ensure consistent comparison.
			$input_date = date_create_from_format( 'Y-m-d', $start_date, new \DateTimeZone( wp_timezone_string() ) );
			if ( $input_date && $input_date->format( 'Y-m-d' ) === $start_date ) {
				$args['date_query'][] = array(
					'column'    => 'post_modified',
					'after'     => $input_date->format( 'Y-m-d 00:00:00' ),
					'inclusive' => true,
				);
			}
		}

		// Filter by post modified end date.
		if ( ! empty( $end_date ) ) {
			// Normalize $end_date to ensure consistent comparison.
			$input_date = date_create_from_format( 'Y-m-d', $end_date, new \DateTimeZone( wp_timezone_string() ) );
			if ( $input_date && $input_date->format( 'Y-m-d' ) === $end_date ) {
				$args['date_query'][] = array(
					'column'    => 'post_modified',
					'before'    => $input_date->format( 'Y-m-d 23:59:59' ),
					'inclusive' => true,
				);
			}
		}

		$leads = get_posts( $args );

		// When it does not return any results then we are finished.
		if ( empty( $leads ) ) {
			wp_send_json_success(
				array(
					'step' => 'finished',
					'url'  => add_query_arg(
						array(
							'page'     => 'utmm-tools',
							'action'   => 'utmm_download_exported_csv',
							'file'     => $filename,
							'_wpnonce' => wp_create_nonce( 'utmm_download_exported_csv' ),
						),
						admin_url( 'admin-post.php' )
					),
				)
			);
			wp_die();
		}

		$data = array();
		foreach ( $leads as $lead_id ) {
			if ( ! is_numeric( $lead_id ) ) {
				continue;
			}

			$lead = get_post( absint( $lead_id ) );
			if ( empty( $lead ) || is_wp_error( $lead ) ) {
				continue;
			}

			$lead_content = maybe_unserialize( $lead->post_content );
			if ( ! is_array( $lead_content ) ) {
				$lead_content = array();
			}

			$row = array();
			foreach ( $fields as $field ) {
				switch ( $field ) {
					case 'ip_address':
						$row[] = wp_strip_all_tags( $lead->post_title );
						break;
					case 'utm_id':
						$row[] = isset( $lead_content['utm_id'] ) ? $lead_content['utm_id'] : '';
						break;
					case 'utm_source':
						$row[] = isset( $lead_content['utm_source'] ) ? $lead_content['utm_source'] : '';
						break;
					case 'utm_medium':
						$row[] = isset( $lead_content['utm_medium'] ) ? $lead_content['utm_medium'] : '';
						break;
					case 'utm_campaign':
						$row[] = isset( $lead_content['utm_campaign'] ) ? $lead_content['utm_campaign'] : '';
						break;
					case 'utm_term':
						$row[] = isset( $lead_content['utm_term'] ) ? $lead_content['utm_term'] : '';
						break;
					case 'utm_content':
						$row[] = isset( $lead_content['utm_content'] ) ? $lead_content['utm_content'] : '';
						break;
					case 'date':
						$row[] = esc_html( $lead->post_modified );
						break;
					default:
						$row[] = apply_filters( 'utm_manager_export_csv_field_' . $field, '', $lead );
				}
			}
			$data[] = $row;
		}

		// Append the data to the file.
		$fp = fopen( $file_path, 'a' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		foreach ( $data as $data_row ) {
			fputcsv( $fp, $data_row );
		}

		// Close the file.
		fclose( $fp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

		// Send the response.
		wp_send_json_success(
			array(
				'step' => $step + 1,
			)
		);
		wp_die();
	}

	/**
	 * Download the exported file: CSV
	 * This function handles the download of the exported CSV file that was generated in the previous step.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public static function download_exported_file() {
		check_admin_referer( 'utmm_download_exported_csv' );
		$referer = wp_get_referer();

		// Check current user can manage options.
		if ( ! current_user_can( 'manage_options' ) ) {
			utm_manager()->add_flash_notice( __( 'You do not have permission to perform this action.', 'utm-manager' ), 'error' );
			wp_safe_redirect( $referer );
			exit;
		}

		// Check if the file isset otherwise redirect back.
		if ( ! isset( $_GET['file'] ) ) {
			utm_manager()->add_flash_notice( __( 'Error: File name is missing.', 'utm-manager' ), 'error' );
			wp_safe_redirect( $referer );
			exit();
		}

		$file_name  = sanitize_text_field( wp_unslash( $_GET['file'] ) );
		$upload_dir = wp_upload_dir();
		$file_path  = trailingslashit( $upload_dir['path'] ) . $file_name . '.csv';

		// Check if the file exists otherwise redirect back.
		if ( ! file_exists( $file_path ) ) {
			utm_manager()->add_flash_notice( __( 'Error: File not found or maybe there are no records to export.', 'utm-manager' ), 'error' );
			wp_safe_redirect( $referer );
			exit();
		}

		// Read the file using the WordPress file system API.
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$file = $wp_filesystem->get_contents( $file_path );

		if ( false !== $file ) {
			// Disable caching.
			header( 'Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate' );
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );

			// Force download.
			header( 'Content-Type: application/force-download' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Type: application/download' );

			// disposition / encoding on response body.
			header( 'Content-Encoding: UTF-8' );
			header( 'Content-Type: text/csv; charset=UTF-8' );
			header( 'Content-Disposition: attachment; filename=' . $file_name . '.csv' );

			// Set headers to force download.
			header( 'Content-Description: CSV File Export' );
			header( 'Content-Disposition: attachment; filename="' . basename( $file_path ) . '"' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Expires: 0' );
			header( 'Pragma: no-cache' );
			header( 'Content-Length: ' . strlen( $file ) );

			// Sanitized output $file.
			echo wp_kses( $file, array() );

			// Delete the file after download.
			$wp_filesystem->delete( $file_path );
			exit;
		}

		wp_safe_redirect( wp_get_referer() );
		exit;
	}
}
