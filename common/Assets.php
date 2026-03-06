<?php
/**
 * Shared asset enqueue logic.
 *
 * @package SmartToLet\Common
 */
namespace SmartToLet\Common;

defined( 'ABSPATH' ) || exit;

/**
 * Class Assets
 *
 * Registers and enqueues CSS/JS for both admin and frontend.
 */
class Assets {

    /** @var Assets|null */
    private static ?Assets $instance = null;

    private function __construct() {
        add_action( 'wp_enqueue_scripts',    [ $this, 'enqueue_frontend_assets' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    public static function get_instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Frontend assets.
     */
    public function enqueue_frontend_assets(): void {

        // ── Common ─────────────────────────────────────────────────────────
        wp_enqueue_style(
            'smarttolet-common',
            SMARTTOLET_URL . 'assets/css/common.css',
            [],
            SMARTTOLET_VERSION
        );

        wp_enqueue_script(
            'smarttolet-common',
            SMARTTOLET_URL . 'assets/js/common.js',
            [ 'jquery' ],
            SMARTTOLET_VERSION,
            true
        );

        // ── Frontend ───────────────────────────────────────────────────────
        wp_enqueue_style(
            'smarttolet-frontend',
            SMARTTOLET_URL . 'frontend/css/frontend.css',
            [ 'smarttolet-common' ],
            SMARTTOLET_VERSION
        );

        wp_enqueue_script(
            'smarttolet-frontend',
            SMARTTOLET_URL . 'frontend/js/frontend.js',
            [ 'jquery', 'smarttolet-common' ],
            SMARTTOLET_VERSION,
            true
        );

        wp_localize_script( 'smarttolet-frontend', 'smarttolet', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'smarttolet_nonce' ),
            'i18n'     => [
                'loading'      => __( 'Loading…',             'smarttolet' ),
                'error'        => __( 'Something went wrong.', 'smarttolet' ),
                'enquiry_sent' => __( 'Enquiry sent!',         'smarttolet' ),
            ],
        ] );
    }

    /**
     * Admin assets – only on plugin pages.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_assets( string $hook ): void {

        // Load on SmartToLet screens and property CPT screens.
        $is_stl_screen = (
            false !== strpos( $hook, 'smarttolet' ) ||
            ( isset( $_GET['post_type'] ) && 'stl_property' === $_GET['post_type'] ) || // phpcs:ignore
            ( function_exists( 'get_current_screen' ) && 'stl_property' === get_current_screen()?->post_type )
        );

        if ( ! $is_stl_screen ) {
            return;
        }

        // ── Common ─────────────────────────────────────────────────────────
        wp_enqueue_style(
            'smarttolet-common',
            SMARTTOLET_URL . 'assets/css/common.css',
            [],
            SMARTTOLET_VERSION
        );

        wp_enqueue_script(
            'smarttolet-common',
            SMARTTOLET_URL . 'assets/js/common.js',
            [ 'jquery' ],
            SMARTTOLET_VERSION,
            true
        );

        // ── Admin ──────────────────────────────────────────────────────────
        wp_enqueue_style(
            'smarttolet-admin',
            SMARTTOLET_URL . 'admin/css/admin.css',
            [ 'smarttolet-common' ],
            SMARTTOLET_VERSION
        );

        wp_enqueue_media();

        wp_enqueue_script(
            'smarttolet-admin',
            SMARTTOLET_URL . 'admin/js/admin.js',
            [ 'jquery', 'jquery-ui-sortable', 'wp-color-picker', 'smarttolet-common' ],
            SMARTTOLET_VERSION,
            true
        );

        wp_localize_script( 'smarttolet-admin', 'smarttolet_admin', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'smarttolet_admin_nonce' ),
            'i18n'     => [
                'confirm_delete' => __( 'Are you sure you want to delete this?', 'smarttolet' ),
                'select_image'   => __( 'Select Image',                          'smarttolet' ),
                'use_image'      => __( 'Use this Image',                        'smarttolet' ),
            ],
        ] );
    }
}