<?php
/**
 * Individual project workspace manager.
 *
 * @package ProjectPress\Admin\Manager\Projects
 */
namespace ProjectPress\Admin\Manager\Projects;

use DateTimeInterface;
use ProjectPress\Admin\Manager\Manager;
use ProjectPress\Assets\Assets;
use ProjectPress\Includes\Functions\Helpers\DBHelper;
use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Pages\Taxonomies;
use ProjectPress\Includes\Project\Core\Project as DomainProject;
use ProjectPress\Includes\Project\Core\ProjectClassificationRepository;
use ProjectPress\Includes\Project\Core\ProjectFieldRepository;
use ProjectPress\Includes\Project\Core\ProjectFieldService;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\TeamRepository;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ProjectManager extends Manager {
	private DomainProjectManager $projects;
	private ?DomainProject $project = null;
	private ProjectClassificationRepository $classifications;
	private TeamRepository $teams;
	private ProjectFieldRepository $fields;
	private ProjectFieldService $field_service;
	private Project $details;

	/** @var array<string, string> */
	private array $tabs = array(
		'overview'    => 'Overview',
		'details'     => 'Details',
		'members'     => 'Members',
		'tasks'       => 'Tasks',
		'milestones'  => 'Milestones',
		'discussions' => 'Discussions',
		'notes'       => 'Notes',
		'files'       => 'Files',
		'activity'    => 'Activity',
		'settings'    => 'Settings',
	);

	public function __construct() {
		$this->page = 'projects';
		$this->projects = DomainProjectManager::get_instance();
		$permissions = new ProjectPermissions();
		$this->classifications = new ProjectClassificationRepository();
		$this->teams = new TeamRepository();
		$this->fields = new ProjectFieldRepository();
		$this->field_service = new ProjectFieldService( $this->fields, $this->projects->repository(), $permissions );
		$this->details = new Project( $this->projects, $permissions );
		add_action( 'admin_post_projectpress_workspace_project_details_save', array( $this->details, 'handle_save' ) );
	}

	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, array( 'projectpress' ), 'projects' );
	}

	public function render( string $tab = '', string $form = '' ): void {
		$identifier = RequestHelper::get_text( 'project', '' );
		if ( '' === $identifier ) {
			$identifier = (string) RequestHelper::get_integer( 'project_id', 0 );
		}
		$resolved = $this->projects->resolve( $identifier );
		if ( is_wp_error( $resolved ) ) {
			wp_die( esc_html( $resolved->get_error_message() ) );
		}
		$this->project = $resolved;
		$aliases = array_combine( array_keys( $this->tabs ), array_keys( $this->tabs ) );
		$tab = $this->normalize_route( '' !== $tab ? $tab : RequestHelper::get_key( 'tab', 'overview' ), $aliases, 'overview' );
		$this->header( $this->project->get_name() );
		$this->render_workspace_header( $tab );
		$this->render_tab( $tab );
		$this->footer();
	}

	private function render_workspace_header( string $active_tab ): void {
		if ( ! $this->project ) { return; }
		?>
		<div class="card card-body mb-4"><div class="d-flex flex-wrap justify-content-between align-items-start gap-3"><div><div class="d-flex flex-wrap align-items-center gap-2 mb-2"><span class="badge text-bg-secondary"><?php echo esc_html( $this->project->get_key() ); ?></span><span class="badge text-bg-light"><?php echo esc_html( $this->humanise( $this->project->get_status() ) ); ?></span><span class="badge text-bg-light"><?php echo esc_html( $this->humanise( $this->project->get_priority() ) ); ?></span></div><?php if ( '' !== trim( $this->project->get_description() ) ) : ?><p class="text-secondary mb-0"><?php echo esc_html( $this->project->get_description() ); ?></p><?php endif; ?></div><a class="btn btn-outline-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=projectpress&group=project-manager&tab=projects' ) ); ?>"><?php esc_html_e( 'Back to Projects', 'projectpress' ); ?></a></div></div>
		<nav class="nav nav-tabs flex-nowrap overflow-auto mb-4" aria-label="<?php esc_attr_e( 'Project workspace', 'projectpress' ); ?>"><?php foreach ( $this->tabs as $key => $label ) : ?><a class="nav-link <?php echo $key === $active_tab ? 'active' : ''; ?>" href="<?php echo esc_url( $this->workspace_url( $key ) ); ?>"><?php echo esc_html( $label ); ?></a><?php endforeach; ?></nav>
		<?php
	}

	private function render_tab( string $tab ): void {
		if ( ! $this->project ) { return; }
		do_action( 'projectpress_project_workspace_before_tab', $this->project, $tab );
		switch ( $tab ) {
			case 'overview': $this->render_overview(); break;
			case 'details': $this->details->render( $this->project ); break;
			default: $this->render_placeholder( $tab );
		}
		do_action( 'projectpress_project_workspace_after_tab', $this->project, $tab );
	}

	private function render_overview(): void {
		if ( ! $this->project ) { return; }
		$project_id = $this->project->get_id();
		$owner = $this->project->get_owner_id() ? get_user_by( 'id', $this->project->get_owner_id() ) : false;
		$parent = $this->project->get_parent_id() ? $this->projects->find( $this->project->get_parent_id() ) : null;
		$classifications = $this->overview_classifications( $project_id );
		$team_assignments = DBHelper::get_all( 'project_teams', array( 'project_id' => $project_id ), '*', 'assigned_at ASC' );
		$field_definitions = $this->fields->all( true );
		$field_values = $this->fields->values( $project_id );
		$activity = array_slice( DBHelper::get_all( 'activity', array( 'project_id' => $project_id ), '*', 'created_at DESC' ), 0, 5 );
		?>
		<div class="row g-3 mb-4"><?php $this->overview_stat( __( 'Status', 'projectpress' ), $this->humanise( $this->project->get_status() ) ); ?><?php $this->overview_stat( __( 'Priority', 'projectpress' ), $this->humanise( $this->project->get_priority() ) ); ?><?php $this->overview_stat( __( 'Visibility', 'projectpress' ), $this->humanise( $this->project->get_visibility() ) ); ?><?php $this->overview_stat( __( 'Teams', 'projectpress' ), (string) count( $team_assignments ) ); ?></div>
		<div class="row g-4">
		<div class="col-xl-7"><div class="card card-body h-100"><h2 class="h4"><?php esc_html_e( 'Project summary', 'projectpress' ); ?></h2><dl class="row mb-0"><dt class="col-sm-4"><?php esc_html_e( 'Project key', 'projectpress' ); ?></dt><dd class="col-sm-8"><?php echo esc_html( $this->project->get_key() ); ?></dd><dt class="col-sm-4"><?php esc_html_e( 'Owner', 'projectpress' ); ?></dt><dd class="col-sm-8"><?php echo esc_html( $owner ? $owner->display_name : __( 'Not assigned', 'projectpress' ) ); ?></dd><dt class="col-sm-4"><?php esc_html_e( 'Parent project', 'projectpress' ); ?></dt><dd class="col-sm-8"><?php echo esc_html( $parent ? $parent->get_name() : __( 'None', 'projectpress' ) ); ?></dd><dt class="col-sm-4"><?php esc_html_e( 'Start date', 'projectpress' ); ?></dt><dd class="col-sm-8"><?php echo esc_html( $this->format_date( $this->project->get_start_date() ) ); ?></dd><dt class="col-sm-4"><?php esc_html_e( 'Target end', 'projectpress' ); ?></dt><dd class="col-sm-8"><?php echo esc_html( $this->format_date( $this->project->get_target_end_date() ) ); ?></dd><dt class="col-sm-4"><?php esc_html_e( 'Actual end', 'projectpress' ); ?></dt><dd class="col-sm-8"><?php echo esc_html( $this->format_date( $this->project->get_actual_end_date() ) ); ?></dd></dl></div></div>
		<div class="col-xl-5"><div class="card card-body h-100"><h2 class="h4"><?php esc_html_e( 'Classifications', 'projectpress' ); ?></h2><?php foreach ( $classifications as $label => $terms ) : ?><div class="mb-3"><strong class="d-block small text-secondary mb-1"><?php echo esc_html( $label ); ?></strong><?php if ( empty( $terms ) ) : ?><span class="text-secondary"><?php esc_html_e( 'None', 'projectpress' ); ?></span><?php else : foreach ( $terms as $term ) : ?><span class="badge text-bg-light me-1 mb-1"><?php echo esc_html( $term->name ); ?></span><?php endforeach; endif; ?></div><?php endforeach; ?></div></div>
		<div class="col-xl-6"><div class="card card-body h-100"><h2 class="h4"><?php esc_html_e( 'Teams', 'projectpress' ); ?></h2><?php if ( empty( $team_assignments ) ) : ?><p class="text-secondary mb-0"><?php esc_html_e( 'No teams are assigned to this project.', 'projectpress' ); ?></p><?php endif; ?><?php foreach ( $team_assignments as $assignment ) : $team = $this->teams->find( (int) $assignment['team_id'] ); if ( ! $team ) { continue; } ?><div class="d-flex justify-content-between border-bottom py-2"><span><?php echo esc_html( $team['team_name'] ); ?></span><span class="badge text-bg-light"><?php echo esc_html( $this->humanise( (string) $assignment['team_role'] ) ); ?></span></div><?php endforeach; ?></div></div>
		<div class="col-xl-6"><div class="card card-body h-100"><h2 class="h4"><?php esc_html_e( 'Global fields', 'projectpress' ); ?></h2><?php if ( empty( $field_definitions ) ) : ?><p class="text-secondary mb-0"><?php esc_html_e( 'No global fields are configured.', 'projectpress' ); ?></p><?php endif; ?><?php foreach ( $field_definitions as $field ) : $row = $field_values[ (int) $field['id'] ] ?? null; $value = $this->field_service->display_value( $field, $row ); ?><div class="border-bottom py-2"><strong class="d-block small text-secondary"><?php echo esc_html( $field['field_name'] ); ?></strong><span><?php echo esc_html( '' !== $value ? $value : __( 'Not set', 'projectpress' ) ); ?></span></div><?php endforeach; ?></div></div>
		<div class="col-12"><div class="card card-body"><div class="d-flex justify-content-between align-items-center gap-3 mb-2"><h2 class="h4 mb-0"><?php esc_html_e( 'Recent activity', 'projectpress' ); ?></h2><a href="<?php echo esc_url( $this->workspace_url( 'activity' ) ); ?>"><?php esc_html_e( 'View activity', 'projectpress' ); ?></a></div><?php if ( empty( $activity ) ) : ?><p class="text-secondary mb-0"><?php esc_html_e( 'No project activity has been recorded yet.', 'projectpress' ); ?></p><?php endif; ?><?php foreach ( $activity as $item ) : ?><div class="border-bottom py-2"><div><?php echo esc_html( (string) ( $item['activity_summary'] ?: $this->humanise( (string) $item['action'] ) ) ); ?></div><small class="text-secondary"><?php echo esc_html( (string) $item['created_at'] ); ?></small></div><?php endforeach; ?></div></div>
		</div><?php
	}

	private function overview_classifications( int $project_id ): array {
		$map = array( __( 'Types', 'projectpress' ) => Taxonomies::PROJECT_TYPE, __( 'Categories', 'projectpress' ) => Taxonomies::PROJECT_CATEGORY, __( 'Tags', 'projectpress' ) => Taxonomies::PROJECT_TAG, __( 'Labels', 'projectpress' ) => Taxonomies::PROJECT_LABEL );
		$result = array(); foreach ( $map as $label => $taxonomy ) { $result[ $label ] = array(); foreach ( $this->classifications->assigned_term_ids( $project_id, $taxonomy ) as $term_id ) { $term = get_term( $term_id, $taxonomy ); if ( $term && ! is_wp_error( $term ) ) { $result[ $label ][] = $term; } } } return $result;
	}
	private function overview_stat( string $label, string $value ): void { ?><div class="col-sm-6 col-xl-3"><div class="card card-body h-100"><span class="small text-secondary"><?php echo esc_html( $label ); ?></span><strong class="fs-5"><?php echo esc_html( $value ); ?></strong></div></div><?php }
	private function workspace_url( string $tab ): string { return add_query_arg( array( 'page' => 'projectpress', 'group' => 'projects', 'project_id' => $this->project ? $this->project->get_id() : 0, 'tab' => $tab ), admin_url( 'admin.php' ) ); }
	private function humanise( string $value ): string { return ucwords( str_replace( '_', ' ', $value ) ); }
	private function format_date( ?DateTimeInterface $date ): string { return $date ? $date->format( 'j M Y' ) : __( 'Not set', 'projectpress' ); }
	private function render_placeholder( string $tab ): void { $label = $this->tabs[ $tab ] ?? __( 'Project', 'projectpress' ); ?><div class="card card-body"><h2 class="h4"><?php echo esc_html( $label ); ?></h2><p class="text-secondary mb-0"><?php esc_html_e( 'This project workspace section is ready for its Phase 5 implementation.', 'projectpress' ); ?></p></div><?php }
}
