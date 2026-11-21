<?php
/**
 * Project Details workspace section.
 *
 * @package ProjectPress\Admin\Manager\Projects
 */
namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Includes\Core\Defaults;
use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\Core\Project as DomainProject;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Project {
	private DomainProjectManager $projects;
	private ProjectPermissions $permissions;

	public function __construct( ?DomainProjectManager $projects = null, ?ProjectPermissions $permissions = null ) {
		$this->projects = $projects ?? DomainProjectManager::get_instance();
		$this->permissions = $permissions ?? new ProjectPermissions();
	}

	public function render( DomainProject $project ): void {
		$can_edit = $this->permissions->can_edit( $project );
		$owner = $project->get_owner_id() ? get_user_by( 'id', $project->get_owner_id() ) : false;
		$parent = $project->get_parent_id() ? $this->projects->find( $project->get_parent_id() ) : null;
		?>
		<div class="card card-body">
			<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
				<h2 class="h4 mb-0"><?php esc_html_e( 'Project Details', 'projectpress' ); ?></h2>
				<?php if ( ! $can_edit ) : ?><span class="badge text-bg-secondary"><?php esc_html_e( 'Read only', 'projectpress' ); ?></span><?php endif; ?>
			</div>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="projectpress_workspace_project_details_save" />
				<input type="hidden" name="project_id" value="<?php echo esc_attr( (string) $project->get_id() ); ?>" />
				<?php wp_nonce_field( 'projectpress_workspace_project_details_save' ); ?>

				<div class="row g-3">
					<div class="col-lg-8"><label class="form-label" for="project-details-name"><?php esc_html_e( 'Name', 'projectpress' ); ?></label><input class="form-control" id="project-details-name" name="name" value="<?php echo esc_attr( $project->get_name() ); ?>" required <?php disabled( ! $can_edit ); ?> /></div>
					<div class="col-lg-4"><label class="form-label" for="project-details-key"><?php esc_html_e( 'Project key', 'projectpress' ); ?></label><input class="form-control" id="project-details-key" value="<?php echo esc_attr( $project->get_key() ); ?>" disabled /></div>
					<div class="col-12"><label class="form-label" for="project-details-description"><?php esc_html_e( 'Description', 'projectpress' ); ?></label><textarea class="form-control" id="project-details-description" name="description" rows="5" <?php disabled( ! $can_edit ); ?>><?php echo esc_textarea( $project->get_description() ); ?></textarea></div>

					<div class="col-md-4"><label class="form-label" for="project-details-status"><?php esc_html_e( 'Status', 'projectpress' ); ?></label><?php $this->render_select( 'status', 'project-details-status', Defaults::project_statuses(), $project->get_status(), $can_edit ); ?></div>
					<div class="col-md-4"><label class="form-label" for="project-details-priority"><?php esc_html_e( 'Priority', 'projectpress' ); ?></label><?php $this->render_select( 'priority', 'project-details-priority', Defaults::project_priorities(), $project->get_priority(), $can_edit ); ?></div>
					<div class="col-md-4"><label class="form-label" for="project-details-visibility"><?php esc_html_e( 'Visibility', 'projectpress' ); ?></label><?php $this->render_select( 'visibility', 'project-details-visibility', Defaults::project_visibilities(), $project->get_visibility(), $can_edit ); ?></div>

					<div class="col-md-4"><label class="form-label" for="project-details-start-date"><?php esc_html_e( 'Start date', 'projectpress' ); ?></label><input class="form-control" id="project-details-start-date" type="date" name="start_date" value="<?php echo esc_attr( $project->get_start_date() ? $project->get_start_date()->format( 'Y-m-d' ) : '' ); ?>" <?php disabled( ! $can_edit ); ?> /></div>
					<div class="col-md-4"><label class="form-label" for="project-details-target-date"><?php esc_html_e( 'Target end date', 'projectpress' ); ?></label><input class="form-control" id="project-details-target-date" type="date" name="target_end_date" value="<?php echo esc_attr( $project->get_target_end_date() ? $project->get_target_end_date()->format( 'Y-m-d' ) : '' ); ?>" <?php disabled( ! $can_edit ); ?> /></div>
					<div class="col-md-4"><label class="form-label" for="project-details-actual-date"><?php esc_html_e( 'Actual end date', 'projectpress' ); ?></label><input class="form-control" id="project-details-actual-date" type="date" name="actual_end_date" value="<?php echo esc_attr( $project->get_actual_end_date() ? $project->get_actual_end_date()->format( 'Y-m-d' ) : '' ); ?>" <?php disabled( ! $can_edit ); ?> /></div>
				</div>

				<div class="row g-3 mt-1">
					<div class="col-md-6"><div class="border rounded p-3 h-100"><span class="small text-secondary d-block"><?php esc_html_e( 'Owner', 'projectpress' ); ?></span><strong><?php echo esc_html( $owner ? $owner->display_name : __( 'Not assigned', 'projectpress' ) ); ?></strong></div></div>
					<div class="col-md-6"><div class="border rounded p-3 h-100"><span class="small text-secondary d-block"><?php esc_html_e( 'Parent project', 'projectpress' ); ?></span><strong><?php echo esc_html( $parent ? $parent->get_name() : __( 'None', 'projectpress' ) ); ?></strong></div></div>
				</div>

				<?php if ( $can_edit ) : ?><button class="btn btn-primary mt-4" type="submit"><?php esc_html_e( 'Save Details', 'projectpress' ); ?></button><?php endif; ?>
			</form>
		</div>
		<?php
	}

	public function handle_save(): void {
		check_admin_referer( 'projectpress_workspace_project_details_save' );
		$project_id = RequestHelper::integer( $_POST, 'project_id', 0 );
		$project = $this->projects->find( $project_id );
		if ( ! $project ) {
			$this->redirect( $project_id, new WP_Error( 'projectpress_project_not_found', __( 'Project not found.', 'projectpress' ) ) );
		}
		if ( ! $this->permissions->can_edit( $project ) ) {
			$this->redirect( $project_id, new WP_Error( 'projectpress_project_details_forbidden', __( 'You do not have permission to edit this project.', 'projectpress' ) ) );
		}

		$result = $this->projects->service()->update(
			$project,
			array(
				'name'            => RequestHelper::text( $_POST, 'name', '' ),
				'description'     => RequestHelper::text( $_POST, 'description', '' ),
				'status'          => RequestHelper::key( $_POST, 'status', $project->get_status() ),
				'priority'        => RequestHelper::key( $_POST, 'priority', $project->get_priority() ),
				'visibility'      => RequestHelper::key( $_POST, 'visibility', $project->get_visibility() ),
				'start_date'      => RequestHelper::text( $_POST, 'start_date', '' ),
				'target_end_date' => RequestHelper::text( $_POST, 'target_end_date', '' ),
				'actual_end_date' => RequestHelper::text( $_POST, 'actual_end_date', '' ),
			)
		);
		$this->redirect( $project_id, $result );
	}

	private function render_select( string $name, string $id, array $values, string $selected_value, bool $enabled ): void {
		?><select class="form-select" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php disabled( ! $enabled ); ?>><?php foreach ( $values as $value ) : ?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected_value, $value ); ?>><?php echo esc_html( ucwords( str_replace( '_', ' ', $value ) ) ); ?></option><?php endforeach; ?></select><?php
	}

	private function redirect( int $project_id, $result ): void {
		$url = add_query_arg( array( 'page' => 'projectpress', 'group' => 'projects', 'project_id' => absint( $project_id ), 'tab' => 'details' ), admin_url( 'admin.php' ) );
		$url = is_wp_error( $result ) ? add_query_arg( 'project_error', rawurlencode( $result->get_error_message() ), $url ) : add_query_arg( 'project_saved', '1', $url );
		wp_safe_redirect( $url );
		exit;
	}
}
