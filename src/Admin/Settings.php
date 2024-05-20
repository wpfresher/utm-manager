<?php

namespace UTMManager\Admin;

use UTMManager\Lib;

defined( 'ABSPATH' ) || exit;

/**
 * Class Settings.
 *
 * @since   1.0.0
 * @package UTMManager\Admin
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
			'general'        => __( 'General', 'utm-manager' ),
			'utm_parameters' => __( 'UTM Parameters', 'utm-manager' ),
			'advanced'       => __( 'Advanced', 'utm-manager' ),
		);

		return apply_filters( 'utm_manager_settings_tabs', $tabs );
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
						'title'     => __( 'General Settings', 'utm-manager' ),
						'type'      => 'title',
						'desc'      => __( 'The following options are the plugin general settings. Theses options affect how the plugin will work.', 'utm-manager' ),
						'id'        => 'general_options',
						'row_class' => 'settings-row-class',
					),
					array(
						'title'    => __( 'Delete leads automatically', 'utm-manager' ),
						'desc'     => __( 'Delete older leads automatically', 'utm-manager' ),
						'desc_tip' => __( 'Enable to automatically delete the older leads.', 'utm-manager' ),
						'id'       => 'utmm_is_auto_delete_leads',
						'default'  => 'no',
						'type'     => 'checkbox',
					),
					array(
						'title'             => __( 'Select the frequency', 'utm-manager' ),
						'desc'              => __( 'Chose an option from the list to delete leads automatically.', 'utm-manager' ),
						'id'                => 'utmm_auto_delete_frequency',
						'type'              => 'select',
						'options'           => array(
							'default' => __( 'Select an option', 'utm-manager' ),
							'daily'   => __( 'Daily', 'utm-manager' ),
							'weekly'  => __( 'Weekly', 'utm-manager' ),
							'monthly' => __( 'Monthly', 'utm-manager' ),
							'yearly'  => __( 'Yearly', 'utm-manager' ),
						),
						'default'           => 'default',
						'css'               => 'width:300px;',
						'custom_attributes' => array(
							'data-cond-id'    => 'utmm_is_auto_delete_leads',
							'data-cond-value' => 'yes',
						),
					),
					array(
						'type' => 'sectionend',
						'id'   => 'general_options',
					),
				);
				break;

			case 'utm_parameters':
				$settings = array(
					array(
						'title'     => __( 'Setup UTM Parameter', 'utm-manager' ),
						'type'      => 'title',
						'desc'      => __( 'The following options are the utm parameter settings.', 'utm-manager' ),
						'id'        => 'utm_parameters_options',
						'row_class' => 'settings-row-class',
					),
					array(
						'title'    => __( 'UTM id', 'utm-manager' ),
						'desc'     => __( 'Enable utm_id', 'utm-manager' ),
						'desc_tip' => __( 'Enable to track the utm_id URL parameter as a lead.', 'utm-manager' ),
						'id'       => 'utmm_utm_id',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'title'    => __( 'UTM source', 'utm-manager' ),
						'desc'     => __( 'Enable utm_source', 'utm-manager' ),
						'desc_tip' => __( 'Enable to track the utm_source URL parameter as a lead.', 'utm-manager' ),
						'id'       => 'utmm_utm_source',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'title'    => __( 'UTM medium', 'utm-manager' ),
						'desc'     => __( 'Enable utm_medium', 'utm-manager' ),
						'desc_tip' => __( 'Enable to track the utm_medium URL parameter as a lead.', 'utm-manager' ),
						'id'       => 'utmm_utm_medium',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'title'    => __( 'UTM campaign', 'utm-manager' ),
						'desc'     => __( 'Enable utm_campaign', 'utm-manager' ),
						'desc_tip' => __( 'Enable to track the utm_campaign URL parameter as a lead.', 'utm-manager' ),
						'id'       => 'utmm_utm_campaign',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'title'    => __( 'UTM term', 'utm-manager' ),
						'desc'     => __( 'Enable utm_term', 'utm-manager' ),
						'desc_tip' => __( 'Enable to track the utm_term URL parameter as a lead.', 'utm-manager' ),
						'id'       => 'utmm_utm_term',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'title'    => __( 'UTM content', 'utm-manager' ),
						'desc'     => __( 'Enable utm_content', 'utm-manager' ),
						'desc_tip' => __( 'Enable to track the utm_content URL parameter as a lead.', 'utm-manager' ),
						'id'       => 'utmm_utm_content',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					// TODO: Bellow are the pro feature.
					array(
						'title'    => __( 'Custom parameters', 'utm-manager' ),
						'desc'     => __( 'Enable custom URL parameters', 'utm-manager' ),
						'desc_tip' => __( 'Enable custom URL parameters.', 'utm-manager' ),
						'id'       => 'utmm_custom_parameters',
						'default'  => 'no',
						'type'     => 'checkbox',
					),
					array(
						'title'             => __( 'URL parameters', 'utm-manager' ),
						'id'                => 'utmm_url_parameters',
						'desc'              => __( 'Enter the custom URL parameters per line or separated by comma.', 'utm-manager' ),
						'desc_tip'          => __( 'Enter the custom URL parameters per line or separated by comma.', 'utm-manager' ),
						'type'              => 'textarea',
						'placeholder'       => 'custom_source,custom_medium',
						'css'               => 'width:300px;',
						'custom_attributes' => array(
							'data-cond-id'    => 'utmm_custom_parameters',
							'data-cond-value' => 'yes',
						),
					),
					array(
						'type' => 'sectionend',
						'id'   => 'utm_parameters_options',
					),
				);
				break;

			case 'advanced':
				$settings = array(
					array(
						'title' => __( 'Advanced Settings', 'utm-manager' ),
						'type'  => 'title',
						'desc'  => __( 'The following options are the plugin advanced settings.', 'utm-manager' ),
						'id'    => 'advanced_options',
					),
					array(
						'title'    => __( 'Delete plugin data', 'utm-manager' ),
						'desc'     => __( 'Delete plugin data.', 'utm-manager' ),
						'desc_tip' => __( 'Enabling this will delete all the data while uninstalling the plugin.', 'utm-manager' ),
						'id'       => 'utmm_delete_data',
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
		return apply_filters( 'utm_manager_get_settings_' . $tab, $settings );
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
		do_action( 'utm_manager_settings_' . $current_tab );
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
		if ( utm_manager()->get_data( 'docs_url' ) ) {
			printf( '<a href="%s" class="nav-tab" target="_blank">%s</a>', esc_url( utm_manager()->get_data( 'docs_url' ) ), esc_html__( 'Documentation', 'utm-manager' ) );
		}
	}
}
