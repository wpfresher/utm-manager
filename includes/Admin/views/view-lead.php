<?php
/**
 * Admin views: View Lead.
 *
 * @since 1.0.0
 * @subpackage Admin/Views
 * @package UrlDev\UTMManager\Admin
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$lead_id = filter_input( INPUT_GET, 'view_lead', FILTER_SANITIZE_NUMBER_INT );
$lead    = utmm_get_lead( $lead_id );
?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'View Lead', 'utm-manager' ); ?>
</h1>

<div class="pev-poststuff">
	<div class="column-1">
		<div class="pev-card">
			<div class="pev-card__header">
				<h3 class="pev-card__title"><?php esc_html_e( 'Lead Details', 'utm-manager' ); ?></h3>
				<p class="pev-card__subtitle">
					#<?php echo esc_html( $lead->ID ); ?>
				</p>
			</div>
			<div class="pev-card__body form-inline">

				<div class="pev-form-field">
					<label>
						<?php esc_html_e( 'IP:', 'utm-manager' ); ?>
					</label>
					<span>
						<?php echo esc_html( $lead->post_title ); ?>
					</span>
				</div>

				<div class="pev-form-field">
					<label>
						<?php esc_html_e( 'UTM ID:', 'utm-manager' ); ?>
					</label>
					<span>
						<?php echo esc_html( get_post_meta( $lead->ID, '_utmm_utm_id', true ) ); ?>
					</span>
				</div>

				<div class="pev-form-field">
					<label>
						<?php esc_html_e( 'UTM Source:', 'utm-manager' ); ?>
					</label>
					<span>
						<?php echo esc_html( get_post_meta( $lead->ID, '_utmm_utm_source', true ) ); ?>
					</span>
				</div>

				<div class="pev-form-field">
					<label>
						<?php esc_html_e( 'UTM Medium:', 'utm-manager' ); ?>
					</label>
					<span>
						<?php echo esc_html( get_post_meta( $lead->ID, '_utmm_utm_medium', true ) ); ?>
					</span>
				</div>

				<div class="pev-form-field">
					<label>
						<?php esc_html_e( 'UTM Campaign:', 'utm-manager' ); ?>
					</label>
					<span>
						<?php echo esc_html( get_post_meta( $lead->ID, '_utmm_utm_campaign', true ) ); ?>
					</span>
				</div>

				<div class="pev-form-field">
					<label>
						<?php esc_html_e( 'UTM Term:', 'utm-manager' ); ?>
					</label>
					<span>
						<?php echo esc_html( get_post_meta( $lead->ID, '_utmm_utm_term', true ) ); ?>
					</span>
				</div>

				<div class="pev-form-field">
					<label>
						<?php esc_html_e( 'UTM Content:', 'utm-manager' ); ?>
					</label>
					<span>
						<?php echo esc_html( get_post_meta( $lead->ID, '_utmm_utm_content', true ) ); ?>
					</span>
				</div>

				<div class="pev-form-field">
					<label>
						<?php esc_html_e( 'Date:', 'utm-manager' ); ?>
					</label>
					<span>
						<?php echo esc_html( $lead->post_date ); ?>
					</span>
				</div>
			</div>
		</div>
	</div>
	<div class="column-2">
		<div class="pev-card">
			<div class="pev-card__header">
				<h3 class="pev-card__title"><?php esc_html_e( 'Actions', 'utm-manager' ); ?></h3>
			</div>
			<div class="pev-card__footer">
				<a class="go-back" href="<?php echo esc_url( admin_url( 'admin.php?page=utm-manager' ) ); ?>"><?php esc_html_e( 'Go back', 'utm-manager' ); ?></a>
				<a class="del button button-warning" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=utm-manager&id=' . $lead->ID ) ), 'bulk-leads' ) ); ?>"><?php esc_html_e( 'Delete', 'utm-manager' ); ?></a>
			</div>
		</div>
	</div>
</div>
