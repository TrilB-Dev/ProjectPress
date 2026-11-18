<?php
/**
 * Global Project Manager coordinator.
 *
 * @package ProjectPress\Admin\Manager\ProjectManager
 */
namespace ProjectPress\Admin\Manager\ProjectManager;

use ProjectPress\Admin\Manager\Manager;
use ProjectPress\Assets\Assets;
use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class ProjectsManager extends Manager {
	private Projects $projects;
	private Categories $categories;
	private Tags $tags;
	private DomainProjectManager $domain;

	public function __construct() {
		$this->page = 'project-manager';
		$this->domain = DomainProjectManager::get_instance();
		$this->projects = new Projects( $this->domain );
		$this->categories = new Categories();
		$this->tags = new Tags();
		$this->register_actions();
	}

	private function register_actions(): void {
		add_action( 'admin_post_projectpress_project_save', array( $this->projects, 'handle_save' ) );
		add_action( 'admin_post_projectpress_project_delete', array( $this->projects, 'handle_delete' ) );
		add_action( 'admin_post_projectpress_projects_bulk', array( $this->projects, 'handle_bulk' ) );
	}

	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, array( 'projectpress' ), 'projects' );
	}

	public function render( string $tab = '', string $form = '' ): void {
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'projectpress_projects_view' ) ) {
			wp_die( esc_html__( 'You are not authorised to view Project Manager.', 'projectpress' ) );
		}

		$tab = $this->normalize_route(
			'' !== $tab ? $tab : RequestHelper::get_key( 'tab', 'projects' ),
			array( 'projects' => 'projects', 'new' => 'new', 'edit' => 'edit', 'categories' => 'categories', 'tags' => 'tags', 'types' => 'types', 'fields' => 'fields', 'teams' => 'teams' ),
			'projects'
		);

		$this->header( __( 'Project Manager', 'projectpress' ) );
		switch ( $tab ) {
			case 'new': $this->projects->render_form(); break;
			case 'edit': $this->projects->render_form( RequestHelper::get_integer( 'project_id', 0 ) ); break;
			case 'categories': $this->categories->render(); break;
			case 'tags': $this->tags->render(); break;
			case 'types': $this->render_placeholder( __( 'Project Types', 'projectpress' ), __( 'Project type management is reserved for the authoritative type storage layer.', 'projectpress' ) ); break;
			case 'fields': $this->render_placeholder( __( 'Global Fields', 'projectpress' ), __( 'Global field management is reserved for the authoritative field storage layer.', 'projectpress' ) ); break;
			case 'teams': $this->render_placeholder( __( 'Teams', 'projectpress' ), __( 'Team configuration is reserved for the ProjectPress team domain.', 'projectpress' ) ); break;
			default: $this->projects->render();
		}
		$this->footer();
	}

	private function render_placeholder( string $title, string $description ): void {
		printf( '<section class="card shadow-sm"><div class="card-body"><h2 class="h4">%s</h2><p class="mb-0">%s</p></div></section>', esc_html( $title ), esc_html( $description ) );
	}
}
