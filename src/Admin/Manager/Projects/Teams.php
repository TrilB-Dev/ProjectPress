<?php
/**
 * Project members workspace section.
 *
 * @package ProjectPress
 * @subpackage Admin/Manager/Projects
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Includes\Functions\Helpers\PermissionHelper;
use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\TeamRepository;
use ProjectPress\Includes\Project\Core\TeamService;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;
use WP_Error;

if (!defined("ABSPATH")) {
	exit();
}

/**
 * Renders project team assignments and handles team assignment actions.
 */
final class Teams {
	/**
	 * Project manager service.
	 *
	 * @var DomainProjectManager
	 */
	private DomainProjectManager $projects;

	/**
	 * Project permission service.
	 *
	 * @var ProjectPermissions
	 */
	private ProjectPermissions $permissions;

	/**
	 * Team repository.
	 *
	 * @var TeamRepository
	 */
	private TeamRepository $teams;

	/**
	 * Team service.
	 *
	 * @var TeamService
	 */
	private TeamService $service;

	/**
	 * Constructor.
	 *
	 * @param DomainProjectManager|null $projects Project manager service.
	 * @param ProjectPermissions|null $permissions Project permission service.
	 */
	public function __construct(?DomainProjectManager $projects = null, ?ProjectPermissions $permissions = null) {
		$this->projects = $projects ?? DomainProjectManager::get_instance();
		$this->permissions = $permissions ?? new ProjectPermissions();
		$this->teams = new TeamRepository();
		$this->service = new TeamService($this->teams, $this->projects->repository(), $this->permissions);
	}

	/**
	 * Render project team assignments.
	 *
	 * @param Project $project Project record.
	 * @return void
	 */
	public function render(Project $project): void {
		$assignments = $this->teams->for_project($project->get_id());
		$can_manage = $this->can_manage($project);
		$available_teams = $can_manage ? $this->teams->all() : [];
		?>
		<div class="row g-4">
			<div class="col-xl-8">
				<div class="card card-body">
					<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
						<h2 class="h4 mb-0">
							<?php esc_html_e("Project Members", "projectpress"); ?>
						</h2>
						<?php if (!$can_manage): ?>
							<span class="badge text-bg-secondary">
								<?php esc_html_e("Read only", "projectpress"); ?>
							</span>
						<?php endif; ?>
					</div>

					<?php if (empty($assignments)): ?>
						<p class="text-secondary mb-0">
							<?php esc_html_e("No teams are assigned to this project.", "projectpress"); ?>
						</p>
					<?php endif; ?>

					<?php foreach ($assignments as $assignment): ?>
						<?php
						$team_id = (int) $assignment["team_id"];
						$team = $this->teams->find($team_id);
						if (!$team) {
							continue;
						}
						$members = $this->teams->members($team_id);
						?>
						<div class="border rounded p-3 mb-3">
							<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
								<div>
									<strong><?php echo esc_html($team["team_name"]); ?></strong>
									<div class="small text-secondary">
										<?php echo esc_html($team["team_description"]); ?>
									</div>
								</div>
								<span class="badge text-bg-light">
									<?php echo esc_html($this->humanise((string) $assignment["team_role"])); ?>
								</span>
							</div>

							<?php if (empty($members)): ?>
								<div class="text-secondary small">
									<?php esc_html_e("This team has no members.", "projectpress"); ?>
								</div>
							<?php endif; ?>

							<?php foreach ($members as $member): ?>
								<?php
								$user = get_user_by("id", (int) $member["user_id"]);
								if (!$user) {
									continue;
								}
								?>
								<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 border-top py-2">
									<div>
										<strong><?php echo esc_html($user->display_name); ?></strong>
										<small class="d-block text-secondary">
											<?php echo esc_html($user->user_email); ?>
										</small>
									</div>
									<span class="badge text-bg-light">
										<?php echo esc_html($this->humanise((string) $member["member_role"])); ?>
									</span>
								</div>
							<?php endforeach; ?>

							<?php if ($can_manage): ?>
								<form
									class="mt-2"
									method="post"
									action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
								>
									<input type="hidden" name="action" value="projectpress_workspace_project_team_remove"/>
									<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
									<input type="hidden" name="team_id" value="<?php echo esc_attr((string) $team_id); ?>"/>
									<?php wp_nonce_field("projectpress_workspace_project_team_remove"); ?>

									<button class="btn btn-sm btn-outline-danger" type="submit">
										<?php esc_html_e("Remove Team", "projectpress"); ?>
									</button>
								</form>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="col-xl-4">
				<div class="card card-body">
					<h2 class="h4">
						<?php esc_html_e("Assign Team", "projectpress"); ?>
					</h2>

					<?php if (!$can_manage): ?>
						<p class="text-secondary mb-0">
							<?php esc_html_e("You can view project members but cannot change project team assignments.", "projectpress"); ?>
						</p>
					<?php elseif (empty($available_teams)): ?>
						<p class="text-secondary mb-0">
							<?php esc_html_e("No teams are available to assign.", "projectpress"); ?>
						</p>
					<?php else: ?>
						<form method="post" action="<?php echo esc_url(admin_url("admin-post.php")); ?>">
							<input type="hidden" name="action" value="projectpress_workspace_project_team_assign"/>
							<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
							<?php wp_nonce_field("projectpress_workspace_project_team_assign"); ?>

							<label class="form-label" for="project-member-team">
								<?php esc_html_e("Team", "projectpress"); ?>
							</label>
							<select class="form-select" id="project-member-team" name="team_id" required>
								<option value="">
									<?php esc_html_e("Select a team", "projectpress"); ?>
								</option>
								<?php foreach ($available_teams as $team): ?>
									<option value="<?php echo esc_attr((string) $team["id"]); ?>">
										<?php echo esc_html($team["team_name"]); ?>
									</option>
								<?php endforeach; ?>
							</select>

							<label class="form-label mt-3" for="project-member-role">
								<?php esc_html_e("Project role", "projectpress"); ?>
							</label>
							<select class="form-select" id="project-member-role" name="team_role">
								<option value="contributor"><?php esc_html_e("Contributor", "projectpress"); ?></option>
								<option value="manager"><?php esc_html_e("Manager", "projectpress"); ?></option>
								<option value="viewer"><?php esc_html_e("Viewer", "projectpress"); ?></option>
								<option value="owner"><?php esc_html_e("Owner", "projectpress"); ?></option>
							</select>

							<button class="btn btn-primary mt-3" type="submit">
								<?php esc_html_e("Assign Team", "projectpress"); ?>
							</button>
						</form>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle a team assignment request.
	 *
	 * @return void
	 */
	public function handle_assign(): void {
		check_admin_referer("projectpress_workspace_project_team_assign");
		$project_id = RequestHelper::integer($_POST, "project_id", 0);
		$team_id = RequestHelper::integer($_POST, "team_id", 0);
		$role = RequestHelper::key($_POST, "team_role", "contributor");
		$project = $this->projects->find($project_id);
		if (!$project || !$this->can_manage($project)) {
			$this->redirect(
				$project_id,
				new WP_Error(
					"projectpress_project_members_forbidden",
					__("You do not have permission to change project members.", "projectpress"),
				),
			);
		}
		$this->redirect($project_id, $this->service->assign_project($team_id, $project_id, $role));
	}

	/**
	 * Handle a team removal request.
	 *
	 * @return void
	 */
	public function handle_remove(): void {
		check_admin_referer("projectpress_workspace_project_team_remove");
		$project_id = RequestHelper::integer($_POST, "project_id", 0);
		$team_id = RequestHelper::integer($_POST, "team_id", 0);
		$project = $this->projects->find($project_id);
		if (!$project || !$this->can_manage($project)) {
			$this->redirect(
				$project_id,
				new WP_Error(
					"projectpress_project_members_forbidden",
					__("You do not have permission to change project members.", "projectpress"),
				),
			);
		}
		$this->redirect($project_id, $this->service->remove_project($team_id, $project_id));
	}

	/**
	 * Determine whether the current user can manage project teams.
	 *
	 * @param Project $project Project record.
	 * @return bool
	 */
	private function can_manage(Project $project): bool {
		return $this->permissions->can_edit($project) &&
			PermissionHelper::can_any(["projectpress_teams_manage", "manage_options"]);
	}

	/**
	 * Convert a machine value to a human-readable label.
	 *
	 * @param string $value Machine value.
	 * @return string
	 */
	private function humanise(string $value): string {
		return ucwords(str_replace("_", " ", $value));
	}

	/**
	 * Redirect to the members workspace tab.
	 *
	 * @param int $project_id Project identifier.
	 * @param mixed $result Operation result.
	 * @return void
	 */
	private function redirect(int $project_id, $result): void {
		$url = add_query_arg(
			[
				"page" => "projectpress",
				"group" => "projects",
				"project_id" => absint($project_id),
				"tab" => "members",
			],
			admin_url("admin.php"),
		);
		$url = is_wp_error($result)
			? add_query_arg("project_error", rawurlencode($result->get_error_message()), $url)
			: add_query_arg("project_saved", "1", $url);
		wp_safe_redirect($url);
		exit();
	}

    /**
     * Renders the teams management interface.
     *
     * @return void
     * @since 1.0.0
     */
    public function render_global(): void {
        $teams = $this->repository->all(); ?>
		<div class="row g-4">
			<div class="col-lg-4">
				<div class="card card-body">
					<h2 class="h4"><?php esc_html_e("Create Team", "projectpress"); ?></h2>

					<form method="post" action="<?php echo esc_url(admin_url("admin-post.php")); ?>">
						<input type="hidden" name="action" value="projectpress_team_save" />
						<?php wp_nonce_field("projectpress_team_save"); ?>

						<label class="form-label" for="team-name">
							<?php esc_html_e("Name", "projectpress"); ?>
						</label>
						<input class="form-control" id="team-name" name="name" required />

						<label class="form-label mt-3" for="team-description">
							<?php esc_html_e("Description", "projectpress"); ?>
						</label>
						<textarea class="form-control" id="team-description" name="description"></textarea>

						<button class="btn btn-primary mt-3" type="submit">
							<?php esc_html_e("Create Team", "projectpress"); ?>
						</button>
					</form>
				</div>
			</div>

			<div class="col-lg-8">
				<div class="card card-body">
					<h2 class="h4"><?php esc_html_e("Teams", "projectpress"); ?></h2>

					<?php if (empty($teams)): ?>
						<p><?php esc_html_e("No teams found.", "projectpress"); ?></p>
					<?php endif; ?>

					<?php foreach ($teams as $team): ?>
						<?php $team_id = (int) $team["id"]; ?>
						<div class="border-bottom py-3">
							<strong><?php echo esc_html($team["team_name"]); ?></strong>
							<div class="text-secondary mb-3">
								<?php echo esc_html($team["team_description"]); ?>
							</div>

							<div class="row g-3">
								<div class="col-md-6">
									<form method="post" action="<?php echo esc_url(admin_url("admin-post.php")); ?>">
										<input type="hidden" name="action" value="projectpress_team_member_save" />
										<input type="hidden" name="team_id" value="<?php echo esc_attr((string) $team_id); ?>" />
										<?php wp_nonce_field("projectpress_team_member_save"); ?>

										<label class="form-label"><?php esc_html_e("Add member by User ID", "projectpress"); ?></label>
										<input class="form-control" type="number" min="1" name="user_id" required />

										<select class="form-select mt-2" name="member_role">
											<option value="member">Member</option>
											<option value="manager">Manager</option>
											<option value="viewer">Viewer</option>
										</select>

										<button class="btn btn-outline-primary mt-2" type="submit">
											<?php esc_html_e("Add Member", "projectpress"); ?>
										</button>
									</form>
								</div>

								<div class="col-md-6">
									<form method="post" action="<?php echo esc_url(admin_url("admin-post.php")); ?>">
										<input type="hidden" name="action" value="projectpress_team_project_save" />
										<input type="hidden" name="team_id" value="<?php echo esc_attr((string) $team_id); ?>" />
										<?php wp_nonce_field("projectpress_team_project_save"); ?>

										<label class="form-label"><?php esc_html_e("Assign Project ID", "projectpress"); ?></label>
										<input class="form-control" type="number" min="1" name="project_id" required />

										<select class="form-select mt-2" name="team_role">
											<option value="contributor">Contributor</option>
											<option value="manager">Manager</option>
											<option value="viewer">Viewer</option>
											<option value="owner">Owner</option>
										</select>

										<button class="btn btn-outline-primary mt-2" type="submit">
											<?php esc_html_e("Assign Project", "projectpress"); ?>
										</button>
									</form>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
    }
    /**
     * Handles saving a team.
     *
     * @return void
     * @since 1.0.0
     */
    public function handle_global_save(): void {
        check_admin_referer("projectpress_team_save");
        $result = $this->service->create([
            "name" => RequestHelper::text($_POST, "name", ""),
            "description" => RequestHelper::text($_POST, "description", ""),
        ]);
        $this->redirect_global($result);
    }
    /**
     * Handles saving a team member.
     *
     * @return void
     * @since 1.0.0
     */
    public function handle_global_member_save(): void {
        check_admin_referer("projectpress_team_member_save");
        $result = $this->service->add_member(
            RequestHelper::integer($_POST, "team_id", 0),
            RequestHelper::integer($_POST, "user_id", 0),
            RequestHelper::key($_POST, "member_role", "member"),
        );
        $this->redirect_global($result);
    }

    /**
     * Handles assigning a project to a team.
     *
     * @return void
     * @since 1.0.0
     */
    public function handle_global_project_save(): void {
        check_admin_referer("projectpress_team_project_save");
        $result = $this->service->assign_project(
            RequestHelper::integer($_POST, "team_id", 0),
            RequestHelper::integer($_POST, "project_id", 0),
            RequestHelper::key($_POST, "team_role", "contributor"),
        );
        $this->redirect_global($result);
    }
    /**
     * Redirects after handling a team-related action.
     *
     * @param mixed $result The result of the action.
     * @return void
     * @since 1.0.0
     */
    private function redirect_global($result): void {
        $url = admin_url("admin.php?page=projectpress&group=project-manager&tab=teams");
        $url = is_wp_error($result)
            ? add_query_arg("team_error", rawurlencode($result->get_error_message()), $url)
            : add_query_arg("team_saved", "1", $url);

        wp_safe_redirect($url);
        exit();
    }

}
