<?php
/**
 * Individual project workspace manager.
 *
 * @package ProjectPress\Admin\Manager\Projects
 */
namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Admin\Manager\Manager;
use ProjectPress\Assets\Assets;
use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\Core\Project as DomainProject;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ProjectManager extends Manager {
	private DomainProjectManager $projects;
	private ?DomainProject $project = null;

	/** @var array<string, string> */
	private array $tabs = array(
		'overview'   => 'Overview',
		'details'    => 'Details',
		'members'    => 'Members',
		'tasks'      => 'Tasks',
		'milestones' => 'Milestones',
		'discussions'=> 'Discussions',
		'notes'      => 'Notes',
		'files'      => 'Files',
		'activity'   => 'Activity',
		'settings'   => 'Settings',
	);

	public function __construct() {
		$this->page = 'projects';
		$this->projects = DomainProjectManager::get_instance();
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
		if ( ! $this->project ) {
			return;
		}
		?>
		<div class="card card-body mb-4">
			<div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
				<div>
					<div class="d-flex flex-wrap align-items-center gap-2 mb-2">
						<span class="badge text-bg-secondary"><?php echo esc_html( $this->project->get_key() ); ?></span>
						<span class="badge text-bg-light"><?php echo esc_html( ucfirst( str_replace( '_', ' ', $this->project->get_status() ) ) ); ?></span>
						<span class="badge text-bg-light"><?php echo esc_html( ucfirst( $this->project->get_priority() ) ); ?></span>
					</div>
					<?php if ( '' !== trim( $this->project->get_description() ) ) : ?>
						<p class="text-secondary mb-0"><?php echo esc_html( $this->project->get_description() ); ?></p>
					<?php endif; ?>
				</div>
				<a class="btn btn-outline-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=projectpress&group=project-manager&tab=projects' ) ); ?>"><?php esc_html_e( 'Back to Projects', 'projectpress' ); ?></a>
			</div>
		</div>
		<nav class="nav nav-tabs flex-nowrap overflow-auto mb-4" aria-label="<?php esc_attr_e( 'Project workspace', 'projectpress' ); ?>">
			<?php foreach ( $this->tabs as $key => $label ) :
				$url = add_query_arg(
					array(
						'page'       => 'projectpress',
						'group'      => 'projects',
						'project_id' => $this->project->get_id(),
						'tab'        => $key,
					),
					admin_url( 'admin.php' )
				);
				?>
				<a class="nav-link <?php echo $key === $active_tab ? 'active' : ''; ?>" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
		<?php
	}

	private function render_tab( string $tab ): void {
		if ( ! $this->project ) {
			return;
		}

		do_action( 'projectpress_project_workspace_before_tab', $this->project, $tab );

		switch ( $tab ) {
			case 'overview':
				$this->render_overview();
				break;
			default:
				$this->render_placeholder( $tab );
				break;
		}

		do_action( 'projectpress_project_workspace_after_tab', $this->project, $tab );
	}

	private function render_overview(): void {
		if ( ! $this->project ) {
			return;
		}
		?>
		<div class="row g-3">
			<div class="col-md-4"><div class="card card-body h-100"><span class="text-secondary small"><?php esc_html_e( 'Status', 'projectpress' ); ?></span><strong><?php echo esc_html( ucfirst( str_replace( '_', ' ', $this->project->get_status() ) ) ); ?></strong></div></div>
			<div class="col-md-4"><div class="card card-body h-100"><span class="text-secondary small"><?php esc_html_e( 'Priority', 'projectpress' ); ?></span><strong><?php echo esc_html( ucfirst( $this->project->get_priority() ) ); ?></strong></div></div>
			<div class="col-md-4"><div class="card card-body h-100"><span class="text-secondary small"><?php esc_html_e( 'Visibility', 'projectpress' ); ?></span><strong><?php echo esc_html( ucfirst( $this->project->get_visibility() ) ); ?></strong></div></div>
		</div>
		<?php
	}

	private function render_placeholder( string $tab ): void {
		$label = $this->tabs[ $tab ] ?? __( 'Project', 'projectpress' );
		?>
		<div class="card card-body">
			<h2 class="h4"><?php echo esc_html( $label ); ?></h2>
			<p class="text-secondary mb-0"><?php esc_html_e( 'This project workspace section is ready for its Phase 5 implementation.', 'projectpress' ); ?></p>
		</div>
		<?php
	}
}
