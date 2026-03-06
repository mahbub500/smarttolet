<?php
/**
 * Admin dashboard view.
 *
 * @package SmartToLet
 */

defined( 'ABSPATH' ) || exit;

global $wpdb;

$total_properties = wp_count_posts( 'stl_property' )->publish;
$total_enquiries  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}smarttolet_enquiries" );
$new_enquiries    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}smarttolet_enquiries WHERE status = 'new'" );
?>
<div class="wrap stl-admin-wrap">
	<h1 class="stl-admin-title">
		<span class="dashicons dashicons-building"></span>
		<?php esc_html_e( 'SmartToLet Dashboard', 'smarttolet' ); ?>
	</h1>

	<div class="stl-stats-grid">
		<div class="stl-stat-card">
			<span class="stl-stat-icon dashicons dashicons-building"></span>
			<div class="stl-stat-content">
				<span class="stl-stat-number"><?php echo esc_html( $total_properties ); ?></span>
				<span class="stl-stat-label"><?php esc_html_e( 'Properties', 'smarttolet' ); ?></span>
			</div>
		</div>
		<div class="stl-stat-card">
			<span class="stl-stat-icon dashicons dashicons-email-alt"></span>
			<div class="stl-stat-content">
				<span class="stl-stat-number"><?php echo esc_html( $total_enquiries ); ?></span>
				<span class="stl-stat-label"><?php esc_html_e( 'Total Enquiries', 'smarttolet' ); ?></span>
			</div>
		</div>
		<div class="stl-stat-card stl-stat-card--highlight">
			<span class="stl-stat-icon dashicons dashicons-bell"></span>
			<div class="stl-stat-content">
				<span class="stl-stat-number"><?php echo esc_html( $new_enquiries ); ?></span>
				<span class="stl-stat-label"><?php esc_html_e( 'New Enquiries', 'smarttolet' ); ?></span>
			</div>
		</div>
	</div>

	<div class="stl-quick-actions">
		<h2><?php esc_html_e( 'Quick Actions', 'smarttolet' ); ?></h2>
		<a class="button button-primary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=stl_property' ) ); ?>">
			<?php esc_html_e( '+ Add Property', 'smarttolet' ); ?>
		</a>
		<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=smarttolet-enquiries' ) ); ?>">
			<?php esc_html_e( 'View Enquiries', 'smarttolet' ); ?>
		</a>
		<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=smarttolet-settings' ) ); ?>">
			<?php esc_html_e( 'Settings', 'smarttolet' ); ?>
		</a>
	</div>
</div>
