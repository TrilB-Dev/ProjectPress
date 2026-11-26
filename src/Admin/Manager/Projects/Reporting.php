<?php
/**
 * Project reporting workspace controller.
 *
 * @package ProjectPress
 * @subpackage Admin/Manager/Projects
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;
use ProjectPress\Includes\Project\Reports\ProjectReportService;

if (!defined("ABSPATH")) {
	exit();
}

/**
 * Renders project report summaries.
 */
final class Reporting {
	/**
	 * Project report service.
	 *
	 * @var ProjectReportService
	 */
	private ProjectReportService $reports;

	/**
	 * Constructor.
	 *
	 * @param DomainProjectManager|null $projects   Domain project manager.
	 * @param ProjectPermissions|null   $permissions Project permissions service.
	 */
	public function __construct(?DomainProjectManager $projects = null, ?ProjectPermissions $permissions = null) {
		$projects = $projects ?? DomainProjectManager::get_instance();
		$this->reports = new ProjectReportService(
			$projects->repository(),
			$permissions ?? new ProjectPermissions(),
		);
	}

	/**
	 * Render the report for a project.
	 *
	 * @param Project $project Project to report on.
	 * @return void
	 */
	public function render(Project $project): void {
		$report = $this->reports->for_project($project->get_id());

		if (!$report) {
			?>
			<div class="alert alert-warning">
				<?php esc_html_e("The project report is not available.", "projectpress"); ?>
			</div>
			<?php
			return;
		}
		?>
		<div class="card card-body mb-4">
			<h2 class="h4">
				<?php esc_html_e("Project Report", "projectpress"); ?>
			</h2>

			<div class="row g-3">
				<?php foreach ($report["totals"] as $key => $value): ?>
					<div class="col-sm-6 col-xl-3">
						<div class="border rounded p-3 h-100">
							<div class="small text-secondary">
								<?php echo esc_html(ucwords(str_replace("_", " ", $key))); ?>
							</div>
							<strong>
								<?php echo esc_html((string) $value); ?>
							</strong>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="row g-4">
			<?php foreach ($report["status"] as $key => $statuses): ?>
				<div class="col-md-6 col-xl-4">
					<div class="card card-body h-100">
						<h3 class="h5">
							<?php echo esc_html(ucwords(str_replace("_", " ", $key))); ?>
						</h3>

						<?php if (empty($statuses)): ?>
							<p class="text-secondary mb-0">
								<?php esc_html_e("No records.", "projectpress"); ?>
							</p>
						<?php endif; ?>

						<?php foreach ($statuses as $status => $count): ?>
							<div class="d-flex justify-content-between border-bottom py-2">
								<span>
									<?php echo esc_html(ucwords(str_replace("_", " ", $status))); ?>
								</span>
								<strong>
									<?php echo esc_html((string) $count); ?>
								</strong>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
