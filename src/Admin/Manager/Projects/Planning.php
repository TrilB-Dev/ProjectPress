<?php
/**
 * Project planning workspace section.
 *
 * @package ProjectPress
 * @subpackage Admin/Manager/Projects
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Planning\GanttChart;
use ProjectPress\Includes\Project\Planning\RoadMap;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;
use ProjectPress\Includes\Project\Workflow\Kanban;
use ProjectPress\Includes\Project\Workflow\Workflow;

if (!defined("ABSPATH")) {
	exit();
}

/**
 * Renders roadmap, Gantt and Kanban planning views for a project.
 */
final class Planning {
	/**
	 * Domain project manager.
	 *
	 * @var DomainProjectManager
	 */
	private DomainProjectManager $projects;

	/**
	 * Creates the planning workspace section.
	 *
	 * @param DomainProjectManager|null $projects Optional domain project manager.
	 */
	public function __construct(?DomainProjectManager $projects = null) {
		$this->projects = $projects ?? DomainProjectManager::get_instance();
	}

	/**
	 * Renders the project planning workspace.
	 *
	 * @param Project $project Project being displayed.
	 * @return void
	 */
	public function render(Project $project): void {
		$plan = $this->projects->plan($project->get_id());

		if (!$plan) {
			?>
			<div class="alert alert-warning">
				<?php esc_html_e("The project plan is not available.", "projectpress"); ?>
			</div>
			<?php
			return;
		}

		$items = $plan->planning_items();
		$roadmap = RoadMap::group_by_status($items);
		$timeline = GanttChart::rows($items);
		$kanban = Kanban::columns($items);
		$workflow = Workflow::project_work();
		?>
		<div class="card card-body mb-4">
			<h2 class="h4">
				<?php esc_html_e("Workflow", "projectpress"); ?>
			</h2>

			<div class="row g-3">
				<?php foreach ($kanban as $status => $rows): ?>
					<div class="col-md-6 col-xl-3">
						<div class="border rounded p-3 h-100">
							<h3 class="h6">
								<?php echo esc_html($this->humanise((string) $status)); ?>
								<span class="badge text-bg-secondary">
									<?php echo esc_html((string) count($rows)); ?>
								</span>
							</h3>

							<?php foreach ($rows as $row): ?>
								<div class="card card-body p-2 mb-2">
									<strong>
										<?php echo esc_html($this->title($row)); ?>
									</strong>
									<small class="text-secondary">
										<?php echo esc_html($this->humanise((string) ($row["planning_type"] ?? "item"))); ?>
									</small>
								</div>
							<?php endforeach; ?>

							<small class="text-secondary">
								<?php
								$next = $workflow->transitions_from((string) $status);
								echo esc_html(
									$next
										? sprintf(
											__("Next: %s", "projectpress"),
											implode(", ", array_map([$this, "humanise"], $next)),
										)
										: __("Terminal state", "projectpress"),
								);
								?>
							</small>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="row g-4">
			<div class="col-xl-7">
				<div class="card card-body h-100">
					<h2 class="h4">
						<?php esc_html_e("Roadmap", "projectpress"); ?>
					</h2>

					<?php if (empty($roadmap)): ?>
						<p class="text-secondary mb-0">
							<?php esc_html_e("No planned work has been added yet.", "projectpress"); ?>
						</p>
					<?php endif; ?>

					<?php foreach ($roadmap as $status => $rows): ?>
						<div class="mb-3">
							<h3 class="h6">
								<?php echo esc_html($this->humanise((string) $status)); ?>
							</h3>

							<?php foreach ($rows as $row): ?>
								<div class="border rounded p-2 mb-2">
									<?php echo esc_html($this->title($row)); ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="col-xl-5">
				<div class="card card-body h-100">
					<h2 class="h4">
						<?php esc_html_e("Gantt Timeline", "projectpress"); ?>
					</h2>

					<?php if (empty($timeline)): ?>
						<p class="text-secondary mb-0">
							<?php esc_html_e("No timeline items are available yet.", "projectpress"); ?>
						</p>
					<?php endif; ?>

					<?php foreach ($timeline as $row): ?>
						<div class="border-bottom py-2">
							<strong>
								<?php echo esc_html($this->title($row)); ?>
							</strong>

							<?php
							$date = $this->date($row);
							if ($date):
								?>
								<small class="d-block text-secondary">
									<?php echo esc_html(substr($date, 0, 10)); ?>
								</small>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Resolves a display title from a planning row.
	 *
	 * @param array<string, mixed> $item Planning row.
	 * @return string
	 */
	private function title(array $item): string {
		return (string) (
			$item["title"]
			?? $item["task_title"]
			?? $item["milestone_name"]
			?? __("Untitled item", "projectpress")
		);
	}

	/**
	 * Resolves a date from a planning row.
	 *
	 * @param array<string, mixed> $item Planning row.
	 * @return string
	 */
	private function date(array $item): string {
		return (string) (
			$item["target_date"]
			?? $item["due_date"]
			?? $item["target_end_date"]
			?? ""
		);
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
