<?php
/**
 * Settings page view.
 *
 * @package SmartToLet
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap stl-admin-wrap">
	<h1><?php esc_html_e( 'SmartToLet Settings', 'smarttolet' ); ?></h1>

	<?php if ( isset( $_GET['settings-updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Settings saved.', 'smarttolet' ); ?></p>
		</div>
	<?php endif; ?>

	<form method="post" action="options.php">
		<?php
		settings_fields( SmartToLet\Admin\Settings::OPTION_GROUP );
		do_settings_sections( SmartToLet\Admin\Settings::OPTION_GROUP );
		submit_button();
		?>
	</form>
</div>
