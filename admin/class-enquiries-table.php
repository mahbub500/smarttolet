<?php
/**
 * Enquiries list table (admin).
 *
 * @package SmartToLet\Admin
 */

namespace SmartToLet\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Enquiries_Table
 *
 * Manages the enquiries admin page – listing and status updates.
 */
class Enquiries_Table {

	/** @var Enquiries_Table|null */
	private static ?Enquiries_Table $instance = null;

	private function __construct() {
		add_action( 'admin_post_stl_update_enquiry_status', [ $this, 'handle_status_update' ] );
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Return paginated enquiries from the DB.
	 *
	 * @param int $per_page Items per page.
	 * @param int $page     Current page.
	 * @return array{items: array, total: int}
	 */
	public function get_enquiries( int $per_page = 20, int $page = 1 ): array {
		global $wpdb;

		$table  = $wpdb->prefix . 'smarttolet_enquiries';
		$offset = ( $page - 1 ) * $per_page;

		$items = $wpdb->get_results( $wpdb->prepare(
			"SELECT e.*, p.post_title AS property_title
			 FROM `{$table}` e
			 LEFT JOIN {$wpdb->posts} p ON p.ID = e.property_id
			 ORDER BY e.created_at DESC
			 LIMIT %d OFFSET %d",
			$per_page, $offset
		), ARRAY_A );

		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$table}`" );

		return compact( 'items', 'total' );
	}

	/**
	 * Handle status update form action.
	 */
	public function handle_status_update(): void {
		if (
			! current_user_can( 'manage_options' ) ||
			! isset( $_POST['stl_enquiry_nonce'] ) ||
			! wp_verify_nonce( $_POST['stl_enquiry_nonce'], 'stl_update_enquiry' )
		) {
			wp_die( esc_html__( 'Permission denied.', 'smarttolet' ) );
		}

		global $wpdb;

		$id     = absint( $_POST['enquiry_id'] ?? 0 );
		$status = sanitize_text_field( $_POST['status'] ?? 'new' );

		$wpdb->update(
			$wpdb->prefix . 'smarttolet_enquiries',
			[ 'status' => $status ],
			[ 'id'     => $id ],
			[ '%s' ],
			[ '%d' ]
		);

		wp_safe_redirect( admin_url( 'admin.php?page=smarttolet-enquiries&updated=1' ) );
		exit;
	}
}
