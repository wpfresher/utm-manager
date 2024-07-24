<?php
/**
 * View lead.
 *
 * @since 1.0.0
 * @package UTMManager
 *
 * @var object $lead Lead post object.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>
<div class="wrap utmm-wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'View Lead', 'utm-manager' ); ?>
		<a href="<?php echo esc_attr( admin_url( 'admin.php?page=utm-manager' ) ); ?>" class="page-title-action">
			<?php esc_html_e( 'Go Back', 'utm-manager' ); ?>
		</a>
	</h1>
	<p><?php esc_html_e( 'The following details provide the lead information:', 'utm-manager' ); ?></p>

	<hr class="wp-header-end">

	<div id="utmm-form">
		<div class="field-group filed-section">
			<h3><?php esc_html_e( 'Lead Details:', 'utm-manager' ); ?> #<?php echo esc_html( $lead->ID ); ?></h3>
			<p><?php esc_html_e( 'The following options are the lead information(s).', 'utm-manager' ); ?></p>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'IP Address:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<span>
					<?php echo esc_html( $lead->post_title ); ?>
				</span>
			</div>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'UTM ID:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<p><?php echo esc_html( get_post_meta( $lead->ID, '_utmm_utm_id', true ) ); ?></p>
			</div>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'UTM Source:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<p><?php echo esc_html( get_post_meta( $lead->ID, '_utmm_utm_source', true ) ); ?></p>
			</div>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'UTM Medium:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<p><?php echo esc_html( get_post_meta( $lead->ID, '_utmm_utm_medium', true ) ); ?></p>
			</div>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'UTM Campaign:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<p><?php echo esc_html( get_post_meta( $lead->ID, '_utmm_utm_campaign', true ) ); ?></p>
			</div>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'UTM Term:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<p><?php echo esc_html( get_post_meta( $lead->ID, '_utmm_utm_term', true ) ); ?></p>
			</div>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'UTM Content:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<p><?php echo wp_kses_post( $lead->post_content ); ?></p>
			</div>
		</div>

		<div class="field-group">
			<div class="field-label">
				<strong><?php esc_html_e( 'Date:', 'utm-manager' ); ?></strong>
			</div>
			<div class="field">
				<?php
				echo wp_kses_post(
					sprintf(
						'<time datetime="%s">%s</time>',
						esc_attr( $lead->post_date ),
						esc_html( date_i18n( get_option( 'date_format' ) . ' | ' . get_option( 'time_format' ), strtotime( $lead->post_date ) ) )
					)
				);
				?>
			</div>
		</div>

		<div class="field-group is-last-item">
			<div class="field-label">
			<?php if ( $lead->ID ) : ?>
				<a class="del button" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=utm-manager&ids=' . $lead->ID ) ), 'bulk-leads' ) ); ?>"><?php esc_html_e( 'Delete lead', 'utm-manager' ); ?></a>
			<?php endif; ?>
			</div>
			<div class="field-submit-btn"></div>
		</div>
	</div>
</div>
<?php
