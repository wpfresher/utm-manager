<?php
/**
 * Leads list table.
 *
 * @since 1.0.0
 * @package UTMManager
 *
 * @var object $list_table Leads list table.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>
<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Leads', 'utm-manager' ); ?>
	</h1>
	<hr class="wp-header-end">
	<form id="utmm-leads-table" method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<?php
		$list_table->views();
		$list_table->search_box( __( 'Search', 'utm-manager' ), 'search' );
		$list_table->display();
		?>
		<input type="hidden" name="page" value="utm-manager">
	</form>
</div>
<?php
