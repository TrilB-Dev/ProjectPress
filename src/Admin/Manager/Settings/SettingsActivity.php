<?php
/**
 * Activity settings page.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\Settings
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\Settings;

use ProjectPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Renders ProjectPress activity settings. @since 1.0.0 */
final class SettingsActivity {
	/** Render activity settings. @since 1.0.0 */
	public function render( array $values ): void {
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="projectpress-settings-form">
			<input type="hidden" name="action" value="projectpress_save_activity_settings" />
			<?php wp_nonce_field( 'projectpress_save_activity_settings' ); ?>
			<table class="form-table table align-middle" role="presentation"><tbody><tr><th scope="row"><?php esc_html_e( 'Project Activity', 'projectpress' ); ?></th><td><?php echo FormFieldHelper::checkbox( 'projectpress_activity[enabled]', '1', __( 'Record project activity', 'projectpress' ), array( 'id' => 'projectpress-activity-enabled', 'checked' => ! empty( $values['enabled'] ) ) ); ?></td></tr></tbody></table>
			<div class="d-flex justify-content-end mt-3"><?php echo FormFieldHelper::button( __( 'Save Changes', 'projectpress' ), array( 'type' => 'submit', 'class' => 'btn-primary' ) ); ?></div>
		</form>
		<?php
	}
}
