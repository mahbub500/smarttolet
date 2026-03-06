<?php
/**
 * Account Model
 *
 * Handles:
 *  - Mobile number (required) registration
 *  - Email (optional) registration
 *  - Login via phone OR username
 *  - Placeholder email when user skips email field
 *  - Hook ready for OTP later
 *
 * @package SmartToLet
 * @namespace SmartToLet\Model
 */

namespace SmartToLet\Model;

use \Directorist\Helper;
use \ATBDP_Permalink;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NOTE: We do NOT extend Directorist_Account directly here because
 * Directorist_Account's constructor is private — it cannot be extended.
 * Instead we replicate its render() logic and delegate where possible.
 */
class Account {

    /* ---------------------------------------------------------------
     * Singleton — matches get_instance() pattern used in SmartToLet.php
     * ------------------------------------------------------------- */
    private static ?Account $instance = null;

    private function __construct() {
        // Hooks are registered from SmartToLet::init() after plugins_loaded
        // so all Directorist functions are guaranteed to exist here.
        $this->register_hooks();
    }

    /**
     * Returns the singleton instance.
     * Called as Account::get_instance() to match your plugin's pattern.
     */
    public static function get_instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /* ---------------------------------------------------------------
     * Hooks
     * ------------------------------------------------------------- */
    private function register_hooks(): void {

        /**
         * Resolve phone → WP username at login.
         * Priority 5 so it runs before Directorist reads $_POST['username'].
         */
        add_action( 'init', [ $this, 'resolve_phone_login' ], 5 );

        /**
         * Save mobile number as user meta after successful registration.
         */
        add_action( 'atbdp_after_user_registration', [ $this, 'save_mobile_number' ] );

        /**
         * Validate mobile: required, valid format, unique.
         */
        add_filter( 'directorist_registration_errors', [ $this, 'validate_mobile_number' ] );

        /**
         * Generate a placeholder email when user leaves email blank.
         * WordPress's wp_insert_user() requires an email internally.
         */
        add_filter( 'directorist_new_user_data', [ $this, 'maybe_generate_placeholder_email' ] );

        /**
         * Register the [smarttolet_account] shortcode.
         */
        add_shortcode( 'smarttolet_account', [ $this, 'shortcode' ] );
    }

    /* ---------------------------------------------------------------
     * Shortcode callback
     * [smarttolet_account active_form="signup"]
     * ------------------------------------------------------------- */
    public function shortcode( $atts ): string {
        return $this->render( (array) $atts );
    }

    /* ---------------------------------------------------------------
     * Default attributes
     * ------------------------------------------------------------- */
    public static function default_atts(): array {
        // Pull Directorist's own defaults via its class (not extending it)
        $directorist_atts = \Directorist\Directorist_Account::shortcode_atts();

        $our_atts = [
            'phone_label'       => __( 'Mobile Number', 'smarttolet' ),
            'phone_placeholder' => __( '+880 1700 000000', 'smarttolet' ),
            'phone_required'    => 'yes',
            'email_required'    => 'no',
        ];

        return apply_filters(
            'smarttolet_account_default_atts',
            array_merge( $directorist_atts, $our_atts )
        );
    }

    /* ---------------------------------------------------------------
     * Render the login / registration form
     * ------------------------------------------------------------- */
    public function render( array $atts = [] ): string {

        // Redirect logged-in users
        if ( is_user_logged_in() && apply_filters( 'directorist_account_page_accessible', true ) ) {
            $msg = sprintf(
                __( 'This page is for logged-out users only. <a href="%s">Go to Dashboard</a>', 'smarttolet' ),
                esc_url( ATBDP_Permalink::get_dashboard_page_link() )
            );
            ob_start();
            \ATBDP()->helper->show_login_message(
                apply_filters( 'atbdp_registration_page_registered_msg', $msg )
            );
            return ob_get_clean();
        }

        $atts = shortcode_atts( self::default_atts(), $atts );

        $user_type = ! empty( $_REQUEST['user_type'] )
            ? sanitize_text_field( wp_unslash( $_REQUEST['user_type'] ) )
            : ( $atts['user_type'] ?? '' );

        $active_form = ( isset( $_GET['signup'] ) && directorist_is_user_registration_enabled() )
            ? 'signup'
            : ( $atts['active_form'] ?? 'signin' );

        // Pass params to Directorist's JS (required for its AJAX to work)
        $js_data = [
            'enable_user_type'                 => $atts['user_role'],
            'user_type'                        => $user_type,
            'enable_registration_password'     => $atts['password'],
            'registration_username'            => $atts['username_label'],
            'enable_registration_website'      => $atts['website'],
            'registration_website_required'    => $atts['website_required'],
            'enable_registration_first_name'   => $atts['firstname'],
            'registration_first_name_required' => $atts['firstname_required'],
            'enable_registration_last_name'    => $atts['lastname'],
            'registration_last_name_required'  => $atts['lastname_required'],
            'enable_registration_bio'          => $atts['bio'],
            'registration_bio_required'        => $atts['bio_required'],
            'enable_registration_privacy'      => $atts['privacy'],
            'enable_registration_terms'        => $atts['terms'],
            'auto_login_after_registration'    => $atts['signin_after_signup'],
            'redirection_after_registration'   => $atts['signup_redirect_url'],
            'active_form'                      => $active_form,
            // SmartToLet extras
            'phone_required'                   => $atts['phone_required'],
            'email_required'                   => $atts['email_required'],
        ];
        wp_localize_script( 'directorist-account', 'directorist_signin_signup_params', $js_data );
        wp_localize_script( 'jquery',              'directorist_signin_signup_params', $js_data );

        // Template variables
        $template_args = [
            'log_username'              => $atts['signin_username_label'],
            'log_password'              => $atts['password_label'],
            'log_button'                => $atts['signin_button_label'],
            'display_recpass'           => $atts['enable_recovery_password'],
            'recpass_text'              => $atts['recovery_password_label'],
            'recpass_desc'              => $atts['recovery_password_description'],
            'recpass_username'          => $atts['recovery_password_email_label'],
            'recpass_placeholder'       => $atts['recovery_password_email_placeholder'],
            'recpass_button'            => $atts['recovery_password_button_label'],
            'reg_text'                  => $atts['signup_label'],
            'reg_url'                   => ATBDP_Permalink::get_registration_page_link(),
            'reg_linktxt'               => $atts['signup_linking_text'],
            'new_user_registration'     => directorist_is_user_registration_enabled(),
            'parent'                    => 0,
            'container_fluid'           => is_directoria_active() ? 'container' : 'container-fluid',
            'username'                  => $atts['username_label'],
            'password'                  => $atts['password_label'],
            'display_password_reg'      => $atts['password'],
            'email'                     => $atts['email_label'],
            'display_website'           => $atts['website'],
            'website'                   => $atts['website_label'],
            'require_website'           => $atts['website_required'],
            'display_fname'             => $atts['firstname'],
            'first_name'                => $atts['firstname_label'],
            'require_fname'             => $atts['firstname_required'],
            'display_lname'             => $atts['lastname'],
            'last_name'                 => $atts['lastname_label'],
            'require_lname'             => $atts['lastname_required'],
            'display_bio'               => $atts['bio'],
            'bio'                       => $atts['bio_label'],
            'require_bio'               => $atts['bio_required'],
            'reg_signup'                => $atts['signup_button_label'],
            'login_text'                => $atts['signin_message'],
            'login_url'                 => ATBDP_Permalink::get_login_page_link(),
            'log_linkingmsg'            => $atts['signin_linking_text'],
            'enable_registration_terms' => $atts['terms'],
            'terms_label'               => $atts['terms_label'],
            'terms_label_link'          => $atts['terms_linking_text'],
            't_C_page_link'             => ATBDP_Permalink::get_terms_and_conditions_page_url(),
            'privacy_page_link'         => ATBDP_Permalink::get_privacy_policy_page_url(),
            'registration_privacy'      => $atts['privacy'],
            'privacy_label'             => $atts['privacy_label'],
            'privacy_label_link'        => $atts['privacy_linking_text'],
            'user_type'                 => $user_type,
            'author_checked'            => ( 'author'  === $user_type ) ? 'checked' : '',
            'general_checked'           => ( 'general' === $user_type ) ? 'checked' : '',
            'enable_user_type'          => $atts['user_role'],
            'author_role_label'         => $atts['author_role_label'],
            'user_role_label'           => $atts['user_role_label'],
            'active_form'               => $active_form,
            'display_rememberme'        => get_directorist_option( 'display_rememberme', 1 ) ? 'yes' : 'no',
            'rememberme_label'          => get_directorist_option( 'log_rememberme' ),
            // SmartToLet phone args
            'phone_label'               => $atts['phone_label'],
            'phone_placeholder'         => $atts['phone_placeholder'],
            'phone_required'            => $atts['phone_required'],
            'email_required'            => $atts['email_required'],
        ];

        return Helper::get_template_contents( 'account/login-registration-form', $template_args );
    }

    /* ---------------------------------------------------------------
     * Hook: resolve phone number → WP username at login
     * ------------------------------------------------------------- */
    public function resolve_phone_login(): void {
        if ( empty( $_POST['login_by_phone'] ) || empty( $_POST['username'] ) ) {
            return;
        }
        // Only during Directorist AJAX login
        if ( empty( $_POST['security'] ) && empty( $_POST['directorist_nonce'] ) ) {
            return;
        }

        $input      = sanitize_text_field( wp_unslash( $_POST['username'] ) );
        $normalized = $this->normalize_phone( $input );

        $users = get_users( [
            'meta_key' => 'smarttolet_mobile', // phpcs:ignore WordPress.DB.SlowDBQuery
            'number'   => -1,
            'fields'   => [ 'ID', 'user_login' ],
        ] );

        foreach ( $users as $u ) {
            $stored = (string) get_user_meta( $u->ID, 'smarttolet_mobile', true );
            if ( $this->normalize_phone( $stored ) === $normalized ) {
                $_POST['username'] = $u->user_login;
                return;
            }
        }
    }

    /* ---------------------------------------------------------------
     * Hook: save mobile number after registration
     * ------------------------------------------------------------- */
    public function save_mobile_number( int $user_id ): void {
        if ( empty( $_POST['mobile_number'] ) ) {
            return;
        }
        $mobile = sanitize_text_field( wp_unslash( $_POST['mobile_number'] ) );
        update_user_meta( $user_id, 'smarttolet_mobile', $mobile );

        /**
         * Fires after mobile number is saved.
         * Use this to plug in OTP verification later:
         *
         *   add_action( 'smarttolet_after_mobile_saved', function( $user_id, $mobile ) {
         *       // MyOTP::send( $mobile );
         *   }, 10, 2 );
         */
        do_action( 'smarttolet_after_mobile_saved', $user_id, $mobile );
    }

    /* ---------------------------------------------------------------
     * Hook: validate mobile on registration
     * ------------------------------------------------------------- */
    public function validate_mobile_number( \WP_Error $errors ): \WP_Error {
        $mobile = isset( $_POST['mobile_number'] )
            ? sanitize_text_field( wp_unslash( $_POST['mobile_number'] ) )
            : '';

        if ( empty( $mobile ) ) {
            $errors->add(
                'smarttolet_mobile_required',
                __( '<strong>Error:</strong> Mobile number is required.', 'smarttolet' )
            );
            return $errors;
        }

        if ( ! preg_match( '/^\+?[\d\s\-().]{7,20}$/', $mobile ) ) {
            $errors->add(
                'smarttolet_mobile_invalid',
                __( '<strong>Error:</strong> Please enter a valid mobile number.', 'smarttolet' )
            );
            return $errors;
        }

        // Uniqueness check
        $normalized = $this->normalize_phone( $mobile );
        $all_users  = get_users( [
            'meta_key' => 'smarttolet_mobile', // phpcs:ignore WordPress.DB.SlowDBQuery
            'number'   => -1,
            'fields'   => 'ids',
        ] );
        foreach ( $all_users as $uid ) {
            $stored = (string) get_user_meta( $uid, 'smarttolet_mobile', true );
            if ( $this->normalize_phone( $stored ) === $normalized ) {
                $errors->add(
                    'smarttolet_mobile_exists',
                    __( '<strong>Error:</strong> This mobile number is already registered.', 'smarttolet' )
                );
                break;
            }
        }

        return $errors;
    }

    /* ---------------------------------------------------------------
     * Hook: generate placeholder email when user skips email field.
     * WordPress requires an email for wp_insert_user() to succeed.
     * ------------------------------------------------------------- */
    public function maybe_generate_placeholder_email( array $user_data ): array {
        if ( ! empty( $user_data['user_email'] ) ) {
            return $user_data;
        }
        if ( ! empty( $_POST['mobile_number'] ) ) {
            $digits = preg_replace( '/\D/', '', sanitize_text_field( wp_unslash( $_POST['mobile_number'] ) ) );
            $user_data['user_email'] = 'mobile_' . $digits . '@noemail.smarttolet.local';
        }
        return $user_data;
    }

    /* ---------------------------------------------------------------
     * Utility: find a WP_User by stored mobile number
     *
     * Usage: Account::get_user_by_mobile( '+8801700000000' );
     * ------------------------------------------------------------- */
    public static function get_user_by_mobile( string $mobile ): \WP_User|false {
        $self       = self::get_instance();
        $normalized = $self->normalize_phone( $mobile );

        $users = get_users( [
            'meta_key' => 'smarttolet_mobile', // phpcs:ignore WordPress.DB.SlowDBQuery
            'number'   => -1,
            'fields'   => [ 'ID', 'user_login' ],
        ] );
        foreach ( $users as $u ) {
            $stored = (string) get_user_meta( $u->ID, 'smarttolet_mobile', true );
            if ( $self->normalize_phone( $stored ) === $normalized ) {
                return get_userdata( $u->ID );
            }
        }
        return false;
    }

    /* ---------------------------------------------------------------
     * Helper: strip formatting for phone comparison
     * +880 1234-567 890 → +8801234567890
     * ------------------------------------------------------------- */
    private function normalize_phone( string $phone ): string {
        $phone    = trim( $phone );
        $has_plus = str_starts_with( $phone, '+' );
        $digits   = preg_replace( '/\D/', '', $phone );
        return $has_plus ? '+' . $digits : $digits;
    }
}