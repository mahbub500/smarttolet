<?php
/**
 * Template loader – allows themes to override plugin templates.
 *
 * @package SmartToLet\Frontend
 */

namespace SmartToLet\Frontend;

defined( 'ABSPATH' ) || exit;

/**
 * Class Template_Loader
 */
class Template_Loader {

	/** @var Template_Loader|null */
	private static ?Template_Loader $instance = null;

	private function __construct() {
		add_filter( 'single_template',  [ $this, 'single_property_template' ] );
		add_filter( 'archive_template', [ $this, 'archive_property_template' ] );
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Load single-stl_property.php, with theme override support.
	 */
	public function single_property_template( string $template ): string {
		if ( is_singular( 'stl_property' ) ) {
			$theme_file  = get_stylesheet_directory() . '/smarttolet/single-property.php';
			$plugin_file = SMARTTOLET_PATH . 'frontend/views/single-property.php';

			if ( file_exists( $theme_file ) ) {
				return $theme_file;
			} elseif ( file_exists( $plugin_file ) ) {
				return $plugin_file;
			}
		}
		return $template;
	}

	/**
	 * Load archive-stl_property.php, with theme override support.
	 */
	public function archive_property_template( string $template ): string {
		if ( is_post_type_archive( 'stl_property' ) || is_tax( [ 'stl_property_type', 'stl_location', 'stl_amenity' ] ) ) {
			$theme_file  = get_stylesheet_directory() . '/smarttolet/archive-property.php';
			$plugin_file = SMARTTOLET_PATH . 'frontend/views/archive-property.php';

			if ( file_exists( $theme_file ) ) {
				return $theme_file;
			} elseif ( file_exists( $plugin_file ) ) {
				return $plugin_file;
			}
		}
		return $template;
	}

	/**
	 * Locate and return a template path, allowing theme overrides.
	 *
	 * @param string $template Template filename (e.g. 'property-card.php').
	 * @return string Absolute path to template.
	 */
	public static function locate( string $template ): string {
		$theme_file  = get_stylesheet_directory() . '/smarttolet/' . $template;
		$plugin_file = SMARTTOLET_PATH . 'frontend/views/' . $template;

		if ( file_exists( $theme_file ) ) {
			return $theme_file;
		}
		return $plugin_file;
	}

	/**
	 * Include a template, passing variables.
	 *
	 * @param string $template Template filename.
	 * @param array  $vars     Variables to extract into template scope.
	 */
	public static function include( string $template, array $vars = [] ): void {
		$path = self::locate( $template );
		if ( file_exists( $path ) ) {
			// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			extract( $vars );
			include $path;
		}
	}
}
