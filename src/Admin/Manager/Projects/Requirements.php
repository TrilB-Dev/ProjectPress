<?php
/**
 * Project requirements workspace controller.
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
use ProjectPress\Includes\Project\Requirements\RequirementRepository;
use ProjectPress\Includes\Project\Requirements\RequirementService;
use WP_Error;

if (!defined("ABSPATH")) {
	exit();
}

final class Requirements {
	private ProjectPermissions $permissions;
	private RequirementRepository $repository;
	private RequirementService $service;

	public function __construct(?DomainProjectManager $projects = null, ?ProjectPermissions $permissions = null) {
		$projects = $projects ?? DomainProjectManager::get_instance();
		$this->permissions = $permissions ?? new ProjectPermissions();
		$this->repository = new RequirementRepository();
		$this->service = new RequirementService($this->repository, $projects->repository(), $this->permissions);
	}

	public function render(Project $project): void {
		$items = $this->repository->for_project($project->get_id());
		$can_edit = $this->permissions->can_edit($project);
		?>
		<div class="row g-4">
			<div class="col-xl-8">
				<div class="card card-body">
					<h2 class="h4">
						<?php esc_html_e("Requirements", "projectpress"); ?>
					</h2>

					<?php if (empty($items)): ?>
						<p class="text-secondary">
							<?php esc_html_e("No requirements have been created for this project.", "projectpress"); ?>
						</p>
					<?php endif; ?>

					<?php foreach ($items as $item): ?>
						<div class="border rounded p-3 mb-3">
							<strong>
								<?php echo esc_html($item->get_title()); ?>
							</strong>

							<div>
								<span class="badge text-bg-light">
									<?php echo esc_html($this->humanise($item->get_status())); ?>
								</span>
								<span class="badge text-bg-light">
									<?php echo esc_html($this->humanise($item->get_priority())); ?>
								</span>
							</div>

							<?php if ($can_edit): ?>
								<form
									class="mt-2"
									method="post"
									action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
								>
									<input type="hidden" name="action" value="projectpress_workspace_requirement_delete"/>
									<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
									<input type="hidden" name="requirement_id" value="<?php echo esc_attr((string) $item->get_id()); ?>"/>
									<?php wp_nonce_field("projectpress_workspace_requirement_delete"); ?>

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
						<?php esc_html_e("Create Requirement", "projectpress"); ?>
					</h2>

					<?php if ($can_edit): ?>
						<form
							method="post"
							action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
						>
							<input type="hidden" name="action" value="projectpress_workspace_requirement_save"/>
							<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
							<?php wp_nonce_field("projectpress_workspace_requirement_save"); ?>

							<label class="form-label" for="requirement-title">
								<?php esc_html_e("Title", "projectpress"); ?>
							</label>
							<input class="form-control" id="requirement-title" name="title" required/>

							<label class="form-label mt-3" for="requirement-description">
								<?php esc_html_e("Description", "projectpress"); ?>
							</label>
							<textarea
								class="form-control"
								id="requirement-description"
								name="description"
							></textarea>

							<label class="form-label mt-3" for="requirement-status">
								<?php esc_html_e("Status", "projectpress"); ?>
							</label>
							<select class="form-select" id="requirement-status" name="status">
								<?php foreach (RequirementService::statuses() as $status): ?>
									<option value="<?php echo esc_attr($status); ?>">
										<?php echo esc_html($this->humanise($status)); ?>
									</option>
								<?php endforeach; ?>
							</select>

							<label class="form-label mt-3" for="requirement-priority">
								<?php esc_html_e("Priority", "projectpress"); ?>
							</label>
							<select class="form-select" id="requirement-priority" name="priority">
								<?php foreach (RequirementService::priorities() as $priority): ?>
									<option value="<?php echo esc_attr($priority); ?>">
										<?php echo esc_html($this->humanise($priority)); ?>
									</option>
								<?php endforeach; ?>
							</select>

							<button class="btn btn-primary mt-3" type="submit">
								<?php esc_html_e("Create Requirement", "projectpress"); ?>
							</button>
						</form>
					<?php else: ?>
						<p class="text-secondary mb-0">
							<?php esc_html_e("You do not have permission to create requirements in this project.", "projectpress"); ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function handle_save(): void {
		check_admin_referer("projectpress_workspace_requirement_save");
		$id = RequestHelper::integer($_POST, "project_id", 0);
		$result = $this->service->create($id, [
			"title" => RequestHelper::text($_POST, "title", ""),
			"description" => RequestHelper::text($_POST, "description", ""),
			"status" => RequestHelper::key($_POST, "status", "draft"),
			"priority" => RequestHelper::key($_POST, "priority", "normal"),
		]);
		$this->redirect($id, $result);
	}

	public function handle_delete(): void {
		check_admin_referer("projectpress_workspace_requirement_delete");
		$id = RequestHelper::integer($_POST, "project_id", 0);
		$item = $this->repository->find(RequestHelper::integer($_POST, "requirement_id", 0));

		if (!$item || $item->get_project_id() !== $id) {
			$this->redirect(
				$id,
				new WP_Error("projectpress_requirement_not_found", __("Requirement not found.", "projectpress")),
			);
		}

		$this->redirect($id, $this->service->delete($item));
	}

	private function redirect(int $id, $result): void {
		$url = add_query_arg(
			[
				"page" => "projectpress",
				"group" => "projects",
				"project_id" => absint($id),
				"tab" => "requirements",
			],
			admin_url("admin.php"),
		);
		$url = is_wp_error($result)
			? add_query_arg("project_error", rawurlencode($result->get_error_message()), $url)
			: add_query_arg("project_saved", "1", $url);

		wp_safe_redirect($url);
		exit();
	}

	private function humanise(string $v): string {
		return ucwords(str_replace("_", " ", $v));
	}
}
