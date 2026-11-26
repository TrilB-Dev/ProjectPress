<?php
/**
 * Project quality workspace controller.
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
use ProjectPress\Includes\Project\Quality\QualityRepository;
use ProjectPress\Includes\Project\Quality\QualityService;
use WP_Error;

if (!defined("ABSPATH")) {
	exit();
}

/**
 * Manages project bugs and test cases.
 */
final class Quality {
	/**
	 * Domain project manager.
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
	 * Supported quality record types.
	 *
	 * @var array<string, string>
	 */
	private const TYPES = [
		"bug" => "Bugs",
		"test-case" => "Test Cases",
	];

	/**
	 * Creates the quality workspace controller.
	 *
	 * @param DomainProjectManager|null $projects   Optional domain project manager.
	 * @param ProjectPermissions|null   $permissions Optional project permission service.
	 */
	public function __construct(?DomainProjectManager $projects = null, ?ProjectPermissions $permissions = null) {
		$this->projects = $projects ?? DomainProjectManager::get_instance();
		$this->permissions = $permissions ?? new ProjectPermissions();
	}

	/**
	 * Renders all project quality record types.
	 *
	 * @param Project $project Project being displayed.
	 * @return void
	 */
	public function render(Project $project): void {
		$can_edit = $this->permissions->can_edit($project);
		?>
		<div class="row g-4">
			<?php foreach (self::TYPES as $type => $label): ?>
				<?php
				$repository = new QualityRepository($type);
				$items = $repository->for_project($project->get_id());
				?>
				<div class="col-xl-6">
					<div class="card card-body h-100">
						<h2 class="h4">
							<?php echo esc_html(__($label, "projectpress")); ?>
						</h2>

						<?php if (empty($items)): ?>
							<p class="text-secondary">
								<?php esc_html_e("No quality records have been added yet.", "projectpress"); ?>
							</p>
						<?php endif; ?>

						<?php foreach ($items as $item): ?>
							<div class="border rounded p-2 mb-2">
								<strong>
									<?php echo esc_html($item->get_title()); ?>
								</strong>

								<div class="small text-secondary">
									<?php echo esc_html(ucwords(str_replace("_", " ", $item->get_status()))); ?>
								</div>

								<?php if ($can_edit): ?>
									<form
										class="mt-2"
										method="post"
										action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
									>
										<input type="hidden" name="action" value="projectpress_workspace_quality_delete"/>
										<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
										<input type="hidden" name="type" value="<?php echo esc_attr($type); ?>"/>
										<input type="hidden" name="record_id" value="<?php echo esc_attr((string) $item->get_id()); ?>"/>
										<?php wp_nonce_field("projectpress_workspace_quality_delete"); ?>

										<button class="btn btn-sm btn-outline-danger" type="submit">
											<?php esc_html_e("Delete", "projectpress"); ?>
										</button>
									</form>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>

						<?php if ($can_edit): ?>
							<form
								class="mt-3"
								method="post"
								action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
							>
								<input type="hidden" name="action" value="projectpress_workspace_quality_save"/>
								<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
								<input type="hidden" name="type" value="<?php echo esc_attr($type); ?>"/>
								<?php wp_nonce_field("projectpress_workspace_quality_save"); ?>

								<label class="form-label" for="quality-<?php echo esc_attr($type); ?>-title">
									<?php esc_html_e("Title", "projectpress"); ?>
								</label>
								<input
									class="form-control"
									id="quality-<?php echo esc_attr($type); ?>-title"
									name="title"
									required
								/>

								<label class="form-label mt-2" for="quality-<?php echo esc_attr($type); ?>-description">
									<?php esc_html_e("Description", "projectpress"); ?>
								</label>
								<textarea
									class="form-control"
									id="quality-<?php echo esc_attr($type); ?>-description"
									name="description"
								></textarea>

								<label class="form-label mt-2" for="quality-<?php echo esc_attr($type); ?>-status">
									<?php esc_html_e("Status", "projectpress"); ?>
								</label>
								<input
									class="form-control"
									id="quality-<?php echo esc_attr($type); ?>-status"
									name="status"
									value="<?php echo esc_attr("bug" === $type ? "open" : "pending"); ?>"
								/>

								<?php if ("bug" === $type): ?>
									<label class="form-label mt-2" for="quality-bug-severity">
										<?php esc_html_e("Severity", "projectpress"); ?>
									</label>
									<input
										class="form-control"
										id="quality-bug-severity"
										name="severity"
										value="normal"
									/>
								<?php endif; ?>

								<label class="form-label mt-2" for="quality-<?php echo esc_attr($type); ?>-target-type">
									<?php esc_html_e("Related record type", "projectpress"); ?>
								</label>
								<input
									class="form-control"
									id="quality-<?php echo esc_attr($type); ?>-target-type"
									name="target_type"
								/>

								<label class="form-label mt-2" for="quality-<?php echo esc_attr($type); ?>-target-id">
									<?php esc_html_e("Related record ID", "projectpress"); ?>
								</label>
								<input
									class="form-control"
									id="quality-<?php echo esc_attr($type); ?>-target-id"
									type="number"
									min="0"
									name="target_id"
								/>

								<button class="btn btn-primary mt-3" type="submit">
									<?php echo esc_html(sprintf(__("Add %s", "projectpress"), rtrim($label, "s"))); ?>
								</button>
							</form>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Handles quality record creation requests.
	 *
	 * @return void
	 */
	public function handle_save(): void {
		check_admin_referer("projectpress_workspace_quality_save");
		$id = RequestHelper::integer($_POST, "project_id", 0);
		$type = RequestHelper::key($_POST, "type", "");

		if (!isset(self::TYPES[$type])) {
			$this->redirect(
				$id,
				new WP_Error(
					"projectpress_quality_type_invalid",
					__("Invalid quality type.", "projectpress"),
				),
			);
		}

		$repository = new QualityRepository($type);
		$service = new QualityService(
			$type,
			$repository,
			$this->projects->repository(),
			$this->permissions,
		);
		$this->redirect(
			$id,
			$service->create($id, [
				"title" => RequestHelper::text($_POST, "title", ""),
				"description" => RequestHelper::text($_POST, "description", ""),
				"status" => RequestHelper::key($_POST, "status", ""),
				"severity" => RequestHelper::key($_POST, "severity", "normal"),
				"target_type" => RequestHelper::key($_POST, "target_type", ""),
				"target_id" => RequestHelper::integer($_POST, "target_id", 0),
			]),
		);
	}

	/**
	 * Handles quality record deletion requests.
	 *
	 * @return void
	 */
	public function handle_delete(): void {
		check_admin_referer("projectpress_workspace_quality_delete");
		$id = RequestHelper::integer($_POST, "project_id", 0);
		$type = RequestHelper::key($_POST, "type", "");

		if (!isset(self::TYPES[$type])) {
			$this->redirect(
				$id,
				new WP_Error(
					"projectpress_quality_type_invalid",
					__("Invalid quality type.", "projectpress"),
				),
			);
		}

		$repository = new QualityRepository($type);
		$record = $repository->find(RequestHelper::integer($_POST, "record_id", 0));

		if (!$record || $record->get_project_id() !== $id) {
			$this->redirect(
				$id,
				new WP_Error(
					"projectpress_quality_not_found",
					__("Quality record not found.", "projectpress"),
				),
			);
		}

		$service = new QualityService(
			$type,
			$repository,
			$this->projects->repository(),
			$this->permissions,
		);
		$this->redirect($id, $service->delete($record));
	}

	/**
	 * Redirects to the project quality tab after a request.
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
				"tab" => "quality",
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
