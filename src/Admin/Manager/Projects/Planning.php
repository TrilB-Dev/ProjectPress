<?php
/**
 * Project planning workspace section.
 *
 * @package ProjectPress\Admin\Manager\Projects
 */
namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Planning\GanttChart;
use ProjectPress\Includes\Project\Planning\RoadMap;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Planning {
	private DomainProjectManager $projects;

	public function __construct( ?DomainProjectManager $projects = null ) {
		$this->projects = $projects ?? DomainProjectManager::get_instance();
	}

	public function render( Project $project ): void {
		$plan = $this->projects->plan( $project->get_id() );
		if ( ! $plan ) {
			?><div class="alert alert-warning"><?php esc_html_e( 'The project plan is not available.', 'projectpress' ); ?></div><?php
			return;
		}

		$milestones = $plan->milestones();
		$work_items = $plan->work_items();
		$roadmap    = RoadMap::group_by_status( array_merge( $milestones, $work_items ) );
		$timeline   = GanttChart::rows( array_merge( $milestones, $work_items ) );
		?>
		<div class="row g-4">
			<div class="col-xl-7">
				<div class="card card-body h-100">
					<h2 class="h4"><?php esc_html_e( 'Roadmap', 'projectpress' ); ?></h2>
					<?php if ( empty( $roadmap ) ) : ?><p class="text-secondary mb-0"><?php esc_html_e( 'No planned work has been added yet.', 'projectpress' ); ?></p><?php endif; ?>
					<?php foreach ( $roadmap as $status => $items ) : ?>
						<div class="mb-3"><h3 class="h6"><?php echo esc_html( ucwords( str_replace( '_', ' ', (string) $status ) ) ); ?></h3>
						<?php foreach ( $items as $item ) : ?><div class="border rounded p-2 mb-2"><?php echo esc_html( $this->title( $item ) ); ?></div><?php endforeach; ?></div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="col-xl-5">
				<div class="card card-body h-100">
					<h2 class="h4"><?php esc_html_e( 'Timeline', 'projectpress' ); ?></h2>
					<?php if ( empty( $timeline ) ) : ?><p class="text-secondary mb-0"><?php esc_html_e( 'No timeline items are available yet.', 'projectpress' ); ?></p><?php endif; ?>
					<?php foreach ( $timeline as $item ) : ?><div class="border-bottom py-2"><strong><?php echo esc_html( $this->title( $item ) ); ?></strong><?php $date = $this->date( $item ); if ( $date ) : ?><small class="d-block text-secondary"><?php echo esc_html( substr( $date, 0, 10 ) ); ?></small><?php endif; ?></div><?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	private function title( array $item ): string {
		return (string) ( $item['title'] ?? $item['task_title'] ?? $item['milestone_name'] ?? __( 'Untitled item', 'projectpress' ) );
	}

	private function date( array $item ): string {
		return (string) ( $item['target_date'] ?? $item['due_date'] ?? $item['target_end_date'] ?? '' );
	}
}
