<?php
/**
 * Reusable property query helper.
 *
 * @package SmartToLet\Frontend
 */

namespace SmartToLet\Frontend;

defined( 'ABSPATH' ) || exit;

/**
 * Class Property_Query
 */
class Property_Query {

	/** @var Property_Query|null */
	private static ?Property_Query $instance = null;

	private function __construct() {
		add_action( 'pre_get_posts', [ $this, 'modify_main_query' ] );
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Adjust the main archive query for properties.
	 */
	public function modify_main_query( \WP_Query $query ): void {
		if (
			! is_admin() &&
			$query->is_main_query() &&
			$query->is_post_type_archive( 'stl_property' )
		) {
			$query->set( 'posts_per_page', 12 );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'meta_key', '_stl_price' );
		}
	}

	/**
	 * Build and return a WP_Query for properties.
	 *
	 * @param array $args Overrides for WP_Query defaults.
	 * @return \WP_Query
	 */
	public static function query( array $args = [] ): \WP_Query {
		$defaults = [
			'post_type'      => 'stl_property',
			'post_status'    => 'publish',
			'posts_per_page' => 9,
			'no_found_rows'  => false,
		];

		return new \WP_Query( wp_parse_args( $args, $defaults ) );
	}

	/**
	 * Get featured properties.
	 *
	 * @param int $limit Number to return.
	 * @return \WP_Query
	 */
	public static function featured( int $limit = 3 ): \WP_Query {
		return self::query( [
			'posts_per_page' => $limit,
			'meta_query'     => [ // phpcs:ignore WordPress.DB.SlowDBQuery
				[
					'key'   => '_stl_featured',
					'value' => '1',
				],
			],
		] );
	}
}
