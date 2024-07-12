<?php
/**
 * Things list table.
 *
 * @since 1.0.0
 * @package WpFreshers\UTMManager
 *
 * @var object $list_table Things list table.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>
<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Things List Table', 'utm-manager' ); ?>
	</h1>
	<hr class="wp-header-end">
	<form id="utmm_thing_list_table" method="post">
		<?php
		$list_table->views();
		$list_table->search_box( __( 'Search', 'utm-manager' ), 'search_lead' );
		$list_table->display();
		?>
		<input type="hidden" name="page" value="things-list-table">
	</form>
</div>
<?php
