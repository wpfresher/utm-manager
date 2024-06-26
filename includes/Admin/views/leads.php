<?php
/**
 * Leads list table.
 *
 * @since 1.0.0
 * @package UTMManager
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$list_table = new UTMManager\Admin\ListTables\LeadsListTable();
$list_table->prepare_items();
?>
<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Leads List Table', 'utm-manager' ); ?>
	</h1>
	<hr class="wp-header-end">
	<form id="utmm_lead_list_table" method="get">
		<?php
		$list_table->views();
		$list_table->search_box( __( 'Search', 'utm-manager' ), 'search_lead' );
		$list_table->display();
		?>
		<input type="hidden" name="page" value="utm-manager">
	</form>
</div>
<?php
