<?php
/**
 * Project collaboration workspace sections.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\Projects
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\Collaboration\AttachmentRepository;
use ProjectPress\Includes\Project\Collaboration\DocumentRepository;
use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;
use WP_Error;

if (!defined("ABSPATH")) {
	exit();
}

/**
 * Renders and handles project documents and file attachments.
 */
final class Collaboration {
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
	 * Document repository.
	 *
	 * @var DocumentRepository
	 */
	private DocumentRepository $documents;

	/**
	 * Attachment repository.
	 *
	 * @var AttachmentRepository
	 */
	private AttachmentRepository $attachments;

	/**
	 * Constructor.
	 *
	 * @param DomainProjectManager|null $projects   Domain project manager.
	 * @param ProjectPermissions|null   $permissions Project permissions service.
	 */
	public function __construct(?DomainProjectManager $projects = null, ?ProjectPermissions $permissions = null) {
		$this->projects = $projects ?? DomainProjectManager::get_instance();
		$this->permissions = $permissions ?? new ProjectPermissions();
		$this->documents = new DocumentRepository();
		$this->attachments = new AttachmentRepository();
	}

	/**
	 * Render project documents.
	 *
	 * @param Project $project Project to render.
	 * @return void
	 */
	public function render_documents(Project $project): void {
		$rows = $this->documents->for_project($project->get_id());
		$can_edit = $this->permissions->can_edit($project);
		?>
		<div class="row g-4">
			<div class="col-xl-8">
				<div class="card card-body">
					<h2 class="h4">
						<?php esc_html_e("Documents", "projectpress"); ?>
					</h2>

					<?php if (empty($rows)): ?>
						<p class="text-secondary mb-0">
							<?php esc_html_e("No documents have been added yet.", "projectpress"); ?>
						</p>
					<?php endif; ?>

					<?php foreach ($rows as $document): ?>
						<div class="border rounded p-3 mb-3">
							<strong>
								<?php echo esc_html($document->get_title()); ?>
							</strong>

							<?php if ($document->get_content()): ?>
								<div class="text-secondary small mt-1">
									<?php echo esc_html(wp_strip_all_tags($document->get_content())); ?>
								</div>
							<?php endif; ?>

							<?php if ($can_edit): ?>
								<?php $this->delete_form($project, "documents", $document->get_id()); ?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="col-xl-4">
				<div class="card card-body">
					<h2 class="h4">
						<?php esc_html_e("Add Document", "projectpress"); ?>
					</h2>

					<?php if (!$can_edit): ?>
						<p class="text-secondary mb-0">
							<?php esc_html_e("This section is read only.", "projectpress"); ?>
						</p>
					<?php else: ?>
						<?php $this->document_form($project); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render project file attachments.
	 *
	 * @param Project $project Project to render.
	 * @return void
	 */
	public function render_files(Project $project): void {
		$rows = $this->attachments->for_project($project->get_id());
		$can_edit = $this->permissions->can_edit($project);
		?>
		<div class="row g-4">
			<div class="col-xl-8">
				<div class="card card-body">
					<h2 class="h4">
						<?php esc_html_e("Files", "projectpress"); ?>
					</h2>

					<?php if (empty($rows)): ?>
						<p class="text-secondary mb-0">
							<?php esc_html_e("No files are attached to this project.", "projectpress"); ?>
						</p>
					<?php endif; ?>

					<?php foreach ($rows as $file): ?>
						<div class="border rounded p-3 mb-3">
							<strong>
								<?php echo esc_html($file->get_label()); ?>
							</strong>
							<div class="small text-secondary">
								<?php echo esc_html(sprintf(__("Attachment #%d", "projectpress"), $file->get_attachment_id())); ?>
							</div>

							<?php if ($can_edit): ?>
								<?php $this->delete_form($project, "files", $file->get_attachment_id()); ?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="col-xl-4">
				<div class="card card-body">
					<h2 class="h4">
						<?php esc_html_e("Attach File", "projectpress"); ?>
					</h2>

					<?php if (!$can_edit): ?>
						<p class="text-secondary mb-0">
							<?php esc_html_e("This section is read only.", "projectpress"); ?>
						</p>
					<?php else: ?>
						<?php $this->file_form($project); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the document creation form.
	 *
	 * @param Project $project Project to add a document to.
	 * @return void
	 */
	private function document_form(Project $project): void {
		?>
		<form
			method="post"
			action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
		>
			<input type="hidden" name="action" value="projectpress_workspace_collaboration_save"/>
			<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
			<input type="hidden" name="section" value="documents"/>
			<?php wp_nonce_field("projectpress_workspace_collaboration_save"); ?>

			<label class="form-label" for="collab-document-title">
				<?php esc_html_e("Title", "projectpress"); ?>
			</label>
			<input class="form-control" id="collab-document-title" name="title" required/>

			<label class="form-label mt-3" for="collab-document-content">
				<?php esc_html_e("Content", "projectpress"); ?>
			</label>
			<textarea
				class="form-control"
				id="collab-document-content"
				name="content"
			></textarea>

			<button class="btn btn-primary mt-3" type="submit">
				<?php esc_html_e("Add Document", "projectpress"); ?>
			</button>
		</form>
		<?php
	}

	/**
	 * Render the file attachment form.
	 *
	 * @param Project $project Project to add a file to.
	 * @return void
	 */
	private function file_form(Project $project): void {
		?>
		<form
			method="post"
			action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
		>
			<input type="hidden" name="action" value="projectpress_workspace_collaboration_save"/>
			<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
			<input type="hidden" name="section" value="files"/>
			<?php wp_nonce_field("projectpress_workspace_collaboration_save"); ?>

			<label class="form-label" for="collab-attachment-id">
				<?php esc_html_e("WordPress attachment ID", "projectpress"); ?>
			</label>
			<input
				class="form-control"
				type="number"
				min="1"
				id="collab-attachment-id"
				name="attachment_id"
				required
			/>

			<label class="form-label mt-3" for="collab-file-label">
				<?php esc_html_e("Label", "projectpress"); ?>
			</label>
			<input class="form-control" id="collab-file-label" name="title"/>

			<button class="btn btn-primary mt-3" type="submit">
				<?php esc_html_e("Attach File", "projectpress"); ?>
			</button>
		</form>
		<?php
	}

	/**
	 * Render a delete or detach form.
	 *
	 * @param Project $project Project to change.
	 * @param string  $section Collaboration section.
	 * @param int     $id      Record or attachment ID.
	 * @return void
	 */
	private function delete_form(Project $project, string $section, int $id): void {
		?>
		<form
			class="mt-2"
			method="post"
			action="<?php echo esc_url(admin_url("admin-post.php")); ?>"
		>
			<input type="hidden" name="action" value="projectpress_workspace_collaboration_delete"/>
			<input type="hidden" name="project_id" value="<?php echo esc_attr((string) $project->get_id()); ?>"/>
			<input type="hidden" name="section" value="<?php echo esc_attr($section); ?>"/>
			<input type="hidden" name="record_id" value="<?php echo esc_attr((string) $id); ?>"/>
			<?php wp_nonce_field("projectpress_workspace_collaboration_delete"); ?>

			<button class="btn btn-sm btn-outline-danger" type="submit">
				<?php
				echo esc_html(
					"files" === $section
						? __("Detach", "projectpress")
						: __("Delete", "projectpress"),
				);
				?>
			</button>
		</form>
		<?php
	}

	/**
	 * Handle saving a collaboration record.
	 *
	 * @return void
	 */
	public function handle_save(): void {
		check_admin_referer("projectpress_workspace_collaboration_save");

		$project_id = RequestHelper::integer($_POST, "project_id", 0);
		$section = RequestHelper::key($_POST, "section", "");
		$project = $this->projects->find($project_id);

		if (!$project || !$this->permissions->can_edit($project)) {
			$this->redirect(
				$project_id,
				$section,
				new WP_Error(
					"projectpress_collaboration_forbidden",
					__("You cannot change this project section.", "projectpress"),
				),
			);
		}

		if ("documents" === $section) {
			$title = RequestHelper::text($_POST, "title", "");

			if ("" === $title) {
				$this->redirect(
					$project_id,
					$section,
					new WP_Error(
						"projectpress_document_title_required",
						__("A document title is required.", "projectpress"),
					),
				);
			}

			$result = $this->documents->create($project_id, [
				"title" => $title,
				"content" => isset($_POST["content"]) ? wp_kses_post(wp_unslash($_POST["content"])) : "",
				"status" => "draft",
			]);

			$this->redirect($project_id, $section, $result);
		}

		if ("files" === $section) {
			$attachment_id = RequestHelper::integer($_POST, "attachment_id", 0);
			$result = $this->attachments->attach(
				$project_id,
				$attachment_id,
				RequestHelper::text($_POST, "title", ""),
			);
			$this->redirect($project_id, $section, $result);
		}

		$this->redirect(
			$project_id,
			$section,
			new WP_Error(
				"projectpress_collaboration_section_invalid",
				__("The collaboration section is invalid.", "projectpress"),
			),
		);
	}

	/**
	 * Handle deleting a collaboration record.
	 *
	 * @return void
	 */
	public function handle_delete(): void {
		check_admin_referer("projectpress_workspace_collaboration_delete");

		$project_id = RequestHelper::integer($_POST, "project_id", 0);
		$section = RequestHelper::key($_POST, "section", "");
		$id = RequestHelper::integer($_POST, "record_id", 0);
		$project = $this->projects->find($project_id);

		if (!$project || !$this->permissions->can_edit($project)) {
			$this->redirect(
				$project_id,
				$section,
				new WP_Error(
					"projectpress_collaboration_forbidden",
					__("You cannot change this project section.", "projectpress"),
				),
			);
		}

		if ("documents" === $section) {
			$document = $this->documents->find($id);
			$result =
				$document && $document->get_project_id() === $project_id
					? $this->documents->delete($id)
					: false;
			$this->redirect($project_id, $section, $result);
		}

		if ("files" === $section) {
			$this->redirect(
				$project_id,
				$section,
				$this->attachments->detach($project_id, $id),
			);
		}

		$this->redirect($project_id, $section, false);
	}

	/**
	 * Redirect back to a collaboration workspace section.
	 *
	 * @param int   $project_id Project ID.
	 * @param string $section   Collaboration section.
	 * @param mixed $result     Service or validation result.
	 * @return void
	 */
	private function redirect(int $project_id, string $section, $result): void {
		$section = in_array($section, ["documents", "files"], true) ? $section : "overview";
		$url = add_query_arg(
			[
				"page" => "projectpress",
				"group" => "projects",
				"project_id" => absint($project_id),
				"tab" => $section,
			],
			admin_url("admin.php"),
		);
		$url =
			is_wp_error($result) || false === $result
				? add_query_arg(
					"project_error",
					rawurlencode(
						is_wp_error($result)
							? $result->get_error_message()
							: __("The change could not be saved.", "projectpress"),
					),
					$url,
				)
				: add_query_arg("project_saved", "1", $url);

		wp_safe_redirect($url);
		exit();
	}
}
