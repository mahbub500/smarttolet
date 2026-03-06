<?php
/**
 * Meta box registration for the property CPT.
 *
 * @package SmartToLet\Admin
 */

namespace SmartToLet\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Meta_Boxes
 */
class Meta_Boxes {

	/** @var Meta_Boxes|null */
	private static ?Meta_Boxes $instance = null;

	/** Meta key prefix. */
	const PREFIX = '_stl_';

	private function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'register' ] );
		add_action( 'save_post_stl_property', [ $this, 'save' ], 10, 2 );
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register meta boxes.
	 */
	public function register(): void {
		add_meta_box(
			'stl_property_details',
			__( 'Property Details', 'smarttolet' ),
			[ $this, 'render_details_box' ],
			'stl_property',
			'normal',
			'high'
		);

		add_meta_box(
			'stl_property_media',
			__( 'Property Gallery', 'smarttolet' ),
			[ $this, 'render_gallery_box' ],
			'stl_property',
			'normal',
			'default'
		);

		add_meta_box(
			'stl_property_location',
			__( 'Property Location / Map', 'smarttolet' ),
			[ $this, 'render_location_box' ],
			'stl_property',
			'normal',
			'default'
		);
	}

	/**
	 * Render the details meta box.
	 */
	public function render_details_box( \WP_Post $post ): void {
		wp_nonce_field( 'stl_save_property', 'stl_nonce' );
		include SMARTTOLET_PATH . 'admin/views/meta-box-details.php';
	}

	/**
	 * Render the gallery meta box.
	 */
	public function render_gallery_box( \WP_Post $post ): void {
		include SMARTTOLET_PATH . 'admin/views/meta-box-gallery.php';
	}

	/**
	 * Render the location meta box.
	 */
	public function render_location_box( \WP_Post $post ): void {
		include SMARTTOLET_PATH . 'admin/views/meta-box-location.php';
	}

	/**
	 * Save meta box data.
	 */
	public function save( int $post_id, \WP_Post $post ): void {
		// Nonce + autosave + permission checks.
		if (
			! isset( $_POST['stl_nonce'] ) ||
			! wp_verify_nonce( $_POST['stl_nonce'], 'stl_save_property' ) ||
			defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ||
			! current_user_can( 'edit_post', $post_id )
		) {
			return;
		}

		$fields = [
			'_stl_price'       => 'absint',
			'_stl_bedrooms'    => 'absint',
			'_stl_bathrooms'   => 'absint',
			'_stl_area'        => 'floatval',
			'_stl_area_unit'   => 'sanitize_text_field',
			'_stl_status'      => 'sanitize_text_field',
			'_stl_furnished'   => 'sanitize_text_field',
			'_stl_available'   => 'sanitize_text_field',
			'_stl_address'     => 'sanitize_text_field',
			'_stl_latitude'    => 'sanitize_text_field',
			'_stl_longitude'   => 'sanitize_text_field',
			'_stl_video_url'   => 'esc_url_raw',
			'_stl_gallery_ids' => 'sanitize_text_field', // comma-separated attachment IDs
			'_stl_featured'    => 'absint',
		];

		foreach ( $fields as $key => $sanitizer ) {
			$raw = $_POST[ $key ] ?? null; // phpcs:ignore
			if ( null !== $raw ) {
				update_post_meta( $post_id, $key, $sanitizer( $raw ) );
			}
		}
	}

	/**
	 * Helper to get a meta value with a fallback.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key (without PREFIX).
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	public static function get( int $post_id, string $key, $default = '' ) {
		$value = get_post_meta( $post_id, self::PREFIX . $key, true );
		return ( '' !== $value ) ? $value : $default;
	}
}
