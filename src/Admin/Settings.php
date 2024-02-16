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
						'title'    => __( 'Text field', 'utm-source-tracker' ), // Required if visible directly.
						'id'       => 'utmst_text_field', // Required.
						'desc'     => __( 'This is the text field description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the text field description tip', 'utm-source-tracker' ),
						'type'     => 'text', // Required.
						'placeholder'  => 'Text field (Placeholder)',
						'default'  => 'Text field (Default)',
						'row_class' => 'utmst-text-field',
						// 'field_name' => 'field_name', // Empty/Nothing will be overridden by 'id'.
						'css' => 'width:300px;', // Style for the input field.
						// 'value' => 'Text field (Value)', // Nothing will be overridden by 'default'. Note: Empty not applicable for overridden.
						'class' => 'input-field-class',
						'suffix' => 'Field suffix text.',
						// 'custom_attributes' => array( // Custom attributes are only allowed for checkbox/select field types.
						// 	'data-cond-id'    => 'field_data_condition_id',
						// 	'data-cond-value' => 'field_data_condition_value',
						// ),
					),
					array(
						'title'    => __( 'Password', 'utm-source-tracker' ),
						'id'       => 'utmst_password',
						'desc'     => __( 'This is the password description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the password description tip', 'utm-source-tracker' ),
						'type'     => 'password',
						'placeholder'  => 'Enter password',
						'css' => 'width:300px;',
					),
					array(
						'title'    => __( 'Datetime', 'utm-source-tracker' ),
						'id'       => 'utmst_datetime',
						'desc'     => __( 'This is the datetime description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the datetime description tip', 'utm-source-tracker' ),
						'type'     => 'datetime',
						'placeholder'  => '12/06/2024 12:00 AM',
						'css' => 'width:300px;',
					),
					array(
						'title'    => __( 'Datetime-local', 'utm-source-tracker' ),
						'id'       => 'utmst_datetime_local',
						'desc'     => __( 'This is the datetime-local description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the datetime-local description tip', 'utm-source-tracker' ),
						'type'     => 'datetime-local',
						'css' => 'width:300px;',
					),
					array(
						'title'    => __( 'Date', 'utm-source-tracker' ),
						'id'       => 'utmst_date',
						'desc'     => __( 'This is the date description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the date description tip', 'utm-source-tracker' ),
						'type'     => 'date',
						'css' => 'width:300px;',
					),
					array(
						'title'    => __( 'Month', 'utm-source-tracker' ),
						'id'       => 'utmst_month',
						'desc'     => __( 'This is the month description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the month description tip', 'utm-source-tracker' ),
						'type'     => 'month',
						'css' => 'width:300px;',
					),
					array(
						'title'    => __( 'Time', 'utm-source-tracker' ),
						'id'       => 'utmst_time',
						'desc'     => __( 'This is the time description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the time description tip', 'utm-source-tracker' ),
						'type'     => 'time',
						'css' => 'width:300px;',
					),
					array(
						'title'    => __( 'Week', 'utm-source-tracker' ),
						'id'       => 'utmst_week',
						'desc'     => __( 'This is the week description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the week description tip', 'utm-source-tracker' ),
						'type'     => 'week',
						'css' => 'width:300px;',
					),
					array(
						'title'    => __( 'Number', 'utm-source-tracker' ),
						'id'       => 'utmst_number',
						'desc'     => __( 'This is the number description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the number description tip', 'utm-source-tracker' ),
						'placeholder' => '123456...',
						'type'     => 'number',
						'css' => 'width:300px;',
					),
					array(
						'title'    => __( 'Email', 'utm-source-tracker' ),
						'id'       => 'utmst_email',
						'desc'     => __( 'This is the email description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the email description tip', 'utm-source-tracker' ),
						'placeholder' => 'admin@domain.com',
						'type'     => 'email',
						'css' => 'width:300px;',
					),
					array(
						'title'    => __( 'URL', 'utm-source-tracker' ),
						'id'       => 'utmst_url',
						'desc'     => __( 'This is the url description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the url description tip', 'utm-source-tracker' ),
						'placeholder' => 'https://domain.com',
						'type'     => 'url',
						'css' => 'width:300px;',
					),
					array(
						'title'    => __( 'Tel', 'utm-source-tracker' ),
						'id'       => 'utmst_tel',
						'desc'     => __( 'This is the tel description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the tel description tip', 'utm-source-tracker' ),
						'placeholder' => '+880 1700 112233',
						'type'     => 'tel',
						'css' => 'width:300px;',
					),
					// Color filed only works with WordPress iris color picker.
					array(
						'title'   => __( 'Color Picker', 'utm-source-tracker' ),
						'desc'    => __( 'Choose the color.', 'utm-source-tracker' ),
						'id'      => 'utmst_color',
						'type'    => 'color',
						'default' => '#cccccc',
						'css' => 'width:280px;',
					),
					array(
						'title'    => __( 'Text field', 'utm-source-tracker' ),
						'id'       => 'utmst_textarea',
						'desc'     => __( 'This is the text field description', 'utm-source-tracker' ),
						'desc_tip' => __( 'This is the text field description tip', 'utm-source-tracker' ),
						'type'     => 'textarea',
						'placeholder'  => 'Textarea field (Placeholder)',
						'css' => 'width:300px;',
					),
					// Select input.
					array(
						'title'   => __( 'Select', 'utm-source-tracker' ),
						'desc'    => __( 'Chose an option from the list.', 'utm-source-tracker' ),
						'id'      => 'utmst_select',
						'type'    => 'select',
						'options' => array(
							'default'               => __( 'Select an option', 'utm-source-tracker' ),
							'item_1'               => __( 'Item 1', 'utm-source-tracker' ),
							'item_2'               => __( 'Item 2', 'utm-source-tracker' ),
							'item_3'            => __( 'Item 3 (It will display the conditional field)', 'utm-source-tracker' ),
						),
						'default' => 'default',
						'css' => 'width:300px;',
					),
					// Bellow is the data condition field for the above select field. This only be visible if the above select field has selected value to item_3.
					// Requirements: data-cond-id must be the dependent field id and data-cond-value must be equal to the option value.
					array(
						'title'             => __( 'Select conditional field', 'utm-source-tracker' ),
						'desc'              => __( 'Enter the text. This field is visible only for "Item 3".', 'utm-source-tracker' ),
						'id'                => 'utmst_conditional_field_for_select',
						'default'           => 'This field is visible only for "Item 3"',
						'type'              => 'text',
						'css' => 'width:300px;',
						'custom_attributes' => array(
							'data-cond-id'    => 'utmst_select',
							'data-cond-value' => 'item_3',
						),
					),
					// Multiselect input.
					array(
						'title'   => __( 'Multiselect', 'utm-source-tracker' ),
						'desc'    => __( 'Chose multiple options from the list. Tip: Use control/command then click to select multiple options.', 'utm-source-tracker' ),
						'id'      => 'utmst_multiselect',
						'type'    => 'multiselect',
						'options' => array(
							'default'               => __( 'Select multiple options', 'utm-source-tracker' ),
							'item_1'               => __( 'Item 1', 'utm-source-tracker' ),
							'item_2'               => __( 'Item 2', 'utm-source-tracker' ),
							'item_3'            => __( 'Item 3', 'utm-source-tracker' ),
						),
						'css' => 'width:300px;',
					),
					// Radio input.
					array(
						'title'   => __( 'Radio', 'utm-source-tracker' ),
						'desc'    => __( 'Select radio fields.', 'utm-source-tracker' ),
						'desc_tip'    => __( 'Select radio fields', 'utm-source-tracker' ),
						'id'      => 'utmst_radio',
						'type'    => 'radio',
						'options' => array(
							'default'               => __( 'Default options', 'utm-source-tracker' ),
							'item_1'               => __( 'Item 1', 'utm-source-tracker' ),
							'item_2'               => __( 'Item 2', 'utm-source-tracker' ),
							'item_3'               => __( 'Item 3', 'utm-source-tracker' ),
						),
						'default' => 'default',
						'disabled' => array(
							'item_1',
						),
						'desc_at_end' => false, // Boolean & Default is false.
					),
					// Checkbox input.
					array(
						'title'   => __( 'Checkbox', 'utm-source-tracker' ),
						'desc'    => __( 'Enable checkbox', 'utm-source-tracker' ),
						'desc_tip'    => __( 'Enable checkbox description tip.', 'utm-source-tracker' ),
						'id'      => 'utmst_checkbox',
						'default' => 'yes', // Or use 'no' instead.
						'type'    => 'checkbox',
					),
					// Conditional field for the above Checkbox input field.
					array(
						'title'             => __( 'Checkbox conditional field', 'utm-source-tracker' ),
						'desc'              => __( 'Enter the text. This field is visible only if the above checkbox is checked.', 'utm-source-tracker' ),
						'id'                => 'utmst_conditional_field_for_checkbox',
						'default'           => 'This field is visible only for "Item 3"',
						'type'              => 'text',
						'css' => 'width:300px;',
						'custom_attributes' => array(
							'data-cond-id'    => 'utmst_checkbox',
							'data-cond-value' => 'yes',
						),
					),
					// Create custom fields by using do_action.
					array(
						'title'   => __( 'Custom field', 'utm-source-tracker' ),
						'type'    => 'custom_field_type',
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
