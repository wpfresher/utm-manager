<?php
/**
 * Settings.
 *
 * @since 1.0.0
 * @package WpFreshers\UTMManager
 */

?>
<div class="wrap utmm-wrap">
	<div id="icon-users" class="icon32"></div>
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Settings', 'utm-manager' ); ?>
	</h1>

	<hr class="wp-header-end">

	<form id="utmm-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
		<div class="field-group filed-section">
			<h3><?php esc_html_e( 'General Settings', 'utm-manager' ); ?></h3>
			<p><?php esc_html_e( 'The following options are the plugin general settings.', 'utm-manager' ); ?></p>
		</div>
		<div class="field-group">
			<div class="field-label">
				<label for="option_name"><?php esc_html_e( 'Option name:', 'utm-manager' ); ?></label>
			</div>
			<input type="text" name="option_name" id="option_name" placeholder="<?php esc_html_e( 'Enter the option name', 'utm-manager' ); ?>">
		</div>

		<div class="field-group">
			<div class="field-label">
				<label for="option_content"><?php esc_html_e( 'Option content:', 'utm-manager' ); ?></label>
			</div>
			<textarea type="text" name="option_content" id="option_content" placeholder="<?php esc_html_e( 'Enter the option content', 'utm-manager' ); ?>"></textarea>
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
