<?php
/**
 * Project defaults settings page.
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

/**
 * Renders project-management default settings.
 *
 * @since 1.0.0
 */
final class SettingsProjects {
	/**
	 * Render the project defaults form fields.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 * @since 1.0.0
	 */
	public function render( array $values ): void {
		$fields = array(
			'default_status'             => __( 'Default Project Status', 'projectpress' ),
			'default_priority'           => __( 'Default Project Priority', 'projectpress' ),
			'default_visibility'         => __( 'Default Project Visibility', 'projectpress' ),
			'default_task_status'        => __( 'Default Task Status', 'projectpress' ),
			'default_task_priority'      => __( 'Default Task Priority', 'projectpress' ),
			'default_milestone_status'   => __( 'Default Milestone Status', 'projectpress' ),
			'default_requirement_status' => __( 'Default Requirement Status', 'projectpress' ),
			'default_feature_status'     => __( 'Default Feature Status', 'projectpress' ),
			'default_story_status'       => __( 'Default User Story Status', 'projectpress' ),
		);
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="projectpress-settings-form">
			<input type="hidden" name="action" value="projectpress_save_projects_settings" />
			<?php wp_nonce_field( 'projectpress_save_projects_settings' ); ?>
			<table class="form-table table align-middle" role="presentation"><tbody>
			<?php foreach ( $fields as $key => $label ) : ?>
				<tr><th scope="row"><?php echo FormFieldHelper::label( 'projectpress-' . esc_attr( $key ), $label ); ?></th><td><?php echo FormFieldHelper::text_input( 'projectpress_projects[' . esc_attr( $key ) . ']', (string) ( $values[ $key ] ?? '' ), array( 'id' => 'projectpress-' . $key, 'class' => 'w-100' ) ); ?></td></tr>
			<?php endforeach; ?>
			</tbody></table>
			<div class="d-flex justify-content-end mt-3"><?php echo FormFieldHelper::button( __( 'Save Changes', 'projectpress' ), array( 'type' => 'submit', 'class' => 'btn-primary' ) ); ?></div>
		</form>
		<?php
	}
}
