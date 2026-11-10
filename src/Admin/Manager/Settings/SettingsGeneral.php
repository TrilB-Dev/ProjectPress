<?php
/**
 * Settings general fields.
 * @package ProjectPress
 * @subpackage Admin\Manager\Settings
 * @since 1.0.0
 */
namespace ProjectPress\Admin\Manager\Settings;

use ProjectPress\Includes\Functions\Helpers\FormFieldHelper;
use ProjectPress\Includes\Functions\Helpers\PermalinkHelper;
use ProjectPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsGeneral {
	/**
	 * Render general ProjectPress settings fields.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 * @since 1.0.0
	 */
	public function render( array $values ): void {
		$edit_capabilities = array(
			'general' => 'projectpress_settings_general_edit',
		);
		$can_edit = isset( $edit_capabilities[ 'general' ] ) && current_user_can( $edit_capabilities[ 'general' ] );
		$root_name = isset( $values['root_name'] ) ? SanitizationHelper::text( (string) $values['root_name'] ) : '';
		$root_description = isset( $values['root_description'] ) ? SanitizationHelper::text( (string) $values['root_description'] ) : '';
		$archive_title = isset( $values['archive_title'] ) ? SanitizationHelper::text( (string) $values['archive_title'] ) : '';
		$archive_description = isset( $values['archive_description'] ) ? SanitizationHelper::text( (string) $values['archive_description'] ) : '';
		$root_slug = isset( $values['root_slug'] ) ? SanitizationHelper::key( (string) $values['root_slug'] ) : '';
		$category_slug = isset( $values['category_slug'] ) ? SanitizationHelper::key( (string) $values['category_slug'] ) : '';
		$tag_slug = isset( $values['tag_slug'] ) ? SanitizationHelper::key( (string) $values['tag_slug'] ) : '';
		$permalink = isset( $values['permalink'] ) ? PermalinkHelper::sanitize_pattern( (string) $values['permalink'] ) : '';
		$token_definitions = array(
			'%mod_root%'   => __( 'The root of ProjectPress.', 'projectpress' ),
			'%mod_type%'   => __( 'The Mod type taxonomy.', 'projectpress' ),
			'%mod_tags%'   => __( 'The Mod tags taxonomy.', 'projectpress' ),
			'%mod_groups%' => __( 'The custom Mod taxonomies.', 'projectpress' ),
			'%mod_post%'   => __( 'The Mod post type.', 'projectpress' ),
		);
		$enable_schema = ! empty( $values['enable_schema'] ?? true );
		?>
		<?php if ( $can_edit ) : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="projectpress-settings-form">
			<input type="hidden" name="action" value="projectpress_save_general_settings" />
			<input type="hidden" name="projectpress_tab" value="general" />
			<?php wp_nonce_field( 'projectpress_save_general_settings' ); ?>
		<?php endif; ?>
			<table class="form-table table align-middle" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-root-name', 
								__( 'ProjectPress Root Name', 'projectpress' ), 
								[
									'description' => __( 'The name used for the main ProjectPress area.', 'projectpress' ),
									'tooltip' => __( 'This name appears in the admin interface and generated titles.', 'projectpress' ),
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::text_input( 
								'projectpress_general[root_name]', 
								$root_name, 
								[ 
									'id' => 'projectpress-root-name',
									'class' => 'w-100',
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-root-description', 
								__( 'ProjectPress Description', 'projectpress' ), 
								[
									'description' => __( 'A short description for the ProjectPress knowledge base.', 'projectpress' ),
									'tooltip' => __( 'This can be used by themes and integrations when describing the ProjectPress area.', 'projectpress' ),
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::textarea( 
								'projectpress_general[root_description]', 
								$root_description, 
								[ 
									'id' => 'projectpress-root-description', 
									'rows' => 3,
									'class' => 'w-100',
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-archive-title', 
								__( 'ProjectPress Archive Title', 'projectpress' ), 
								[
									'description' => __( 'The title shown on ProjectPress archive and index views.', 'projectpress' ),
									'tooltip' => __( 'Use a concise title that makes the documentation area clear to visitors.', 'projectpress' ),
								] ); ?></th>
						<td>
							<?php echo FormFieldHelper::text_input( 
								'projectpress_general[archive_title]', 
								$archive_title, 
								[ 
									'id' => 'projectpress-archive-title',
									'class' => 'w-100',
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-archive-description', 
								__( 'ProjectPress Archive Description', 'projectpress' ), 
								[
									'description' => __( 'Supporting text shown on ProjectPress archive and index views.', 'projectpress' ),
									'tooltip' => __( 'A short introduction helps visitors understand what they can find in the ProjectPress area.', 'projectpress' ),
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::textarea( 
								'projectpress_general[archive_description]', 
								$archive_description, 
								[ 
									'id' => 'projectpress-archive-description', 
									'rows' => 3,
									'class' => 'w-100',
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-root-slug', 
								__( 'ProjectPress Root Slug', 'projectpress' ), 
								[
									'description' => __( 'The URL slug for the ProjectPress root.', 'projectpress' ),
									'tooltip' => __( 'Use lowercase letters, numbers, and hyphens for the most reliable URLs.', 'projectpress' ),
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::text_input( 
								'projectpress_general[root_slug]', 
								$root_slug, 
								[ 
									'id' => 'projectpress-root-slug',
									'class' => 'w-100',
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-category-slug', 
								__( 'Custom Category Slug', 'projectpress' ), 
								[
									'description' => __( 'The URL slug used for ProjectPress categories.', 'projectpress' ),
									'tooltip' => __( 'Changing this value flushes the WordPress rewrite rules.', 'projectpress' ),
									'tooltip_type' => 'info',
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::text_input( 
								'projectpress_general[category_slug]', 
								$category_slug, 
								[ 
									'id' => 'projectpress-category-slug',
									'class' => 'w-100',
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-tag-slug', 
								__( 'Custom Tags Slug', 'projectpress' ), 
								[
									'description' => __( 'The URL slug used for ProjectPress tags.', 'projectpress' ),
									'tooltip' => __( 'Changing this value flushes the WordPress rewrite rules.', 'projectpress' ),
									'tooltip_type' => 'info',
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::text_input( 
								'projectpress_general[tag_slug]', 
								$tag_slug, 
								[ 
									'id' => 'projectpress-tag-slug',
									'class' => 'w-100',
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-permalink', 
								__( 'ProjectPress Permalink', 'projectpress' ), 
								[
									'description' => __( 'The permalink structure used by ProjectPress content.', 'projectpress' ),
									'tooltip' => __( 'Choose a structure that remains readable and stable after publication.', 'projectpress' ),
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::text_input( 
								'projectpress_general[permalink]', 
								$permalink, 
								[ 
									'id' => 'projectpress-permalink', 
									'data-permalink-field' => 'permalink',
									'class' => 'w-100',
								] 
							); ?>
							<div class="projectpress-permalink-tokens mt-2" aria-label="<?php echo esc_attr__( 'Available permalink tokens', 'projectpress' ); ?>">
								<?php foreach ( $token_definitions as $token => $description ) : ?>
									<?php echo FormFieldHelper::button(
										$token,
										[
											'class' => 'btn-sm btn-outline-secondary me-1 mb-1',
											'type' => 'button',
											'attributes' => [
												'data-permalink-token' => $token,
												'title' => $description,
											],
										]
									); ?>
								<?php endforeach; ?>
							</div>
							<div class="form-text"><?php echo esc_html__( 'Click a token to add it to the pattern. Tokens are inserted with a trailing slash and reappear when removed.', 'projectpress' ); ?></div>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-enable-schema', 
								__( 'Enable ProjectPress Schema', 'projectpress' ), 
								[
									'description' => __( 'Allow themes and integrations to expose ProjectPress metadata.', 'projectpress' ),
									'tooltip' => __( 'Keep this enabled when search engines and integrations should understand the ProjectPress structure.', 'projectpress' ),
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::checkbox( 
								'projectpress_general[enable_schema]', 
								'1', 
								__( 'Enable ProjectPress Schema', 'projectpress' ), 
								[ 
									'id' => 'projectpress-enable-schema', 
									'checked' => $enable_schema 
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


