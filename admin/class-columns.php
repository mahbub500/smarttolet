<?php
/**
 * Custom columns for the property list table.
 *
 * @package SmartToLet\Admin
 */

namespace SmartToLet\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Columns
 */
class Columns {

	/** @var Columns|null */
	private static ?Columns $instance = null;

	private function __construct() {
		add_filter( 'manage_stl_property_posts_columns',       [ $this, 'register' ] );
		add_action( 'manage_stl_property_posts_custom_column', [ $this, 'render' ], 10, 2 );
		add_filter( 'manage_edit-stl_property_sortable_columns', [ $this, 'sortable' ] );
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Define columns.
	 */
	public function register( array $columns ): array {
		$new = [];
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['stl_thumbnail'] = __( 'Photo',     'smarttolet' );
				$new['stl_price']     = __( 'Price',     'smarttolet' );
				$new['stl_bedrooms']  = __( 'Beds',      'smarttolet' );
				$new['stl_status']    = __( 'Status',    'smarttolet' );
				$new['stl_featured']  = __( 'Featured',  'smarttolet' );
			}
		}
		return $new;
	}

	/**
	 * Render column content.
	 */
	public function render( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'stl_thumbnail':
				echo get_the_post_thumbnail( $post_id, [ 60, 60 ] );
				break;

			case 'stl_price':
				$price = Meta_Boxes::get( $post_id, 'price', '—' );
				$sym   = Settings::get( 'currency_symbol', '£' );
				$suf   = Settings::get( 'price_suffix', '/mo' );
				echo esc_html( $price ? "{$sym}{$price}{$suf}" : '—' );
				break;

			case 'stl_bedrooms':
				echo esc_html( Meta_Boxes::get( $post_id, 'bedrooms', '—' ) );
				break;

			case 'stl_status':
				$status = Meta_Boxes::get( $post_id, 'status', 'available' );
				$label  = ucfirst( str_replace( '_', ' ', $status ) );
				$class  = 'available' === $status ? 'stl-badge stl-badge--green' : 'stl-badge stl-badge--red';
				echo '<span class="' . esc_attr( $class ) . '">' . esc_html( $label ) . '</span>';
				break;

			case 'stl_featured':
				$featured = Meta_Boxes::get( $post_id, 'featured', 0 );
				echo $featured
					? '<span class="dashicons dashicons-star-filled" style="color:#f0b429"></span>'
					: '<span class="dashicons dashicons-star-empty"></span>';
				break;
		}
	}

	/**
	 * Make price column sortable.
	 */
	public function sortable( array $columns ): array {
		$columns['stl_price'] = 'stl_price';
		return $columns;
	}
}
