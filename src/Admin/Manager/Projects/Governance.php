<?php
/**
 * Project governance workspace controller.
 *
 * @package ProjectPress
 * @subpackage Admin/Manager/Projects
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Governance\GovernanceRepository;
use ProjectPress\Includes\Project\Governance\GovernanceService;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;
use WP_Error;

if (!defined("ABSPATH")) {
	exit();
}

/**
 * Manages project risks, issues, dependencies and decisions.
 */
final class Governance {
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
	 * Supported governance record types.
	 *
	 * @var array<string, string>
	 */
	private const TYPES = [
		"risk" => "Risks",
		"issue" => "Issues",
		"dependency" => "Dependencies",
		"decision" => "Decisions",
	];

	/**
	 * Creates the governance workspace controller.
	 *
	 * @param DomainProjectManager|null $projects   Optional domain project manager.
	 * @param ProjectPermissions|null   $permissions Optional project permission service.
	 */
	public function __construct(?DomainProjectManager $projects = null, ?ProjectPermissions $permissions = null) {
		$this->projects = $projects ?? DomainProjectManager::get_instance();
		$this->permissions = $permissions ?? new ProjectPermissions();
	}

	/**
	 * Renders all project governance record types.
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
				$repository = new GovernanceRepository($type);
				$items = $repository->for_project($project->get_id());
				?>
				<div class="col-xl-6">
					<div class="card card-body h-100">
						<h2 class="h4">
							<?php echo esc_html(__($label, "projectpress")); ?>
						</h2>

						<?php if (empty($items)): ?>
							<p class="text-secondary">
								<?php esc_html_e("No records have been added yet.", "projectpress"); ?>
							</p>
						<?php endif; ?>

						<?php foreach ($items as $item): ?>
							<div class="border rounded p-2 mb-2">
								<strong>
									<?php echo esc_html((string) $item->get("title", "")); ?>
								</strong>

								<div class="small text-secondary">
									<?php echo esc_html(ucwords(str_replace("_", " ", (string) $item->get("status", "open")))); ?>
								</div>

								<?php if ($can_edit): ?>
									<form
										class="mt-2"
										method="post"
										action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
									>
										<input type="hidden" name="action" value="projectpress_workspace_governance_delete"/>
										<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
										<input type="hidden" name="type" value="<?php echo esc_attr($type); ?>"/>
										<input type="hidden" name="record_id" value="<?php echo esc_attr((string) $item->get_id()); ?>"/>
										<?php wp_nonce_field("projectpress_workspace_governance_delete"); ?>

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
								<input type="hidden" name="action" value="projectpress_workspace_governance_save"/>
								<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
								<input type="hidden" name="type" value="<?php echo esc_attr($type); ?>"/>
								<?php wp_nonce_field("projectpress_workspace_governance_save"); ?>

								<label class="form-label" for="governance-<?php echo esc_attr($type); ?>-title">
									<?php esc_html_e("Title", "projectpress"); ?>
								</label>
								<input
									class="form-control"
									id="governance-<?php echo esc_attr($type); ?>-title"
									name="title"
									required
								/>

								<label class="form-label mt-2" for="governance-<?php echo esc_attr($type); ?>-description">
									<?php esc_html_e("Description", "projectpress"); ?>
								</label>
								<textarea
									class="form-control"
									id="governance-<?php echo esc_attr($type); ?>-description"
									name="description"
								></textarea>

								<label class="form-label mt-2" for="governance-<?php echo esc_attr($type); ?>-status">
									<?php esc_html_e("Status", "projectpress"); ?>
								</label>
								<input
									class="form-control"
									id="governance-<?php echo esc_attr($type); ?>-status"
									name="status"
									value="open"
								/>

								<label class="form-label mt-2" for="governance-<?php echo esc_attr($type); ?>-target-type">
									<?php esc_html_e("Related record type", "projectpress"); ?>
								</label>
								<input
									class="form-control"
									id="governance-<?php echo esc_attr($type); ?>-target-type"
									name="target_type"
								/>

								<label class="form-label mt-2" for="governance-<?php echo esc_attr($type); ?>-target-id">
									<?php esc_html_e("Related record ID", "projectpress"); ?>
								</label>
								<input
									class="form-control"
									id="governance-<?php echo esc_attr($type); ?>-target-id"
									type="number"
									min="0"
									name="target_id"
								/>

								<?php if ("risk" === $type): ?>
									<label class="form-label mt-2" for="governance-risk-probability">
										<?php esc_html_e("Probability", "projectpress"); ?>
									</label>
									<input
										class="form-control"
										id="governance-risk-probability"
										name="probability"
									/>

									<label class="form-label mt-2" for="governance-risk-impact">
										<?php esc_html_e("Impact", "projectpress"); ?>
									</label>
									<input
										class="form-control"
										id="governance-risk-impact"
										name="impact"
									/>
								<?php endif; ?>

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
	 * Handles governance record creation requests.
	 *
	 * @return void
	 */
	public function handle_save(): void {
		check_admin_referer("projectpress_workspace_governance_save");
		$project_id = RequestHelper::integer($_POST, "project_id", 0);
		$type = RequestHelper::key($_POST, "type", "");

		if (!isset(self::TYPES[$type])) {
			$this->redirect(
				$project_id,
				new WP_Error(
					"projectpress_governance_type_invalid",
					__("Invalid governance type.", "projectpress"),
				),
			);
		}

		$repository = new GovernanceRepository($type);
		$service = new GovernanceService(
			$type,
			$repository,
			$this->projects->repository(),
			$this->permissions,
		);
		$result = $service->create($project_id, [
			"title" => RequestHelper::text($_POST, "title", ""),
			"description" => RequestHelper::text($_POST, "description", ""),
			"status" => RequestHelper::key($_POST, "status", "open"),
			"probability" => RequestHelper::key($_POST, "probability", ""),
			"impact" => RequestHelper::key($_POST, "impact", ""),
			"target_type" => RequestHelper::key($_POST, "target_type", ""),
			"target_id" => RequestHelper::integer($_POST, "target_id", 0),
		]);

		$this->redirect($project_id, $result);
	}

	/**
	 * Handles governance record deletion requests.
	 *
	 * @return void
	 */
	public function handle_delete(): void {
		check_admin_referer("projectpress_workspace_governance_delete");
		$project_id = RequestHelper::integer($_POST, "project_id", 0);
		$type = RequestHelper::key($_POST, "type", "");

		if (!isset(self::TYPES[$type])) {
			$this->redirect(
				$project_id,
				new WP_Error(
					"projectpress_governance_type_invalid",
					__("Invalid governance type.", "projectpress"),
				),
			);
		}

		$repository = new GovernanceRepository($type);
		$record = $repository->find(RequestHelper::integer($_POST, "record_id", 0));

		if (!$record || $record->get_project_id() !== $project_id) {
			$this->redirect(
				$project_id,
				new WP_Error(
					"projectpress_governance_not_found",
					__("Governance record not found.", "projectpress"),
				),
			);
		}

		$service = new GovernanceService(
			$type,
			$repository,
			$this->projects->repository(),
			$this->permissions,
		);
		$this->redirect($project_id, $service->delete($record));
	}

	/**
	 * Redirects to the project governance tab after a request.
	 *
	 * @param int   $project_id Project ID.
	 * @param mixed $result     Service result or WordPress error.
	 * @return void
	 */
	private function redirect(int $project_id, $result): void {
		$url = add_query_arg(
			[
				"page" => "projectpress",
				"group" => "projects",
				"project_id" => absint($project_id),
				"tab" => "governance",
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
