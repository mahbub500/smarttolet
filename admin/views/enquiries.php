<?php
/**
 * Enquiries list view.
 *
 * @package SmartToLet
 */

defined( 'ABSPATH' ) || exit;

$table    = SmartToLet\Admin\Enquiries_Table::get_instance();
$per_page = 20;
$page     = max( 1, absint( $_GET['paged'] ?? 1 ) );
$data     = $table->get_enquiries( $per_page, $page );
$items    = $data['items'];
$total    = $data['total'];
$pages    = (int) ceil( $total / $per_page );
?>
<div class="wrap stl-admin-wrap">
	<h1><?php esc_html_e( 'Enquiries', 'smarttolet' ); ?></h1>

	<?php if ( isset( $_GET['updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Enquiry status updated.', 'smarttolet' ); ?></p>
		</div>
	<?php endif; ?>

	<table class="wp-list-table widefat fixed striped stl-enquiries-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'ID',       'smarttolet' ); ?></th>
				<th><?php esc_html_e( 'Property', 'smarttolet' ); ?></th>
				<th><?php esc_html_e( 'Name',     'smarttolet' ); ?></th>
				<th><?php esc_html_e( 'Email',    'smarttolet' ); ?></th>
				<th><?php esc_html_e( 'Phone',    'smarttolet' ); ?></th>
				<th><?php esc_html_e( 'Message',  'smarttolet' ); ?></th>
				<th><?php esc_html_e( 'Status',   'smarttolet' ); ?></th>
				<th><?php esc_html_e( 'Date',     'smarttolet' ); ?></th>
				<th><?php esc_html_e( 'Action',   'smarttolet' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if ( $items ) : foreach ( $items as $item ) : ?>
			<tr>
				<td><?php echo esc_html( $item['id'] ); ?></td>
				<td><?php echo esc_html( $item['property_title'] ?: '—' ); ?></td>
				<td><?php echo esc_html( $item['name'] ); ?></td>
				<td><a href="mailto:<?php echo esc_attr( $item['email'] ); ?>"><?php echo esc_html( $item['email'] ); ?></a></td>
				<td><?php echo esc_html( $item['phone'] ); ?></td>
				<td><?php echo esc_html( wp_trim_words( $item['message'], 12 ) ); ?></td>
				<td>
					<span class="stl-badge stl-badge--<?php echo esc_attr( 'new' === $item['status'] ? 'green' : 'grey' ); ?>">
						<?php echo esc_html( ucfirst( $item['status'] ) ); ?>
					</span>
				</td>
				<td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $item['created_at'] ) ) ); ?></td>
				<td>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<?php wp_nonce_field( 'stl_update_enquiry', 'stl_enquiry_nonce' ); ?>
						<input type="hidden" name="action"      value="stl_update_enquiry_status">
						<input type="hidden" name="enquiry_id"  value="<?php echo esc_attr( $item['id'] ); ?>">
						<select name="status">
							<option value="new"      <?php selected( $item['status'], 'new' ); ?>><?php esc_html_e( 'New',      'smarttolet' ); ?></option>
							<option value="replied"  <?php selected( $item['status'], 'replied' ); ?>><?php esc_html_e( 'Replied', 'smarttolet' ); ?></option>
							<option value="archived" <?php selected( $item['status'], 'archived' ); ?>><?php esc_html_e( 'Archived','smarttolet' ); ?></option>
						</select>
						<button type="submit" class="button button-small"><?php esc_html_e( 'Update', 'smarttolet' ); ?></button>
					</form>
				</td>
			</tr>
		<?php endforeach; else : ?>
			<tr><td colspan="9"><?php esc_html_e( 'No enquiries yet.', 'smarttolet' ); ?></td></tr>
		<?php endif; ?>
		</tbody>
	</table>

	<?php if ( $pages > 1 ) : ?>
		<div class="stl-pagination">
			<?php
			echo paginate_links( [
				'base'    => add_query_arg( 'paged', '%#%' ),
				'format'  => '',
				'current' => $page,
				'total'   => $pages,
			] );
			?>
		</div>
	<?php endif; ?>
</div>
