<?php

namespace UTMManager;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Class Installer.
 *
 * Handles plugin updates and migrations.
 *
 * @since 1.2.6
 * @package UTMManager
 */
class Installer {

	/**
	 * Update callbacks.
	 *
	 * @since 1.2.6
	 * @var array
	 */
	protected $updates = array();

	/**
	 * Installer constructor.
	 *
	 * @since 1.2.6
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'check_update' ), 1 );
		add_filter( 'cron_schedules', array( __CLASS__, 'add_cron_intervals' ) ); // phpcs:ignore -- Need two minute interval for migration.
		add_action( 'utmm_migrate_data', array( $this, 'migrate_data' ) );
	}

	/**
	 * Add custom cron intervals.
	 *
	 * @param array $schedules Existing cron schedules.
	 *
	 * @since 1.2.6
	 * @return array Modified cron schedules.
	 */
	public static function add_cron_intervals( $schedules ) {
		if ( ! isset( $schedules['five_minutes'] ) ) {
			$schedules['utmm_two_minutes'] = array(
				'interval' => 60,
				'display'  => __( 'Every Minute', 'utm-manager' ),
			);
		}

		return $schedules;
	}

	/**
	 * Check the plugin version and run the updater if necessary.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 *
	 * @since 1.2.6
	 * @return void
	 */
	public function check_update() {
		$db_version      = get_option( 'utmm_version', '1.0.0' );
		$current_version = utm_manager()->get_version();
		$requires_update = version_compare( $db_version, $current_version, '<' );
		$can_install     = ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ! defined( 'IFRAME_REQUEST' );

		if ( $can_install && $requires_update ) {
			static::install();
			$update_versions = array_keys( $this->updates );
			usort( $update_versions, 'version_compare' );

			if ( ! is_null( $db_version ) && version_compare( $db_version, end( $update_versions ), '<' ) ) {
				$this->update();
			} else {
				update_option( 'utmm_version', $current_version );
			}
		}
	}

	/**
	 * Update the plugin.
	 *
	 * @since 1.2.6
	 * @return void
	 */
	public function update() {
		$db_version = get_option( 'utmm_version', '1.0.0' );

		foreach ( $this->updates as $version => $callbacks ) {
			$callbacks = (array) $callbacks;

			if ( version_compare( $db_version, $version, '<' ) ) {
				foreach ( $callbacks as $callback ) {
					// If the callback returns false, update the db version.
					$continue = call_user_func( array( $this, $callback ) );

					if ( ! $continue ) {
						update_option( 'utmm_version', $version );
						$notice = sprintf(
							/* translators: %s: version number */
							__( 'UTM Manager updated to version %s successfully.', 'utm-manager' ),
							esc_html( $version ),
						);
						utm_manager()->add_flash_notice( $notice );
					}
				}
			}
		}
	}

	/**
	 * Install the plugin.
	 *
	 * @since 1.2.6
	 * @return void
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Get the status of the migration.
		$is_migrated = (int) get_option( 'utmm_data_migrated' );

		// On install, Schedule the migration cron job (runs per two minutes).
		if ( ! wp_next_scheduled( 'utmm_migrate_data' ) && empty( $is_migrated ) ) {
			wp_schedule_event( time(), 'utmm_two_minutes', 'utmm_migrate_data' );
		}

		// Set installation options.
		update_option( 'utmm_version', utm_manager()->get_version() );
		add_option( 'utmm_installed', wp_date( 'U' ) );

		// Add default settings on install.
		add_option( 'utmm_utm_id', 'yes' );
		add_option( 'utmm_utm_source', 'yes' );
		add_option( 'utmm_utm_medium', 'yes' );
		add_option( 'utmm_utm_campaign', 'yes' );
		add_option( 'utmm_utm_term', 'yes' );
		add_option( 'utmm_utm_content', 'yes' );
	}

	/**
	 * Process migration in batches via cron.
	 *
	 * @since 1.2.6
	 * @return void
	 */
	public function migrate_data() {
		// Get current page offset.
		$paged = (int) get_option( 'utmm_migration_page', 1 );

		// Fetch posts to migrate.
		$lead_ids = get_posts(
			array(
				'post_type'      => 'utmm_lead',
				'posts_per_page' => 30,
				'paged'          => $paged,
				'fields'         => 'ids',
			)
		);

		if ( $lead_ids ) {
			foreach ( $lead_ids as $lead_id ) {
				self::migrate_single_lead( $lead_id );
			}

			// Increment the page offset.
			update_option( 'utmm_migration_page', $paged + 1 );
		} else {
			// No more posts left to migrate, cleanup.
			delete_option( 'utmm_migration_page' );
			update_option( 'utmm_data_migrated', 'yes' );
			wp_clear_scheduled_hook( 'utmm_migrate_data' );
		}
	}

	/**
	 * Migrate a single lead post.
	 *
	 * @param int $post_id The post ID to migrate.
	 *
	 * @since 1.2.6
	 * @return void
	 */
	private static function migrate_single_lead( $post_id ) {
		$post = get_post( $post_id );

		if ( ! $post || 'utmm_lead' !== $post->post_type ) {
			return;
		}

		// Skip if already migrated (content is already serialized).
		if ( is_serialized( $post->post_content ) ) {
			return;
		}

		$utm_parameters = array();
		$key_mapping    = array(
			'_utmm_utm_id'       => 'utm_id',
			'_utmm_utm_source'   => 'utm_source',
			'_utmm_utm_medium'   => 'utm_medium',
			'_utmm_utm_campaign' => 'utm_campaign',
			'_utmm_utm_term'     => 'utm_term',
		);

		foreach ( $key_mapping as $old_key => $new_key ) {
			$value = get_post_meta( $post_id, $old_key, true );
			if ( ! empty( $value ) ) {
				$utm_parameters[ $new_key ] = $value;
			}
		}

		// Handle old utm_content stored in post_content.
		if ( ! empty( $post->post_content ) ) {
			$utm_parameters['utm_content'] = $post->post_content;
		}

		// Update post with serialized UTM parameters.
		if ( ! empty( $utm_parameters ) ) {
			wp_update_post(
				array(
					'ID'           => $post_id,
					'post_content' => maybe_serialize( $utm_parameters ),
				)
			);
		}

		$old_meta_keys = array(
			'_utmm_utm_id',
			'_utmm_utm_source',
			'_utmm_utm_medium',
			'_utmm_utm_campaign',
			'_utmm_utm_term',
		);

		// Delete old meta keys.
		foreach ( $old_meta_keys as $meta_key ) {
			delete_post_meta( $post_id, $meta_key );
		}
	}

	/**
	 * Plugin deactivation cleanup.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function deactivate() {
		$timestamp = wp_next_scheduled( 'utmm_migrate_data' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'utmm_migrate_data' );
		}

		// Delete migration options.
		delete_option( 'utmm_data_migrated' );
	}
}
