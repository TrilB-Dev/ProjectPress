<?php
/**
 * Project features workspace controller.
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
use ProjectPress\Includes\Project\Work\FeatureRepository;
use ProjectPress\Includes\Project\Work\FeatureService;
use WP_Error;

if (!defined("ABSPATH")) {
	exit();
}

/**
 * Manages project features in the individual project workspace.
 */
final class Features {
	/**
	 * Project permission service.
	 *
	 * @var ProjectPermissions
	 */
	private ProjectPermissions $permissions;

	/**
	 * Feature repository.
	 *
	 * @var FeatureRepository
	 */
	private FeatureRepository $repository;

	/**
	 * Feature service.
	 *
	 * @var FeatureService
	 */
	private FeatureService $service;

	/**
	 * Creates the project features controller.
	 *
	 * @param DomainProjectManager|null $projects   Optional domain project manager.
	 * @param ProjectPermissions|null   $permissions Optional project permission service.
	 */
	public function __construct(?DomainProjectManager $projects = null, ?ProjectPermissions $permissions = null) {
		$projects = $projects ?? DomainProjectManager::get_instance();
		$this->permissions = $permissions ?? new ProjectPermissions();
		$this->repository = new FeatureRepository();
		$this->service = new FeatureService(
			$this->repository,
			$projects->repository(),
			$this->permissions,
		);
	}

	/**
	 * Renders project features and the create form.
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
						<?php esc_html_e("Features", "projectpress"); ?>
					</h2>

					<?php if (empty($items)): ?>
						<p class="text-secondary">
							<?php esc_html_e("No features have been created for this project.", "projectpress"); ?>
						</p>
					<?php endif; ?>

					<?php foreach ($items as $item): ?>
						<div class="border rounded p-3 mb-3">
							<strong>
								<?php echo esc_html($item->get_title()); ?>
							</strong>

							<span class="badge text-bg-light">
								<?php echo esc_html($this->humanise($item->get_status())); ?>
							</span>

							<?php if ($can_edit): ?>
								<form
									class="mt-2"
									method="post"
									action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
								>
									<input type="hidden" name="action" value="projectpress_workspace_feature_delete"/>
									<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
									<input type="hidden" name="feature_id" value="<?php echo esc_attr((string) $item->get_id()); ?>"/>
									<?php wp_nonce_field("projectpress_workspace_feature_delete"); ?>

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
						<?php esc_html_e("Create Feature", "projectpress"); ?>
					</h2>

					<?php if ($can_edit): ?>
						<form
							method="post"
							action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
						>
							<input type="hidden" name="action" value="projectpress_workspace_feature_save"/>
							<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
							<?php wp_nonce_field("projectpress_workspace_feature_save"); ?>

							<label class="form-label" for="feature-title">
								<?php esc_html_e("Title", "projectpress"); ?>
							</label>
							<input class="form-control" id="feature-title" name="title" required/>

							<label class="form-label mt-3" for="feature-description">
								<?php esc_html_e("Description", "projectpress"); ?>
							</label>
							<textarea
								class="form-control"
								id="feature-description"
								name="description"
							></textarea>

							<label class="form-label mt-3" for="feature-status">
								<?php esc_html_e("Status", "projectpress"); ?>
							</label>
							<select class="form-select" id="feature-status" name="status">
								<?php foreach (FeatureService::statuses() as $status): ?>
									<option value="<?php echo esc_attr($status); ?>">
										<?php echo esc_html($this->humanise($status)); ?>
									</option>
								<?php endforeach; ?>
							</select>

							<button class="btn btn-primary mt-3" type="submit">
								<?php esc_html_e("Create Feature", "projectpress"); ?>
							</button>
						</form>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handles feature creation requests.
	 *
	 * @return void
	 */
	public function handle_save(): void {
		check_admin_referer("projectpress_workspace_feature_save");
		$id = RequestHelper::integer($_POST, "project_id", 0);
		$this->redirect(
			$id,
			$this->service->create($id, [
				"title" => RequestHelper::text($_POST, "title", ""),
				"description" => RequestHelper::text($_POST, "description", ""),
				"status" => RequestHelper::key($_POST, "status", "planned"),
			]),
		);
	}

	/**
	 * Handles feature deletion requests.
	 *
	 * @return void
	 */
	public function handle_delete(): void {
		check_admin_referer("projectpress_workspace_feature_delete");
		$id = RequestHelper::integer($_POST, "project_id", 0);
		$item = $this->repository->find(RequestHelper::integer($_POST, "feature_id", 0));

		if (!$item || $item->get_project_id() !== $id) {
			$this->redirect(
				$id,
				new WP_Error("projectpress_feature_not_found", __("Feature not found.", "projectpress")),
			);
		}

		$this->redirect($id, $this->service->delete($item));
	}

	/**
	 * Redirects to the project features tab after a request.
	 *
	 * @param int        $id     Project ID.
	 * @param mixed      $result Service result or WordPress error.
	 * @return void
	 */
	private function redirect(int $id, $result): void {
		$url = add_query_arg(
			[
				"page" => "projectpress",
				"group" => "projects",
				"project_id" => absint($id),
				"tab" => "features",
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
	 * Converts a machine value into a readable label.
	 *
	 * @param string $value Machine value.
	 * @return string
	 */
	private function humanise(string $value): string {
		return ucwords(str_replace("_", " ", $value));
	}
}
