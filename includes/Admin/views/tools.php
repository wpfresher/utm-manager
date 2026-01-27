<?php
/**
 * View: Tools Page.
 *
 * @since 1.3.0
 * @package UTMManager\Admin\Views
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>
<div class="wrap utmm-wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Tools', 'utm-manager' ); ?>
	</h1>
	<p><?php esc_html_e( 'Use the tools below to manage the UTM Manager plugin data', 'utm-manager' ); ?></p>
	<hr class="wp-header-end">

	<form id="utmm-form" class="utmm-export-form" method="post" enctype="multipart/form-data">

		<div class="field-group field-section has-spinner">
			<h3><?php esc_html_e( 'CSV File Export', 'utm-manager' ); ?></h3>
			<span class="spinner"></span>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'Date Range:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<label for="lead_start_date"><?php esc_html_e( 'Start Date:', 'utm-manager' ); ?></label>
				<input type="date" name="lead_start_date" id="lead_start_date" value=""/>

				<label for="lead_end_date"><?php esc_html_e( 'End Date:', 'utm-manager' ); ?></label>
				<input type="date" name="lead_end_date" id="lead_end_date" value=""/>
				<p class="description"><?php esc_html_e( 'Select a date range to export UTM leads. Leave blank to export all leads. Please make sure the end date is not earlier than the start date.', 'utm-manager' ); ?></p>
			</div>
		</div>

		<div class="field-group">
			<label for="fields" class="field-label">
				<strong><?php esc_html_e( 'Select Fields:', 'utm-manager' ); ?></strong>
			</label>
			<div class="field">
				<select name="fields[]" id="fields" multiple required>
					<?php
					$fields = array(
						'ip_address'   => __( 'IP Address', 'utm-manager' ),
						'utm_id'       => __( 'UTM ID', 'utm-manager' ),
						'utm_source'   => __( 'UTM Source', 'utm-manager' ),
						'utm_medium'   => __( 'UTM Medium', 'utm-manager' ),
						'utm_campaign' => __( 'UTM Campaign', 'utm-manager' ),
						'utm_term'     => __( 'UTM Term', 'utm-manager' ),
						'utm_content'  => __( 'UTM Content', 'utm-manager' ),
						'date'         => __( 'Date', 'utm-manager' ),
					);
					foreach ( $fields as $field => $label ) :
						?>
						<option value="<?php echo esc_attr( $field ); ?>" selected="selected"><?php echo esc_html( $label ); ?></option>
						<?php
					endforeach;
					?>
				</select>
				<p class="description"><?php esc_html_e( 'Select the fields to export. Hold down the Ctrl (Windows) / Command (Mac) button to select multiple options.', 'utm-manager' ); ?></p>
			</div>
		</div>

		<div class="field-group is-last-item">
			<div class="field-submit-btn">
				<?php wp_nonce_field( 'utmm_export_csv' ); ?>
				<?php submit_button( __( 'Export', 'utm-manager' ), 'primary', 'submit', false ); ?>
			</div>
		</div>
	</form>
</div>
<?php
