<?php
/**
 * AJAX handler registration.
 *
 * @package SmartToLet\Common
 */

namespace SmartToLet\Common;

defined( 'ABSPATH' ) || exit;

/**
 * Class Ajax
 *
 * Registers both logged-in (wp_ajax_) and public (wp_ajax_nopriv_) handlers.
 */
class Ajax {

	/** @var Ajax|null */
	private static ?Ajax $instance = null;

	private function __construct() {
		// Property search.
		add_action( 'wp_ajax_stl_search',        [ $this, 'handle_search' ] );
		add_action( 'wp_ajax_nopriv_stl_search',  [ $this, 'handle_search' ] );

		// Enquiry submission.
		add_action( 'wp_ajax_stl_enquiry',        [ $this, 'handle_enquiry' ] );
		add_action( 'wp_ajax_nopriv_stl_enquiry', [ $this, 'handle_enquiry' ] );

		// Toggle favourite.
		add_action( 'wp_ajax_stl_toggle_favourite', [ $this, 'handle_toggle_favourite' ] );
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Handle property search AJAX.
	 */
	public function handle_search(): void {
		check_ajax_referer( 'smarttolet_nonce', 'nonce' );

		$keyword  = sanitize_text_field( $_POST['keyword']  ?? '' );
		$type     = sanitize_text_field( $_POST['type']     ?? '' );
		$location = sanitize_text_field( $_POST['location'] ?? '' );
		$min      = absint( $_POST['min_price'] ?? 0 );
		$max      = absint( $_POST['max_price'] ?? 0 );

		$args = [
			'post_type'      => 'stl_property',
			'post_status'    => 'publish',
			'posts_per_page' => 12,
			's'              => $keyword,
		];

		$tax_query = [];
		if ( $type ) {
			$tax_query[] = [
				'taxonomy' => 'stl_property_type',
				'field'    => 'slug',
				'terms'    => $type,
			];
		}
		if ( $location ) {
			$tax_query[] = [
				'taxonomy' => 'stl_location',
				'field'    => 'slug',
				'terms'    => $location,
			];
		}
		if ( $tax_query ) {
			$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery
		}

		$meta_query = [];
		if ( $min ) {
			$meta_query[] = [
				'key'     => '_stl_price',
				'value'   => $min,
				'compare' => '>=',
				'type'    => 'NUMERIC',
			];
		}
		if ( $max ) {
			$meta_query[] = [
				'key'     => '_stl_price',
				'value'   => $max,
				'compare' => '<=',
				'type'    => 'NUMERIC',
			];
		}
		if ( $meta_query ) {
			$args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery
		}

		$query = new \WP_Query( $args );
		$html  = '';

		if ( $query->have_posts() ) {
			ob_start();
			while ( $query->have_posts() ) {
				$query->the_post();
				include SMARTTOLET_PATH . 'frontend/views/property-card.php';
			}
			wp_reset_postdata();
			$html = ob_get_clean();
		}

		wp_send_json_success( [
			'html'  => $html,
			'found' => $query->found_posts,
		] );
	}

	/**
	 * Handle enquiry form AJAX submission.
	 */
	public function handle_enquiry(): void {
		check_ajax_referer( 'smarttolet_nonce', 'nonce' );

		$property_id = absint( $_POST['property_id'] ?? 0 );
		$name        = sanitize_text_field( $_POST['name']  ?? '' );
		$email       = sanitize_email( $_POST['email']      ?? '' );
		$phone       = sanitize_text_field( $_POST['phone'] ?? '' );
		$message     = sanitize_textarea_field( $_POST['message'] ?? '' );

		if ( ! $property_id || ! $name || ! is_email( $email ) ) {
			wp_send_json_error( [ 'message' => __( 'Please fill in all required fields.', 'smarttolet' ) ] );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'smarttolet_enquiries';

		$inserted = $wpdb->insert( $table, [
			'property_id' => $property_id,
			'name'        => $name,
			'email'       => $email,
			'phone'       => $phone,
			'message'     => $message,
		], [ '%d', '%s', '%s', '%s', '%s' ] );

		if ( $inserted ) {
			// Notify the property author.
			$this->send_enquiry_notification( $property_id, $name, $email, $phone, $message );
			wp_send_json_success( [ 'message' => __( 'Your enquiry has been sent!', 'smarttolet' ) ] );
		}

		wp_send_json_error( [ 'message' => __( 'Could not save your enquiry. Please try again.', 'smarttolet' ) ] );
	}

	/**
	 * Toggle favourite (logged-in users only).
	 */
	public function handle_toggle_favourite(): void {
		check_ajax_referer( 'smarttolet_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => __( 'You must be logged in.', 'smarttolet' ) ] );
		}

		$user_id     = get_current_user_id();
		$property_id = absint( $_POST['property_id'] ?? 0 );

		global $wpdb;
		$table = $wpdb->prefix . 'smarttolet_favourites';

		$existing = $wpdb->get_var( $wpdb->prepare(
			"SELECT id FROM `{$table}` WHERE user_id = %d AND property_id = %d",
			$user_id, $property_id
		) );

		if ( $existing ) {
			$wpdb->delete( $table, [ 'id' => $existing ], [ '%d' ] );
			wp_send_json_success( [ 'favourited' => false ] );
		} else {
			$wpdb->insert( $table, [
				'user_id'     => $user_id,
				'property_id' => $property_id,
			], [ '%d', '%d' ] );
			wp_send_json_success( [ 'favourited' => true ] );
		}
	}

	/**
	 * Send an email notification to the property owner.
	 */
	private function send_enquiry_notification( int $property_id, string $name, string $email, string $phone, string $message ): void {
		$author_id    = (int) get_post_field( 'post_author', $property_id );
		$author_email = get_the_author_meta( 'user_email', $author_id );
		$title        = get_the_title( $property_id );

		$subject = sprintf( __( 'New enquiry for: %s', 'smarttolet' ), $title );
		$body    = sprintf(
			"Name: %s\nEmail: %s\nPhone: %s\n\n%s",
			$name, $email, $phone, $message
		);

		wp_mail( $author_email, $subject, $body );
	}
}
