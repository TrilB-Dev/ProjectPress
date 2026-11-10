<?php
/**
 * Settings access restriction fields.
 *
 * @package TrilBDev
 * @subpackage Admin\Manager\Settings
 */
namespace ProjectPress\Admin\Manager\Settings;

use ProjectPress\Includes\Functions\Helpers\FormFieldHelper;
use ProjectPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsAccess {
	/**
	 * Render access restriction fields.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 */
	public function render( array $values ): void {
		$can_edit = current_user_can( 'manage_options' ) || current_user_can( 'projectpress_settings_access_edit' );
		$options = [
			[ 'value' => 'manage_options', 'label' => __( 'Administrators', 'projectpress' ) ],
			[ 'value' => 'edit_posts', 'label' => __( 'Editors', 'projectpress' ) ],
			[ 'value' => 'publish_posts', 'label' => __( 'Authors', 'projectpress' ) ],
		];
		$create_mods = $values['create_mods'] ?? [ 'manage_options' ];
		$write_pages = $values['write_pages'] ?? [ 'manage_options' ];
		$view_analytics = $values['view_analytics'] ?? [ 'manage_options' ];
		$manage_plugins = $values['manage_plugins'] ?? [ 'manage_options' ];
		?>
		<?php if ( $can_edit ) : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="projectpress-settings-form">
			<input type="hidden" name="action" value="projectpress_save_access_settings" />
			<input type="hidden" name="projectpress_tab" value="access" />
			<?php wp_nonce_field( 'projectpress_save_access_settings' ); ?>
		<?php endif; ?>
			<table class="form-table table align-middle" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'projectpress-access-create-mods',
								__( 'Who can create mods?', 'projectpress' ),
								[
									'description' => __( 'Choose the minimum capability required to create ProjectPress mods.', 'projectpress' ),
									'tooltip' => __( 'Users without this capability cannot create new mods.', 'projectpress' ),
								]
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_multiselect(
								'projectpress_access[create_mods]',
								[
									'id' => 'projectpress-access-create-mods',
									'data' => $options,
									'selected' => array_values( 
										array_filter( 
											array_map( 
												'sanitize_key', 
												(array) $create_mods 
											) 
										) 
									),
								]
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'projectpress-access-write-pages',
								__( 'Who can write mod pages?', 'projectpress' ),
								[
									'description' => __( 'Choose the minimum capability required to create or edit mod pages.', 'projectpress' ),
									'tooltip' => __( 'This controls editing access to mod page content.', 'projectpress' ),
								]
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_multiselect(
								'projectpress_access[write_pages]',
								[
									'id' => 'projectpress-access-write-pages',
									'data' => $options,
									'selected' => array_values( 
										array_filter( 
											array_map( 
												'sanitize_key', 
												(array) $write_pages 
											) 
										) 
									),
								]
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'projectpress-access-view-analytics',
								__( 'Who can check analytics?', 'projectpress' ),
								[
									'description' => __( 'Choose the minimum capability required to view ProjectPress analytics.', 'projectpress' ),
									'tooltip' => __( 'Analytics data is shown only to users who meet this capability.', 'projectpress' ),
									'tooltip_type' => 'info',
								]
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_multiselect(
								'projectpress_access[view_analytics]',
								[
									'id' => 'projectpress-access-view-analytics',
									'data' => $options,
									'selected' => array_values( 
										array_filter( 
											array_map( 
												'sanitize_key', 
												(array) $view_analytics 
											) 
										) 
									),
								]
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'projectpress-access-manage-plugins',
								__( 'Who can manage plugins?', 'projectpress' ),
								[
									'description' => __( 'Choose the minimum capability required to manage ProjectPress plugins.', 'projectpress' ),
									'tooltip' => __( 'Use a trusted administrator-level capability for plugin management.', 'projectpress' ),
									'tooltip_icon' => 'fa-shield-halved',
								]
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_multiselect(
								'projectpress_access[manage_plugins]',
								[
									'id' => 'projectpress-access-manage-plugins',
									'data' => $options,
									'selected' => array_values( 
										array_filter( 
											array_map( 
												'sanitize_key', 
												(array) $manage_plugins 
											) 
										) 
									),
								]
							); ?>
						</td>
					</tr>
				</tbody>
			</table>
		<?php if ( $can_edit ) : ?>
			<div class="projectpress-settings-actions d-flex justify-content-end mt-3">
				<?php echo FormFieldHelper::button(
					__( 'Save Changes', 'projectpress' ),
					[
						'type' => 'submit',
						'name' => 'submit',
						'class' => 'btn-primary',
					]
				); ?>
			</div>
		</form>
		<?php endif; ?>
		<?php
	}
}


