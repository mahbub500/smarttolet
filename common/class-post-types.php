<?php
/**
 * Custom post-type registration.
 *
 * @package SmartToLet\Common
 */

namespace SmartToLet\Common;

defined( 'ABSPATH' ) || exit;

/**
 * Class Post_Types
 */
class Post_Types {

	/** @var Post_Types|null */
	private static ?Post_Types $instance = null;

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
	 * Register the `property` CPT.
	 */
	public function register(): void {
		$labels = [
			'name'               => __( 'Properties',            'smarttolet' ),
			'singular_name'      => __( 'Property',              'smarttolet' ),
			'add_new'            => __( 'Add Property',          'smarttolet' ),
			'add_new_item'       => __( 'Add New Property',      'smarttolet' ),
			'edit_item'          => __( 'Edit Property',         'smarttolet' ),
			'new_item'           => __( 'New Property',          'smarttolet' ),
			'view_item'          => __( 'View Property',         'smarttolet' ),
			'search_items'       => __( 'Search Properties',     'smarttolet' ),
			'not_found'          => __( 'No properties found',   'smarttolet' ),
			'not_found_in_trash' => __( 'No properties in trash','smarttolet' ),
			'menu_name'          => __( 'Properties',            'smarttolet' ),
		];

		register_post_type( 'stl_property', [
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'smarttolet',
			'query_var'          => true,
			'rewrite'            => [ 'slug' => 'property' ],
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'author' ],
			'show_in_rest'       => true,
		] );
	}
}
