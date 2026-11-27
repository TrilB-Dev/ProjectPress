<?php
/**
 * Individual project workspace manager.
 *
 * @package ProjectPress
 * @subpackage Admin/Manager/Projects
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\Projects;

use DateTimeInterface;
use ProjectPress\Admin\Manager\Manager;
use ProjectPress\Assets\Assets;
use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\Core\Project as DomainProject;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\TeamRepository;
use ProjectPress\Includes\Project\Integration\ProjectExtensionRegistry;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;
use ProjectPress\Includes\Project\Tracking\ActivityRepository;

if (!defined("ABSPATH")) {
	exit();
}

/**
 * Coordinates the individual project workspace.
 */
final class ProjectManager extends Manager {
	/**
	 * Project manager service.
	 *
	 * @var DomainProjectManager
	 * @since 1.0.0
	 */
	private DomainProjectManager $projects;

	/**
	 * Resolved project record.
	 *
	 * @var DomainProject|null
	 * @since 1.0.0
	 */
	private ?DomainProject $project = null;

	/**
	 * Team repository.
	 *
	 * @var TeamRepository
	 * @since 1.0.0
	 */
	private TeamRepository $teams;

	/**
	 * Activity repository.
	 *
	 * @var ActivityRepository
	 * @since 1.0.0
	 */
	private ActivityRepository $activity;

	/**
	 * @var Project
	 * @since 1.0.0
	 */
	private Project $details;

	/**
	 * Global project list and lifecycle handler.
	 *
	 * @var Project
	 * @since 1.0.0
	 */
	private Project $global_projects;

	/**
	 * Global category handler.
	 *
	 * @var Categories
	 * @since 1.0.0
	 */
	private Categories $categories;

	/**
	 * Global tag handler.
	 *
	 * @var Tags
	 * @since 1.0.0
	 */
	private Tags $tags;

	/**
	 * Global type handler.
	 *
	 * @var Types
	 * @since 1.0.0
	 */
	private Types $types;

	/**
	 * Global label handler.
	 *
	 * @var Labels
	 * @since 1.0.0
	 */
	private Labels $labels;

	/**
	 * Global team handler.
	 *
	 * @var Teams
	 * @since 1.0.0
	 */
	private Teams $global_teams;

	/**
	 * Global field handler.
	 *
	 * @var Fields
	 * @since 1.0.0
	 */
	private Fields $fields;

	/**
	 * @var Teams
	 * @since 1.0.0
	 */
	private Teams $members;

	/**
	 * @var Tasks
	 * @since 1.0.0
	 */
	private Tasks $tasks;

	/**
	 * @var Milestones
	 * @since 1.0.0
	 */
	private Milestones $milestones;

	/**
	 * @var Planning
	 * @since 1.0.0
	 */
	private Planning $planning;

	/**
	 * @var Collaboration
	 * @since 1.0.0
	 */
	private Collaboration $collaboration;

	/**
	 * @var WorkspaceContent
	 * @since 1.0.0
	 */
	private WorkspaceContent $content;

	/**
	 * @var Requirements
	 * @since 1.0.0
	 */
	private Requirements $requirements;

	/**
	 * @var Features
	 * @since 1.0.0
	 */
	private Features $features;

	/**
	 * @var UserStories
	 * @since 1.0.0
	 */
	private UserStories $stories;

	/**
	 * @var Backlog
	 * @since 1.0.0
	 */
	private Backlog $backlog;

	/**
	 * @var Governance
	 * @since 1.0.0
	 */
	private Governance $governance;

	/**
	 * @var Quality
	 * @since 1.0.0
	 */
	private Quality $quality;

	/**
	 * @var Delivery
	 * @since 1.0.0
	 */
	private Delivery $delivery;

	/**
	 * @var PeopleCommunication
	 * @since 1.0.0
	 */
	private PeopleCommunication $people_communication;

	/**
	 * @var Reporting
	 * @since 1.0.0
	 */
	private Reporting $reporting;

	/**
	 * @var Composition
	 * @since 1.0.0
	 */
	private Composition $composition;

	/**
	 * Workspace tabs.
	 *
	 * @var array<string, string>
	 * @since 1.0.0
	 */
	private array $tabs = [
		"overview" => "Overview",
		"details" => "Details",
		"members" => "Members",
		"planning" => "Planning",
		"requirements" => "Requirements",
		"features" => "Features",
		"user-stories" => "User Stories",
		"backlog" => "Backlog",
		"governance" => "Governance",
		"quality" => "Quality",
		"delivery" => "Delivery",
		"people-communication" => "People & Communication",
		"reporting" => "Reporting",
		"composition" => "Composition",
		"tasks" => "Tasks",
		"milestones" => "Milestones",
		"documents" => "Documents",
		"discussions" => "Discussions",
		"notes" => "Notes",
		"files" => "Files",
		"activity" => "Activity",
		"settings" => "Settings",
	];

	/**
	 * Constructor.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->page = "projects";
		$this->projects = DomainProjectManager::get_instance();
		$permissions = new ProjectPermissions();
		$this->teams = new TeamRepository();
		$this->activity = new ActivityRepository();
		$this->details = new Project($this->projects, $permissions);
		$this->members = new Teams($this->projects, $permissions);
		$this->global_projects = new Project($this->projects, $permissions);
		$this->global_teams = new Teams($this->projects, $permissions);
		$this->categories = new Categories();
		$this->tags = new Tags();
		$this->types = new Types();
		$this->labels = new Labels();
		$this->fields = new Fields();
		$this->tasks = new Tasks($this->projects, $permissions);
		$this->milestones = new Milestones($this->projects, $permissions);
		$this->planning = new Planning($this->projects);
		$this->collaboration = new Collaboration($this->projects, $permissions);
		$this->content = new WorkspaceContent($this->projects, $permissions);
		$this->requirements = new Requirements($this->projects, $permissions);
		$this->features = new Features($this->projects, $permissions);
		$this->stories = new UserStories($this->projects, $permissions);
		$this->backlog = new Backlog();
		$this->governance = new Governance($this->projects, $permissions);
		$this->quality = new Quality($this->projects, $permissions);
		$this->delivery = new Delivery($this->projects, $permissions);
		$this->people_communication = new PeopleCommunication($this->projects, $permissions);
		$this->reporting = new Reporting($this->projects, $permissions);
		$this->composition = new Composition($this->projects, $permissions);

		add_action("admin_post_projectpress_project_save", [$this->global_projects, "handle_global_save"]);
		add_action("admin_post_projectpress_project_archive", [$this->global_projects, "handle_global_archive"]);
		add_action("admin_post_projectpress_project_restore", [$this->global_projects, "handle_global_restore"]);
		add_action("admin_post_projectpress_project_delete", [$this->global_projects, "handle_global_delete"]);
		add_action("admin_post_projectpress_projects_bulk", [$this->global_projects, "handle_global_bulk"]);
		add_action("admin_post_projectpress_classification_save", [$this->types, "handle_save"]);
		add_action("admin_post_projectpress_team_save", [$this->global_teams, "handle_global_save"]);
		add_action("admin_post_projectpress_team_member_save", [$this->global_teams, "handle_global_member_save"]);
		add_action("admin_post_projectpress_team_project_save", [$this->global_teams, "handle_global_project_save"]);
		add_action("admin_post_projectpress_field_save", [$this->fields, "handle_save"]);
		add_action("admin_post_projectpress_workspace_project_details_save", [$this->details, "handle_save"]);
		add_action("admin_post_projectpress_workspace_project_team_assign", [$this->members, "handle_assign"]);
		add_action("admin_post_projectpress_workspace_project_team_remove", [$this->members, "handle_remove"]);
		add_action("admin_post_projectpress_workspace_task_save", [$this->tasks, "handle_save"]);
		add_action("admin_post_projectpress_workspace_task_delete", [$this->tasks, "handle_delete"]);
		add_action("admin_post_projectpress_workspace_requirement_save", [$this->requirements, "handle_save"]);
		add_action("admin_post_projectpress_workspace_requirement_delete", [$this->requirements, "handle_delete"]);
		add_action("admin_post_projectpress_workspace_feature_save", [$this->features, "handle_save"]);
		add_action("admin_post_projectpress_workspace_feature_delete", [$this->features, "handle_delete"]);
		add_action("admin_post_projectpress_workspace_story_save", [$this->stories, "handle_save"]);
		add_action("admin_post_projectpress_workspace_story_delete", [$this->stories, "handle_delete"]);
		add_action("admin_post_projectpress_workspace_governance_save", [$this->governance, "handle_save"]);
		add_action("admin_post_projectpress_workspace_governance_delete", [$this->governance, "handle_delete"]);
		add_action("admin_post_projectpress_workspace_quality_save", [$this->quality, "handle_save"]);
		add_action("admin_post_projectpress_workspace_quality_delete", [$this->quality, "handle_delete"]);
		add_action("admin_post_projectpress_workspace_delivery_save", [$this->delivery, "handle_save"]);
		add_action("admin_post_projectpress_workspace_delivery_delete", [$this->delivery, "handle_delete"]);
		add_action("admin_post_projectpress_workspace_people_save", [$this->people_communication, "handle_save"]);
		add_action("admin_post_projectpress_workspace_people_delete", [$this->people_communication, "handle_delete"]);
		add_action("admin_post_projectpress_workspace_composition_save", [$this->composition, "handle_save"]);
		add_action("admin_post_projectpress_workspace_composition_delete", [$this->composition, "handle_delete"]);
		add_action("admin_post_projectpress_workspace_milestone_save", [$this->milestones, "handle_save"]);
		add_action("admin_post_projectpress_workspace_milestone_delete", [$this->milestones, "handle_delete"]);
		add_action("admin_post_projectpress_workspace_collaboration_save", [$this->collaboration, "handle_save"]);
		add_action("admin_post_projectpress_workspace_collaboration_delete", [$this->collaboration, "handle_delete"]);
		add_action("admin_post_projectpress_workspace_content_save", [$this->content, "handle_save"]);
		add_action("admin_post_projectpress_workspace_content_comment", [$this->content, "handle_comment"]);
		add_action("admin_post_projectpress_workspace_setting_save", [$this->content, "handle_setting_save"]);
		add_action("admin_post_projectpress_workspace_content_delete", [$this->content, "handle_delete"]);
	}

	/**
	 * Register workspace assets.
	 *
	 * @param Assets $assets Asset registrar.
	 * @return void
	 */
	public function register_assets(Assets $assets): void {
		$this->register_page_assets($assets, ["projectpress"], "projects");
	}

	/**
	 * Render the project workspace.
	 *
	 * @param string $tab Initial workspace tab.
	 * @param string $form Initial workspace form.
	 * @return void
	 */
	public function render(string $tab = "", string $form = ""): void {
		$group = sanitize_key(RequestHelper::get_key("group", ""));
		if ("project-manager" === $group) {
			$this->render_global($tab, $form);
			return;
		}
		$this->render_workspace($tab, $form);
	}

	/**
	 * Render global Project Manager routes.
	 *
	 * @param string $tab Global route tab.
	 * @param string $form Global route form.
	 * @return void
	 * @since 1.0.0
	 */
	public function render_global(string $tab = "", string $form = ""): void {
		if (!current_user_can("manage_options") && !current_user_can("projectpress_projects_view")) {
			wp_die(esc_html__("You are not authorised to view Project Manager.", "projectpress"));
		}

		$allowed_tabs = ["projects", "new", "edit", "categories", "tags", "types", "labels", "fields", "teams"];
		$tab = $this->normalize_route(
			"" !== $tab ? $tab : RequestHelper::get_key("tab", "projects"),
			array_combine($allowed_tabs, $allowed_tabs),
			"projects",
		);

		$this->header(__("Project Manager", "projectpress"));

		switch ($tab) {
			case "new":
				$this->global_projects->render_global_form();
				break;
			case "edit":
				$this->global_projects->render_global_form(RequestHelper::get_integer("project_id", 0));
				break;
			case "categories":
				$this->categories->render();
				break;
			case "tags":
				$this->tags->render();
				break;
			case "types":
				$this->types->render();
				break;
			case "labels":
				$this->labels->render();
				break;
			case "fields":
				$this->fields->render();
				break;
			case "teams":
				$this->global_teams->render_global();
				break;
			default:
				$this->global_projects->render_global_list();
		}

		$this->footer();
	}

	/**
	 * Render an individual project workspace.
	 *
	 * @param string $tab Initial workspace tab.
	 * @param string $form Initial workspace form.
	 * @return void
	 * @since 1.0.0
	 */
	public function render_workspace(string $tab = "", string $form = ""): void {
		$identifier = RequestHelper::get_text("project", "");
		if ("" === $identifier) {
			$identifier = (string) RequestHelper::get_integer("project_id", 0);
		}
		$resolved = $this->projects->resolve($identifier);
		if (is_wp_error($resolved)) {
			wp_die(esc_html($resolved->get_error_message()));
		}
		$this->project = $resolved;
		$tabs = $this->workspace_tabs();
		$aliases = array_combine(array_keys($tabs), array_keys($tabs));
		$tab = $this->normalize_route(
			"" !== $tab ? $tab : RequestHelper::get_key("tab", "overview"),
			$aliases,
			"overview",
		);
		$this->tabs = $tabs;
		$this->header($this->project->get_name());
		$this->render_workspace_header($tab);
		$this->render_tab($tab);
		$this->footer();
	}

	/**
	 * Build the available workspace tabs.
	 *
	 * @return array<string, string>
	 */
	private function workspace_tabs(): array {
		$tabs = $this->tabs;
		foreach (ProjectExtensionRegistry::get_instance()->workspace_tabs() as $key => $definition) {
			$tabs[$key] = (string) $definition["label"];
		}
		return apply_filters("projectpress_project_workspace_tabs", $tabs, $this->project);
	}

	/**
	 * Render the workspace project header and tab navigation.
	 *
	 * @param string $active Active tab.
	 * @return void
	 */
	private function render_workspace_header(string $active): void {
		?>
		<div class="card card-body mb-4">
			<div class="d-flex flex-wrap justify-content-between gap-3">
				<div>
					<div class="d-flex gap-2 mb-2">
						<span class="badge text-bg-secondary">
							<?php echo esc_html($this->project->get_key()); ?>
						</span>
						<span class="badge text-bg-light">
							<?php echo esc_html($this->humanise($this->project->get_status())); ?>
						</span>
						<span class="badge text-bg-light">
							<?php echo esc_html($this->humanise($this->project->get_priority())); ?>
						</span>
					</div>
					<p class="text-secondary mb-0">
						<?php echo esc_html($this->project->get_description()); ?>
					</p>
				</div>
				<a
					class="btn btn-outline-secondary"
					href="<?php echo esc_url(admin_url("admin.php?page=projectpress&group=project-manager&tab=projects")); ?>"
				>
					<?php esc_html_e("Back to Projects", "projectpress"); ?>
				</a>
			</div>
		</div>

		<nav class="nav nav-tabs flex-nowrap overflow-auto mb-4">
			<?php foreach ($this->tabs as $key => $label): ?>
				<a
					class="nav-link <?php echo $key === $active ? "active" : ""; ?>"
					href="<?php echo esc_url($this->workspace_url($key)); ?>"
				>
					<?php echo esc_html($label); ?>
				</a>
			<?php endforeach; ?>
		</nav>
		<?php
	}

	/**
	 * Render the active workspace tab.
	 *
	 * @param string $tab Active tab.
	 * @return void
	 */
	private function render_tab(string $tab): void {
		do_action("projectpress_project_workspace_before_tab", $this->project, $tab);
		switch ($tab) {
			case "overview":
				$this->render_overview();
				break;
			case "details":
				$this->details->render($this->project);
				break;
			case "members":
				$this->members->render($this->project);
				break;
			case "planning":
				$this->planning->render($this->project);
				break;
			case "requirements":
				$this->requirements->render($this->project);
				break;
			case "features":
				$this->features->render($this->project);
				break;
			case "user-stories":
				$this->stories->render($this->project);
				break;
			case "backlog":
				$this->backlog->render($this->project);
				break;
			case "governance":
				$this->governance->render($this->project);
				break;
			case "quality":
				$this->quality->render($this->project);
				break;
			case "delivery":
				$this->delivery->render($this->project);
				break;
			case "people-communication":
				$this->people_communication->render($this->project);
				break;
			case "reporting":
				$this->reporting->render($this->project);
				break;
			case "composition":
				$this->composition->render($this->project);
				break;
			case "tasks":
				$this->tasks->render($this->project);
				break;
			case "milestones":
				$this->milestones->render($this->project);
				break;
			case "documents":
				$this->collaboration->render_documents($this->project);
				break;
			case "files":
				$this->collaboration->render_files($this->project);
				break;
			case "notes":
			case "discussions":
			case "activity":
			case "settings":
				$this->content->render($this->project, $tab);
				break;
			default:
				$this->render_extension_tab($tab);
				break;
		}
		do_action("projectpress_project_workspace_after_tab", $this->project, $tab);
	}

	/**
	 * Render an extension-provided workspace tab.
	 *
	 * @param string $tab Extension tab identifier.
	 * @return void
	 */
	private function render_extension_tab(string $tab): void {
		$definition = ProjectExtensionRegistry::get_instance()->workspace_tabs()[$tab] ?? null;
		if (!$definition) {
			return;
		}
		$allowed =
			!isset($definition["permission_callback"]) ||
			null === $definition["permission_callback"] ||
			call_user_func($definition["permission_callback"], $this->project);
		if (!$allowed) {
			wp_die(esc_html__("You do not have permission to view this project section.", "projectpress"));
		}
		call_user_func($definition["render_callback"], $this->project);
	}

	/**
	 * Render the project overview tab.
	 *
	 * @return void
	 */
	private function render_overview(): void {
		$id = $this->project->get_id();
		$owner = $this->project->get_owner_id() ? get_user_by("id", $this->project->get_owner_id()) : false;
		$teams = $this->teams->for_project($id);
		$activity = $this->activity->for_project($id, 5);
		?>
		<div class="row g-3 mb-4">
			<?php
			$this->stat(__("Status", "projectpress"), $this->humanise($this->project->get_status()));
			$this->stat(__("Priority", "projectpress"), $this->humanise($this->project->get_priority()));
			$this->stat(__("Visibility", "projectpress"), $this->humanise($this->project->get_visibility()));
			$this->stat(__("Teams", "projectpress"), (string) count($teams));
			?>
		</div>

		<div class="row g-4">
			<div class="col-xl-7">
				<div class="card card-body">
					<h2 class="h4">
						<?php esc_html_e("Project summary", "projectpress"); ?>
					</h2>
					<p>
						<strong><?php esc_html_e("Owner:", "projectpress"); ?></strong>
						<?php echo esc_html($owner ? $owner->display_name : __("Not assigned", "projectpress")); ?>
					</p>
					<p>
						<strong><?php esc_html_e("Start:", "projectpress"); ?></strong>
						<?php echo esc_html($this->format_date($this->project->get_start_date())); ?>
					</p>
					<p class="mb-0">
						<strong><?php esc_html_e("Target:", "projectpress"); ?></strong>
						<?php echo esc_html($this->format_date($this->project->get_target_end_date())); ?>
					</p>
				</div>
			</div>

			<div class="col-xl-5">
				<div class="card card-body">
					<h2 class="h4">
						<?php esc_html_e("Recent activity", "projectpress"); ?>
					</h2>
					<?php if (empty($activity)): ?>
						<p class="text-secondary mb-0">
							<?php esc_html_e("No activity yet.", "projectpress"); ?>
						</p>
					<?php endif; ?>
					<?php foreach ($activity as $item): ?>
						<div class="border-bottom py-2">
							<?php
							echo esc_html(
								(string) ($item["activity_summary"] ?: $this->humanise((string) $item["action"])),
							);
							?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render an overview statistic card.
	 *
	 * @param string $label Statistic label.
	 * @param string $value Statistic value.
	 * @return void
	 */
	private function stat(string $label, string $value): void {
		?>
		<div class="col-sm-6 col-xl-3">
			<div class="card card-body">
				<span class="small text-secondary"><?php echo esc_html($label); ?></span>
				<strong><?php echo esc_html($value); ?></strong>
			</div>
		</div>
		<?php
	}

	/**
	 * Build a project workspace URL.
	 *
	 * @param string $tab Workspace tab.
	 * @return string
	 */
	private function workspace_url(string $tab): string {
		return add_query_arg(
			[
				"page" => "projectpress",
				"group" => "projects",
				"project_id" => $this->project->get_id(),
				"tab" => $tab,
			],
			admin_url("admin.php"),
		);
	}

	/**
	 * Convert a machine value to a human-readable label.
	 *
	 * @param string $v Machine value.
	 * @return string
	 */
	private function humanise(string $v): string {
		return ucwords(str_replace("_", " ", $v));
	}

	/**
	 * Format an optional project date.
	 *
	 * @param DateTimeInterface|null $d Date value.
	 * @return string
	 */
	private function format_date(?DateTimeInterface $d): string {
		return $d ? $d->format("j M Y") : __("Not set", "projectpress");
	}
}
