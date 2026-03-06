<?php
/**
 * Enquiry form shortcode view.
 *
 * @var array $atts Shortcode attributes.
 * @package SmartToLet
 */

defined( 'ABSPATH' ) || exit;

$property_id = absint( $atts['property_id'] ?? get_the_ID() );
?>
<div class="stl-enquiry-form">
	<h3 class="stl-enquiry-form__title"><?php esc_html_e( 'Send an Enquiry', 'smarttolet' ); ?></h3>

	<div class="stl-enquiry-form__message" id="stl-enquiry-msg" aria-live="polite"></div>

	<div class="stl-form" id="stl-enquiry-form" data-property="<?php echo esc_attr( $property_id ); ?>">
		<div class="stl-form__row">
			<label for="stl-name"><?php esc_html_e( 'Your Name *', 'smarttolet' ); ?></label>
			<input type="text" id="stl-name" name="name" required>
		</div>
		<div class="stl-form__row">
			<label for="stl-email"><?php esc_html_e( 'Email Address *', 'smarttolet' ); ?></label>
			<input type="email" id="stl-email" name="email" required>
		</div>
		<div class="stl-form__row">
			<label for="stl-phone"><?php esc_html_e( 'Phone Number', 'smarttolet' ); ?></label>
			<input type="tel" id="stl-phone" name="phone">
		</div>
		<div class="stl-form__row">
			<label for="stl-message"><?php esc_html_e( 'Message', 'smarttolet' ); ?></label>
			<textarea id="stl-message" name="message" rows="5"></textarea>
		</div>
		<div class="stl-form__row">
			<button type="button" class="stl-btn stl-btn--primary" id="stl-enquiry-submit">
				<?php esc_html_e( 'Send Enquiry', 'smarttolet' ); ?>
			</button>
		</div>
	</div>
</div>
