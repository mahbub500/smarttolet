<?php
/**
 * Plugin settings (Settings API).
 *
 * @package SmartToLet\Admin
 */

namespace SmartToLet\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Settings
 */
class Settings {

	/** Option group/page slug. */
	const OPTION_GROUP = 'smarttolet_settings';
	const OPTION_NAME  = 'smarttolet_options';

	/** @var Settings|null */
	private static ?Settings $instance = null;

	private function __construct() {
		add_action( 'admin_init', [ $this, 'register' ] );
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register settings, sections and fields.
	 */
	public function register(): void {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			[ $this, 'sanitize' ]
		);

		// ── General section ────────────────────────────────────────────────
		add_settings_section(
			'stl_general',
			__( 'General Settings', 'smarttolet' ),
			'__return_false',
			self::OPTION_GROUP
		);

		add_settings_field( 'currency_symbol', __( 'Currency Symbol', 'smarttolet' ), [ $this, 'field_text' ], self::OPTION_GROUP, 'stl_general', [ 'key' => 'currency_symbol', 'default' => '£' ] );
		add_settings_field( 'price_suffix',    __( 'Price Suffix',    'smarttolet' ), [ $this, 'field_text' ], self::OPTION_GROUP, 'stl_general', [ 'key' => 'price_suffix',    'default' => '/mo' ] );
		add_settings_field( 'listings_page',   __( 'Listings Page',   'smarttolet' ), [ $this, 'field_page' ], self::OPTION_GROUP, 'stl_general', [ 'key' => 'listings_page' ] );

		// ── Maps section ───────────────────────────────────────────────────
		add_settings_section(
			'stl_maps',
			__( 'Maps & Geolocation', 'smarttolet' ),
			'__return_false',
			self::OPTION_GROUP
		);

		add_settings_field( 'maps_api_key', __( 'Google Maps API Key', 'smarttolet' ), [ $this, 'field_text' ], self::OPTION_GROUP, 'stl_maps', [ 'key' => 'maps_api_key' ] );

		// ── Email section ──────────────────────────────────────────────────
		add_settings_section(
			'stl_email',
			__( 'Email Notifications', 'smarttolet' ),
			'__return_false',
			self::OPTION_GROUP
		);

		add_settings_field( 'notify_email', __( 'Admin Notification Email', 'smarttolet' ), [ $this, 'field_text' ], self::OPTION_GROUP, 'stl_email', [ 'key' => 'notify_email', 'default' => get_option( 'admin_email' ) ] );
	}

	/**
	 * Sanitize saved options.
	 */
	public function sanitize( array $input ): array {
		$output = [];
		$keys   = [ 'currency_symbol', 'price_suffix', 'maps_api_key', 'notify_email' ];

		foreach ( $keys as $key ) {
			if ( isset( $input[ $key ] ) ) {
				$output[ $key ] = sanitize_text_field( $input[ $key ] );
			}
		}

		$output['listings_page'] = absint( $input['listings_page'] ?? 0 );

		return $output;
	}

	/** Text field renderer. */
	public function field_text( array $args ): void {
		$options = get_option( self::OPTION_NAME, [] );
		$value   = $options[ $args['key'] ] ?? ( $args['default'] ?? '' );
		printf(
			'<input type="text" name="%s[%s]" value="%s" class="regular-text" />',
			esc_attr( self::OPTION_NAME ),
			esc_attr( $args['key'] ),
			esc_attr( $value )
		);
	}

	/** Page dropdown renderer. */
	public function field_page( array $args ): void {
		$options = get_option( self::OPTION_NAME, [] );
		$value   = $options[ $args['key'] ] ?? 0;
		wp_dropdown_pages( [
			'name'              => self::OPTION_NAME . '[' . $args['key'] . ']',
			'selected'          => $value,
			'show_option_none'  => __( '— Select page —', 'smarttolet' ),
			'option_none_value' => '0',
		] );
	}

	/**
	 * Retrieve a single option value.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Fallback.
	 * @return mixed
	 */
	public static function get( string $key, $default = '' ) {
		$options = get_option( self::OPTION_NAME, [] );
		return $options[ $key ] ?? $default;
	}
}
