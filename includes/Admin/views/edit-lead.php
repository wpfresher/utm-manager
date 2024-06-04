<?php
/**
 * Edit lead.
 *
 * @since 1.0.0
 * @package WpFreshers\UTMManager
 */

?>
<div class="wrap utmm-wrap">
	<div id="icon-users" class="icon32"></div>
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Edit Lead', 'utm-manager' ); ?>
		<!-- Add another lead -->
		<a href="<?php echo esc_attr( admin_url( 'admin.php?page=utm-manager&add=1' ) ); ?>" class="page-title-action">
			<?php esc_html_e( 'Add Another', 'utm-manager' ); ?>
		</a>
		<a href="<?php echo esc_attr( admin_url( 'admin.php?page=utm-manager' ) ); ?>" class="page-title-action">
			<?php esc_html_e( 'Go Back', 'utm-manager' ); ?>
		</a>
	</h1>
	<p><?php esc_html_e( 'Here is the example list table updated at March 26, 2024', 'utm-manager' ); ?></p>

	<hr class="wp-header-end">

	<form id="utmm-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
		<div class="field-group">
			<div class="field-label">
				<label for="lead_name"><?php esc_html_e( 'Lead name:', 'utm-manager' ); ?></label>
			</div>
			<input type="text" name="lead_name" id="lead_name" value="<?php echo esc_html( $lead->post_title ); ?>">
		</div>

		<div class="field-group">
			<div class="field-label">
				<label for="lead_content"><?php esc_html_e( 'Lead content:', 'utm-manager' ); ?></label>
			</div>
			<textarea type="text" name="lead_content" id="lead_content"><?php echo esc_html( $lead->post_content ); ?></textarea>
		</div>

		<div class="field-group is-last-item">
			<div class="field-label">
			<?php if ( $lead->ID ) : ?>
				<a class="del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=utm-manager&id=' . $lead->ID ) ), 'bulk-utmm_lead' ) ); ?>"><?php esc_html_e( 'Delete', 'utm-manager' ); ?></a>
			<?php endif; ?>
			</div>
			<div class="field-submit-btn">
				<button class="button button-primary"><?php esc_html_e( 'Save lead', 'utm-manager' ); ?></button>
			</div>
		</div>

		<input type="hidden" name="action" value="utmm_edit_lead">
		<?php wp_nonce_field( 'utmm_edit_lead' ); ?>
		<input type="hidden" name="id" value="<?php echo esc_attr( $lead->ID ); ?>">
	</form>
</div>
<?php
