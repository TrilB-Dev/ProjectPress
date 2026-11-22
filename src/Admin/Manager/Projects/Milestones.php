<?php
/**
 * Project milestones workspace controller.
 *
 * @package ProjectPress\Admin\Manager\Projects
 */
namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;
use ProjectPress\Includes\Project\Work\MilestoneRepository;
use ProjectPress\Includes\Project\Work\MilestoneService;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Milestones {
	private DomainProjectManager $projects;
	private ProjectPermissions $permissions;
	private MilestoneRepository $milestones;
	private MilestoneService $milestone_service;

	public function __construct( ?DomainProjectManager $projects = null, ?ProjectPermissions $permissions = null ) {
		$this->projects          = $projects ?? DomainProjectManager::get_instance();
		$this->permissions       = $permissions ?? new ProjectPermissions();
		$this->milestones        = new MilestoneRepository();
		$this->milestone_service = new MilestoneService( $this->milestones, $this->projects->repository(), $this->permissions );
	}

	public function render( Project $project ): void {
		$rows     = $this->milestones->for_project( $project->get_id() );
		$can_edit = $this->permissions->can_edit( $project );
		?>
		<div class="row g-4">
			<div class="col-xl-8"><div class="card card-body">
				<h2 class="h4"><?php esc_html_e( 'Milestones', 'projectpress' ); ?></h2>
				<?php if ( empty( $rows ) ) : ?><p class="text-secondary mb-0"><?php esc_html_e( 'No milestones have been added yet.', 'projectpress' ); ?></p><?php endif; ?>
				<?php foreach ( $rows as $milestone ) : ?>
					<div class="border rounded p-3 mb-3">
						<strong><?php echo esc_html( $milestone->get_title() ); ?></strong>
						<div class="text-secondary small mt-1"><?php echo esc_html( ucwords( str_replace( '_', ' ', $milestone->get_status() ) ) ); ?><?php if ( $milestone->get_target_date() ) : ?> · <?php echo esc_html( sprintf( __( 'Target %s', 'projectpress' ), substr( $milestone->get_target_date(), 0, 10 ) ) ); ?><?php endif; ?></div>
						<?php if ( $can_edit ) : $this->delete_form( $project, $milestone->get_id() ); endif; ?>
					</div>
				<?php endforeach; ?>
			</div></div>
			<div class="col-xl-4"><div class="card card-body">
				<h2 class="h4"><?php esc_html_e( 'Add Milestone', 'projectpress' ); ?></h2>
				<?php if ( ! $can_edit ) : ?><p class="text-secondary mb-0"><?php esc_html_e( 'This section is read only.', 'projectpress' ); ?></p><?php else : $this->render_form( $project ); endif; ?>
			</div></div>
		</div>
		<?php
	}

	private function render_form( Project $project ): void {
		?><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="projectpress_workspace_milestone_save"/><input type="hidden" name="project_id" value="<?php echo esc_attr( (string) $project->get_id() ); ?>"/><?php wp_nonce_field( 'projectpress_workspace_milestone_save' ); ?><label class="form-label" for="milestone-title"><?php esc_html_e( 'Title', 'projectpress' ); ?></label><input class="form-control" id="milestone-title" name="title" required/><label class="form-label mt-3" for="milestone-content"><?php esc_html_e( 'Description', 'projectpress' ); ?></label><textarea class="form-control" id="milestone-content" name="content"></textarea><label class="form-label mt-3" for="milestone-status"><?php esc_html_e( 'Status', 'projectpress' ); ?></label><select class="form-select" id="milestone-status" name="status"><?php foreach ( MilestoneService::statuses() as $status ) : ?><option value="<?php echo esc_attr( $status ); ?>"><?php echo esc_html( ucwords( str_replace( '_', ' ', $status ) ) ); ?></option><?php endforeach; ?></select><label class="form-label mt-3" for="milestone-target"><?php esc_html_e( 'Target date', 'projectpress' ); ?></label><input class="form-control" type="date" id="milestone-target" name="target_date"/><button class="btn btn-primary mt-3" type="submit"><?php esc_html_e( 'Add', 'projectpress' ); ?></button></form><?php
	}

	private function delete_form( Project $project, int $id ): void {
		?><form class="mt-2" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="projectpress_workspace_milestone_delete"/><input type="hidden" name="project_id" value="<?php echo esc_attr( (string) $project->get_id() ); ?>"/><input type="hidden" name="record_id" value="<?php echo esc_attr( (string) $id ); ?>"/><?php wp_nonce_field( 'projectpress_workspace_milestone_delete' ); ?><button class="btn btn-sm btn-outline-danger" type="submit"><?php esc_html_e( 'Delete', 'projectpress' ); ?></button></form><?php
	}

	public function handle_save(): void {
		check_admin_referer( 'projectpress_workspace_milestone_save' );
		$project_id = RequestHelper::integer( $_POST, 'project_id', 0 );
		$result     = $this->milestone_service->create(
			$project_id,
			array(
				'title'       => RequestHelper::text( $_POST, 'title', '' ),
				'description' => RequestHelper::text( $_POST, 'content', '' ),
				'status'      => RequestHelper::key( $_POST, 'status', 'planned' ),
				'target_date' => RequestHelper::text( $_POST, 'target_date', '' ),
			)
		);
		$this->redirect( $project_id, $result );
	}

	public function handle_delete(): void {
		check_admin_referer( 'projectpress_workspace_milestone_delete' );
		$project_id = RequestHelper::integer( $_POST, 'project_id', 0 );
		$milestone  = $this->milestones->find( RequestHelper::integer( $_POST, 'record_id', 0 ) );
		$result     = $milestone && $milestone->get_project_id() === $project_id ? $this->milestone_service->delete( $milestone ) : new WP_Error( 'projectpress_milestone_not_found', __( 'Milestone not found.', 'projectpress' ) );
		$this->redirect( $project_id, $result );
	}

	private function redirect( int $project_id, $result ): void {
		$url = add_query_arg( array( 'page' => 'projectpress', 'group' => 'projects', 'project_id' => absint( $project_id ), 'tab' => 'milestones' ), admin_url( 'admin.php' ) );
		$url = is_wp_error( $result ) || false === $result ? add_query_arg( 'project_error', rawurlencode( is_wp_error( $result ) ? $result->get_error_message() : __( 'The change could not be saved.', 'projectpress' ) ), $url ) : add_query_arg( 'project_saved', '1', $url );
		wp_safe_redirect( $url );
		exit;
	}
}
