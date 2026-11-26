<?php
/**
 * Backlog view for the project manager.
 *
 * @package ProjectPress
 * @subpackage Admin/Manager/Projects
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Requirements\RequirementRepository;
use ProjectPress\Includes\Project\Work\Backlog as DomainBacklog;
use ProjectPress\Includes\Project\Work\FeatureRepository;
use ProjectPress\Includes\Project\Work\TaskRepository;
use ProjectPress\Includes\Project\Work\UserStoryRepository;

if (!defined("ABSPATH")) {
	exit();
}

/**
 * Renders open project work grouped by record type.
 */
final class Backlog {
	/**
	 * Renders the backlog view for the given project.
	 *
	 * @param Project $project Project to display the backlog for.
	 * @return void
	 */
	public function render(Project $project): void {
		$id = $project->get_id();
		$groups = [
			__("Requirements", "projectpress") => DomainBacklog::open(
				new RequirementRepository()->for_project($id),
			),
			__("Features", "projectpress") => DomainBacklog::open(
				new FeatureRepository()->for_project($id),
			),
			__("User Stories", "projectpress") => DomainBacklog::open(
				new UserStoryRepository()->for_project($id),
			),
			__("Tasks", "projectpress") => DomainBacklog::open(
				new TaskRepository()->for_project($id),
			),
		];
		?>
		<div class="card card-body">
			<h2 class="h4">
				<?php esc_html_e("Backlog", "projectpress"); ?>
			</h2>

			<p class="text-secondary">
				<?php esc_html_e("Open project work, grouped by its canonical record type.", "projectpress"); ?>
			</p>

			<div class="row g-4">
				<?php foreach ($groups as $label => $items): ?>
					<div class="col-lg-6">
						<h3 class="h5">
							<?php echo esc_html($label); ?>
							<span class="badge text-bg-secondary">
								<?php echo esc_html((string) count($items)); ?>
							</span>
						</h3>

						<?php if (empty($items)): ?>
							<p class="text-secondary">
								<?php esc_html_e("No open items.", "projectpress"); ?>
							</p>
						<?php endif; ?>

						<?php foreach ($items as $item): ?>
							<div class="border rounded p-2 mb-2">
								<strong>
									<?php
									echo esc_html(
										method_exists($item, "get_title")
											? $item->get_title()
											: (string) $item->get("title", ""),
									);
									?>
								</strong>

								<?php if (method_exists($item, "get_status")): ?>
									<div class="small text-secondary">
										<?php echo esc_html(ucwords(str_replace("_", " ", $item->get_status()))); ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
