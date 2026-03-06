<?php
/**
 * Taxonomy registration.
 *
 * @package SmartToLet\Common
 */

namespace SmartToLet\Common;

defined( 'ABSPATH' ) || exit;

/**
 * Class Taxonomies
 */
class Taxonomies {

	/** @var Taxonomies|null */
	private static ?Taxonomies $instance = null;

	private function __construct() {
		add_action( 'init', [ $this, 'register' ] );
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register all taxonomies.
	 */
	public function register(): void {
		$this->register_property_type();
		$this->register_property_location();
		$this->register_property_amenity();
	}

	private function register_property_type(): void {
		register_taxonomy( 'stl_property_type', 'stl_property', [
			'labels'            => [
				'name'          => __( 'Property Types', 'smarttolet' ),
				'singular_name' => __( 'Property Type',  'smarttolet' ),
				'add_new_item'  => __( 'Add New Type',   'smarttolet' ),
				'edit_item'     => __( 'Edit Type',      'smarttolet' ),
			],
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_admin_column' => true,
			'rewrite'           => [ 'slug' => 'property-type' ],
			'show_in_rest'      => true,
		] );
	}

	private function register_property_location(): void {
		register_taxonomy( 'stl_location', 'stl_property', [
			'labels'            => [
				'name'          => __( 'Locations', 'smarttolet' ),
				'singular_name' => __( 'Location',  'smarttolet' ),
				'add_new_item'  => __( 'Add New Location', 'smarttolet' ),
			],
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'rewrite'           => [ 'slug' => 'location' ],
			'show_in_rest'      => true,
		] );
	}

	private function register_property_amenity(): void {
		register_taxonomy( 'stl_amenity', 'stl_property', [
			'labels'       => [
				'name'          => __( 'Amenities', 'smarttolet' ),
				'singular_name' => __( 'Amenity',   'smarttolet' ),
				'add_new_item'  => __( 'Add New Amenity', 'smarttolet' ),
			],
			'hierarchical' => false,
			'show_ui'      => true,
			'rewrite'      => [ 'slug' => 'amenity' ],
			'show_in_rest' => true,
		] );
	}
}
