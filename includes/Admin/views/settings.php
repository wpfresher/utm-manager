<?php
/**
 * Settings.
 *
 * @since 1.0.0
 * @package UTMManager
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>
<div class="wrap utmm-wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Settings', 'utm-manager' ); ?>
	</h1>
	<p><?php esc_html_e( 'The following options are the plugin general settings.', 'utm-manager' ); ?></p>
	<p><strong><?php esc_html_e( 'Example URL:', 'utm-manager' ); ?></strong></p>
	<p><?php echo esc_html( 'https://your-domain.com/?utm_id=12345&utm_source=google&utm_medium=advertising&utm_campaign=black-friday-sale&utm_term=campaign-term&utm_content=campaign-content' ); ?></p>
	<hr class="wp-header-end">
	<form id="utmm-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">

		<div class="field-group filed-section">
			<h3><?php esc_html_e( 'Setup UTM Parameter(s)', 'utm-manager' ); ?></h3>
			<p><?php esc_html_e( 'The following options are the utm parameter settings. Al least an option needs to be checked to track the URL parameters.', 'utm-manager' ); ?></p>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'UTM ID:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<label for="utmm_utm_id">
					<input name="utmm_utm_id" id="utmm_utm_id" type="checkbox" value="yes" <?php checked( get_option( 'utmm_utm_id' ), 'yes' ); ?>>
					<?php esc_html_e( 'Enable utm_id', 'utm-manager' ); ?>
				</label>
				<p class="description"><?php esc_html_e( 'Enable to track the utm_id URL parameter as a lead.', 'utm-manager' ); ?></p>
			</div>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'UTM Source:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<label for="utmm_utm_source">
					<input name="utmm_utm_source" id="utmm_utm_source" type="checkbox" value="yes" <?php checked( get_option( 'utmm_utm_source' ), 'yes' ); ?>>
					<?php esc_html_e( 'Enable utm_source', 'utm-manager' ); ?>
				</label>
				<p class="description"><?php esc_html_e( 'Enable to track the utm_source URL parameter as a lead.', 'utm-manager' ); ?></p>
			</div>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'UTM Medium:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<label for="utmm_utm_medium">
					<input name="utmm_utm_medium" id="utmm_utm_medium" type="checkbox" value="yes" <?php checked( get_option( 'utmm_utm_medium' ), 'yes' ); ?>>
					<?php esc_html_e( 'Enable utm_medium', 'utm-manager' ); ?>
				</label>
				<p class="description"><?php esc_html_e( 'Enable to track the utm_medium URL parameter as a lead.', 'utm-manager' ); ?></p>
			</div>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'UTM Campaign:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<label for="utmm_utm_campaign">
					<input name="utmm_utm_campaign" id="utmm_utm_campaign" type="checkbox" value="yes" <?php checked( get_option( 'utmm_utm_campaign' ), 'yes' ); ?>>
					<?php esc_html_e( 'Enable utm_campaign', 'utm-manager' ); ?>
				</label>
				<p class="description"><?php esc_html_e( 'Enable to track the utm_campaign URL parameter as a lead.', 'utm-manager' ); ?></p>
			</div>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'UTM Term:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<label for="utmm_utm_term">
					<input name="utmm_utm_term" id="utmm_utm_term" type="checkbox" value="yes" <?php checked( get_option( 'utmm_utm_term' ), 'yes' ); ?>>
					<?php esc_html_e( 'Enable utm_term', 'utm-manager' ); ?>
				</label>
				<p class="description"><?php esc_html_e( 'Enable to track the utm_term URL parameter as a lead.', 'utm-manager' ); ?></p>
			</div>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'UTM Content:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<label for="utmm_utm_content">
					<input name="utmm_utm_content" id="utmm_utm_content" type="checkbox" value="yes" <?php checked( get_option( 'utmm_utm_content' ), 'yes' ); ?>>
					<?php esc_html_e( 'Enable utm_content', 'utm-manager' ); ?>
				</label>
				<p class="description"><?php esc_html_e( 'Enable to track the utm_content URL parameter as a lead.', 'utm-manager' ); ?></p>
			</div>
		</div>

		<div class="field-group is-last-item">
			<div class="field-submit-btn">
				<button class="button button-primary"><?php esc_html_e( 'Save Changes', 'utm-manager' ); ?></button>
			</div>
		</div>

		<input type="hidden" name="action" value="utmm_update_settings">
		<?php wp_nonce_field( 'utmm_update_settings' ); ?>
	</form>
</div>
<?php
