<?php
/**
 * Shortcode registration.
 *
 * @package SmartToLet\Common
 */

namespace SmartToLet\Common;

defined( 'ABSPATH' ) || exit;

/**
 * Class Shortcodes
 */
class Shortcodes {

	/** @var Shortcodes|null */
	private static ?Shortcodes $instance = null;

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
	 * Register all shortcodes.
	 */
	public function register(): void {
		add_shortcode( 'smarttolet_listings',  [ $this, 'render_listings'  ] );
		add_shortcode( 'smarttolet_search',    [ $this, 'render_search'    ] );
		add_shortcode( 'smarttolet_enquiry',   [ $this, 'render_enquiry'   ] );
		add_shortcode( 'smarttolet_featured',  [ $this, 'render_featured'  ] );
	}

	/**
	 * [smarttolet_listings per_page="9" type="" location=""]
	 */
	public function render_listings( array $atts ): string {
		$atts = shortcode_atts( [
			'per_page' => 9,
			'type'     => '',
			'location' => '',
			'orderby'  => 'date',
			'order'    => 'DESC',
		], $atts, 'smarttolet_listings' );

		ob_start();
		include SMARTTOLET_PATH . 'frontend/views/listings.php';
		return ob_get_clean();
	}

	/**
	 * [smarttolet_search]
	 */
	public function render_search( array $atts ): string {
		$atts = shortcode_atts( [], $atts, 'smarttolet_search' );

		ob_start();
		include SMARTTOLET_PATH . 'frontend/views/search.php';
		return ob_get_clean();
	}

	/**
	 * [smarttolet_enquiry property_id=""]
	 */
	public function render_enquiry( array $atts ): string {
		$atts = shortcode_atts( [
			'property_id' => get_the_ID(),
		], $atts, 'smarttolet_enquiry' );

		ob_start();
		include SMARTTOLET_PATH . 'frontend/views/enquiry-form.php';
		return ob_get_clean();
	}

	/**
	 * [smarttolet_featured limit="3"]
	 */
	public function render_featured( array $atts ): string {
		$atts = shortcode_atts( [ 'limit' => 3 ], $atts, 'smarttolet_featured' );

		ob_start();
		include SMARTTOLET_PATH . 'frontend/views/featured.php';
		return ob_get_clean();
	}
}
