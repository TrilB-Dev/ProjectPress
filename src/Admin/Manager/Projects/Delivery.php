<?php
/**
 * Project delivery workspace controller.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\Projects
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Delivery\DeliveryRepository;
use ProjectPress\Includes\Project\Delivery\DeliveryService;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;
use WP_Error;

if (!defined("ABSPATH")) {
	exit();
}

/**
 * Renders and handles project delivery records.
 */
final class Delivery {
	/**
	 * Domain project manager.
	 *
	 * @var DomainProjectManager
	 */
	private DomainProjectManager $projects;

	/**
	 * Project permissions service.
	 *
	 * @var ProjectPermissions
	 */
	private ProjectPermissions $permissions;

	/**
	 * Supported delivery record types and labels.
	 *
	 * @var array<string, string>
	 */
	private const TYPES = [
		"sprint" => "Sprints",
		"release" => "Releases",
		"deployment" => "Deployments",
		"environment" => "Environments",
	];

	/**
	 * Constructor.
	 *
	 * @param DomainProjectManager|null $projects   Domain project manager.
	 * @param ProjectPermissions|null   $permissions Project permissions service.
	 */
	public function __construct(?DomainProjectManager $projects = null, ?ProjectPermissions $permissions = null) {
		$this->projects = $projects ?? DomainProjectManager::get_instance();
		$this->permissions = $permissions ?? new ProjectPermissions();
	}

	/**
	 * Render delivery records for a project.
	 *
	 * @param Project $project Project to render.
	 * @return void
	 */
	public function render(Project $project): void {
		$can = $this->permissions->can_edit($project);
		?>
		<div class="row g-4">
			<?php foreach (self::TYPES as $type => $label): ?>
				<?php
				$repo = new DeliveryRepository($type);
				$items = $repo->for_project($project->get_id());
				?>
				<div class="col-xl-6">
					<div class="card card-body h-100">
						<h2 class="h4">
							<?php echo esc_html(__($label, "projectpress")); ?>
						</h2>

						<?php if (empty($items)): ?>
							<p class="text-secondary">
								<?php esc_html_e("No delivery records have been added yet.", "projectpress"); ?>
							</p>
						<?php endif; ?>

						<?php foreach ($items as $item): ?>
							<div class="border rounded p-2 mb-2">
								<strong>
									<?php echo esc_html((string) $item->get("name", "")); ?>
								</strong>
								<div class="small text-secondary">
									<?php echo esc_html(ucwords(str_replace("_", " ", (string) $item->get("status", "planned")))); ?>
								</div>

								<?php if ($can): ?>
									<form
										class="mt-2"
										method="post"
										action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
									>
										<input type="hidden" name="action" value="projectpress_workspace_delivery_delete"/>
										<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
										<input type="hidden" name="type" value="<?php echo esc_attr($type); ?>"/>
										<input type="hidden" name="record_id" value="<?php echo esc_attr((string) $item->get_id()); ?>"/>
										<?php wp_nonce_field("projectpress_workspace_delivery_delete"); ?>

										<button class="btn btn-sm btn-outline-danger" type="submit">
											<?php esc_html_e("Delete", "projectpress"); ?>
										</button>
									</form>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>

						<?php if ($can): ?>
							<form
								class="mt-3"
								method="post"
								action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
							>
								<input type="hidden" name="action" value="projectpress_workspace_delivery_save"/>
								<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
								<input type="hidden" name="type" value="<?php echo esc_attr($type); ?>"/>
								<?php wp_nonce_field("projectpress_workspace_delivery_save"); ?>

								<label class="form-label" for="delivery-name-<?php echo esc_attr($type); ?>">
									<?php esc_html_e("Name", "projectpress"); ?>
								</label>
								<input
									class="form-control"
									id="delivery-name-<?php echo esc_attr($type); ?>"
									name="name"
									required
								/>

								<label class="form-label mt-2" for="delivery-description-<?php echo esc_attr($type); ?>">
									<?php esc_html_e("Description", "projectpress"); ?>
								</label>
								<textarea
									class="form-control"
									id="delivery-description-<?php echo esc_attr($type); ?>"
									name="description"
								></textarea>

								<label class="form-label mt-2" for="delivery-status-<?php echo esc_attr($type); ?>">
									<?php esc_html_e("Status", "projectpress"); ?>
								</label>
								<input
									class="form-control"
									id="delivery-status-<?php echo esc_attr($type); ?>"
									name="status"
									value="planned"
								/>

								<?php if ("release" === $type): ?>
									<label class="form-label mt-2" for="delivery-version-<?php echo esc_attr($type); ?>">
										<?php esc_html_e("Version", "projectpress"); ?>
									</label>
									<input
										class="form-control"
										id="delivery-version-<?php echo esc_attr($type); ?>"
										name="version"
									/>
								<?php endif; ?>

								<?php if ("sprint" === $type): ?>
									<label class="form-label mt-2" for="delivery-start-<?php echo esc_attr($type); ?>">
										<?php esc_html_e("Start date", "projectpress"); ?>
									</label>
									<input
										class="form-control"
										id="delivery-start-<?php echo esc_attr($type); ?>"
										type="date"
										name="start_date"
									/>

									<label class="form-label mt-2" for="delivery-end-<?php echo esc_attr($type); ?>">
										<?php esc_html_e("End date", "projectpress"); ?>
									</label>
									<input
										class="form-control"
										id="delivery-end-<?php echo esc_attr($type); ?>"
										type="date"
										name="end_date"
									/>
								<?php endif; ?>

								<?php if ("deployment" === $type): ?>
									<label class="form-label mt-2" for="delivery-environment-<?php echo esc_attr($type); ?>">
										<?php esc_html_e("Environment ID", "projectpress"); ?>
									</label>
									<input
										class="form-control"
										id="delivery-environment-<?php echo esc_attr($type); ?>"
										type="number"
										min="0"
										name="environment_id"
									/>
								<?php endif; ?>

								<label class="form-label mt-2" for="delivery-target-type-<?php echo esc_attr($type); ?>">
									<?php esc_html_e("Related record type", "projectpress"); ?>
								</label>
								<input
									class="form-control"
									id="delivery-target-type-<?php echo esc_attr($type); ?>"
									name="target_type"
								/>

								<label class="form-label mt-2" for="delivery-target-id-<?php echo esc_attr($type); ?>">
									<?php esc_html_e("Related record ID", "projectpress"); ?>
								</label>
								<input
									class="form-control"
									id="delivery-target-id-<?php echo esc_attr($type); ?>"
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
	 * Handle delivery record creation.
	 *
	 * @return void
	 */
	public function handle_save(): void {
		check_admin_referer("projectpress_workspace_delivery_save");
		$id = RequestHelper::integer($_POST, "project_id", 0);
		$type = RequestHelper::key($_POST, "type", "");

		if (!isset(self::TYPES[$type])) {
			$this->redirect(
				$id,
				new WP_Error("projectpress_delivery_type_invalid", __("Invalid delivery type.", "projectpress")),
			);
		}

		$repo = new DeliveryRepository($type);
		$service = new DeliveryService($type, $repo, $this->projects->repository(), $this->permissions);
		$this->redirect(
			$id,
			$service->create($id, [
				"name" => RequestHelper::text($_POST, "name", ""),
				"description" => RequestHelper::text($_POST, "description", ""),
				"status" => RequestHelper::key($_POST, "status", "planned"),
				"version" => RequestHelper::text($_POST, "version", ""),
				"start_date" => RequestHelper::text($_POST, "start_date", ""),
				"end_date" => RequestHelper::text($_POST, "end_date", ""),
				"environment_id" => RequestHelper::integer($_POST, "environment_id", 0),
				"target_type" => RequestHelper::key($_POST, "target_type", ""),
				"target_id" => RequestHelper::integer($_POST, "target_id", 0),
			]),
		);
	}

	/**
	 * Handle delivery record deletion.
	 *
	 * @return void
	 */
	public function handle_delete(): void {
		check_admin_referer("projectpress_workspace_delivery_delete");
		$id = RequestHelper::integer($_POST, "project_id", 0);
		$type = RequestHelper::key($_POST, "type", "");

		if (!isset(self::TYPES[$type])) {
			$this->redirect(
				$id,
				new WP_Error("projectpress_delivery_type_invalid", __("Invalid delivery type.", "projectpress")),
			);
		}

		$repo = new DeliveryRepository($type);
		$record = $repo->find(RequestHelper::integer($_POST, "record_id", 0));

		if (!$record || $record->get_project_id() !== $id) {
			$this->redirect(
				$id,
				new WP_Error("projectpress_delivery_not_found", __("Delivery record not found.", "projectpress")),
			);
		}

		$this->redirect(
			$id,
			new DeliveryService($type, $repo, $this->projects->repository(), $this->permissions)->delete($record),
		);
	}

	/**
	 * Redirect back to the delivery workspace tab.
	 *
	 * @param int   $id     Project ID.
	 * @param mixed $result Service or validation result.
	 * @return void
	 */
	private function redirect(int $id, $result): void {
		$url = add_query_arg(
			[
				"page" => "projectpress",
				"group" => "projects",
				"project_id" => absint($id),
				"tab" => "delivery",
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
