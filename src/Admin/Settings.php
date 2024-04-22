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
						'title'       => __( 'Text field', 'utm-manager' ), // Required if visible directly.
						'id'          => 'utmm_text_field', // Required.
						'desc'        => __( 'This is the text field description', 'utm-manager' ),
						'desc_tip'    => __( 'This is the text field description tip', 'utm-manager' ),
						'type'        => 'text', // Required.
						'placeholder' => 'Text field (Placeholder)',
						'default'     => 'Text field (Default)',
						'row_class'   => 'utmm-text-field',
						// 'field_name' => 'field_name', // Empty/Nothing will be overridden by 'id'.
						'css'         => 'width:300px;', // Style for the input field.
						// 'value' => 'Text field (Value)', // Nothing will be overridden by 'default'. Note: Empty not applicable for overridden.
						'class'       => 'input-field-class',
						'suffix'      => 'Field suffix text.',
						// 'custom_attributes' => array( // Custom attributes are only allowed for checkbox/select field types.
						// 'data-cond-id'    => 'field_data_condition_id',
						// 'data-cond-value' => 'field_data_condition_value',
						// ),
					),
					array(
						'title'       => __( 'Password', 'utm-manager' ),
						'id'          => 'utmm_password',
						'desc'        => __( 'This is the password description', 'utm-manager' ),
						'desc_tip'    => __( 'This is the password description tip', 'utm-manager' ),
						'type'        => 'password',
						'placeholder' => 'Enter password',
						'css'         => 'width:300px;',
					),
					array(
						'title'       => __( 'Datetime', 'utm-manager' ),
						'id'          => 'utmm_datetime',
						'desc'        => __( 'This is the datetime description', 'utm-manager' ),
						'desc_tip'    => __( 'This is the datetime description tip', 'utm-manager' ),
						'type'        => 'datetime',
						'placeholder' => '12/06/2024 12:00 AM',
						'css'         => 'width:300px;',
					),
					array(
						'title'    => __( 'Datetime-local', 'utm-manager' ),
						'id'       => 'utmm_datetime_local',
						'desc'     => __( 'This is the datetime-local description', 'utm-manager' ),
						'desc_tip' => __( 'This is the datetime-local description tip', 'utm-manager' ),
						'type'     => 'datetime-local',
						'css'      => 'width:300px;',
					),
					array(
						'title'    => __( 'Date', 'utm-manager' ),
						'id'       => 'utmm_date',
						'desc'     => __( 'This is the date description', 'utm-manager' ),
						'desc_tip' => __( 'This is the date description tip', 'utm-manager' ),
						'type'     => 'date',
						'css'      => 'width:300px;',
					),
					array(
						'title'    => __( 'Month', 'utm-manager' ),
						'id'       => 'utmm_month',
						'desc'     => __( 'This is the month description', 'utm-manager' ),
						'desc_tip' => __( 'This is the month description tip', 'utm-manager' ),
						'type'     => 'month',
						'css'      => 'width:300px;',
					),
					array(
						'title'    => __( 'Time', 'utm-manager' ),
						'id'       => 'utmm_time',
						'desc'     => __( 'This is the time description', 'utm-manager' ),
						'desc_tip' => __( 'This is the time description tip', 'utm-manager' ),
						'type'     => 'time',
						'css'      => 'width:300px;',
					),
					array(
						'title'    => __( 'Week', 'utm-manager' ),
						'id'       => 'utmm_week',
						'desc'     => __( 'This is the week description', 'utm-manager' ),
						'desc_tip' => __( 'This is the week description tip', 'utm-manager' ),
						'type'     => 'week',
						'css'      => 'width:300px;',
					),
					array(
						'title'       => __( 'Number', 'utm-manager' ),
						'id'          => 'utmm_number',
						'desc'        => __( 'This is the number description', 'utm-manager' ),
						'desc_tip'    => __( 'This is the number description tip', 'utm-manager' ),
						'placeholder' => '123456...',
						'type'        => 'number',
						'css'         => 'width:300px;',
					),
					array(
						'title'       => __( 'Email', 'utm-manager' ),
						'id'          => 'utmm_email',
						'desc'        => __( 'This is the email description', 'utm-manager' ),
						'desc_tip'    => __( 'This is the email description tip', 'utm-manager' ),
						'placeholder' => 'admin@domain.com',
						'type'        => 'email',
						'css'         => 'width:300px;',
					),
					array(
						'title'       => __( 'URL', 'utm-manager' ),
						'id'          => 'utmm_url',
						'desc'        => __( 'This is the url description', 'utm-manager' ),
						'desc_tip'    => __( 'This is the url description tip', 'utm-manager' ),
						'placeholder' => 'https://domain.com',
						'type'        => 'url',
						'css'         => 'width:300px;',
					),
					array(
						'title'       => __( 'Tel', 'utm-manager' ),
						'id'          => 'utmm_tel',
						'desc'        => __( 'This is the tel description', 'utm-manager' ),
						'desc_tip'    => __( 'This is the tel description tip', 'utm-manager' ),
						'placeholder' => '+880 1700 112233',
						'type'        => 'tel',
						'css'         => 'width:300px;',
					),
					// Color filed only works with WordPress iris color picker.
					array(
						'title'   => __( 'Color Picker', 'utm-manager' ),
						'desc'    => __( 'Choose the color.', 'utm-manager' ),
						'id'      => 'utmm_color',
						'type'    => 'color',
						'default' => '#cccccc',
						'css'     => 'width:280px;',
					),
					array(
						'title'       => __( 'Text field', 'utm-manager' ),
						'id'          => 'utmm_textarea',
						'desc'        => __( 'This is the text field description', 'utm-manager' ),
						'desc_tip'    => __( 'This is the text field description tip', 'utm-manager' ),
						'type'        => 'textarea',
						'placeholder' => 'Textarea field (Placeholder)',
						'css'         => 'width:300px;',
					),
					// Select input.
					array(
						'title'   => __( 'Select', 'utm-manager' ),
						'desc'    => __( 'Chose an option from the list.', 'utm-manager' ),
						'id'      => 'utmm_select',
						'type'    => 'select',
						'options' => array(
							'default' => __( 'Select an option', 'utm-manager' ),
							'item_1'  => __( 'Item 1', 'utm-manager' ),
							'item_2'  => __( 'Item 2', 'utm-manager' ),
							'item_3'  => __( 'Item 3 (It will display the conditional field)', 'utm-manager' ),
						),
						'default' => 'default',
						'css'     => 'width:300px;',
					),
					// Bellow is the data condition field for the above select field. This only be visible if the above select field has selected value to item_3.
					// Requirements: data-cond-id must be the dependent field id and data-cond-value must be equal to the option value.
					array(
						'title'             => __( 'Select conditional field', 'utm-manager' ),
						'desc'              => __( 'Enter the text. This field is visible only for "Item 3".', 'utm-manager' ),
						'id'                => 'utmm_conditional_field_for_select',
						'default'           => 'This field is visible only for "Item 3"',
						'type'              => 'text',
						'css'               => 'width:300px;',
						'custom_attributes' => array(
							'data-cond-id'    => 'utmm_select',
							'data-cond-value' => 'item_3',
						),
					),
					// Multiselect input.
					array(
						'title'   => __( 'Multiselect', 'utm-manager' ),
						'desc'    => __( 'Chose multiple options from the list. Tip: Use control/command then click to select multiple options.', 'utm-manager' ),
						'id'      => 'utmm_multiselect',
						'type'    => 'multiselect',
						'options' => array(
							'default' => __( 'Select multiple options', 'utm-manager' ),
							'item_1'  => __( 'Item 1', 'utm-manager' ),
							'item_2'  => __( 'Item 2', 'utm-manager' ),
							'item_3'  => __( 'Item 3', 'utm-manager' ),
						),
						'css'     => 'width:300px;',
					),
					// Radio input.
					array(
						'title'       => __( 'Radio', 'utm-manager' ),
						'desc'        => __( 'Select radio fields.', 'utm-manager' ),
						'desc_tip'    => __( 'Select radio fields', 'utm-manager' ),
						'id'          => 'utmm_radio',
						'type'        => 'radio',
						'options'     => array(
							'default' => __( 'Default options', 'utm-manager' ),
							'item_1'  => __( 'Item 1', 'utm-manager' ),
							'item_2'  => __( 'Item 2', 'utm-manager' ),
							'item_3'  => __( 'Item 3', 'utm-manager' ),
						),
						'default'     => 'default',
						'disabled'    => array(
							'item_1',
						),
						'desc_at_end' => false, // Boolean & Default is false.
					),
					// Checkbox input.
					array(
						'title'    => __( 'Checkbox', 'utm-manager' ),
						'desc'     => __( 'Enable checkbox', 'utm-manager' ),
						'desc_tip' => __( 'Enable checkbox description tip.', 'utm-manager' ),
						'id'       => 'utmm_checkbox',
						'default'  => 'yes', // Or use 'no' instead.
						'type'     => 'checkbox',
					),
					// Conditional field for the above Checkbox input field.
					array(
						'title'             => __( 'Checkbox conditional field', 'utm-manager' ),
						'desc'              => __( 'Enter the text. This field is visible only if the above checkbox is checked.', 'utm-manager' ),
						'id'                => 'utmm_conditional_field_for_checkbox',
						'default'           => 'This field is visible only for "Item 3"',
						'type'              => 'text',
						'css'               => 'width:300px;',
						'custom_attributes' => array(
							'data-cond-id'    => 'utmm_checkbox',
							'data-cond-value' => 'yes',
						),
					),
					// Create custom fields by using do_action.
					array(
						'title' => __( 'Custom field', 'utm-manager' ),
						'type'  => 'custom_field_type',
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
