<?php

namespace UTMSourceTracker\Lib;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Basic implementation of the Plugin interface which stores core data about a
 * WordPress plugin (Prefix, version number, etc.). Data is passed as an array on construction.
 *
 * @since 1.0.0
 * @author Kawsar Ahmed <kawsar@urldev.com>
 * @license   GPL-3.0
 *
 * @version   1.0.0
 * @package  WpStarterPlugin\Lib
 */
abstract class PremiumPlugin extends Plugin {
	/**
	 * PremiumPlugin constructor.
	 *
	 * @param array $data The plugin data.
	 *
	 * @since 1.0.0
	 */
	protected function __construct( $data ) {
		parent::__construct( $data );

		if ( empty( $this->get_item_id() ) ) {
			// translators: %s is the plugin name.
			wp_die( esc_html( sprintf( __( 'The item_id is missing for %s', 'utm-source-tracker', 'utm-source-tracker' ), $this->data['name'] ) ) );
		}

		add_action( 'admin_footer', array( $this, 'license_notices' ), PHP_INT_MAX );
		add_action( 'plugin_action_links_' . $this->get_basename(), array( $this, 'add_license_link' ), 5 );
		add_action( 'after_plugin_row_' . $this->get_basename(), array( $this, 'add_license_row' ), PHP_INT_MAX );
		add_action( 'wp_ajax_' . $this->get_basename() . '_license_action', array( $this, 'license_ajax_handler' ) );
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
		add_action( 'wp_version_check', array( $this, 'refresh_license_status' ) );
	}

	/**
	 * Get the item ID.
	 *
	 * @since 1.0.0
	 * @return int
	 */
	public function get_item_id() {
		return (int) $this->data['item_id'];
	}

	/**
	 * get license key.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_license_key() {
		return get_option( $this->data['prefix'] . '_license_key', '' );
	}

	/**
	 * get license key status.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_license_status() {
		return get_option( $this->data['prefix'] . '_license_status', '' );
	}

	/**
	 * Is license valid.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_license_valid() {
		return 'valid' === $this->get_license_status();
	}

	/**
	 * License notices.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function license_notices() {
		if ( ! current_user_can( 'manage_options' ) || ! empty( $this->get_license_status() ) ) {
			return;
		}
		$license = $this->get_license_key();
		?>
		<div id="<?php echo esc_attr( $this->data['slug'] ); ?>-license-notice" class="notice notice-info license-notice is-dismissible" style="background-color: #f0f6fc;">
			<div class="license-notice__content" style="display:flex;align-items: center;margin:22px 0 23px;">
				<!-- <div class="license-notice__icon" style="width:60px;height: 60px;margin-right:20px;">UrlDev</div> -->
				<div class="license-notice__text">
					<h2 style="margin:0 0 10px;padding: 0;">
						<?php // translators: %s: plugin name. ?>
						<?php printf( esc_html__( 'Thanks for installing "%s"!', 'utm-source-tracker', 'utm-source-tracker' ), esc_html( $this->data['name'] ) ); ?>
					</h2>
					<?php
					printf(
						'<p style="margin:0 0 10px;font-size: 14px;">%s</p>',
						wp_kses_post(
							sprintf(
							// translators: %1$s is the plugin name, %2$s is the license key, %3$s is the license status.
								__( 'Please activate your license to get updates and support. You can find your license key in your account on %1$surldev.com%2$s and in the email you received after purchase.', 'utm-source-tracker', 'utm-source-tracker' ),
								'<a href="https://urldev.com/my-account/" target="_blank">',
								'</a>'
							)
						)
					);
					?>
					<form method="post" style="display:flex;flex-direction:row;align-items:center;flex-wrap:wrap;gap: 10px;">
						<?php wp_nonce_field( $this->get_basename() . '_license_action', 'nonce' ); ?>
						<input type="hidden" name="operation" value="activate_license">
						<input type="hidden" name="action" value="<?php echo esc_attr( $this->get_basename() ); ?>_license_action">
						<input class="regular-text" type="text" name="key" placeholder="<?php echo esc_attr__( 'Enter your license key', 'utm-source-tracker', 'utm-source-tracker' ); ?>" required value="<?php echo esc_attr( $license ); ?>" style="margin-right:-10px; border-top-right-radius:0; border-bottom-right-radius:0; border-right:0;">
						<button type="submit" class="button button-secondary" style="border-top-left-radius:0; border-bottom-left-radius:0;line-height: 20px;"><span class="dashicons dashicons-admin-network"></span>&nbsp;<?php echo esc_html__( 'Activate', 'utm-source-tracker', 'utm-source-tracker' ); ?></button>
						<?php printf( '<a href="%s" target="_blank">%s</a>', esc_url( $this->data['pluginuri'] ), esc_html__( 'Buy License', 'utm-source-tracker', 'utm-source-tracker' ) ); ?>
						<?php printf( '<a href="https://urldev.com/support/" target="_blank">%s</a>', esc_html__( 'Contact Support', 'utm-source-tracker', 'utm-source-tracker' ) ); ?>
						<span class="spinner"></span>
					</form>
				</div>
			</div>
		</div>
		<script type="application/javascript">
			addEventListener('DOMContentLoaded', () => {
				if (typeof jQuery !== 'undefined') {
					jQuery(function ($) {
						// When document is ready, and form is submitted with license key make an ajax request to activate the license.
						$(document).on('submit', '#<?php echo esc_attr( $this->data['slug'] ); ?>-license-notice form', function (e) {
							e.preventDefault();
							var $form = $(this);
							var $spinner = $form.find('span.spinner');
							var $notice = $form.closest('.notice.license-notice');
							$.ajax({
								url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
								method: 'POST',
								data: $form.serialize(),
								beforeSend: function () {
									$form.find('button').attr('disabled', 'disabled');
									$spinner.addClass('is-active');
								},
								success: function (response) {
									if (response.data && response.data.message) {
										alert(response.data.message);
									}
									if (response.data.reload) {
										$notice.remove()
										location.reload();
									}
								},
								error: function (response) {
									if (response && response.data && response.data.message) {
										alert(response.data.message);
									}
								},
								complete: function () {
									$form.find('button').removeAttr('disabled');
									$spinner.removeClass('is-active');
								}
							});
						});
					});
				}
			});
		</script>
		<?php
	}

	/**
	 * Add license link to plugin action links.
	 *
	 * @param array $links The plugin action links.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function add_license_link( $links ) {
		if ( ! $this->is_license_valid() ) {
			return $links;
		}
		$action_links = array(
			'license' => sprintf(
				'<a href="javascript:void(0);" class="license-manage-link" aria-label="%s">%s</a>',
				esc_attr__( 'license', 'utm-source-tracker', 'utm-source-tracker' ),
				esc_html__( 'License', 'utm-source-tracker', 'utm-source-tracker' )
			),
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Add license row to plugin row.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_license_row() {
		$screen  = get_current_screen();
		$columns = get_column_headers( $screen );
		$colspan = ! is_countable( $columns ) ? 3 : count( $columns );
		$action  = $this->get_basename() . '_license_action';
		$nonce   = wp_create_nonce( $this->get_basename() . '_license_action' );
		$visible = $this->is_license_valid() ? 'hidden' : 'visible';
		$button  = '<button class="button license-button" data-action="%1$s" data-operation="%2$s" data-nonce="%3$s" style="line-height: 20px;%4$s"><span class="dashicons %5$s"></span>&nbsp;%6$s</button>';
		?>
		<tr class="license-row notice-warning notice-alt plugin-update-tr <?php echo esc_attr( $visible ); ?>" data-plugin="<?php echo esc_attr( $this->get_basename() ); ?>">
			<td colspan="<?php echo esc_attr( $colspan ); ?>" class="plugin-update colspanchange">
				<div class="update-message" style="margin-top: 15px;display: flex;flex-direction: row;align-items: center;flex-wrap: wrap;gap: 10px;">
					<?php if ( 'valid' === $this->get_license_status() ) : ?>
						<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
						<span><?php esc_html_e( 'License is valid.', 'utm-source-tracker', 'utm-source-tracker' ); ?></span>
					<?php elseif ( 'expired' === $this->get_license_status() ) : ?>
						<span class="dashicons dashicons-warning" style="color: #dc3232;"></span>
						<span><?php esc_html_e( 'License is expired.', 'utm-source-tracker', 'utm-source-tracker' ); ?></span>
					<?php elseif ( '' === $this->get_license_status() ) : ?>
						<span class="dashicons dashicons-warning" style="color: #dc3232;"></span>
						<span><?php esc_html_e( 'Please activate your license.', 'utm-source-tracker', 'utm-source-tracker' ); ?></span>
					<?php else : ?>
						<span class="dashicons dashicons-warning" style="color: #dc3232;"></span>
						<?php /* translators: %s: license status */ ?>
						<span><?php printf( esc_html__( 'License is %s.', 'utm-source-tracker', 'utm-source-tracker' ), esc_html( $this->get_license_status() ) ); ?></span>
					<?php endif; ?>
					<?php
					printf(
						'<input class="regular-text license-key" type="text" placeholder="%s" value="%s" style="margin-right:-10px; border-top-right-radius:0; border-bottom-right-radius:0; border-right:0;" />',
						esc_attr__( 'Enter your license key', 'utm-source-tracker', 'utm-source-tracker' ),
						esc_attr( $this->get_license_key() )
					);
					printf(
						wp_kses_post( $button ),
						esc_attr( $action ),
						esc_attr( 'activate_license' ),
						esc_attr( $nonce ),
						esc_attr( 'border-top-left-radius:0; border-bottom-left-radius:0;' ),
						esc_attr( 'dashicons-admin-network' ),
						esc_html__( 'Activate License', 'utm-source-tracker', 'utm-source-tracker' )
					);
					if ( 'valid' === $this->get_license_status() ) {
						printf(
							wp_kses_post( $button ),
							esc_attr( $action ),
							esc_attr( 'deactivate_license' ),
							esc_attr( $nonce ),
							esc_attr( '' ),
							esc_attr( 'dashicons-no-alt' ),
							esc_html__( 'Deactivate License', 'utm-source-tracker', 'utm-source-tracker' )
						);

						printf(
							wp_kses_post( $button ),
							esc_attr( $action ),
							esc_attr( 'check_license' ),
							esc_attr( $nonce ),
							esc_attr( '' ),
							esc_attr( 'dashicons-update' ),
							esc_html__( 'Check License', 'utm-source-tracker', 'utm-source-tracker' )
						);
					} elseif ( 'expired' === $this->get_license_status() ) {
						printf(
							'<a href="%s" target="_blank">%s</a>',
							esc_url( 'https://urldev.com/checkouts/' . $this->get_item_id() . '?edd_license_key=' . $this->get_license_key() ),
							esc_html__( 'Renew License', 'utm-source-tracker', 'utm-source-tracker' )
						);
					} elseif ( in_array( $this->get_license_status(), array( 'revoked', 'disabled' ), true ) ) {
						printf(
							'<a href="%s" target="_blank">%s</a>',
							esc_url( 'https://urldev.com/my-account/' ),
							esc_html__( 'Contact Support', 'utm-source-tracker', 'utm-source-tracker' )
						);
					} else {
						printf(
							'<a href="%s" target="_blank">%s</a>',
							esc_url( 'https://urldev.com/checkouts?edd_action=add_to_cart&download_id=' . $this->get_item_id() ),
							esc_html__( 'Buy License', 'utm-source-tracker', 'utm-source-tracker' )
						);
					}
					?>
					<span class="spinner"></span>
					<script type="application/javascript">
						addEventListener('DOMContentLoaded', () => {
							// Check if Jquery is loaded. If not load return.
							if (typeof jQuery !== 'undefined') {
								jQuery(function ($) {
									$('body')
										.on('click', '[data-plugin="<?php echo esc_attr( $this->get_basename() ); ?>"] .license-manage-link', function (e) {
											e.preventDefault();
											const plugin = $(this).closest('tr').data('plugin');
											$(this).closest('tr').siblings('.license-row[data-plugin="' + plugin + '"]').toggle();
										})
										.on('click', '[data-plugin="<?php echo esc_attr( $this->get_basename() ); ?>"] .license-button', function (e) {
											e.preventDefault();
											var $this = $(this);
											var $row = $this.closest('tr');
											var $spinner = $row.find('.spinner');
											var $buttons = $row.find('.license-button');
											var $key = $row.find('.license-key');
											var action = $this.data('action');
											var operation = $this.data('operation');
											var nonce = $this.data('nonce');
											var key = $key.val();
											$.ajax({
												url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
												method: 'POST',
												data: {
													action: action,
													operation: operation,
													nonce: nonce,
													key: key,
												},
												beforeSend: function () {
													$spinner.addClass('is-active');
													$buttons.prop('disabled', true);
												},
												success: function (response) {
													if (response.data && response.data.message) {
														alert(response.data.message);
													}
													if (response.data.reload) {
														$row.fadeOut('fast');
														location.reload();
													}
												},
												error: function (response) {
													if (response.data && response.data.message) {
														alert(response.data.message);
													}
												},
												complete: function () {
													$spinner.removeClass('is-active');
													$buttons.prop('disabled', false);
												}
											});
										});

								});
							}
						});
					</script>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * License AJAX handler.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function license_ajax_handler() {
		if ( ! isset( $_POST['action'] ) || $this->get_basename() . '_license_action' !== $_POST['action'] ) {
			return;
		}

		if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), $this->get_basename() . '_license_action' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error( __( 'Invalid nonce', 'utm-source-tracker', 'utm-source-tracker' ) );
		}

		if ( ! isset( $_POST['operation'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid operation', 'utm-source-tracker', 'utm-source-tracker' ) ) );
		}

		$operation = sanitize_text_field( wp_unslash( $_POST['operation'] ) );
		$license   = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
		if ( empty( $license ) ) {
			wp_send_json_error( array( 'message' => __( 'License key is required', 'utm-source-tracker', 'utm-source-tracker' ) ) );
		}

		switch ( $operation ) {
			case 'activate_license':
				if ( $this->is_license_valid() && $license === $this->get_license_key() ) {
					wp_send_json_error( array( 'message' => __( 'License key is already activated', 'utm-source-tracker', 'utm-source-tracker' ) ) );
				}

				if ( ! empty( $this->get_license_key() ) && $this->get_license_key() !== $license && $this->get_license_status() ) {
					$this->get_license_api_data( $this->get_license_key(), 'deactivate_license' );
				}

				$api_data = $this->get_license_api_data( $license, 'activate_license' );
				if ( is_wp_error( $api_data ) || ! $api_data->success ) {
					wp_send_json_error(
						array(
							'message' => sprintf(
							/* translators: %s: error message */
								__( 'License activation failed. %s', 'utm-source-tracker', 'utm-source-tracker' ),
								esc_html( is_wp_error( $api_data ) ? $api_data->get_error_message() : $api_data->error_message )
							),
							'data'    => $api_data,
						)
					);
				}
				if ( isset( $api_data->license ) && 'valid' === $api_data->license ) {
					update_option( $this->data['prefix'] . '_license_key', $license );
					update_option( $this->data['prefix'] . '_license_status', $api_data->license );
					delete_transient( $this->data['prefix'] . '_latest_version' );
					delete_site_transient( 'update_plugins' );
					wp_send_json_success(
						array(
							'message' => __( 'License activated successfully.', 'utm-source-tracker', 'utm-source-tracker' ),
							'code'    => $api_data->license,
							'reload'  => true,
						)
					);
				} else {
					wp_send_json_error(
						array(
							'message' => __( 'License activation failed.', 'utm-source-tracker', 'utm-source-tracker' ),
							'data'    => $api_data,
						)
					);
				}
				break;
			case 'deactivate_license':
				$api_data = $this->get_license_api_data( $license, 'deactivate_license' );
				if ( is_wp_error( $api_data ) || ! $api_data->success ) {
					wp_send_json_error(
						array(
							'message' => sprintf(
							/* translators: %s: error message */
								__( 'License deactivation failed. %s', 'utm-source-tracker', 'utm-source-tracker' ),
								esc_html( is_wp_error( $api_data ) ? $api_data->get_error_message() : $api_data->error_message )
							),
							'data'    => $api_data,
						)
					);
				}
				if ( isset( $api_data->license ) && 'deactivated' === $api_data->license ) {
					delete_option( $this->data['prefix'] . '_license_status' );
					delete_transient( $this->data['prefix'] . '_latest_version' );
					delete_site_transient( 'update_plugins' );
					wp_send_json_success(
						array(
							'message' => __( 'License deactivated successfully', 'utm-source-tracker', 'utm-source-tracker' ),
							'code'    => $api_data->license,
							'reload'  => true,
						)
					);
				} else {
					wp_send_json_error(
						array(
							'message' => __( 'License deactivation failed', 'utm-source-tracker', 'utm-source-tracker' ),
							'code'    => $api_data->license,
						)
					);
				}
				break;
			case 'check_license':
				$api_data = $this->get_license_api_data( $license, 'check_license' );
				if ( is_wp_error( $api_data ) || ! $api_data->success ) {
					wp_send_json_error(
						array(
							'message' => sprintf(
							/* translators: %s: error message */
								__( 'License check was failed. %s', 'utm-source-tracker', 'utm-source-tracker' ),
								esc_html( is_wp_error( $api_data ) ? $api_data->get_error_message() : $api_data->error_message )
							),
							'data'    => $api_data,
						)
					);
				}
				if ( isset( $api_data->license ) && 'valid' === $api_data->license ) {
					update_option( $this->data['prefix'] . '_license_status', $api_data->license );
					delete_transient( $this->data['prefix'] . '_latest_version' );
					delete_site_transient( 'update_plugins' );
					$message = __( 'Your license key is valid.', 'utm-source-tracker', 'utm-source-tracker' );
					// if set activation limit.
					if ( isset( $api_data->activations_left ) && $api_data->activations_left > 0 ) {
						/* translators: %s: number of activations left */
						$message .= ' ' . sprintf( __( 'You have %s activations left.', 'utm-source-tracker', 'utm-source-tracker' ), number_format( $api_data->activations_left ) );
					}
					// if set expiration date.
					if ( isset( $api_data->expires ) && 'lifetime' !== $api_data->expires ) {
						/* translators: %s: expiration date */
						$message .= ' ' . sprintf( __( 'Your license key expires on %s.', 'utm-source-tracker', 'utm-source-tracker' ), date_i18n( get_option( 'date_format' ), strtotime( $api_data->expires ) ) );
					}
					wp_send_json_success(
						array(
							'message' => $message,
							'code'    => $api_data->license,
						)
					);
				} else {
					wp_send_json_error(
						array(
							'message' => __( 'License is invalid', 'utm-source-tracker', 'utm-source-tracker' ),
							'code'    => $api_data->license,
						)
					);
				}
				break;
			default:
				wp_send_json_error(
					array(
						'message' => __( 'Invalid action', 'utm-source-tracker', 'utm-source-tracker' ),
						'code'    => 'invalid_action',
					)
				);
				break;
		}
	}

	/**
	 * Check for plugin update.
	 *
	 * @param object $transient_data The update plugins transient.
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public function check_for_update( $transient_data ) {
		global $pagenow;

		if ( ! is_object( $transient_data ) ) {
			$transient_data = new \stdClass();
		}

		if ( 'plugins.php' === $pagenow && is_multisite() ) {
			return $transient_data;
		}

		$basename = $this->get_basename();

		// First check if plugin info already exists in the WP transient.
		if ( ! empty( $transient_data->response ) && ! empty( $transient_data->response[ $basename ] ) ) {
			return $transient_data;
		}

		$latest_version = $this->get_latest_version();

		if ( is_object( $latest_version ) && isset( $latest_version->new_version ) ) {
			if ( version_compare( $this->data['version'], $latest_version->new_version, '<' ) ) {
				$transient_data->response[ $basename ]         = $latest_version;
				$transient_data->response[ $basename ]->plugin = $basename;
				$transient_data->response[ $basename ]->id     = $basename;
				if ( ! $this->is_license_valid() ) {
					$transient_data->package = '';
				}
			} else {
				$transient_data->no_update[ $basename ] = (object) array(
					'id'            => $basename,
					'slug'          => $this->data['slug'],
					'plugin'        => $basename,
					'new_version'   => $this->data['version'],
					'url'           => '',
					'package'       => '',
					'icons'         => array(),
					'banners'       => array(),
					'banners_rtl'   => array(),
					'tested'        => '',
					'requires_php'  => '',
					'compatibility' => new \stdClass(),
				);
			}

			$transient_data->last_checked         = time();
			$transient_data->checked[ $basename ] = $this->data['version'];
		}

		return $transient_data;
	}

	/**
	 * Plugin API calls to get plugin information.
	 *
	 * @param mixed  $result Result.
	 * @param string $action The type of information being requested from the Plugin Installation API.
	 * @param object $args Plugin API arguments.
	 *
	 * @since 1.0.0
	 */
	public function plugins_api_filter( $result, $action, $args ) {
		if ( 'plugin_information' !== $action || ! isset( $args->slug ) || $args->slug !== $this->data['slug'] ) {
			return $result;
		}

		$request = $this->get_latest_version();

		if ( ! is_object( $request ) || ! isset( $request->sections ) ) {
			return $result;
		}

		if ( ! $this->is_license_valid() ) {
			$request->package               = '';
			$request->sections['changelog'] = sprintf( esc_html__( 'Please activate your license key to get the latest updates and changelog.', 'utm-source-tracker', 'utm-source-tracker' ), $this->data['name'] );
		}

		return $request;
	}

	/**
	 * Check license status.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function refresh_license_status() {
		$license_key = $this->get_license_key();
		if ( empty( $license_key ) ) {
			return;
		}
		$license_data = $this->get_license_api_data( $license_key, 'check_license' );
		if ( ! is_wp_error( $license_data ) && isset( $license_data->license ) ) {
			update_option( $this->data['prefix'] . '_license_status', $license_data->license );
		}
	}

	/**
	 * Get latest version.
	 *
	 * @param bool $force Force update.
	 *
	 * @since 1.0.0
	 * @return \stdClass|false The latest version or false.
	 */
	public function get_latest_version( $force = false ) {
		$cache_key = $this->data['prefix'] . '_latest_version';
		$api_data  = get_transient( $cache_key );

		if ( $force || false === $api_data ) {
			$api_data = $this->get_license_api_data( $this->get_license_key(), 'get_version' );
			if ( ! is_wp_error( $api_data ) && is_object( $api_data ) && isset( $api_data->new_version ) ) {
				foreach ( get_object_vars( $api_data ) as $prop => $data ) {
					$api_data->$prop = maybe_unserialize( $data );
				}
				$api_data->name = $this->data['name'];
				$api_data->slug = $this->data['slug'];
				set_transient( $cache_key, $api_data, 2 * HOUR_IN_SECONDS );
			}
		}

		if ( ! $this->is_license_valid() && isset( $api_data->package ) ) {
			$api_data->package = '';
		}

		return $api_data;
	}

	/**
	 * Get license API data.
	 *
	 * @param string $license License key.
	 * @param string $action Action to perform.
	 * @param array  $args Additional arguments.
	 *
	 * @since 1.0.0
	 * @return object|\WP_Error Response object or WP_Error on failure.
	 */
	protected function get_license_api_data( $license, $action, $args = array() ) {
		$api_params = array(
			'edd_action'        => $action,
			'license'           => $license,
			'item_id'           => $this->get_item_id(),
			'url'               => home_url(),
			'version'           => $this->data['version'],
			'wp_version'        => get_bloginfo( 'version' ),
			'php_version'       => PHP_VERSION,
			'mysql_version'     => $GLOBALS['wpdb']->db_version(),
			'framework_version' => $this->data['framework_version'],
		);

		$api_params = array_merge( $api_params, $args );
		$response   = wp_remote_post(
			$this->data['api_url'],
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new \WP_Error( 'error', is_wp_error( $response ) ? $response->get_error_message() : wp_remote_retrieve_response_message( $response ) );
		}

		$api_data = json_decode( wp_remote_retrieve_body( $response ) );
		if ( ! is_object( $api_data ) ) {
			return new \WP_Error( 'error', __( 'Something went wrong. Please try again.', 'utm-source-tracker', 'utm-source-tracker' ) );
		}

		if ( isset( $api_data->success ) && false === $api_data->success ) {
			switch ( $api_data->error ) {
				case 'expired':
					$message = sprintf(
					/* translators: %s: license key */
						__( 'Your license key expired on %s.', 'utm-source-tracker', 'utm-source-tracker' ),
						date_i18n( get_option( 'date_format' ), strtotime( $api_data->expires ) )
					);
					break;
				case 'revoked':
				case 'disabled':
					$message = __( 'Your license key has been disabled. Please contact support.', 'utm-source-tracker', 'utm-source-tracker' );
					break;
				case 'missing':
					$message = __( 'Invalid license.', 'utm-source-tracker', 'utm-source-tracker' );
					break;
				case 'invalid':
				case 'site_inactive':
					$message = __( 'Your license is not active for this URL.', 'utm-source-tracker', 'utm-source-tracker' );
					break;
				case 'item_name_mismatch':
				case 'invalid_item_id':
					/* translators: %s: plugin name */
					$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'utm-source-tracker', 'utm-source-tracker' ), esc_html( $this->data['name'] ) );
					break;
				case 'no_activations_left':
					$message = __( 'Your license key has reached its activation limit.', 'utm-source-tracker', 'utm-source-tracker' );
					break;
				default:
					$message = __( 'An error occurred, please try again.', 'utm-source-tracker', 'utm-source-tracker' );
					break;
			}

			$api_data->error_message = $message;
		}

		return $api_data;
	}
}
