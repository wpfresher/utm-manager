<?php
/**
 * Admin views: View Lead.
 *
 * @since 1.0.0
 * @package UTMManager
 */

defined( 'ABSPATH' ) || exit;
$lead_id = filter_input( INPUT_GET, 'view_lead', FILTER_SANITIZE_NUMBER_INT );
$lead    = utmm_get_lead( $lead_id );
?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'View Lead', 'utm-manager' ); ?>
</h1>

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<div class="pev-poststuff">
		<div class="column-1">
			<div class="pev-card">
				<div class="pev-card__header">
					<h3 class="pev-card__title"><?php esc_html_e( 'Lead Details', 'utm-manager' ); ?></h3>
					<p class="pev-card__subtitle">
						#<?php echo esc_html( $lead->ID ); ?>
					</p>
				</div>
				<div class="pev-card__body inline--fields">

					<div class="pev-form-field">
						<label>
							<?php esc_html_e( 'Product', 'utm-manager' ); ?>
						</label>
					</div>

					<div class="pev-form-field">
						<label>
							<?php esc_html_e( 'Order', 'utm-manager' ); ?>
						</label>
						<?php
						$order = 'sdjfd'; //$lead->get_order();
						if ( $order ) {
							echo sprintf( '<a href="%s">#%d %s</a>', esc_url( get_edit_post_link( '$order->get_id()' ) ), esc_html( '$order->get_id()' ), esc_html( '$order->get_formatted_billing_full_name()' ) );
						} else {
							esc_html_e( 'No order assigned.', 'utm-manager' );
						}
						?>

					</div>

					<div class="pev-form-field">
						<label>
							<?php esc_html_e( 'Customer', 'utm-manager' ); ?>
						</label>
						<?php
						$customer = 'df'; //$lead->get_customer();
						if ( $customer ) {
							echo sprintf( '<a href="%s">#%d %s</a>', esc_url( get_edit_post_link( '$customer->get_id()' ) ), esc_html( '$customer->get_id()' ), esc_html( '$lead->get_customer_name()' ) );
						} else {
							esc_html_e( 'No customer assigned.', 'utm-manager' );
						}
						?>
					</div>

					<div class="pev-form-field">
						<label for="lead_name"><?php esc_html_e( 'Name', 'utm-manager' ); ?></label>
						<input type="text" name="lead_name" id="lead_name" value="<?php echo esc_attr( '$lead->get_name()' ); ?>" class="regular-text">
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
					<a class="del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=utm-manager&id=' . $lead->ID ) ), 'bulk-leads' ) ); ?>"><?php esc_html_e( 'Delete', 'utm-manager' ); ?></a>
					<button class="button button-primary"><?php esc_html_e( 'Save Lead', 'utm-manager' ); ?></button>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="action" value="utmm_save_lead">
	<input type="hidden" name="lead_id" value="<?php echo esc_attr( $lead->ID ); ?>">
	<?php wp_nonce_field( 'utmm_save_lead' ); ?>
</form>
