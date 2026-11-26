<?php
/**
 * Project user stories workspace controller.
 *
 * @package ProjectPress
 * @subpackage Admin/Manager/Projects
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;
use ProjectPress\Includes\Project\Work\UserStoryRepository;
use ProjectPress\Includes\Project\Work\UserStoryService;
use WP_Error;

if (!defined("ABSPATH")) {
	exit();
}

/**
 * Manages project user stories in the individual project workspace.
 */
final class UserStories {
	/**
	 * Project permission service.
	 *
	 * @var ProjectPermissions
	 */
	private ProjectPermissions $permissions;

	/**
	 * User story repository.
	 *
	 * @var UserStoryRepository
	 */
	private UserStoryRepository $repository;

	/**
	 * User story service.
	 *
	 * @var UserStoryService
	 */
	private UserStoryService $service;

	/**
	 * Creates the project user stories controller.
	 *
	 * @param DomainProjectManager|null $projects   Optional domain project manager.
	 * @param ProjectPermissions|null   $permissions Optional project permission service.
	 */
	public function __construct(?DomainProjectManager $projects = null, ?ProjectPermissions $permissions = null) {
		$projects = $projects ?? DomainProjectManager::get_instance();
		$this->permissions = $permissions ?? new ProjectPermissions();
		$this->repository = new UserStoryRepository();
		$this->service = new UserStoryService(
			$this->repository,
			$projects->repository(),
			$this->permissions,
		);
	}

	/**
	 * Renders project user stories and the create form.
	 *
	 * @param Project $project Project being displayed.
	 * @return void
	 */
	public function render(Project $project): void {
		$items = $this->repository->for_project($project->get_id());
		$can_edit = $this->permissions->can_edit($project);
		?>
		<div class="row g-4">
			<div class="col-xl-8">
				<div class="card card-body">
					<h2 class="h4">
						<?php esc_html_e("User Stories", "projectpress"); ?>
					</h2>

					<?php if (empty($items)): ?>
						<p class="text-secondary">
							<?php esc_html_e("No user stories have been created for this project.", "projectpress"); ?>
						</p>
					<?php endif; ?>

					<?php foreach ($items as $item): ?>
						<div class="border rounded p-3 mb-3">
							<strong>
								<?php echo esc_html($item->get_title()); ?>
							</strong>

							<span class="badge text-bg-light">
								<?php echo esc_html((string) $item->get_points()); ?> pts
							</span>

							<?php if ($can_edit): ?>
								<form
									class="mt-2"
									method="post"
									action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
								>
									<input type="hidden" name="action" value="projectpress_workspace_story_delete"/>
									<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
									<input type="hidden" name="story_id" value="<?php echo esc_attr((string) $item->get_id()); ?>"/>
									<?php wp_nonce_field("projectpress_workspace_story_delete"); ?>

									<button class="btn btn-sm btn-outline-danger" type="submit">
										<?php esc_html_e("Delete", "projectpress"); ?>
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
						<?php esc_html_e("Create User Story", "projectpress"); ?>
					</h2>

					<?php if ($can_edit): ?>
						<form
							method="post"
							action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
						>
							<input type="hidden" name="action" value="projectpress_workspace_story_save"/>
							<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
							<?php wp_nonce_field("projectpress_workspace_story_save"); ?>

							<label class="form-label" for="story-title">
								<?php esc_html_e("Title", "projectpress"); ?>
							</label>
							<input class="form-control" id="story-title" name="title" required/>

							<label class="form-label mt-3" for="story-description">
								<?php esc_html_e("Description", "projectpress"); ?>
							</label>
							<textarea
								class="form-control"
								id="story-description"
								name="description"
							></textarea>

							<label class="form-label mt-3" for="story-status">
								<?php esc_html_e("Status", "projectpress"); ?>
							</label>
							<select class="form-select" id="story-status" name="status">
								<?php foreach (UserStoryService::statuses() as $status): ?>
									<option value="<?php echo esc_attr($status); ?>">
										<?php echo esc_html(ucwords(str_replace("_", " ", $status))); ?>
									</option>
								<?php endforeach; ?>
							</select>

							<label class="form-label mt-3" for="story-points">
								<?php esc_html_e("Points", "projectpress"); ?>
							</label>
							<input
								class="form-control"
								type="number"
								min="0"
								id="story-points"
								name="points"
							/>

							<button class="btn btn-primary mt-3" type="submit">
								<?php esc_html_e("Create User Story", "projectpress"); ?>
							</button>
						</form>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handles user story creation requests.
	 *
	 * @return void
	 */
	public function handle_save(): void {
		check_admin_referer("projectpress_workspace_story_save");
		$id = RequestHelper::integer($_POST, "project_id", 0);
		$this->redirect(
			$id,
			$this->service->create($id, [
				"title" => RequestHelper::text($_POST, "title", ""),
				"description" => RequestHelper::text($_POST, "description", ""),
				"status" => RequestHelper::key($_POST, "status", "backlog"),
				"points" => RequestHelper::integer($_POST, "points", 0),
			]),
		);
	}

	/**
	 * Handles user story deletion requests.
	 *
	 * @return void
	 */
	public function handle_delete(): void {
		check_admin_referer("projectpress_workspace_story_delete");
		$id = RequestHelper::integer($_POST, "project_id", 0);
		$item = $this->repository->find(RequestHelper::integer($_POST, "story_id", 0));

		if (!$item || $item->get_project_id() !== $id) {
			$this->redirect(
				$id,
				new WP_Error("projectpress_story_not_found", __("User story not found.", "projectpress")),
			);
		}

		$this->redirect($id, $this->service->delete($item));
	}

	/**
	 * Redirects to the project user stories tab after a request.
	 *
	 * @param int   $id     Project ID.
	 * @param mixed $result Service result or WordPress error.
	 * @return void
	 */
	private function redirect(int $id, $result): void {
		$url = add_query_arg(
			[
				"page" => "projectpress",
				"group" => "projects",
				"project_id" => absint($id),
				"tab" => "user-stories",
			],
			admin_url("admin.php"),
		);
		$url = is_wp_error($result)
			? add_query_arg("project_error", rawurlencode($result->get_error_message()), $url)
			: add_query_arg("project_saved", "1", $url);

		wp_safe_redirect($url);
		exit();
	}
}
