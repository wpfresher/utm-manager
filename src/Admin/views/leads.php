<?php
/**
 * Admin View: List Lead
 *
 * @package UTMManager
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;
$list_table = new \UTMManager\Admin\ListTables\LeadsListTable();
$action     = $list_table->current_action();
$list_table->process_bulk_action( $action );
$list_table->prepare_items();
?>

<div class="pev-admin-page__header">
	<div>
		<h1 class="wp-heading-inline">
			<?php esc_html_e( 'Leads', 'utm-manager' ); ?>
		</h1>
		<span><?php esc_html_e( 'The list of leads.', 'utm-manager' ); ?></span>
	</div>
</div>
<form id="leads-list-table" method="get">
	<?php
	// TODO: Maybe status need to be removed if not required.
	wp_verify_nonce( '_wpnonce' );
	$status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
	$list_table->views();
	$list_table->search_box( __( 'Search', 'utm-manager' ), 'key' );
	$list_table->display();
	?>
	<input type="hidden" name="status" value="<?php echo esc_attr( $status ); ?>">
	<input type="hidden" name="page" value="utm-manager">
</form>

