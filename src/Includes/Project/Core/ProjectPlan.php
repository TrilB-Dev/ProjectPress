<?php
/**
 * Project planning aggregate.
 *
 * @package ProjectPress
 */
namespace ProjectPress\Includes\Project\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ProjectPlan {
	private int $project_id;
	private array $milestones;
	private array $work_items;

	public function __construct( int $project_id, array $milestones = array(), array $work_items = array() ) {
		$this->project_id = absint( $project_id );
		$this->milestones = array_values( $milestones );
		$this->work_items = array_values( $work_items );
	}

	public function get_project_id(): int {
		return $this->project_id;
	}

	public function milestones(): array {
		return $this->milestones;
	}

	public function work_items(): array {
		return $this->work_items;
	}

	public function add_milestone( $milestone ): self {
		$this->milestones[] = $milestone;
		return $this;
	}

	public function add_work_item( $work_item ): self {
		$this->work_items[] = $work_item;
		return $this;
	}

	public function to_array(): array {
		return array(
			'project_id' => $this->project_id,
			'milestones' => array_map( array( self::class, 'normalise' ), $this->milestones ),
			'work_items' => array_map( array( self::class, 'normalise' ), $this->work_items ),
		);
	}

	private static function normalise( $item ) {
		return is_object( $item ) && method_exists( $item, 'to_array' ) ? $item->to_array() : (array) $item;
	}
}
