<?php
/**
 * Project list, form and lifecycle administration.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\ProjectManager
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\ProjectManager;

use ProjectPress\Includes\Core\Defaults;
use ProjectPress\Includes\Functions\Helpers\FormFieldHelper;
use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Pages\Taxonomies;
use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Core\ProjectClassificationRepository;
use ProjectPress\Includes\Project\Core\ProjectFieldRepository;
use ProjectPress\Includes\Project\Core\ProjectFieldService;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;

if (!defined("ABSPATH")) {
    exit();
}

final class Projects {
    /**
     * Project manager instance.
     *
     * @var DomainProjectManager
     * @since 1.0.0
     */
    private DomainProjectManager $manager;

    /**
     * Project classification repository instance.
     *
     * @var ProjectClassificationRepository
     * @since 1.0.0
     */
    private ProjectClassificationRepository $classifications;

    /**
     * Project field repository instance.
     *
     * @var ProjectFieldRepository
     * @since 1.0.0
     */
    private ProjectFieldRepository $fields;

    /**
     * Project field service instance.
     *
     * @var ProjectFieldService
     * @since 1.0.0
     */
    private ProjectFieldService $field_service;
    /**
     * Constructor.
     *
     * @param DomainProjectManager $manager Project manager instance.
     * @param ProjectClassificationRepository $classifications Project classification repository instance.
     * @param ProjectFieldRepository $fields Project field repository instance.
     * @param ProjectFieldService $field_service Project field service instance.
     * @return void
     * @since 1.0.0
     */
    public function __construct(DomainProjectManager $manager) {
        $this->manager = $manager;
        $this->classifications = new ProjectClassificationRepository();
        $this->fields = new ProjectFieldRepository();
        $this->field_service = new ProjectFieldService($this->fields, $manager->repository(), new ProjectPermissions());
    }
    /**
     * Render the project list.
     *
     * @param array $args Query arguments for fetching projects.
     * @return void
     * @since 1.0.0
     */
    public function render(): void {
        $args = [
            "status" => RequestHelper::get_key("status", ""),
            "priority" => RequestHelper::get_key("priority", ""),
            "visibility" => RequestHelper::get_key("visibility", ""),
            "project_type" => RequestHelper::get_integer("project_type", 0),
            "project_category" => RequestHelper::get_integer("project_category", 0),
            "project_tag" => RequestHelper::get_integer("project_tag", 0),
            "project_label" => RequestHelper::get_integer("project_label", 0),
            "search" => RequestHelper::get_text("s", ""),
            "page" => max(1, RequestHelper::get_integer("paged", 1)),
            "per_page" => 20,
        ];

        $projects = $this->manager->query($args);
        $total = $this->manager->count($args);
        $pages = max(1, (int) ceil($total / $args["per_page"]));
        $new_url = admin_url("admin.php?page=projectpress&group=project-manager&tab=new");
        ?>
		<div class="d-flex justify-content-between align-items-center mb-3">
			<p class="text-secondary mb-0">
				<?php printf(
        esc_html(
            /* translators: %d: Number of projects found. */
            __n("%d project found.", "%d projects found.", $total, "projectpress"),
        ),
        $total,
    ); ?>
			</p>

			<?php if (current_user_can("manage_options") || current_user_can("projectpress_projects_create")): ?>
				<a class="btn btn-primary" href="<?php echo esc_url($new_url); ?>">
					<?php esc_html_e("New Project", "projectpress"); ?>
				</a>
			<?php endif; ?>
		</div>

		<form method="get" class="card card-body mb-3">
			<input type="hidden" name="page" value="projectpress" />
			<input type="hidden" name="group" value="project-manager" />
			<input type="hidden" name="tab" value="projects" />

			<div class="row g-2">
				<div class="col-md-4">
					<?php echo FormFieldHelper::text_input("s", $args["search"], [
         "placeholder" => __("Search projects", "projectpress"),
     ]); ?>
				</div>
				<div class="col-md-2">
					<?php echo FormFieldHelper::select(
         "status",
         $this->choices(Defaults::project_statuses(), __("All statuses", "projectpress")),
         $args["status"],
     ); ?>
				</div>
				<div class="col-md-2">
					<?php echo FormFieldHelper::select(
         "priority",
         $this->choices(Defaults::project_priorities(), __("All priorities", "projectpress")),
         $args["priority"],
     ); ?>
				</div>
				<div class="col-md-2">
					<?php echo FormFieldHelper::select(
         "visibility",
         $this->choices(Defaults::project_visibilities(), __("All visibility", "projectpress")),
         $args["visibility"],
     ); ?>
				</div>
				<div class="col-md-2">
					<button class="btn btn-outline-primary w-100" type="submit">
						<?php esc_html_e("Filter", "projectpress"); ?>
					</button>
				</div>

				<?php foreach ($this->filter_map() as $key => $definition): ?>
					<div class="col-md-3">
						<?php echo FormFieldHelper::select(
          $key,
          $this->term_choices($definition["taxonomy"], $definition["all"]),
          $args[$key],
      ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</form>

		<form method="post" action="<?php echo esc_url(admin_url("admin-post.php")); ?>">
			<input type="hidden" name="action" value="projectpress_projects_bulk" />
			<?php wp_nonce_field("projectpress_projects_bulk"); ?>

			<div class="d-flex gap-2 mb-2">
				<select class="form-select w-auto" name="bulk_action">
					<option value=""><?php esc_html_e("Bulk actions", "projectpress"); ?></option>
					<option value="archive"><?php esc_html_e("Archive", "projectpress"); ?></option>
					<option value="restore"><?php esc_html_e("Restore", "projectpress"); ?></option>
				</select>
				<button class="btn btn-outline-secondary" type="submit">
					<?php esc_html_e("Apply", "projectpress"); ?>
				</button>
			</div>

			<div class="table-responsive">
				<table class="table align-middle">
					<thead>
						<tr>
							<th><span class="visually-hidden"><?php esc_html_e("Select", "projectpress"); ?></span></th>
							<th><?php esc_html_e("Project", "projectpress"); ?></th>
							<th><?php esc_html_e("Key", "projectpress"); ?></th>
							<th><?php esc_html_e("Status", "projectpress"); ?></th>
							<th><?php esc_html_e("Priority", "projectpress"); ?></th>
							<th><?php esc_html_e("Visibility", "projectpress"); ?></th>
							<th><?php esc_html_e("Actions", "projectpress"); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($projects)): ?>
							<tr>
								<td colspan="7"><?php esc_html_e("No projects found.", "projectpress"); ?></td>
							</tr>
						<?php endif; ?>

						<?php foreach ($projects as $project): ?>
							<?php $edit_url = admin_url(
           "admin.php?page=projectpress&group=project-manager&tab=edit&project_id=" . $project->get_id(),
       ); ?>
							<tr>
								<td>
									<input
										class="form-check-input"
										type="checkbox"
										name="project_ids[]"
										value="<?php echo esc_attr((string) $project->get_id()); ?>"
									/>
								</td>
								<td>
									<a href="<?php echo esc_url($edit_url); ?>">
										<?php echo esc_html($project->get_name()); ?>
									</a>
								</td>
								<td><?php echo esc_html($project->get_key()); ?></td>
								<td><?php echo esc_html($project->get_status()); ?></td>
								<td><?php echo esc_html($project->get_priority()); ?></td>
								<td><?php echo esc_html($project->get_visibility()); ?></td>
								<td><?php $this->render_lifecycle_actions($project); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</form>

		<?php if ($pages > 1): ?>
			<?php $base_args = array_filter(
       [
           "page" => "projectpress",
           "group" => "project-manager",
           "tab" => "projects",
           "s" => $args["search"],
           "status" => $args["status"],
           "priority" => $args["priority"],
           "visibility" => $args["visibility"],
           "project_type" => $args["project_type"],
           "project_category" => $args["project_category"],
           "project_tag" => $args["project_tag"],
           "project_label" => $args["project_label"],
       ],
       static function ($value): bool {
           return "" !== $value && 0 !== $value;
       },
   ); ?>
			<nav>
				<ul class="pagination">
					<?php for ($page = 1; $page <= $pages; $page++): ?>
						<li class="page-item <?php echo $page === $args["page"] ? "active" : ""; ?>">
							<a
								class="page-link"
								href="<?php echo esc_url(add_query_arg(array_merge($base_args, ["paged" => $page]), admin_url("admin.php"))); ?>"
							>
								<?php echo esc_html((string) $page); ?>
							</a>
						</li>
					<?php endfor; ?>
				</ul>
			</nav>
		<?php endif; ?>
		<?php
    }
    /**
     * Renders the project form.
     *
     * @param int $project_id The ID of the project to edit, or 0 to create a new project.
     * @return void
     * @since 1.0.0
     */
    public function render_form(int $project_id = 0): void {
        $project = $project_id > 0 ? $this->manager->find($project_id) : null;

        if ($project_id > 0 && !$project) {
            echo '<div class="notice notice-error"><p>' .
                esc_html__("Project not found.", "projectpress") .
                "</p></div>";
            return;
        }

        $data = $project
            ? $project->to_array()
            : [
                "name" => "",
                "description" => "",
                "status" => Defaults::PROJECT_STATUS_DEFAULT,
                "priority" => Defaults::PROJECT_PRIORITY_DEFAULT,
                "visibility" => Defaults::PROJECT_VISIBILITY_DEFAULT,
            ];
        $field_values = $project ? $this->fields->values($project_id) : [];
        $global_fields = $this->fields->all(true);
        ?>
		<form
			class="card card-body"
			method="post"
			action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
		>
			<input type="hidden" name="action" value="projectpress_project_save" />
			<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project_id); ?>" />
			<?php wp_nonce_field("projectpress_project_save"); ?>

			<div class="mb-3">
				<?php echo FormFieldHelper::label("project-name", __("Name", "projectpress")); ?>
				<?php echo FormFieldHelper::text_input("name", (string) $data["name"], [
        "id" => "project-name",
        "required" => true,
    ]); ?>
			</div>

			<div class="mb-3">
				<?php echo FormFieldHelper::label("project-description", __("Description", "projectpress")); ?>
				<?php echo FormFieldHelper::textarea("description", (string) $data["description"], [
        "id" => "project-description",
        "rows" => 5,
    ]); ?>
			</div>

			<div class="row g-3">
				<div class="col-md-4">
					<?php echo FormFieldHelper::select(
         "status",
         array_combine(Defaults::project_statuses(), Defaults::project_statuses()),
         $data["status"],
     ); ?>
				</div>
				<div class="col-md-4">
					<?php echo FormFieldHelper::select(
         "priority",
         array_combine(Defaults::project_priorities(), Defaults::project_priorities()),
         $data["priority"],
     ); ?>
				</div>
				<div class="col-md-4">
					<?php echo FormFieldHelper::select(
         "visibility",
         array_combine(Defaults::project_visibilities(), Defaults::project_visibilities()),
         $data["visibility"],
     ); ?>
				</div>
			</div>

			<hr />

			<div class="row g-3">
				<?php foreach ($this->classification_map() as $field => $definition): ?>
					<?php
     $terms = $this->classifications->terms($definition["taxonomy"]);
     $selected = $project ? $this->classifications->assigned_term_ids($project_id, $definition["taxonomy"]) : [];
     ?>
					<div class="col-md-6">
						<label class="form-label"><?php echo esc_html($definition["label"]); ?></label>
						<select class="form-select" name="<?php echo esc_attr($field); ?>[]" multiple>
							<?php foreach ($terms as $term): ?>
								<option
									value="<?php echo esc_attr((string) $term->term_id); ?>"
									<?php selected(in_array((int) $term->term_id, $selected, true)); ?>
								>
									<?php echo esc_html($term->name); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ($global_fields): ?>
				<hr />
				<h2 class="h5"><?php esc_html_e("Global Fields", "projectpress"); ?></h2>
				<div class="row g-3">
					<?php foreach ($global_fields as $field): ?>
						<div class="col-md-6">
							<?php $this->render_global_field($field, $field_values[(int) $field["id"]] ?? null); ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<button class="btn btn-primary mt-3" type="submit">
				<?php echo esc_html($project ? __("Update Project", "projectpress") : __("Create Project", "projectpress")); ?>
			</button>
		</form>
		<?php
    }
    /**
     * Handles saving a project.
     *
     * @return void
     * @since 1.0.0
     */
    public function handle_save(): void {
        check_admin_referer("projectpress_project_save");

        $id = RequestHelper::integer($_POST, "project_id", 0);
        $input = [
            "name" => RequestHelper::text($_POST, "name", ""),
            "description" => RequestHelper::text($_POST, "description", ""),
            "status" => RequestHelper::key($_POST, "status", Defaults::PROJECT_STATUS_DEFAULT),
            "priority" => RequestHelper::key($_POST, "priority", Defaults::PROJECT_PRIORITY_DEFAULT),
            "visibility" => RequestHelper::key($_POST, "visibility", Defaults::PROJECT_VISIBILITY_DEFAULT),
        ];

        if ($id > 0) {
            $project = $this->manager->find($id);
            $result = $project
                ? $this->manager->service()->update($project, $input)
                : new \WP_Error("projectpress_project_not_found", __("Project not found.", "projectpress"));
        } else {
            $result = $this->manager->create($input);
        }

        if (!is_wp_error($result) && $result instanceof Project) {
            foreach ($this->classification_map() as $field => $definition) {
                $this->classifications->assign(
                    $result->get_id(),
                    $definition["taxonomy"],
                    array_map("absint", RequestHelper::array($_POST, $field, [])),
                );
            }

            $field_result = $this->field_service->save_project_values($result->get_id(), $this->project_field_input());
            if (is_wp_error($field_result)) {
                $result = $field_result;
            }
        }

        $this->redirect_result($result);
    }
    /**
     * Handles archiving a project.
     *
     * @return void
     * @since 1.0.0
     */
    public function handle_archive(): void {
        check_admin_referer("projectpress_project_archive");
        $this->handle_single_lifecycle("archive");
    }

    /**
     * Handles restoring a project.
     *
     * @return void
     * @since 1.0.0
     */
    public function handle_restore(): void {
        check_admin_referer("projectpress_project_restore");
        $this->handle_single_lifecycle("restore");
    }

    /**
     * Handles deleting a project.
     *
     * @return void
     * @since 1.0.0
     */
    public function handle_delete(): void {
        check_admin_referer("projectpress_project_delete");
        $this->handle_single_lifecycle("delete");
    }

    /**
     * Handles bulk actions on projects.
     *
     * @return void
     * @since 1.0.0
     */
    public function handle_bulk(): void {
        check_admin_referer("projectpress_projects_bulk");

        $action = RequestHelper::key($_POST, "bulk_action", "");
        if (!in_array($action, ["archive", "restore"], true)) {
            $this->redirect_result(
                new \WP_Error("projectpress_bulk_action_invalid", __("Select a valid bulk action.", "projectpress")),
            );
        }

        $ids = array_unique(array_filter(array_map("absint", RequestHelper::array($_POST, "project_ids", []))));

        if (empty($ids)) {
            $this->redirect_result(
                new \WP_Error("projectpress_bulk_empty", __("Select at least one project.", "projectpress")),
            );
        }

        $processed = 0;
        $skipped = 0;

        foreach ($ids as $id) {
            $project = $this->manager->find($id);
            if (!$project) {
                ++$skipped;
                continue;
            }

            $result =
                "archive" === $action
                    ? $this->manager->service()->archive($project)
                    : $this->manager->service()->restore($project);

            if (is_wp_error($result)) {
                ++$skipped;
                continue;
            }

            ++$processed;
        }

        $this->redirect_result(true, [
            "bulk_processed" => $processed,
            "bulk_skipped" => $skipped,
        ]);
    }

    /**
     * Handles a single lifecycle action on a project.
     *
     * @param string $action The lifecycle action to perform (archive, restore, delete).
     * @return void
     * @since 1.0.0
     */
    private function handle_single_lifecycle(string $action): void {
        $id = RequestHelper::integer($_POST, "project_id", 0);
        $project = $this->manager->find($id);

        if (!$project) {
            $this->redirect_result(
                new \WP_Error("projectpress_project_not_found", __("Project not found.", "projectpress")),
            );
        }

        $service = $this->manager->service();
        $result =
            "archive" === $action
                ? $service->archive($project)
                : ("restore" === $action
                    ? $service->restore($project)
                    : $service->delete($project));

        $this->redirect_result($result);
    }
    /**
     * Renders the lifecycle action buttons for a project.
     *
     * @param Project $project The project to render actions for.
     * @return void
     * @since 1.0.0
     */
    private function render_lifecycle_actions(Project $project): void {
        $archived = "archived" === $project->get_status(); ?>
		<div class="d-flex gap-1 flex-wrap">
			<?php if ($archived): ?>
				<form method="post" action="<?php echo esc_url(admin_url("admin-post.php")); ?>">
					<input type="hidden" name="action" value="projectpress_project_restore" />
					<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>" />
					<?php wp_nonce_field("projectpress_project_restore"); ?>
					<button class="btn btn-sm btn-outline-primary" type="submit">
						<?php esc_html_e("Restore", "projectpress"); ?>
					</button>
				</form>

				<?php if (current_user_can("projectpress_projects_delete") || current_user_can("manage_options")): ?>
					<form method="post" action="<?php echo esc_url(admin_url("admin-post.php")); ?>">
						<input type="hidden" name="action" value="projectpress_project_delete" />
						<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>" />
						<?php wp_nonce_field("projectpress_project_delete"); ?>
						<button class="btn btn-sm btn-outline-danger" type="submit">
							<?php esc_html_e("Delete Permanently", "projectpress"); ?>
						</button>
					</form>
				<?php endif; ?>
			<?php else: ?>
				<form method="post" action="<?php echo esc_url(admin_url("admin-post.php")); ?>">
					<input type="hidden" name="action" value="projectpress_project_archive" />
					<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>" />
					<?php wp_nonce_field("projectpress_project_archive"); ?>
					<button class="btn btn-sm btn-outline-secondary" type="submit">
						<?php esc_html_e("Archive", "projectpress"); ?>
					</button>
				</form>
			<?php endif; ?>
		</div>
		<?php
    }
    /**
     * Renders a global field for a project.
     *
     * @param array $field The field definition.
     * @param array|null $row The current row data.
     * @return void
     * @since 1.0.0
     */
    private function render_global_field(array $field, ?array $row): void {
        $id = (int) $field["id"];
        $name = "project_fields[" . $id . "]";
        $html_id = "project-field-" . $id;
        $type = (string) $field["field_type"];
        $value = $this->field_service->display_value($field, $row);
        $required = !empty($field["is_required"]);
        ?>
		<label class="form-label" for="<?php echo esc_attr($html_id); ?>">
			<?php echo esc_html($field["field_name"]); ?>
			<?php if ($required): ?>
				<span class="text-danger">*</span>
			<?php endif; ?>
		</label>

		<?php if ("textarea" === $type): ?>
			<textarea
				class="form-control"
				id="<?php echo esc_attr($html_id); ?>"
				name="<?php echo esc_attr($name); ?>"
				<?php echo $required ? "required" : ""; ?>
			><?php echo esc_textarea($value); ?></textarea>
		<?php elseif ("boolean" === $type): ?>
			<input type="hidden" name="<?php echo esc_attr($name); ?>" value="0" />
			<input
				class="form-check-input"
				type="checkbox"
				id="<?php echo esc_attr($html_id); ?>"
				name="<?php echo esc_attr($name); ?>"
				value="1"
				<?php checked("1", $value); ?>
			/>
		<?php else: ?>
			<?php
   $input_type = in_array($type, ["integer", "decimal", "user"], true)
       ? "number"
       : ("email" === $type
           ? "email"
           : ("url" === $type
               ? "url"
               : ("date" === $type
                   ? "date"
                   : ("datetime" === $type
                       ? "datetime-local"
                       : "text"))));
   $display =
       "datetime" === $type && $value
           ? gmdate("Y-m-d\TH:i", strtotime($value))
           : ("date" === $type && $value
               ? substr($value, 0, 10)
               : $value);
   ?>
			<input
				class="form-control"
				type="<?php echo esc_attr($input_type); ?>"
				id="<?php echo esc_attr($html_id); ?>"
				name="<?php echo esc_attr($name); ?>"
				value="<?php echo esc_attr($display); ?>"
				<?php echo $required ? "required" : ""; ?>
			/>
		<?php endif; ?>

		<?php if (!empty($field["field_description"])): ?>
			<div class="form-text"><?php echo esc_html($field["field_description"]); ?></div>
		<?php endif; ?>
		<?php
    }
    /**
     * Renders a project field input.
     *
     * @param array $field The field definition.
     * @param array|null $row The current row data.
     * @return void
     * @since 1.0.0
     */
    private function choices(array $values, string $all): array {
        $choices = ["" => $all];
        foreach ($values as $value) {
            $choices[$value] = ucwords(str_replace("_", " ", $value));
        }
        return $choices;
    }
    /**
     * Generates choices for a select input.
     *
     * @param array $values The available values.
     * @param string $all The label for the "all" option.
     * @return array The choices array.
     * @since 1.0.0
     */
    private function term_choices(string $taxonomy, string $all): array {
        $choices = [0 => $all];
        foreach ($this->classifications->terms($taxonomy) as $term) {
            $choices[(int) $term->term_id] = $term->name;
        }
        return $choices;
    }
    /**
     * Generates term choices for a taxonomy.
     *
     * @param string $taxonomy The taxonomy name.
     * @param string $all The label for the "all" option.
     * @return array The choices array.
     * @since 1.0.0
     */
    private function filter_map(): array {
        return [
            "project_type" => [
                "taxonomy" => "pp_project_type",
                "all" => __("All types", "projectpress"),
            ],
            "project_category" => [
                "taxonomy" => "pp_project_category",
                "all" => __("All categories", "projectpress"),
            ],
            "project_tag" => [
                "taxonomy" => "pp_project_tag",
                "all" => __("All tags", "projectpress"),
            ],
            "project_label" => [
                "taxonomy" => "pp_project_label",
                "all" => __("All labels", "projectpress"),
            ],
        ];
    }
    /**
     * Returns the mapping of filterable project attributes.
     *
     * @return array The filter map.
     * @since 1.0.0
     */
    private function project_field_input(): array {
        $raw = RequestHelper::array($_POST, "project_fields", []);
        $values = [];

        foreach ($raw as $id => $value) {
            $id = absint($id);
            if ($id > 0) {
                $values[$id] = is_array($value)
                    ? array_map("sanitize_text_field", $value)
                    : sanitize_text_field((string) $value);
            }
        }

        return $values;
    }
    /**
     * Returns the mapping of project classification taxonomies.
     *
     * @return array The classification map.
     * @since 1.0.0
     */
    private function classification_map(): array {
        return [
            "project_types" => [
                "taxonomy" => "pp_project_type",
                "label" => __("Types", "projectpress"),
            ],
            "project_categories" => [
                "taxonomy" => "pp_project_category",
                "label" => __("Categories", "projectpress"),
            ],
            "project_tags" => [
                "taxonomy" => "pp_project_tag",
                "label" => __("Tags", "projectpress"),
            ],
            "project_labels" => [
                "taxonomy" => "pp_project_label",
                "label" => __("Labels", "projectpress"),
            ],
        ];
    }
    /**
     * Redirects to the project result page with appropriate query parameters.
     *
     * @param WP_Error|mixed $result The result of the project operation.
     * @param array $args Additional query arguments.
     * @return void
     * @since 1.0.0
     */
    private function redirect_result($result, array $args = []): void {
        $url = admin_url("admin.php?page=projectpress&group=project-manager&tab=projects");

        if (is_wp_error($result)) {
            $url = add_query_arg("project_error", rawurlencode($result->get_error_message()), $url);
        } else {
            $url = add_query_arg(array_merge(["project_saved" => "1"], $args), $url);
        }

        wp_safe_redirect($url);
        exit();
    }
}
