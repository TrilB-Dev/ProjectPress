<?php
/**
 * Project workspace content, activity and settings controller.
 *
 * @package ProjectPress
 * @subpackage Admin/Manager/Projects
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\Collaboration\ContentRepository;
use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectSettingsRepository;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;
use ProjectPress\Includes\Project\Tracking\ActivityRepository;

if (!defined("ABSPATH")) {
	exit();
}

/**
 * Renders and handles project posts, activity and settings.
 */
final class WorkspaceContent {
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
	 * Project content repository.
	 *
	 * @var ContentRepository
	 */
	private ContentRepository $content;

	/**
	 * Activity repository.
	 *
	 * @var ActivityRepository
	 */
	private ActivityRepository $activity;

	/**
	 * Project settings repository.
	 *
	 * @var ProjectSettingsRepository
	 */
	private ProjectSettingsRepository $settings;

	/**
	 * Constructor.
	 *
	 * @param DomainProjectManager|null $projects   Domain project manager.
	 * @param ProjectPermissions|null   $permissions Project permissions service.
	 */
	public function __construct(?DomainProjectManager $projects = null, ?ProjectPermissions $permissions = null) {
		$this->projects = $projects ?? DomainProjectManager::get_instance();
		$this->permissions = $permissions ?? new ProjectPermissions();
		$this->content = new ContentRepository();
		$this->activity = new ActivityRepository();
		$this->settings = new ProjectSettingsRepository();
	}

	/**
	 * Render a project workspace section.
	 *
	 * @param Project $project Project to render.
	 * @param string  $section Workspace section.
	 * @return void
	 */
	public function render(Project $project, string $section): void {
		if ("activity" === $section) {
			$this->render_activity($project);
			return;
		}

		if ("settings" === $section) {
			$this->render_settings($project);
			return;
		}

		if (in_array($section, ["notes", "discussions"], true)) {
			$this->render_posts($project, $section);
		}
	}

	/**
	 * Render project notes or discussions.
	 *
	 * @param Project $project Project to render.
	 * @param string  $section Workspace section.
	 * @return void
	 */
	private function render_posts(Project $project, string $section): void {
		$type = "notes" === $section ? "pp_note" : "pp_discussion";
		$rows = $this->content->for_project($type, $project->get_id());
		$can_edit = $this->permissions->can_edit($project);
		?>
		<div class="row g-4">
			<div class="col-xl-8">
				<div class="card card-body">
					<h2 class="h4">
						<?php echo esc_html(ucfirst($section)); ?>
					</h2>

					<?php if (empty($rows)): ?>
						<p class="text-secondary mb-0">
							<?php esc_html_e("Nothing has been added yet.", "projectpress"); ?>
						</p>
					<?php endif; ?>

					<?php foreach ($rows as $post): ?>
						<div class="border rounded p-3 mb-3">
							<strong>
								<?php echo esc_html($post->post_title); ?>
							</strong>

							<div class="text-secondary small mt-1">
								<?php echo esc_html(wp_strip_all_tags($post->post_content)); ?>
							</div>

							<?php if ("discussions" === $section): ?>
								<?php $comments = $this->content->comments((int) $post->ID); ?>

								<?php foreach ($comments as $comment): ?>
									<div class="border-top mt-2 pt-2 small">
										<strong>
											<?php echo esc_html($comment->comment_author); ?>
										</strong>:
										<?php echo esc_html(wp_strip_all_tags($comment->comment_content)); ?>
									</div>
								<?php endforeach; ?>

								<?php if ($can_edit): ?>
									<form
										class="mt-2"
										method="post"
										action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
									>
										<input type="hidden" name="action" value="projectpress_workspace_content_comment"/>
										<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
										<input type="hidden" name="discussion_id" value="<?php echo esc_attr((string) $post->ID); ?>"/>
										<?php wp_nonce_field("projectpress_workspace_content_comment"); ?>

										<textarea
											class="form-control"
											name="content"
											required
										></textarea>
										<button class="btn btn-sm btn-outline-primary mt-2" type="submit">
											<?php esc_html_e("Reply", "projectpress"); ?>
										</button>
									</form>
								<?php endif; ?>
							<?php endif; ?>

							<?php if ($can_edit): ?>
								<?php $this->delete_form($project, $section, (int) $post->ID); ?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="col-xl-4">
				<div class="card card-body">
					<h2 class="h4">
						<?php
						$add_label =
							"notes" === $section
								? __("Note", "projectpress")
								: __("Discussion", "projectpress");
						/* translators: %s: Content type label. */
						echo esc_html(sprintf(__("Add %s", "projectpress"), $add_label));
						?>
					</h2>

					<?php if (!$can_edit): ?>
						<p class="text-secondary mb-0">
							<?php esc_html_e("This section is read only.", "projectpress"); ?>
						</p>
					<?php else: ?>
						<?php $this->post_form($project, $section); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a note or discussion creation form.
	 *
	 * @param Project $project Project to add content to.
	 * @param string  $section Workspace section.
	 * @return void
	 */
	private function post_form(Project $project, string $section): void {
		?>
		<form
			method="post"
			action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
		>
			<input type="hidden" name="action" value="projectpress_workspace_content_save"/>
			<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
			<input type="hidden" name="section" value="<?php echo esc_attr($section); ?>"/>
			<?php wp_nonce_field("projectpress_workspace_content_save"); ?>

			<label class="form-label" for="content-title">
				<?php esc_html_e("Title", "projectpress"); ?>
			</label>
			<input class="form-control" id="content-title" name="title" required/>

			<label class="form-label mt-3" for="content-body">
				<?php esc_html_e("Content", "projectpress"); ?>
			</label>
			<textarea
				class="form-control"
				id="content-body"
				name="content"
			></textarea>

			<button class="btn btn-primary mt-3" type="submit">
				<?php esc_html_e("Add", "projectpress"); ?>
			</button>
		</form>
		<?php
	}

	/**
	 * Render project activity records.
	 *
	 * @param Project $project Project to render.
	 * @return void
	 */
	private function render_activity(Project $project): void {
		$rows = $this->activity->for_project($project->get_id());
		?>
		<div class="card card-body">
			<h2 class="h4">
				<?php esc_html_e("Activity", "projectpress"); ?>
			</h2>

			<?php if (empty($rows)): ?>
				<p class="text-secondary mb-0">
					<?php esc_html_e("No project activity has been recorded yet.", "projectpress"); ?>
				</p>
			<?php endif; ?>

			<?php foreach ($rows as $row): ?>
				<div class="border-bottom py-2">
					<strong>
						<?php
						$summary =
							$row["activity_summary"]
								? $row["activity_summary"]
								: ucwords(str_replace("_", " ", (string) $row["action"]));
						echo esc_html((string) $summary);
						?>
					</strong>
					<small class="d-block text-secondary">
						<?php echo esc_html((string) $row["created_at"]); ?>
					</small>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render project settings.
	 *
	 * @param Project $project Project to render.
	 * @return void
	 */
	private function render_settings(Project $project): void {
		$rows = $this->settings->for_project($project->get_id());
		$can_edit = $this->permissions->can_edit($project);
		?>
		<div class="row g-4">
			<div class="col-xl-8">
				<div class="card card-body">
					<h2 class="h4">
						<?php esc_html_e("Project Settings", "projectpress"); ?>
					</h2>

					<?php foreach ($rows as $row): ?>
						<div class="border rounded p-3 mb-3">
							<strong>
								<?php echo esc_html($row["setting_key"]); ?>
							</strong>
							<div class="text-secondary small">
								<?php echo esc_html((string) $row["setting_value"]); ?>
							</div>

							<?php if ($can_edit): ?>
								<?php $this->delete_form($project, "settings", (int) $row["id"]); ?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="col-xl-4">
				<div class="card card-body">
					<h2 class="h4">
						<?php esc_html_e("Set Project Setting", "projectpress"); ?>
					</h2>

					<?php if ($can_edit): ?>
						<form
							method="post"
							action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
						>
							<input type="hidden" name="action" value="projectpress_workspace_setting_save"/>
							<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
							<?php wp_nonce_field("projectpress_workspace_setting_save"); ?>

							<label class="form-label" for="setting-key">
								<?php esc_html_e("Setting key", "projectpress"); ?>
							</label>
							<input class="form-control" id="setting-key" name="key" required/>

							<label class="form-label mt-3" for="setting-value">
								<?php esc_html_e("Value", "projectpress"); ?>
							</label>
							<textarea
								class="form-control"
								id="setting-value"
								name="value"
							></textarea>

							<button class="btn btn-primary mt-3" type="submit">
								<?php esc_html_e("Save", "projectpress"); ?>
							</button>
						</form>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a delete form.
	 *
	 * @param Project $project Project to change.
	 * @param string  $section Workspace section.
	 * @param int     $id      Record ID.
	 * @return void
	 */
	private function delete_form(Project $project, string $section, int $id): void {
		?>
		<form
			class="mt-2"
			method="post"
			action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
		>
			<input type="hidden" name="action" value="projectpress_workspace_content_delete"/>
			<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
			<input type="hidden" name="section" value="<?php echo esc_attr($section); ?>"/>
			<input type="hidden" name="record_id" value="<?php echo esc_attr((string) $id); ?>"/>
			<?php wp_nonce_field("projectpress_workspace_content_delete"); ?>

			<button class="btn btn-sm btn-outline-danger" type="submit">
				<?php esc_html_e("Delete", "projectpress"); ?>
			</button>
		</form>
		<?php
	}

	/**
	 * Handle saving project notes or discussions.
	 *
	 * @return void
	 */
	public function handle_save(): void {
		check_admin_referer("projectpress_workspace_content_save");

		$project_id = RequestHelper::integer($_POST, "project_id", 0);
		$section = RequestHelper::key($_POST, "section", "");
		$project = $this->projects->find($project_id);

		if (
			!$project
			|| !$this->permissions->can_edit($project)
			|| !in_array($section, ["notes", "discussions"], true)
		) {
			$this->redirect($project_id, $section, false);
		}

		$type = "notes" === $section ? "pp_note" : "pp_discussion";
		$result = $this->content->create($type, $project_id, [
			"title" => RequestHelper::text($_POST, "title", ""),
			"content" => isset($_POST["content"]) ? wp_kses_post(wp_unslash($_POST["content"])) : "",
		]);

		$this->redirect($project_id, $section, $result);
	}

	/**
	 * Handle adding a discussion comment.
	 *
	 * @return void
	 */
	public function handle_comment(): void {
		check_admin_referer("projectpress_workspace_content_comment");

		$project_id = RequestHelper::integer($_POST, "project_id", 0);
		$project = $this->projects->find($project_id);

		if (!$project || !$this->permissions->can_edit($project)) {
			$this->redirect($project_id, "discussions", false);
		}

		$result = $this->content->add_comment(
			$project_id,
			RequestHelper::integer($_POST, "discussion_id", 0),
			isset($_POST["content"]) ? wp_kses_post(wp_unslash($_POST["content"])) : "",
		);

		$this->redirect($project_id, "discussions", $result);
	}

	/**
	 * Handle saving a project setting.
	 *
	 * @return void
	 */
	public function handle_setting_save(): void {
		check_admin_referer("projectpress_workspace_setting_save");

		$project_id = RequestHelper::integer($_POST, "project_id", 0);
		$project = $this->projects->find($project_id);

		if (!$project || !$this->permissions->can_edit($project)) {
			$this->redirect($project_id, "settings", false);
		}

		$key = RequestHelper::key($_POST, "key", "");
		$result =
			"" !== $key
				? $this->settings->save(
					$project_id,
					$key,
					RequestHelper::text($_POST, "value", ""),
				)
				: false;

		$this->redirect($project_id, "settings", $result);
	}

	/**
	 * Handle deleting workspace content or a project setting.
	 *
	 * @return void
	 */
	public function handle_delete(): void {
		check_admin_referer("projectpress_workspace_content_delete");

		$project_id = RequestHelper::integer($_POST, "project_id", 0);
		$section = RequestHelper::key($_POST, "section", "");
		$id = RequestHelper::integer($_POST, "record_id", 0);
		$project = $this->projects->find($project_id);

		if (!$project || !$this->permissions->can_edit($project)) {
			$this->redirect($project_id, $section, false);
		}

		if ("settings" === $section) {
			$this->redirect(
				$project_id,
				$section,
				$this->settings->delete($project_id, $id),
			);
		}

		$type =
			"notes" === $section
				? "pp_note"
				: ("discussions" === $section ? "pp_discussion" : "");
		$result =
			"" !== $type
				? $this->content->delete($type, $project_id, $id)
				: false;

		$this->redirect($project_id, $section, $result);
	}

	/**
	 * Redirect back to a workspace section.
	 *
	 * @param int   $project_id Project ID.
	 * @param string $section   Workspace section.
	 * @param mixed $result     Operation result.
	 * @return void
	 */
	private function redirect(int $project_id, string $section, $result): void {
		$section =
			in_array($section, ["notes", "discussions", "settings"], true)
				? $section
				: "overview";
		$url = add_query_arg(
			[
				"page" => "projectpress",
				"group" => "projects",
				"project_id" => absint($project_id),
				"tab" => $section,
			],
			admin_url("admin.php"),
		);

		if (is_wp_error($result) || false === $result) {
			$message =
				is_wp_error($result)
					? $result->get_error_message()
					: __("The change could not be saved.", "projectpress");
			$url = add_query_arg("project_error", rawurlencode($message), $url);
		} else {
			$url = add_query_arg("project_saved", "1", $url);
		}

		wp_safe_redirect($url);
		exit();
	}
}
