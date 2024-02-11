<?php

namespace UTMSourceTracker\Admin;

use UTMSourceTracker\Lib;

defined( 'ABSPATH' ) || exit;

/**
 * Class Settings.
 *
 * @since   1.0.0
 * @package WooCommerceDonationManager\Admin
 */
class Settings extends Lib\Settings {

	/**
	 * Get settings tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_tabs() {
		$tabs = array(
			'general'  => __( 'General', 'utm-source-tracker' ),
			'advanced' => __( 'Advanced', 'utm-source-tracker' ),
		);

		return apply_filters( 'utm_source_tracker_settings_tabs', $tabs );
	}

	/**
	 * Get settings.
	 *
	 * @param string $tab Current tab.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_settings( $tab ) {
		$settings = array();

		switch ( $tab ) {
			case 'general':
				$settings = array(
					array(
						'title' => __( 'General Settings', 'utm-source-tracker' ),
						'type'  => 'title',
						'desc'  => __( 'The following options are the plugin general settings. Theses options affect how the plugin will work.', 'utm-source-tracker' ),
						'id'    => 'general_options',
						'row_class' => 'settings-row-class'
					),
					array(
						'title'    => __( 'Text field', 'utm-source-tracker' ),
						'id'       => 'utmst_text_field',
						'desc'     => __( 'This is the text field description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the text field description tip', 'utm-source-tracker' ),
						'type'     => 'text',
						'placeholder'  => 'Text field (Placeholder)',
						'default'  => 'Text field (Default)',
						'row_class' => 'utmst-text-field',
					),
					array(
						'title'    => __( 'Password', 'utm-source-tracker' ),
						'id'       => 'utmst_password',
						'desc'     => __( 'This is the password description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the password description tip', 'utm-source-tracker' ),
						'type'     => 'password',
						'placeholder'  => 'Password (Placeholder)',
						//'default'  => 'Password (Default)',
					),
					array(
						'title'   => __( 'Checkbox', 'utm-source-tracker' ),
						'desc'    => __( 'Enable checkbox', 'utm-source-tracker' ),
						'desc_tip'    => __( 'Enable checkbox description tip', 'utm-source-tracker' ),
						'id'      => 'utmst_checkbox',
						'default' => 'yes', // Or use 'no' instead.
						'type'    => 'checkbox',
					),
					array(
						'title'   => __( 'Image Effect', 'utm-source-tracker' ),
						'desc'    => __( 'Choose the effect for the products images.', 'utm-source-tracker' ),
						'id'      => 'utmst_image_effect',
						'type'    => 'select',
						'options' => array(
							'flip'               => __( 'Flip', 'utm-source-tracker' ),
							'fade'               => __( 'Fade on Hover', 'utm-source-tracker' ),
							'enlarge'            => __( 'Enlarge', 'utm-source-tracker' ),
							'picture_in_picture' => __( 'Picture in Picture', 'utm-source-tracker' ),
							'slide'              => __( 'Slide', 'utm-source-tracker' ),
						),
						'default' => 'flip',
					),
					array(
						'title'             => __( 'Image Count', 'utm-source-tracker' ),
						'desc'              => __( 'Enter how many images should be displayed. Use -1 to display all available.', 'utm-source-tracker' ),
						'id'                => 'utmst_slide_img_count',
						'default'           => '-1',
						'type'              => 'number',
						'custom_attributes' => array(
							'data-cond-id'    => 'utmst_image_effect',
							'data-cond-value' => 'slide',
						),
					),
					array(
						'title'             => __( 'Autoplay', 'utm-source-tracker' ),
						'desc'              => __( 'Select the slider autoplay setting.', 'utm-source-tracker' ),
						'id'                => 'utmst_slide_img_autoplay',
						'type'              => 'select',
						'options'           => array(
							'yes' => __( 'Autoplay on Hover', 'utm-source-tracker' ),
							'no'  => __( 'Forced Autoplay', 'utm-source-tracker' ),
						),
						'default'           => 'yes',
						'custom_attributes' => array(
							'data-cond-id'    => 'utmst_image_effect',
							'data-cond-value' => 'slide',
						),
					),

					array(
						'title'    => __( 'Skip cart', 'utm-source-tracker' ),
						'desc'     => __( 'Skip cart.', 'utm-source-tracker' ),
						'desc_tip' => __( 'This will redirect donors to the cart page after adding a UTM source tracker product to the cart item.', 'utm-source-tracker' ),
						'id'       => 'utmst_skip_cart',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'general_options',
					),
				);
				break;
			case 'advanced':
				$settings = array(
					array(
						'title' => __( 'Advanced Settings', 'utm-source-tracker' ),
						'type'  => 'title',
						'desc'  => __( 'The following options are the plugin advanced settings.', 'utm-source-tracker' ),
						'id'    => 'advanced_options',
					),
					array(
						'title'    => __( 'Delete plugin data', 'utm-source-tracker' ),
						'desc'     => __( 'Delete plugin data.', 'utm-source-tracker' ),
						'desc_tip' => __( 'Enabling this will delete all the data while uninstalling the plugin.', 'utm-source-tracker' ),
						'id'       => 'utmst_delete_data',
						'default'  => 'no',
						'type'     => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'advanced_options',
					),
				);
				break;
		}

		/**
		 * Filter the settings for the plugin.
		 *
		 * @param array $settings The settings.
		 * @param string $tab The current tab.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'utm_source_tracker_get_settings_' . $tab, $settings );
	}

	/**
	 * Output settings form.
	 *
	 * @param array $settings Settings.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function output_form( $settings ) {
		$current_tab = $this->get_current_tab();
		/**
		 * Action hook to output settings form.
		 *
		 * @since 1.0.0
		 */
		do_action( 'utm_source_tracker_settings_' . $current_tab );
		parent::output_form( $settings );
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
		parent::output_tabs( $tabs );
		if ( utm_source_tracker()->get_data( 'docs_url' ) ) {
			printf( '<a href="%s" class="nav-tab" target="_blank">%s</a>', esc_url( utm_source_tracker()->get_data( 'docs_url' ) ), esc_html__( 'Documentation', 'utm-source-tracker' ) );
		}
	}
}
