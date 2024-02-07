<?php

namespace UTMSourceTracker\Lib;

defined( 'ABSPATH' ) || exit;

/**
 * Class Settings
 *
 * @since 1.0.0
 * @version 1.0.4
 * @subpackage Lib\Settings
 * @package Lib
 */
abstract class Settings {
	/**
	 * Init settings.
	 *
	 * @since 1.0.3
	 * @return self
	 */
	public static function instance() {
		static $instance = null;
		$class_name      = get_called_class();
		if ( null === $instance ) {
			$instance = new $class_name();
		}

		return $instance;
	}

	/**
	 * Init settings.
	 *
	 * @since 1.0.0
	 * @depecated 1.0.3
	 * @return self
	 */
	public static function get_instance() {
		_doing_it_wrong( __FUNCTION__, 'Use static::instance() instead.', '1.0.3' );
		return static::instance();
	}

	/**
	 * Settings constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'init', array( $this, 'buffer_start' ) );
		add_action( 'admin_init', array( $this, 'save_settings' ), 1 );
	}

	/**
	 * Buffer start.
	 *
	 * @since 1.0.0
	 */
	public function buffer_start() {
		ob_start();
	}

	/**
	 * Get settings tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	abstract public function get_tabs();

	/**
	 * Get settings.
	 *
	 * @param string $tab Tab name.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	abstract public function get_settings( $tab );

	/**
	 * Save settings.
	 *
	 * @since 1.0.0
	 * @return bool True if saved, false otherwise.
	 */
	public function save_settings() {
		$class_name = get_called_class();
		if ( empty( $_POST ) || ! isset( $_POST[ $class_name ] ) ) {
			return false;
		}
		check_admin_referer( $class_name );
		$current_tab = $this->get_current_tab();
		$settings    = $this->get_settings( $current_tab );
		//if ( class_exists( '\WC_Admin_Settings' ) && ! empty( $settings ) && \WC_Admin_Settings::save_fields( $settings ) ) {
		if (  ! empty( $settings ) && self::save_fields( $settings ) ) {
			add_settings_error( $class_name, 'response', __( 'Settings saved.', 'utm-source-tracker' ), 'updated' );

			return true;
		}

		return false;
	}

	/**
	 * Output settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function output_settings() {
		$tabs        = $this->get_tabs();
		$current_tab = $this->get_current_tab();
		$tab_exists  = isset( $tabs[ $current_tab ] );
		$settings    = $this->get_settings( $current_tab );
		if ( ! empty( $tabs ) && ! $tab_exists && ! headers_sent() ) {
			wp_safe_redirect( admin_url( 'admin.php?page=' . $this->get_current_page() ) );
			exit();
		}
		?>
		<div class="wrap pev-wrap woocommerce">
			<nav class="nav-tab-wrapper pev-navbar">
				<?php $this->output_tabs( $tabs ); ?>
			</nav>
			<hr class="wp-header-end">
			<div class="pev-poststuff">
				<div class="column-1">
					<?php $this->output_form( $settings ); ?>
				</div>
				<div class="column-2">
					<?php $this->output_widgets(); ?>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			document.addEventListener('DOMContentLoaded', function () {
				document.querySelectorAll('[data-cond-id]').forEach(function (element) {
					var $this = element;
					var conditional_id = $this.getAttribute('data-cond-id');
					var conditional_value = $this.getAttribute('data-cond-value') || '';
					var conditional_operator = $this.getAttribute('data-cond-operator') || '==';
					var $conditional_field = document.getElementById(conditional_id);
					$conditional_field.addEventListener('change', function () {
						var value = this.value.trim();
						if (this.type === 'checkbox' || this.type === 'radio') {
							conditional_operator = 'checked';
						}

						var show = false;
						if (conditional_operator === '==') {
							show = value == conditional_value ? true : false; // eslint-disable-line eqeqeq
						} else if (conditional_operator === '!=') {
							show = value != conditional_value; // eslint-disable-line eqeqeq
						} else if (conditional_operator === 'contains') {
							show = value.indexOf(conditional_value) > -1;
						} else if (conditional_operator === 'checked') {
							show = this.checked;
						} else {
							show = false;
						}

						if (show) {
							$this.closest('tr').style.display = 'table-row';
						} else {
							$this.closest('tr').style.display = 'none';
						}
					});

					$conditional_field.dispatchEvent(new Event('change'));
				});

				// if Jquery is not loaded, return.
				if (typeof jQuery === 'undefined') {
					return;
				}

				// trigger change event on load.
				jQuery(document).ready(function ($) {
					// check if iris is loaded.
					if (typeof $.fn.iris !== 'undefined') {
						// Color picker.
						$('.colorpick')
							.iris({
								change: function (event, ui) {
									$(this)
										.parent()
										.find('.colorpickpreview')
										.css({backgroundColor: ui.color.toString()});
								},
								hide: true,
								border: true,
							})
							.on('click focus', function (event) {
								event.stopPropagation();
								$('.iris-picker').hide();
								$(this).closest('td').find('.iris-picker').show();
								$(this).data('originalValue', $(this).val());
							})
							.on('change', function () {
								if ($(this).is('.iris-error')) {
									var original_value = $(this).data('originalValue');
									if (
										original_value.match(
											/^\#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/
										)
									) {
										$(this)
											.val($(this).data('originalValue'))
											.trigger('change');
									} else {
										$(this).val('').trigger('change');
									}
								}
							});

						$('body').on('click', function () {
							$('.iris-picker').hide();
						});
					}
				});
			});
		</script>

		<?php
	}

	/**
	 * Output tabs.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function output_tabs( $tabs ) {
		foreach ( $tabs as $tab_id => $tab_name ) {
			?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->get_current_page() . '&tab=' . $tab_id ) ); ?>" class="nav-tab <?php echo esc_attr( $this->get_current_tab() === $tab_id ? 'nav-tab-active' : '' ); ?>">
				<?php echo esc_html( $tab_name ); ?>
			</a>
			<?php
		}
	}

	/**
	 * Output settings form.
	 *
	 * @param array $settings Settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function output_form( $settings ) {
		if ( ! empty( $settings ) ) {
			$class_name = get_called_class();
			settings_errors( $class_name );
			?>
			<form method="post" id="mainform" action="" enctype="multipart/form-data">
				<?php
//				if ( function_exists( 'woocommerce_admin_fields' ) ) {
					self::output_fields( $settings );
//				}
				?>
				<?php wp_nonce_field( $class_name ); ?>
				<?php submit_button( null, 'primary', $class_name ); ?>
			</form>
			<?php
		}
	}

	/**
	 * Output settings sidebar.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function output_widgets() {
//		$this->output_premium_widget();
//		$this->output_plugins_widget();
//		$this->output_support_widget();
	}

	/**
	 * Output premium widget.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function output_premium_widget() {
		// Premium widget.
	}

	/**
	 * Output promo plugins.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function output_plugins_widget() {
		$promo_plugins = $this->get_promo_plugins();
		if ( ! empty( $promo_plugins ) ) {
			$installed = get_plugins();
			foreach ( $promo_plugins as $promo_plugin ) {
				$promo_plugin = wp_parse_args(
					$promo_plugin,
					array(
						'name'        => '',
						'description' => '',
						'basename'    => '',
						'slug'        => '',
						'badge'       => esc_html__( 'Recommended', 'utm-source-tracker' ),
						'button'      => esc_html__( 'Install Now', 'utm-source-tracker' ),
						'installed'   => false,
					)
				);
				// If basename or slug is not set, skip.
				if ( empty( $promo_plugin['basename'] ) && empty( $promo_plugin['slug'] ) ) {
					continue;
				}
				if ( ! empty( $promo_plugin['basename'] ) ) {
					$basename = $promo_plugin['basename'];
				} else {
					$basename = $promo_plugin['slug'] . '/' . $promo_plugin['slug'] . '.php';
				}
				if ( isset( $installed[ $basename ] ) ) {
					continue;
				}
				// get file name from basename.
				$basename_parts = explode( '/', $basename );
				$slug           = current( $basename_parts );
				$install_url    = add_query_arg(
					array(
						'action' => 'install-plugin',
						'plugin' => $slug,
					),
					network_admin_url( 'update.php' )
				);
				$install_url    = wp_nonce_url( $install_url, 'install-plugin_' . $slug );
				?>
				<div class="pev-panel">
					<?php if ( ! empty( $promo_plugin['badge'] ) ) : ?>
						<span class="pev-panel__legend"><?php echo esc_html( $promo_plugin['badge'] ); ?></span>
					<?php endif; ?>
					<div class="pev-panel__group">
						<span class="icon dashicons dashicons-admin-plugins"></span>
						<h3>
							<?php echo esc_html( $promo_plugin['name'] ); ?>
						</h3>
					</div>
					<?php echo wp_kses_post( wpautop( $promo_plugin['description'] ) ); ?>
					<a href="<?php echo esc_url( $install_url ); ?>" class="button" target="_blank">
						<?php echo esc_html( $promo_plugin['button'] ); ?>
					</a>
				</div>
				<?php
			}
		}
	}

	/**
	 * Output sidebar links.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function output_support_widget() {
		$support_links = $this->get_support_links();
		if ( ! empty( $support_links ) ) {
			?>
			<div class="pev-panel">
				<h3><?php esc_html_e( 'Need Help?', 'utm-source-tracker' ); ?></h3>
				<ul>
					<?php foreach ( $support_links as $support_link ) : ?>
						<li>
							<a href="<?php echo esc_url( $support_link['url'] ); ?>" target="_blank">
								<?php echo esc_html( $support_link['label'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php
		}
	}

	/**
	 * Get promo plugins.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_promo_plugins() {
		return array(
			array(
				'name'        => 'WC Min Max Quantities',
				'slug'        => 'wc-min-max-quantities',
				'description' => 'Set minimum and maximum price or quantity for WooCommerce products.',
				'link'        => 'https://wordpress.org/plugins/wc-min-max-quantities/',
				'badge'       => esc_html__( 'Recommended', 'utm-source-tracker' ),
				'button'      => esc_html__( 'Install Now', 'utm-source-tracker' ),
			),
			array(
				'name'        => 'Product Category Showcase for WooCommerce',
				'slug'        => 'wc-category-showcase',
				'description' => 'Display WooCommerce categories in a beautiful way.',
				'link'        => 'https://wordpress.org/plugins/wc-category-showcase/',
				'badge'       => esc_html__( 'Recommended', 'utm-source-tracker' ),
				'button'      => esc_html__( 'Install Now', 'utm-source-tracker' ),
			),
			array(
				'name'        => 'Product Category Slider for WooCommerce',
				'basename'    => 'woo-category-slider-by-pluginever/woo-category-slider.php',
				'description' => 'Display WooCommerce categories in a beautiful way.',
				'link'        => 'https://wordpress.org/plugins/woo-category-slider-by-pluginever/',
				'badge'       => esc_html__( 'Recommended', 'utm-source-tracker' ),
				'button'      => esc_html__( 'Install Now', 'utm-source-tracker' ),
			),
		);
	}

	/**
	 * Get support links.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_support_links() {
		return array(
			'facebook'        => array(
				'label' => __( 'Join our Community', 'utm-source-tracker' ),
				'url'   => 'https://www.facebook.com/groups/pluginever',
			),
			'feature-request' => array(
				'label' => __( 'Request a Feature', 'utm-source-tracker' ),
				'url'   => 'https://www.pluginever.com/contact/',
			),
			'bug-report'      => array(
				'label' => __( 'Report a Bug', 'utm-source-tracker' ),
				'url'   => 'https://www.pluginever.com/contact/',
			),
		);
	}

	/**
	 * Get current page.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_current_page() {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS );
		return ! empty( $page ) ? sanitize_text_field( wp_unslash( $page ) ) : '';
	}

	/**
	 * Get the current tab.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_current_tab() {
		$tabs = $this->get_tabs();
		$tab  = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_SPECIAL_CHARS );
		$tab  = ! empty( $tab ) ? sanitize_text_field( wp_unslash( $tab ) ) : '';

		if ( ! array_key_exists( $tab, $tabs ) ) {
			$tab = key( $tabs );
		}

		return $tab;
	}

	/**
	 * Save default settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function save_defaults() {
		$tabs = $this->get_tabs();
		foreach ( $tabs as $tab => $label ) {
			$options = $this->get_settings( $tab );

			foreach ( $options as $option ) {
				if ( isset( $option['default'] ) && isset( $option['id'] ) ) {
					$autoload = isset( $option['autoload'] ) ? (bool) $option['autoload'] : true;
					add_option( $option['id'], $option['default'], '', $autoload );
				}
			}
		}
	}

	/**
	 * Output settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		self::instance()->output_settings();
	}


	// Settings fields.

	/**
	 * Output admin fields.
	 *
	 * Loops through the plugin options array and outputs each field.
	 *
	 * @param array[] $options Opens array to output.
	 */
	public static function output_fields( $options ) {
		foreach ( $options as $value ) {
			if ( ! isset( $value['type'] ) ) {
				continue;
			}
			if ( ! isset( $value['id'] ) ) {
				$value['id'] = '';
			}

			// The 'field_name' key can be used when it is useful to specify an input field name that is different
			// from the input field ID. We use the key 'field_name' because 'name' is already in use for a different
			// purpose.
			if ( ! isset( $value['field_name'] ) ) {
				$value['field_name'] = $value['id'];
			}
			if ( ! isset( $value['title'] ) ) {
				$value['title'] = $value['name'] ?? '';
			}
			if ( ! isset( $value['class'] ) ) {
				$value['class'] = '';
			}
			if ( ! isset( $value['css'] ) ) {
				$value['css'] = '';
			}
			if ( ! isset( $value['default'] ) ) {
				$value['default'] = '';
			}
			if ( ! isset( $value['desc'] ) ) {
				$value['desc'] = '';
			}
			if ( ! isset( $value['desc_tip'] ) ) {
				$value['desc_tip'] = false;
			}
			if ( ! isset( $value['placeholder'] ) ) {
				$value['placeholder'] = '';
			}
			if ( ! isset( $value['row_class'] ) ) {
				$value['row_class'] = '';
			}
			if ( ! empty( $value['row_class'] ) && ! str_starts_with( $value['row_class'], 'settings-row-' ) ) {
				$value['row_class'] = 'settings-row-' . $value['row_class'];
			}
			if ( ! isset( $value['suffix'] ) ) {
				$value['suffix'] = '';
			}
			if ( ! isset( $value['value'] ) ) {
				$value['value'] = self::get_option( $value['id'], $value['default'] );
			}

			// Custom attribute handling.
			$custom_attributes = array();

			if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
				foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
					$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
				}
			}

			// Description handling.
			$field_description = self::get_field_description( $value );
			$description       = $field_description['description'];
			$tooltip_html      = $field_description['tooltip_html'];

			// Switch based on field type.
			switch ( $value['type'] ) {

				// Section Titles.
				case 'title':
					if ( ! empty( $value['title'] ) ) {
						echo '<h2>' . esc_html( $value['title'] ) . '</h2>';
					}
					if ( ! empty( $value['desc'] ) ) {
						echo '<div id="' . esc_attr( sanitize_title( $value['id'] ) ) . '-description">';
						echo wp_kses_post( wpautop( wptexturize( $value['desc'] ) ) );
						echo '</div>';
					}
					echo '<table class="form-table">' . "\n\n";
					if ( ! empty( $value['id'] ) ) {
						do_action( 'settings_' . sanitize_title( $value['type'] ) . '_' . sanitize_title( $value['id'] ) );
					}
					break;

				case 'info':
					?><tr valign="top"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
						<th scope="row" class="titledesc"></th><td style="<?php echo esc_attr( $value['css'] ); ?>">
					<?php
					echo wp_kses_post( wpautop( wptexturize( $value['text'] ) ) );
					echo '</td></tr>';
					break;

				// Section Ends.
				case 'sectionend':
					if ( ! empty( $value['id'] ) ) {
						do_action( 'settings_' . sanitize_title( $value['type'] ) . '_' . sanitize_title( $value['id'] ) . '_end' );
					}
					echo '</table>';
					if ( ! empty( $value['id'] ) ) {
						do_action( 'settings_' . sanitize_title( $value['type'] ) . '_' . sanitize_title( $value['id'] ) . '_after' );
					}
					break;

				// TODO: Need to check bellow lines.

				// Standard text inputs and subtypes like 'number'.
				case 'text':
				case 'password':
				case 'datetime':
				case 'datetime-local':
				case 'date':
				case 'month':
				case 'time':
				case 'week':
				case 'number':
				case 'email':
				case 'url':
				case 'tel':
					$option_value = $value['value'];

					?>
					<tr valign="top"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<input
								name="<?php echo esc_attr( $value['field_name'] ); ?>"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								type="<?php echo esc_attr( $value['type'] ); ?>"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								value="<?php echo esc_attr( $option_value ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>"
								placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
								<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
								/><?php echo esc_html( $value['suffix'] ); ?> <?php echo $description; // WPCS: XSS ok. ?>
						</td>
					</tr>
					<?php
					break;

				// Color picker.
				case 'color':
					$option_value = $value['value'];

					?>
					<tr valign="top"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">&lrm;
							<span class="colorpickpreview" style="background: <?php echo esc_attr( $option_value ); ?>">&nbsp;</span>
							<input
								name="<?php echo esc_attr( $value['field_name'] ); ?>"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								type="text"
								dir="ltr"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								value="<?php echo esc_attr( $option_value ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>colorpick"
								placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
								<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
								/>&lrm; <?php echo $description; // WPCS: XSS ok. ?>
								<div id="colorPickerDiv_<?php echo esc_attr( $value['id'] ); ?>" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
						</td>
					</tr>
					<?php
					break;

				// Textarea.
				case 'textarea':
					$option_value = $value['value'];

					?>
					<tr valign="top"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<?php echo $description; // WPCS: XSS ok. ?>

							<textarea
								name="<?php echo esc_attr( $value['field_name'] ); ?>"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>"
								placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
								<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
								><?php echo esc_textarea( $option_value ); // WPCS: XSS ok. ?></textarea>
						</td>
					</tr>
					<?php
					break;

				// Select boxes.
				case 'select':
				case 'multiselect':
					$option_value = $value['value'];

					?>
					<tr valign="top"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<select
								name="<?php echo esc_attr( $value['field_name'] ); ?><?php echo ( 'multiselect' === $value['type'] ) ? '[]' : ''; ?>"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>"
								<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
								<?php echo 'multiselect' === $value['type'] ? 'multiple="multiple"' : ''; ?>
								>
								<?php
								foreach ( $value['options'] as $key => $val ) {
									?>
									<option value="<?php echo esc_attr( $key ); ?>"
										<?php

										if ( is_array( $option_value ) ) {
											selected( in_array( (string) $key, $option_value, true ), true );
										} else {
											selected( $option_value, (string) $key );
										}

										?>
									><?php echo esc_html( $val ); ?></option>
									<?php
								}
								?>
							</select> <?php echo $description; // WPCS: XSS ok. ?>
						</td>
					</tr>
					<?php
					break;

				// Radio inputs.
				case 'radio':
					$option_value     = $value['value'];
					$disabled_values  = $value['disabled'] ?? array();
					$show_desc_at_end = $value['desc_at_end'] ?? false;

					?>
					<tr valign="top"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<fieldset>
								<?php
								if ( ! $show_desc_at_end ) {
									echo wp_kses_post( $description );
								}
								?>
								<ul>
								<?php
								foreach ( $value['options'] as $key => $val ) {
									?>
									<li>
										<label><input
											name="<?php echo esc_attr( $value['field_name'] ); ?>"
											value="<?php echo esc_attr( $key ); ?>"
											type="radio"
											<?php
											if ( in_array( $key, $disabled_values, true ) ) {
												echo 'disabled'; }
											?>
											style="<?php echo esc_attr( $value['css'] ); ?>"
											class="<?php echo esc_attr( $value['class'] ); ?>"
											<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
											<?php checked( $key, $option_value ); ?>
											/> <?php echo esc_html( $val ); ?></label>
									</li>
									<?php
								}
								if ( $show_desc_at_end ) {
									echo wp_kses_post( "<p class='description description-thin'>{$description}</p>" );
								}
								?>
								</ul>
							</fieldset>
						</td>
					</tr>
					<?php
					break;

				// Checkbox input.
				case 'checkbox':
					$option_value     = $value['value'];
					$visibility_class = array();

					if ( ! isset( $value['hide_if_checked'] ) ) {
						$value['hide_if_checked'] = false;
					}
					if ( ! isset( $value['show_if_checked'] ) ) {
						$value['show_if_checked'] = false;
					}
					if ( 'yes' === $value['hide_if_checked'] || 'yes' === $value['show_if_checked'] ) {
						$visibility_class[] = 'hidden_option';
					}
					if ( 'option' === $value['hide_if_checked'] ) {
						$visibility_class[] = 'hide_options_if_checked';
					}
					if ( 'option' === $value['show_if_checked'] ) {
						$visibility_class[] = 'show_options_if_checked';
					}
					if ( $value['row_class'] ) {
						$visibility_class[] = $value['row_class'];
					}

					$must_disable = $value['disabled'] ?? false;

					if ( ! isset( $value['checkboxgroup'] ) || 'start' === $value['checkboxgroup'] ) {
						$has_tooltip             = isset( $value['tooltip'] ) && '' !== $value['tooltip'];
						$tooltip_container_class = $has_tooltip ? 'with-tooltip' : '';
						?>
							<tr valign="top" class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
								<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ); ?></th>
								<td class="forminp forminp-checkbox <?php echo esc_html( $tooltip_container_class ); ?>">
									<?php if ( $has_tooltip ) : ?>
										<span class="help-tooltip"><?php //echo field_help_tip( esc_html( $value['tooltip'] ) ); ?></span>
									<?php endif; ?>
									<fieldset>
						<?php
					} else {
						?>
							<fieldset class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
						<?php
					}

					if ( ! empty( $value['title'] ) ) {
						?>
							<legend class="screen-reader-text"><span><?php echo esc_html( $value['title'] ); ?></span></legend>
						<?php
					}

					?>
						<label for="<?php echo esc_attr( $value['id'] ); ?>">
							<input
								<?php echo $must_disable ? 'disabled' : ''; ?>
								name="<?php echo esc_attr( $value['field_name'] ); ?>"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								type="checkbox"
								class="<?php echo esc_attr( isset( $value['class'] ) ? $value['class'] : '' ); ?>"
								value="1"
								<?php disabled( $value['disabled'] ?? false ); ?>
								<?php checked( $option_value, 'yes' ); ?>
								<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
							/> <?php echo $description; // WPCS: XSS ok. ?>
						</label> <?php echo $tooltip_html; // WPCS: XSS ok. ?>
					<?php

					if ( ! isset( $value['checkboxgroup'] ) || 'end' === $value['checkboxgroup'] ) {
						?>
									</fieldset>
								</td>
							</tr>
						<?php
					} else {
						?>
							</fieldset>
						<?php
					}
					break;

				// Image width settings. @todo deprecate and remove in 4.0. No longer needed by core.
				case 'image_width':
					$image_size       = str_replace( '_image_size', '', $value['id'] );
					$size             = wc_get_image_size( $image_size );
					$width            = isset( $size['width'] ) ? $size['width'] : $value['default']['width'];
					$height           = isset( $size['height'] ) ? $size['height'] : $value['default']['height'];
					$crop             = isset( $size['crop'] ) ? $size['crop'] : $value['default']['crop'];
					$disabled_attr    = '';
					$disabled_message = '';

					if ( has_filter( 'woocommerce_get_image_size_' . $image_size ) ) {
						$disabled_attr    = 'disabled="disabled"';
						$disabled_message = '<p><small>' . esc_html__( 'The settings of this image size have been disabled because its values are being overwritten by a filter.', 'utm-source-tracker' ) . '</small></p>';
					}

					?>
					<tr valign="top"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
						<th scope="row" class="titledesc">
						<label><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html . $disabled_message; // WPCS: XSS ok. ?></label>
					</th>
						<td class="forminp image_width_settings">

							<input name="<?php echo esc_attr( $value['field_name'] ); ?>[width]" <?php echo $disabled_attr; // WPCS: XSS ok. ?> id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3" value="<?php echo esc_attr( $width ); ?>" /> &times; <input name="<?php echo esc_attr( $value['id'] ); ?>[height]" <?php echo $disabled_attr; // WPCS: XSS ok. ?> id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3" value="<?php echo esc_attr( $height ); ?>" />px

							<label><input name="<?php echo esc_attr( $value['field_name'] ); ?>[crop]" <?php echo $disabled_attr; // WPCS: XSS ok. ?> id="<?php echo esc_attr( $value['id'] ); ?>-crop" type="checkbox" value="1" <?php checked( 1, $crop ); ?> /> <?php esc_html_e( 'Hard crop?', 'utm-source-tracker' ); ?></label>

							</td>
					</tr>
					<?php
					break;

				// Single page selects.
				case 'single_select_page':
					$args = array(
						'name'             => $value['field_name'],
						'id'               => $value['id'],
						'sort_column'      => 'menu_order',
						'sort_order'        => 'ASC',
						'show_option_none' => ' ',
						'class'            => $value['class'],
						'echo'             => false,
						'selected'         => absint( $value['value'] ),
						'post_status'      => 'publish,private,draft',
					);

					if ( isset( $value['args'] ) ) {
						$args = wp_parse_args( $value['args'], $args );
					}

					?>
					<tr valign="top" class="single_select_page"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
						<th scope="row" class="titledesc">
							<label><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
						</th>
						<td class="forminp">
							<?php echo str_replace( ' id=', " data-placeholder='" . esc_attr__( 'Select a page&hellip;', 'utm-source-tracker' ) . "' style='" . $value['css'] . "' class='" . $value['class'] . "' id=", wp_dropdown_pages( $args ) ); // WPCS: XSS ok. ?> <?php echo $description; // WPCS: XSS ok. ?>
						</td>
					</tr>
					<?php
					break;

				case 'single_select_page_with_search':
					$option_value = $value['value'];
					$page         = get_post( $option_value );

					if ( ! is_null( $page ) ) {
						$page                = get_post( $option_value );
						$option_display_name = sprintf(
							/* translators: 1: page name 2: page ID */
							__( '%1$s (ID: %2$s)', 'utm-source-tracker' ),
							$page->post_title,
							$option_value
						);
					}
					?>
					<tr valign="top" class="single_select_page"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<select
								name="<?php echo esc_attr( $value['field_name'] ); ?>"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>"
								<?php echo implode( ' ', $custom_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								data-placeholder="<?php esc_attr_e( 'Search for a page&hellip;', 'utm-source-tracker' ); ?>"
								data-allow_clear="true"
								data-exclude="<?php echo wc_esc_json( wp_json_encode( $value['args']['exclude'] ) ); ?>"
								>
								<option value=""></option>
								<?php if ( ! is_null( $page ) ) { ?>
									<option value="<?php echo esc_attr( $option_value ); ?>" selected="selected">
									<?php echo wp_strip_all_tags( $option_display_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</option>
								<?php } ?>
							</select> <?php echo $description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</td>
					</tr>
					<?php
					break;

				// Single country selects.
				case 'single_select_country':
					$country_setting = (string) $value['value'];

					if ( strstr( $country_setting, ':' ) ) {
						$country_setting = explode( ':', $country_setting );
						$country         = current( $country_setting );
						$state           = end( $country_setting );
					} else {
						$country = $country_setting;
						$state   = '*';
					}
					?>
					<tr valign="top"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
						</th>
						<td class="forminp"><select name="<?php echo esc_attr( $value['field_name'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>" data-placeholder="<?php esc_attr_e( 'Choose a country / region&hellip;', 'utm-source-tracker' ); ?>" aria-label="<?php esc_attr_e( 'Country / Region', 'utm-source-tracker' ); ?>" class="wc-enhanced-select">
							<?php WC()->countries->country_dropdown_options( $country, $state ); ?>
						</select> <?php echo $description; // WPCS: XSS ok. ?>
						</td>
					</tr>
					<?php
					break;

				// Country multiselects.
				case 'multi_select_countries':
					$selections = (array) $value['value'];

					if ( ! empty( $value['options'] ) ) {
						$countries = $value['options'];
					} else {
						$countries = WC()->countries->countries;
					}

					asort( $countries );
					?>
					<tr valign="top"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
						</th>
						<td class="forminp">
							<select multiple="multiple" name="<?php echo esc_attr( $value['field_name'] ); ?>[]" style="width:350px" data-placeholder="<?php esc_attr_e( 'Choose countries / regions&hellip;', 'utm-source-tracker' ); ?>" aria-label="<?php esc_attr_e( 'Country / Region', 'utm-source-tracker' ); ?>" class="wc-enhanced-select">
								<?php
								if ( ! empty( $countries ) ) {
									foreach ( $countries as $key => $val ) {
										echo '<option value="' . esc_attr( $key ) . '"' . wc_selected( $key, $selections ) . '>' . esc_html( $val ) . '</option>'; // WPCS: XSS ok.
									}
								}
								?>
							</select> <?php echo ( $description ) ? $description : ''; // WPCS: XSS ok. ?> <br /><a class="select_all button" href="#"><?php esc_html_e( 'Select all', 'utm-source-tracker' ); ?></a> <a class="select_none button" href="#"><?php esc_html_e( 'Select none', 'utm-source-tracker' ); ?></a>
						</td>
					</tr>
					<?php
					break;

				// Days/months/years selector.
				case 'relative_date_selector':
					$periods      = array(
						'days'   => __( 'Day(s)', 'utm-source-tracker' ),
						'weeks'  => __( 'Week(s)', 'utm-source-tracker' ),
						'months' => __( 'Month(s)', 'utm-source-tracker' ),
						'years'  => __( 'Year(s)', 'utm-source-tracker' ),
					);
					$option_value = wc_parse_relative_date_option( $value['value'] );
					?>
					<tr valign="top"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
						</th>
						<td class="forminp">
						<input
								name="<?php echo esc_attr( $value['field_name'] ); ?>[number]"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								type="number"
								style="width: 80px;"
								value="<?php echo esc_attr( $option_value['number'] ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>"
								placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
								step="1"
								min="1"
								<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
							/>&nbsp;
							<select name="<?php echo esc_attr( $value['field_name'] ); ?>[unit]" style="width: auto;">
								<?php
								foreach ( $periods as $value => $label ) {
									echo '<option value="' . esc_attr( $value ) . '"' . selected( $option_value['unit'], $value, false ) . '>' . esc_html( $label ) . '</option>';
								}
								?>
							</select> <?php echo ( $description ) ? $description : ''; // WPCS: XSS ok. ?>
						</td>
					</tr>
					<?php
					break;

				// Default: run an action.
				default:
					do_action( 'woocommerce_admin_field_' . $value['type'], $value );
					break;
			}
		}
	}

	/**
	 * Get a setting from the settings API.
	 *
	 * @param string $option_name Option name.
	 * @param mixed  $default     Default value.
	 * @return mixed
	 */
	public static function get_option( $option_name, $default = '' ) {
		if ( ! $option_name ) {
			return $default;
		}

		// Array value.
		if ( strstr( $option_name, '[' ) ) {

			parse_str( $option_name, $option_array );

			// Option name is first key.
			$option_name = current( array_keys( $option_array ) );

			// Get value.
			$option_values = get_option( $option_name, '' );

			$key = key( $option_array[ $option_name ] );

			if ( isset( $option_values[ $key ] ) ) {
				$option_value = $option_values[ $key ];
			} else {
				$option_value = null;
			}
		} else {
			// Single value.
			$option_value = get_option( $option_name, null );
		}

		if ( is_array( $option_value ) ) {
			$option_value = wp_unslash( $option_value );
		} elseif ( ! is_null( $option_value ) ) {
			$option_value = stripslashes( $option_value );
		}

		return ( null === $option_value ) ? $default : $option_value;
	}

	/**
	 * Helper function to get the formatted description and tip HTML for a
	 * given form field. Plugins can call this when implementing their own custom
	 * settings types.
	 *
	 * @param  array $value The form field value array.
	 * @return array The description and tip as a 2 element array.
	 */
	public static function get_field_description( $value ) {
		$description  = '';
		$tooltip_html = '';

		if ( true === $value['desc_tip'] ) {
			$tooltip_html = $value['desc'];
		} elseif ( ! empty( $value['desc_tip'] ) ) {
			$description  = $value['desc'];
			$tooltip_html = $value['desc_tip'];
		} elseif ( ! empty( $value['desc'] ) ) {
			$description = $value['desc'];
		}

		$description_is_error    = $value['description_is_error'] ?? false;
		$extra_description_style = $description_is_error ? " style='color:red'" : '';

		if ( $description && in_array( $value['type'], array( 'textarea', 'radio' ), true ) ) {
			$description = '<p style="margin-top:0">' . wp_kses_post( $description ) . '</p>';
		} elseif ( $description && in_array( $value['type'], array( 'checkbox' ), true ) ) {
			$description = wp_kses_post( $description );
		} elseif ( $description ) {
			$description = '<p class="description"' . $extra_description_style . '>' . wp_kses_post( $description ) . '</p>';
		}

		if ( $tooltip_html && in_array( $value['type'], array( 'checkbox' ), true ) ) {
			$tooltip_html = '<p class="description"' . $extra_description_style . '>' . $tooltip_html . '</p>';
		} elseif ( $tooltip_html ) {
			$tooltip_html = self::field_help_tip( $tooltip_html );
		}

		return array(
			'description'  => $description,
			'tooltip_html' => $tooltip_html,
		);
	}

	/**
	 * Display a WooCommerce help tip.
	 *
	 * @since  2.5.0
	 *
	 * @param  string $tip        Help tip text.
	 * @param  bool   $allow_html Allow sanitized HTML if true or escape.
	 * @return string
	 */
	public static function field_help_tip( $tip, $allow_html = false ) {
		if ( $allow_html ) {
			$sanitized_tip = self::sanitize_tooltip( $tip );
		} else {
			$sanitized_tip = esc_attr( $tip );
		}

		/**
		 * Filter the help tip.
		 *
		 * @since 1.0.0
		 *
		 * @param string $tip_html       Help tip HTML.
		 * @param string $sanitized_tip  Sanitized help tip text.
		 * @param string $tip            Original help tip text.
		 * @param bool   $allow_html     Allow sanitized HTML if true or escape.
		 *
		 * @return string
		 */
		return apply_filters( 'field_help_tip', '<span class="field-help-tip" tabindex="0" aria-label="' . $sanitized_tip . '" data-tip="' . $sanitized_tip . '"></span>', $sanitized_tip, $tip, $allow_html );
	}

	/**
	 * Sanitize a string destined to be a tooltip.
	 *
	 * @since  1.0.0 Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
	 * @param  string $var Data to sanitize.
	 * @return string
	 */
	public static function sanitize_tooltip( $var ) {
		return htmlspecialchars(
			wp_kses(
				html_entity_decode( $var ?? '' ),
				array(
					'br'     => array(),
					'em'     => array(),
					'strong' => array(),
					'small'  => array(),
					'span'   => array(),
					'ul'     => array(),
					'li'     => array(),
					'ol'     => array(),
					'p'      => array(),
				)
			)
		);
	}

	/**
	 * Save admin fields.
	 *
	 * Loops through the woocommerce options array and outputs each field.
	 *
	 * @param array $options Options array to output.
	 * @param array $data    Optional. Data to use for saving. Defaults to $_POST.
	 * @return bool
	 */
	public static function save_fields( $options, $data = null ) {
		if ( is_null( $data ) ) {
			$data = $_POST; // WPCS: input var okay, CSRF ok.
		}
		if ( empty( $data ) ) {
			return false;
		}

		// Options to update will be stored here and saved later.
		$update_options   = array();
		$autoload_options = array();

		// Loop options and get values to save.
		foreach ( $options as $option ) {
			if ( ! isset( $option['id'] ) || ! isset( $option['type'] ) || ( isset( $option['is_option'] ) && false === $option['is_option'] ) ) {
				continue;
			}

			$option_name = $option['field_name'] ?? $option['id'];

			// Get posted value.
			if ( strstr( $option_name, '[' ) ) {
				parse_str( $option_name, $option_name_array );
				$option_name  = current( array_keys( $option_name_array ) );
				$setting_name = key( $option_name_array[ $option_name ] );
				$raw_value    = isset( $data[ $option_name ][ $setting_name ] ) ? wp_unslash( $data[ $option_name ][ $setting_name ] ) : null;
			} else {
				$setting_name = '';
				$raw_value    = isset( $data[ $option_name ] ) ? wp_unslash( $data[ $option_name ] ) : null;
			}

			// Format the value based on option type.
			switch ( $option['type'] ) {
				case 'checkbox':
					$value = '1' === $raw_value || 'yes' === $raw_value ? 'yes' : 'no';
					break;
				case 'textarea':
					$value = wp_kses_post( trim( $raw_value ) );
					break;
				case 'multiselect':
				case 'multi_select_countries':
					// $value = array_filter( array_map( 'wc_clean', (array) $raw_value ) );
					$value = array_filter( array_map( 'map_deep', (array) $raw_value ) );
					break;
				case 'image_width':
					$value = array();
					if ( isset( $raw_value['width'] ) ) {
						//$value['width']  = wc_clean( $raw_value['width'] );
						$value['width']  = $raw_value['width'];
						// $value['height'] = wc_clean( $raw_value['height'] );
						$value['height'] = $raw_value['height'];
						$value['crop']   = isset( $raw_value['crop'] ) ? 1 : 0;
					} else {
						$value['width']  = $option['default']['width'];
						$value['height'] = $option['default']['height'];
						$value['crop']   = $option['default']['crop'];
					}
					break;
				case 'select':
					$allowed_values = empty( $option['options'] ) ? array() : array_map( 'strval', array_keys( $option['options'] ) );
					if ( empty( $option['default'] ) && empty( $allowed_values ) ) {
						$value = null;
						break;
					}
					$default = ( empty( $option['default'] ) ? $allowed_values[0] : $option['default'] );
					$value   = in_array( $raw_value, $allowed_values, true ) ? $raw_value : $default;
					break;
				case 'relative_date_selector':
					// $value = wc_parse_relative_date_option( $raw_value );
					$value = $raw_value;
					break;
				default:
					// $value = wc_clean( $raw_value );
					$value = $raw_value;
					break;
			}

			/**
			 * Fire an action when a certain 'type' of field is being saved.
			 *
			 * @deprecated 2.4.0 - doesn't allow manipulation of values!
			 */
			if ( has_action( 'woocommerce_update_option_' . sanitize_title( $option['type'] ) ) ) {
				// wc_deprecated_function( 'The woocommerce_update_option_X action', '2.4.0', 'woocommerce_admin_settings_sanitize_option filter' );
				do_action( 'woocommerce_update_option_' . sanitize_title( $option['type'] ), $option );
				continue;
			}

			/**
			 * Sanitize the value of an option.
			 *
			 * @since 2.4.0
			 */
			$value = apply_filters( 'woocommerce_admin_settings_sanitize_option', $value, $option, $raw_value );

			/**
			 * Sanitize the value of an option by option name.
			 *
			 * @since 2.4.0
			 */
			$value = apply_filters( "woocommerce_admin_settings_sanitize_option_$option_name", $value, $option, $raw_value );

			if ( is_null( $value ) ) {
				continue;
			}

			// Check if option is an array and handle that differently to single values.
			if ( $option_name && $setting_name ) {
				if ( ! isset( $update_options[ $option_name ] ) ) {
					$update_options[ $option_name ] = get_option( $option_name, array() );
				}
				if ( ! is_array( $update_options[ $option_name ] ) ) {
					$update_options[ $option_name ] = array();
				}
				$update_options[ $option_name ][ $setting_name ] = $value;
			} else {
				$update_options[ $option_name ] = $value;
			}

			$autoload_options[ $option_name ] = isset( $option['autoload'] ) ? (bool) $option['autoload'] : true;

			/**
			 * Fire an action before saved.
			 *
			 * @deprecated 2.4.0 - doesn't allow manipulation of values!
			 */
			do_action( 'woocommerce_update_option', $option );
		}

		// Save all options in our array.
		foreach ( $update_options as $name => $value ) {
			update_option( $name, $value, $autoload_options[ $name ] ? 'yes' : 'no' );
		}

		return true;
	}

}
