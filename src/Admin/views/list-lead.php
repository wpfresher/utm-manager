<?php
/**
 * Admin View: List Thing
 *
 * @package UTMSourceTracker
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;
$list_table = new \UTMSourceTracker\Admin\ListTables\LeadsListTable(); //utmst_get_list_table( 'leads' );
$action     = $list_table->current_action();
$list_table->process_bulk_action( $action );
$list_table->prepare_items();
?>

<div class="pev-admin-page__header">
	<div>
		<h1 class="wp-heading-inline">
			<?php esc_html_e( 'Leads', 'plugin-text-domain' ); ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=utm-source-tracker&new=1' ) ); ?>" class="page-title-action">
				<?php esc_html_e( 'Add New', 'plugin-text-domain' ); ?>
			</a>
		</h1>
	</div>
</div>
<form id="leads-list-table" method="get">
	<?php
	$status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$list_table->views();
	$list_table->search_box( __( 'Search', 'plugin-text-domain' ), 'key' );
	$list_table->display();
	?>
	<input type="hidden" name="status" value="<?php echo esc_attr( $status ); ?>">
	<input type="hidden" name="page" value="utm-source-tracker">
</form>

