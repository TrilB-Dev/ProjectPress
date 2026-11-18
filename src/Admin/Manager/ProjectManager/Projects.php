<?php
/**
 * Global Projects admin screen and browser action handlers.
 *
 * @package ProjectPress\Admin\Manager\ProjectManager
 */
namespace ProjectPress\Admin\Manager\ProjectManager;

use ProjectPress\Includes\Core\Defaults;
use ProjectPress\Includes\Functions\Helpers\FormFieldHelper;
use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Projects {
	private DomainProjectManager $manager;
	public function __construct( DomainProjectManager $manager ) { $this->manager = $manager; }

	public function render(): void {
		$args = array(
			'status' => RequestHelper::get_key( 'status', '' ),
			'priority' => RequestHelper::get_key( 'priority', '' ),
			'visibility' => RequestHelper::get_key( 'visibility', '' ),
			'search' => RequestHelper::get_text( 's', '' ),
			'page' => max( 1, RequestHelper::get_integer( 'paged', 1 ) ),
			'per_page' => 20,
		);
		$projects = $this->manager->query( $args );
		$new_url = admin_url( 'admin.php?page=projectpress&group=project-manager&tab=new' );
		?>
		<div class="d-flex justify-content-between align-items-center mb-3">
			<p class="text-secondary mb-0"><?php esc_html_e( 'Manage projects across ProjectPress.', 'projectpress' ); ?></p>
			<?php if ( current_user_can( 'manage_options' ) || current_user_can( 'projectpress_projects_create' ) ) : ?><a class="btn btn-primary" href="<?php echo esc_url( $new_url ); ?>"><?php esc_html_e( 'New Project', 'projectpress' ); ?></a><?php endif; ?>
		</div>
		<form method="get" class="card card-body mb-3">
			<input type="hidden" name="page" value="projectpress" /><input type="hidden" name="group" value="project-manager" /><input type="hidden" name="tab" value="projects" />
			<div class="row g-2"><div class="col-md-5"><?php echo FormFieldHelper::text_input( 's', $args['search'], array( 'placeholder' => __( 'Search projects', 'projectpress' ) ) ); ?></div>
			<div class="col-md-2"><?php echo FormFieldHelper::select( 'status', array_combine( Defaults::project_statuses(), array_map( 'ucwords', array_map( static fn( $v ) => str_replace( '_', ' ', $v ), Defaults::project_statuses() ) ) ), $args['status'] ); ?></div>
			<div class="col-md-2"><?php echo FormFieldHelper::select( 'priority', array_combine( Defaults::project_priorities(), array_map( 'ucwords', Defaults::project_priorities() ) ), $args['priority'] ); ?></div>
			<div class="col-md-2"><?php echo FormFieldHelper::select( 'visibility', array_combine( Defaults::project_visibilities(), array_map( 'ucwords', Defaults::project_visibilities() ) ), $args['visibility'] ); ?></div>
			<div class="col-md-1"><button class="btn btn-outline-primary w-100" type="submit"><?php esc_html_e( 'Filter', 'projectpress' ); ?></button></div></div>
		</form>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="projectpress_projects_bulk" /><?php wp_nonce_field( 'projectpress_projects_bulk' ); ?>
			<div class="table-responsive"><table class="table align-middle"><thead><tr><th><span class="screen-reader-text"><?php esc_html_e( 'Select', 'projectpress' ); ?></span></th><th><?php esc_html_e( 'Project', 'projectpress' ); ?></th><th><?php esc_html_e( 'Key', 'projectpress' ); ?></th><th><?php esc_html_e( 'Status', 'projectpress' ); ?></th><th><?php esc_html_e( 'Priority', 'projectpress' ); ?></th><th><?php esc_html_e( 'Visibility', 'projectpress' ); ?></th></tr></thead><tbody>
			<?php if ( empty( $projects ) ) : ?><tr><td colspan="6"><?php esc_html_e( 'No projects found.', 'projectpress' ); ?></td></tr><?php endif; ?>
			<?php foreach ( $projects as $project ) : $edit = admin_url( 'admin.php?page=projectpress&group=project-manager&tab=edit&project_id=' . $project->get_id() ); ?><tr><td><input type="checkbox" name="project_ids[]" value="<?php echo esc_attr( (string) $project->get_id() ); ?>" /></td><td><a href="<?php echo esc_url( $edit ); ?>"><?php echo esc_html( $project->get_name() ); ?></a></td><td><?php echo esc_html( $project->get_key() ); ?></td><td><?php echo esc_html( $project->get_status() ); ?></td><td><?php echo esc_html( $project->get_priority() ); ?></td><td><?php echo esc_html( $project->get_visibility() ); ?></td></tr><?php endforeach; ?>
			</tbody></table></div>
			<?php if ( current_user_can( 'manage_options' ) || current_user_can( 'projectpress_projects_delete' ) ) : ?><div class="d-flex gap-2"><select class="form-select w-auto" name="bulk_action"><option value=""><?php esc_html_e( 'Bulk actions', 'projectpress' ); ?></option><option value="delete"><?php esc_html_e( 'Delete', 'projectpress' ); ?></option></select><button class="btn btn-outline-danger" type="submit"><?php esc_html_e( 'Apply', 'projectpress' ); ?></button></div><?php endif; ?>
		</form>
		<?php
	}

	public function render_form( int $project_id = 0 ): void {
		$project = $project_id > 0 ? $this->manager->find( $project_id ) : null;
		if ( $project_id > 0 && ! $project ) { echo '<div class="notice notice-error"><p>' . esc_html__( 'Project not found.', 'projectpress' ) . '</p></div>'; return; }
		if ( $project && ! current_user_can( 'manage_options' ) && ! current_user_can( 'projectpress_projects_edit' ) ) { wp_die( esc_html__( 'You are not authorised to edit projects.', 'projectpress' ) ); }
		if ( ! $project && ! current_user_can( 'manage_options' ) && ! current_user_can( 'projectpress_projects_create' ) ) { wp_die( esc_html__( 'You are not authorised to create projects.', 'projectpress' ) ); }
		$data = $project ? $project->to_array() : array( 'name' => '', 'description' => '', 'slug' => '', 'key' => '', 'status' => Defaults::PROJECT_STATUS_DEFAULT, 'priority' => Defaults::PROJECT_PRIORITY_DEFAULT, 'visibility' => Defaults::PROJECT_VISIBILITY_DEFAULT );
		?>
		<form class="card card-body" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="projectpress_project_save" /><input type="hidden" name="project_id" value="<?php echo esc_attr( (string) $project_id ); ?>" /><?php wp_nonce_field( 'projectpress_project_save' ); ?>
			<div class="mb-3"><?php echo FormFieldHelper::label( 'project-name', __( 'Name', 'projectpress' ) ); ?><?php echo FormFieldHelper::text_input( 'name', (string) $data['name'], array( 'id' => 'project-name', 'required' => true ) ); ?></div>
			<div class="mb-3"><?php echo FormFieldHelper::label( 'project-description', __( 'Description', 'projectpress' ) ); ?><?php echo FormFieldHelper::textarea( 'description', (string) $data['description'], array( 'id' => 'project-description', 'rows' => 5 ) ); ?></div>
			<div class="row g-3"><div class="col-md-4"><?php echo FormFieldHelper::label( 'project-status', __( 'Status', 'projectpress' ) ); ?><?php echo FormFieldHelper::select( 'status', array_combine( Defaults::project_statuses(), array_map( 'ucwords', array_map( static fn( $v ) => str_replace( '_', ' ', $v ), Defaults::project_statuses() ) ) ), $data['status'], array( 'id' => 'project-status' ) ); ?></div><div class="col-md-4"><?php echo FormFieldHelper::label( 'project-priority', __( 'Priority', 'projectpress' ) ); ?><?php echo FormFieldHelper::select( 'priority', array_combine( Defaults::project_priorities(), array_map( 'ucwords', Defaults::project_priorities() ) ), $data['priority'], array( 'id' => 'project-priority' ) ); ?></div><div class="col-md-4"><?php echo FormFieldHelper::label( 'project-visibility', __( 'Visibility', 'projectpress' ) ); ?><?php echo FormFieldHelper::select( 'visibility', array_combine( Defaults::project_visibilities(), array_map( 'ucwords', Defaults::project_visibilities() ) ), $data['visibility'], array( 'id' => 'project-visibility' ) ); ?></div></div>
			<div class="mt-3"><button class="btn btn-primary" type="submit"><?php echo esc_html( $project ? __( 'Update Project', 'projectpress' ) : __( 'Create Project', 'projectpress' ) ); ?></button></div>
		</form><?php
	}

	public function handle_save(): void {
		check_admin_referer( 'projectpress_project_save' );
		$id = RequestHelper::integer( $_POST, 'project_id', 0 );
		$input = array( 'name' => RequestHelper::text( $_POST, 'name', '' ), 'description' => RequestHelper::text( $_POST, 'description', '' ), 'status' => RequestHelper::key( $_POST, 'status', Defaults::PROJECT_STATUS_DEFAULT ), 'priority' => RequestHelper::key( $_POST, 'priority', Defaults::PROJECT_PRIORITY_DEFAULT ), 'visibility' => RequestHelper::key( $_POST, 'visibility', Defaults::PROJECT_VISIBILITY_DEFAULT ) );
		$result = $id > 0 ? ( ( $project = $this->manager->find( $id ) ) ? $this->manager->service()->update( $project, $input ) : new \WP_Error( 'projectpress_project_not_found', __( 'Project not found.', 'projectpress' ) ) ) : $this->manager->create( $input );
		$this->redirect_result( $result );
	}

	public function handle_delete(): void {
		check_admin_referer( 'projectpress_project_delete' );
		$id = RequestHelper::integer( $_POST, 'project_id', 0 );
		$project = $this->manager->find( $id );
		$result = $project ? $this->manager->service()->delete( $project ) : new \WP_Error( 'projectpress_project_not_found', __( 'Project not found.', 'projectpress' ) );
		$this->redirect_result( $result );
	}

	public function handle_bulk(): void {
		check_admin_referer( 'projectpress_projects_bulk' );
		$action = RequestHelper::key( $_POST, 'bulk_action', '' );
		$ids = RequestHelper::array( $_POST, 'project_ids', array() );
		if ( 'delete' !== $action ) { $this->redirect_result( new \WP_Error( 'projectpress_bulk_invalid', __( 'Invalid bulk action.', 'projectpress' ) ) ); }
		$errors = array();
		foreach ( $ids as $id ) { $project = $this->manager->find( absint( $id ) ); if ( ! $project ) { continue; } $result = $this->manager->service()->delete( $project ); if ( is_wp_error( $result ) ) { $errors[] = $result->get_error_message(); } }
		$this->redirect_result( empty( $errors ) ? true : new \WP_Error( 'projectpress_bulk_partial', implode( ' ', $errors ) ) );
	}

	private function redirect_result( $result ): void {
		$url = admin_url( 'admin.php?page=projectpress&group=project-manager&tab=projects' );
		if ( is_wp_error( $result ) ) { $url = add_query_arg( array( 'project_error' => rawurlencode( $result->get_error_message() ) ), $url ); } else { $url = add_query_arg( 'project_saved', '1', $url ); }
		wp_safe_redirect( $url ); exit;
	}
}
