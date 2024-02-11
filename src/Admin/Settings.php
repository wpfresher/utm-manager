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
						'title'    => __( 'Add to cart button text', 'utm-source-tracker' ),
						'id'       => 'utmst_add_to_cart_btn_text',
						'desc'     => __( 'Enter the add to cart button text. This will be applicable only for campaigns or UTM source tracker product types.', 'utm-source-tracker' ),
						'desc_tip' => __( 'Enter the add to cart button text. This will be applicable only for campaigns or UTM source tracker product types.', 'utm-source-tracker' ),
						'type'     => 'text',
						'default'  => 'UTM Source Tracker Now',
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
