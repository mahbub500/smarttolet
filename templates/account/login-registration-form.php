<?php
/**
 * SmartToLet login/registration template
 *
 * Variables available from Account::render():
 *   $phone_label        — e.g. "Mobile Number"
 *   $phone_placeholder  — e.g. "+880 1700 000000"
 *   $phone_required     — 'yes' | 'no'
 *   $email_required     — 'yes' | 'no'
 *   $active_form        — 'signin' | 'signup'
 *   ... all standard Directorist template variables
 *
 * Deploy to: your-theme/directorist/account/login-registration-form.php
 *
 * @package SmartToLet
 */

use \Directorist\Helper;

$user_email           = isset( $_GET['user'] ) ? sanitize_email( wp_unslash( base64_decode( $_GET['user'] ) ) ) : '';
$key                  = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
$registration_success = false;

// Ensure $active_form always has a default
if ( empty( $active_form ) ) {
    $active_form = 'signin';
}

if ( ! empty( $_GET['registration_status'] ) ) {
    $active_form          = 'signin';
    $registration_success = true;
}

// Phone field defaults (in case template is used without the model)
$phone_label       = isset( $phone_label )       ? $phone_label       : __( 'Mobile Number', 'smarttolet' );
$phone_placeholder = isset( $phone_placeholder ) ? $phone_placeholder : '+880 1700 000000';
$phone_required    = isset( $phone_required )    ? $phone_required    : 'yes';
$email_required    = isset( $email_required )    ? $email_required    : 'no';
?>

<div class="directorist-w-100">
    <div class="<?php Helper::directorist_container_fluid(); ?>">
        <div class="<?php Helper::directorist_row(); ?>">

            <!-- ===================== LOGIN FORM ===================== -->
            <div class="directorist-col-md-6 directorist-offset-md-3 directorist-login-wrapper directorist-authentication <?php echo esc_attr( $active_form === 'signin' ? 'active' : '' ); ?>">
                <div class="atbdp_login_form_shortcode directorist-authentication__form">

                    <?php if ( $registration_success ) : ?>
                        <p style="padding:20px" class="directorist-alert directorist-alert-success">
                            <span><?php esc_html_e( 'Registration completed. Please login here.', 'smarttolet' ); ?></span>
                        </p>
                    <?php endif; ?>

                    <?php if ( directorist_is_email_verification_enabled() && ! empty( $_GET['verification'] ) && is_email( $user_email ) ) : ?>
                        <p class="directorist-alert directorist-alert-success"><span>
                            <?php
                            $send_confirm_mail_url = add_query_arg( [
                                'action'            => 'directorist_send_confirmation_email',
                                'user'              => $user_email,
                                'directorist_nonce' => wp_create_nonce( 'directorist_nonce' ),
                            ], admin_url( 'admin-ajax.php' ) );
                            echo wp_kses( sprintf( __( "Thank you for signing up! Please verify your email.<br><br>Can't find it? <a href='%s'>Resend confirmation email</a>.", 'smarttolet' ), esc_url( $send_confirm_mail_url ) ), [ 'a' => [ 'href' => [] ], 'br' => [] ] );
                            ?>
                        </span></p>
                    <?php endif; ?>

                    <?php
                    // ---- Password reset flow ----
                    if ( is_email( $user_email ) && ! empty( $key ) ) :
                        $user = get_user_by( 'email', $user_email );
                        if ( ! $user ) : ?>
                            <p class="directorist-alert directorist-alert-danger"><?php esc_html_e( 'Sorry, user not found.', 'smarttolet' ); ?></p>
                        <?php else :
                            $valid_key = check_password_reset_key( $key, $user->user_login );
                            if ( is_wp_error( $valid_key ) ) : ?>
                                <p class="directorist-alert directorist-alert-danger"><?php echo esc_html( $valid_key->get_error_message() ); ?></p>
                            <?php else :
                                if ( ! empty( $_POST['directorist_reset_password'] ) && directorist_verify_nonce( 'directorist-reset-password-nonce', 'reset_password' ) ) :
                                    $pw1 = isset( $_POST['password_1'] ) ? $_POST['password_1'] : ''; // phpcs:ignore
                                    $pw2 = isset( $_POST['password_2'] ) ? $_POST['password_2'] : ''; // phpcs:ignore
                                    if ( empty( $pw1 ) || empty( $pw2 ) ) : ?>
                                        <p class="directorist-alert directorist-alert-danger"><?php esc_html_e( 'Passwords cannot be empty.', 'smarttolet' ); ?></p>
                                    <?php elseif ( $pw1 !== $pw2 ) : ?>
                                        <p class="directorist-alert directorist-alert-danger"><?php esc_html_e( 'Passwords do not match.', 'smarttolet' ); ?></p>
                                    <?php else :
                                        wp_set_password( $pw2, $user->ID );
                                        delete_user_meta( $user->ID, 'directorist_user_email_unverified' ); ?>
                                        <p class="directorist-alert directorist-alert-success">
                                            <?php echo wp_kses( sprintf( __( 'Password changed. <a href="%s">Click here to login</a>.', 'smarttolet' ), esc_url( ATBDP_Permalink::get_signin_signup_page_link() ) ), [ 'a' => [ 'href' => [] ] ] ); ?>
                                        </p>
                                    <?php endif;
                                endif;

                                if ( ! empty( $_GET['password_reset'] ) ) {
                                    include ATBDP_DIR . 'templates/account/password-reset-form.php';
                                }

                                if ( ! empty( $_GET['confirm_mail'] ) ) {
                                    delete_user_meta( $user->ID, 'directorist_user_email_unverified' );
                                    ATBDP()->email->custom_wp_new_user_notification_email( $user->ID ); ?>
                                    <div class="directorist-alert directorist-alert-success">
                                        <?php echo wp_kses( sprintf( __( 'Email verified. <a href="%s">Click here to login</a>.', 'smarttolet' ), esc_url( ATBDP_Permalink::get_signin_signup_page_link() ) ), [ 'a' => [ 'href' => [] ] ] ); ?>
                                    </div>
                                <?php }
                            endif;
                        endif;

                    else : // ---- Normal login form ---- ?>

                        <form action="#" id="directorist__authentication__login" method="POST" class="directorist__authentication__signin">
                            <p class="status"></p>

                            <!-- Phone OR Username field -->
                            <div class="directorist-form-group directorist-mb-15">
                                <label for="username"><?php esc_html_e( 'Phone Number or Username', 'smarttolet' ); ?></label>
                                <input
                                    type="text"
                                    class="directorist-form-element"
                                    id="username"
                                    name="username"
                                    placeholder="<?php esc_attr_e( 'Phone number or username', 'smarttolet' ); ?>"
                                />
                            </div>

                            <!-- Password field -->
                            <div class="directorist-form-group directorist-password-group">
                                <label for="password"><?php echo esc_html( $log_password ); ?></label>
                                <input type="password" id="password" autocomplete="off" name="password" class="directorist-form-element directorist-password-group-input" />
                                <span class="directorist-password-group-toggle">
                                    <svg class="directorist-password-group-eyeIcon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24">
                                        <path stroke="#888" stroke-width="2" d="M1.5 12S5.5 5.5 12 5.5 22.5 12 22.5 12 18.5 18.5 12 18.5 1.5 12 1.5 12Z"/>
                                        <circle cx="12" cy="12" r="3.5" stroke="#888" stroke-width="2"/>
                                    </svg>
                                </span>
                            </div>

                            <div class="directorist-authentication__form__actions">
                                <?php if ( ! empty( $display_rememberme ) && 'yes' === $display_rememberme ) : ?>
                                    <div class="keep_signed directorist-checkbox">
                                        <input type="checkbox" id="directorist_login_keep_signed_in" value="1" name="keep_signed_in" checked />
                                        <label for="directorist_login_keep_signed_in" class="directorist-checkbox__label not_empty">
                                            <?php echo esc_html( $rememberme_label ); ?>
                                        </label>
                                    </div>
                                <?php endif; ?>
                                <?php if ( ! empty( $display_recpass ) && 'yes' === $display_recpass ) :
                                    echo wp_kses_post( sprintf( '<a href="" class="atbdp_recovery_pass">%s</a>', esc_html( $recpass_text ) ) );
                                endif; ?>
                            </div>

                            <div class="directorist-form-group directorist-mb-15 directorist-authentication__form__btn-wrapper">
                                <button class="directorist-btn directorist-btn-block directorist-authentication__form__btn" type="submit" name="submit" aria-label="Signin Button">
                                    <?php echo esc_html( $log_button ); ?>
                                </button>
                                <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
                            </div>
                        </form>

                        <div class="atbd_social_login">
                            <?php do_action( 'atbdp_before_login_form_end' ); ?>
                        </div>

                        <?php if ( directorist_is_user_registration_enabled() ) : ?>
                            <div class="directorist-authentication__form__toggle-area">
                                <?php echo esc_html( $reg_text ); ?>
                                <button class="directorist-authentication__btn directorist-authentication__btn--signup">
                                    <?php echo esc_html( $reg_linktxt ); ?>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php
                        // ---- Password recovery ----
                        $error = $success = '';
                        if ( isset( $_POST['action'] ) && 'reset' === $_POST['action'] && directorist_verify_nonce() ) :
                            echo '<style>#recover-pass-modal{display:block}</style>';
                            $reset_email = isset( $_POST['user_login'] ) ? sanitize_email( wp_unslash( $_POST['user_login'] ) ) : '';
                            if ( empty( $reset_email ) ) {
                                $error = __( 'Email cannot be empty.', 'smarttolet' );
                            } elseif ( ! is_email( $reset_email ) ) {
                                $error = __( 'Invalid email address.', 'smarttolet' );
                            } elseif ( ! email_exists( $reset_email ) ) {
                                $error = __( 'No user registered with that email.', 'smarttolet' );
                            } else {
                                $reset_user = get_user_by( 'email', $reset_email );
                                $subject    = sprintf( __( '[%s] Reset Your Password', 'smarttolet' ), get_option( 'blogname' ) );
                                $title      = __( 'Password Reset Request', 'smarttolet' );
                                $site_name  = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
                                $message    = sprintf( __( '<strong>Site:</strong> %1$s<br><strong>User:</strong> %2$s<br>Click to <a href="%3$s">reset your password</a>.<br>Ignore if this wasn\'t you.' ), $site_name, $reset_user->user_login, esc_url( directorist_password_reset_url( $reset_user, true ) ) );
                                $message    = atbdp_email_html( $title, nl2br( wp_kses( $message, [ 'br' => [], 'strong' => [], 'a' => [ 'href' => [] ] ] ) ) );
                                $mail       = wp_mail( $reset_email, esc_html( $subject ), $message, [ 'Content-Type: text/html; charset=UTF-8' ] );
                                if ( $mail ) {
                                    $success = __( 'Password reset email sent! Check your inbox.', 'smarttolet' );
                                    echo '<style>#recover-pass-modal{display:none}</style>';
                                } else {
                                    $error = __( 'Could not send reset email. Please contact support.', 'smarttolet' );
                                }
                            }
                            if ( $error )   echo '<p class="directorist-alert directorist-alert-danger">'  . wp_kses( $error, [ 'strong' => [] ] ) . '</p>';
                            if ( $success ) echo '<p class="directorist-alert directorist-alert-success">' . esc_html( $success ) . '</p>';
                        endif; ?>

                        <div id="recover-pass-modal" class="directorist-mt-15 directorist-authentication__form__recover-pass-modal">
                            <form action="#" method="post">
                                <fieldset class="directorist-form-group">
                                    <p><?php echo esc_html( $recpass_desc ); ?></p>
                                    <label for="reset_user_login"><?php echo esc_html( $recpass_username ); ?></label>
                                    <input type="text" class="directorist-mb-15 directorist-form-element" name="user_login" id="reset_user_login" value="<?php echo isset( $_POST['user_login'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) ) : ''; ?>" placeholder="<?php echo esc_attr( $recpass_placeholder ); ?>" required />
                                    <div class="directorist-authentication__form__btn-wrapper">
                                        <input type="hidden" name="action" value="reset" />
                                        <button type="submit" class="directorist-btn directorist-authentication__form__btn"><?php echo esc_html( $recpass_button ); ?></button>
                                        <input type="hidden" value="<?php echo esc_attr( wp_create_nonce( directorist_get_nonce_key() ) ); ?>" name="directorist_nonce">
                                    </div>
                                </fieldset>
                            </form>
                        </div>

                    <?php endif; // end login form ?>
                </div>
            </div>

            <?php if ( directorist_is_user_registration_enabled() ) : ?>
            <!-- ===================== REGISTRATION FORM ===================== -->
            <div class="directorist-col-md-6 directorist-offset-md-3 directorist-registration-wrapper directorist-authentication <?php echo esc_attr( $active_form === 'signup' ? 'active' : '' ); ?>">
                <div class="directory_register_form_wrap directorist-authentication__form">

                    <!-- Success/error messages -->
                    <div class="add_listing_title atbd_success_mesage directorist-authentication__message">
                        <?php if ( ! empty( $_GET['registration_status'] ) ) : ?>
                            <p style="padding:20px" class="directorist-alert directorist-alert-success">
                                <span>
                                    <?php esc_html_e( 'Registration complete!', 'smarttolet' ); ?>
                                    <?php echo wp_kses_post( sprintf( __( ' Click %s to login.', 'smarttolet' ), '<button class="directorist-authentication__btn directorist-authentication__btn--signin"><span style="color:red">' . __( 'here', 'smarttolet' ) . '</span></button>' ) ); ?>
                                </span>
                            </p>
                        <?php endif; ?>
                        <p style="padding:20px;display:none" class="alert-danger directorist-register-error"><?php directorist_icon( 'las la-exclamation-triangle' ); ?></p>
                    </div>

                    <form action="#" method="post" class="directorist__authentication__signup" id="smarttolet-reg-form">

                        <!-- USERNAME (required) -->
                        <div class="directorist-form-group directorist-mb-35">
                            <label for="st_username">
                                <?php echo esc_html( $username ); ?> <strong class="directorist-form-required">*</strong>
                            </label>
                            <input
                                id="st_username"
                                class="directorist-form-element"
                                type="text"
                                name="username"
                                value="<?php echo isset( $_REQUEST['username'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['username'] ) ) ) : ''; ?>"
                                required
                            >
                        </div>

                        <!-- MOBILE NUMBER (required) -->
                        <div class="directorist-form-group directorist-mb-35">
                            <label for="st_mobile">
                                <?php echo esc_html( $phone_label ); ?>
                                <?php if ( 'yes' === $phone_required ) : ?>
                                    <strong class="directorist-form-required">*</strong>
                                <?php else : ?>
                                    <span style="font-size:0.85em;color:#888;"><?php esc_html_e( '(optional)', 'smarttolet' ); ?></span>
                                <?php endif; ?>
                            </label>
                            <input
                                id="st_mobile"
                                class="directorist-form-element"
                                type="tel"
                                name="mobile_number"
                                value="<?php echo isset( $_REQUEST['mobile_number'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['mobile_number'] ) ) ) : ''; ?>"
                                placeholder="<?php echo esc_attr( $phone_placeholder ); ?>"
                                <?php echo ( 'yes' === $phone_required ) ? 'required' : ''; ?>
                            >
                        </div>

                        <!-- EMAIL (optional) -->
                        <div class="directorist-form-group directorist-mb-35">
                            <label for="st_email">
                                <?php echo esc_html( $email ); ?>
                                <?php if ( 'yes' !== $email_required ) : ?>
                                    <span style="font-size:0.85em;color:#888;"><?php esc_html_e( '(optional)', 'smarttolet' ); ?></span>
                                <?php else : ?>
                                    <strong class="directorist-form-required">*</strong>
                                <?php endif; ?>
                            </label>
                            <input
                                id="st_email"
                                class="directorist-form-element"
                                type="email"
                                name="email"
                                value="<?php echo isset( $_REQUEST['email'] ) ? esc_attr( sanitize_email( wp_unslash( $_REQUEST['email'] ) ) ) : ''; ?>"
                                <?php echo ( 'yes' === $email_required ) ? 'required' : ''; ?>
                            >
                        </div>

                        <!-- PASSWORD -->
                        <?php if ( ! empty( $display_password_reg ) && 'yes' === $display_password_reg ) : ?>
                            <div class="directorist-form-group directorist-mb-35">
                                <label for="st_password">
                                    <?php echo esc_html( $password ); ?> <strong class="directorist-form-required">*</strong>
                                </label>
                                <input id="st_password" class="directorist-form-element" type="password" name="password" value="" required>
                            </div>
                        <?php endif; ?>

                        <!-- FIRST NAME -->
                        <?php if ( ! empty( $display_fname ) && 'yes' === $display_fname ) : ?>
                            <div class="directorist-form-group directorist-mb-35">
                                <label for="st_fname"><?php echo esc_html( $first_name ); ?><?php echo ( ! empty( $require_fname ) && 'yes' === $require_fname ? '<strong class="directorist-form-required">*</strong>' : '' ); ?></label>
                                <input id="st_fname" class="directorist-form-element" type="text" name="fname" value="<?php echo isset( $_REQUEST['fname'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['fname'] ) ) ) : ''; ?>" <?php echo ( ! empty( $require_fname ) && 'yes' === $require_fname ? 'required' : '' ); ?>>
                            </div>
                        <?php endif; ?>

                        <!-- LAST NAME -->
                        <?php if ( ! empty( $display_lname ) && 'yes' === $display_lname ) : ?>
                            <div class="directorist-form-group directorist-mb-35">
                                <label for="st_lname"><?php echo esc_html( $last_name ); ?><?php echo ( ! empty( $require_lname ) && 'yes' === $require_lname ? '<strong class="directorist-form-required">*</strong>' : '' ); ?></label>
                                <input id="st_lname" class="directorist-form-element" type="text" name="lname" value="<?php echo isset( $_REQUEST['lname'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['lname'] ) ) ) : ''; ?>" <?php echo ( ! empty( $require_lname ) && 'yes' === $require_lname ? 'required' : '' ); ?>>
                            </div>
                        <?php endif; ?>

                        <!-- WEBSITE -->
                        <?php if ( ! empty( $display_website ) && 'yes' === $display_website ) : ?>
                            <div class="directorist-form-group directorist-mb-35">
                                <label for="st_website"><?php echo esc_html( $website ); ?><?php echo ( ! empty( $require_website ) && 'yes' === $require_website ? '<strong class="directorist-form-required">*</strong>' : '' ); ?></label>
                                <input id="st_website" class="directorist-form-element" type="text" name="website" value="<?php echo isset( $_REQUEST['website'] ) ? esc_url( sanitize_text_field( wp_unslash( $_REQUEST['website'] ) ) ) : ''; ?>" <?php echo ( ! empty( $require_website ) && 'yes' === $require_website ? 'required' : '' ); ?>>
                            </div>
                        <?php endif; ?>

                        <!-- BIO -->
                        <?php if ( ! empty( $display_bio ) && 'yes' === $display_bio ) : ?>
                            <div class="directorist-form-group directorist-mb-35">
                                <label for="st_bio"><?php echo esc_html( $bio ); ?><?php echo ( ! empty( $require_bio ) && 'yes' === $require_bio ? '<strong class="directorist-form-required">*</strong>' : '' ); ?></label>
                                <textarea id="st_bio" class="directorist-form-element" name="bio" rows="5" <?php echo ( ! empty( $require_bio ) && 'yes' === $require_bio ? 'required' : '' ); ?>><?php echo isset( $_REQUEST['bio'] ) ? esc_textarea( sanitize_text_field( wp_unslash( $_REQUEST['bio'] ) ) ) : ''; ?></textarea>
                            </div>
                        <?php endif; ?>

                        <!-- USER TYPE -->
                        <?php if ( ! empty( $enable_user_type ) && 'yes' === $enable_user_type ) : ?>
                            <div class="directorist-radio directorist-radio-circle directorist-mb-35">
                                <input id="author_type" type="radio" name="user_type" value="author" <?php echo esc_attr( $author_checked ); ?>>
                                <label for="author_type" class="directorist-radio__label"><?php echo esc_html( $author_role_label ); ?></label>
                            </div>
                            <div class="directorist-radio directorist-radio-circle directorist-mb-35">
                                <input id="general_type" type="radio" name="user_type" value="general" <?php echo esc_attr( $general_checked ); ?>>
                                <label for="general_type" class="directorist-radio__label"><?php echo esc_html( $user_role_label ); ?></label>
                            </div>
                        <?php endif; ?>

                        <!-- PRIVACY -->
                        <?php if ( ! empty( $registration_privacy ) && 'yes' === $registration_privacy ) : ?>
                            <div class="directorist-checkbox directorist-mb-20">
                                <input id="st_privacy" type="checkbox" name="privacy_policy" <?php echo ( isset( $privacy_policy ) && 'on' === $privacy_policy ) ? 'checked' : ''; ?>>
                                <label for="st_privacy" class="directorist-checkbox__label">
                                    <?php echo esc_html( $privacy_label ); ?>
                                    <a style="color:red" target="_blank" href="<?php echo esc_url( $privacy_page_link ); ?>"><?php echo esc_html( $privacy_label_link ); ?></a>
                                    <span class="directorist-form-required">*</span>
                                </label>
                            </div>
                        <?php endif; ?>

                        <!-- TERMS -->
                        <?php if ( ! empty( $enable_registration_terms ) && 'yes' === $enable_registration_terms ) : ?>
                            <div class="directorist-checkbox directorist-mb-30">
                                <input id="st_terms" type="checkbox" name="t_c_check" <?php echo ( isset( $t_c_check ) && 'on' === $t_c_check ) ? 'checked' : ''; ?>>
                                <label for="st_terms" class="directorist-checkbox__label">
                                    <?php echo esc_html( $terms_label ); ?>
                                    <a style="color:red" target="_blank" href="<?php echo esc_url( $t_C_page_link ); ?>"><?php echo esc_html( $terms_label_link ); ?></a>
                                    <span class="directorist-form-required">*</span>
                                </label>
                            </div>
                        <?php endif; ?>

                        <?php do_action( 'atbdp_before_user_registration_submit' ); ?>

                        <div class="directory_regi_btn directorist-mb-15">
                            <?php if ( get_directorist_option( 'redirection_after_reg' ) === 'previous_page' ) : ?>
                                <input type="hidden" name="previous_page" value="<?php echo esc_url( wp_get_referer() ); ?>">
                            <?php endif; ?>
                            <input type="hidden" value="<?php echo esc_attr( wp_create_nonce( directorist_get_nonce_key() ) ); ?>" name="directorist_nonce">
                            <a class="directorist-btn directorist-authentication__form__btn" href="#"><?php echo esc_html( $reg_signup ); ?></a>
                        </div>

                        <div class="directorist-authentication__form__toggle-area">
                            <p>
                                <?php echo esc_html( $login_text ); ?>
                                <button class="directorist-authentication__btn directorist-authentication__btn--signin"><?php echo esc_html( $log_linkingmsg ); ?></button>
                            </p>
                        </div>

                    </form>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
(function () {
    /* Panel toggle */
    function showPanel( panel ) {
        var login = document.querySelector( '.directorist-login-wrapper' );
        var reg   = document.querySelector( '.directorist-registration-wrapper' );
        if ( ! login || ! reg ) return;
        if ( panel === 'signup' ) {
            login.classList.remove( 'active' );
            reg.classList.add( 'active' );
        } else {
            reg.classList.remove( 'active' );
            login.classList.add( 'active' );
        }
    }

    document.addEventListener( 'click', function ( e ) {
        if ( e.target.closest( '.directorist-authentication__btn--signup' ) ) { e.preventDefault(); showPanel( 'signup' ); }
        if ( e.target.closest( '.directorist-authentication__btn--signin' ) ) { e.preventDefault(); showPanel( 'signin' ); }
    } );

    /* Login: flag phone input so Account model can resolve it */
    var loginForm = document.getElementById( 'directorist__authentication__login' );
    if ( loginForm ) {
        loginForm.addEventListener( 'submit', function () {
            var input = document.getElementById( 'username' );
            if ( input && /^[+\d\s\-().]{7,}$/.test( input.value.trim() ) ) {
                var flag  = document.createElement( 'input' );
                flag.type = 'hidden'; flag.name = 'login_by_phone'; flag.value = '1';
                loginForm.appendChild( flag );
            }
        } );
    }
})();
</script>