<?php
/**
 * Notification settings page.
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

/** Renders notification defaults. @since 1.0.0 */
final class SettingsNotifications {
	/** Render notification settings. @since 1.0.0 */
	public function render( array $values ): void {
		$fields = array(
			'enabled'                  => __( 'Enable Notifications', 'projectpress' ),
			'assignment_notifications' => __( 'Assignment Notifications', 'projectpress' ),
			'due_date_notifications'   => __( 'Due Date Notifications', 'projectpress' ),
			'milestone_notifications'  => __( 'Milestone Notifications', 'projectpress' ),
		);
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="projectpress-settings-form">
			<input type="hidden" name="action" value="projectpress_save_notifications_settings" />
			<?php wp_nonce_field( 'projectpress_save_notifications_settings' ); ?>
			<table class="form-table table align-middle" role="presentation"><tbody>
			<?php foreach ( $fields as $key => $label ) : ?>
				<tr><th scope="row"><?php echo esc_html( $label ); ?></th><td><?php echo FormFieldHelper::checkbox( 'projectpress_notifications[' . esc_attr( $key ) . ']', '1', $label, array( 'id' => 'projectpress-' . $key, 'checked' => ! empty( $values[ $key ] ) ) ); ?></td></tr>
			<?php endforeach; ?>
			</tbody></table>
			<div class="d-flex justify-content-end mt-3"><?php echo FormFieldHelper::button( __( 'Save Changes', 'projectpress' ), array( 'type' => 'submit', 'class' => 'btn-primary' ) ); ?></div>
		</form>
		<?php
	}
}
