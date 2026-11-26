<?php
/**
 * CompositionRepository for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Composition;

use ProjectPress\Includes\Functions\Helpers\DBHelper;
		if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * CompositionRepository implementation.
 *
 * @since 1.0.0
 */
final class CompositionRepository {
	/**
	 * table property.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $table;
	/**
	 * class property.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $class;
	/**
	 * __construct.
	 *
	 * @param string $type type parameter.
	 *
	 * @since 1.0.0
	 */
	public function __construct( string $type ) {
		$map = array(
			'component'    => array( 'components', Component::class ),
			'package'      => array( 'packages', Package::class ),
			'asset'        => array( 'composition_assets', Asset::class ),
			'licence'      => array( 'composition_licences', Licence::class ),
			'attribution'  => array( 'attributions', Attribution::class ),
			'provenance'   => array( 'provenance', Provenance::class ),
			'relationship' => array( 'composition_relationships', Relationship::class ),
			'sbom'         => array( 'sboms', SBOM::class ),
		);
		if ( ! isset( $map[ $type ] ) ) {
			throw new \InvalidArgumentException( 'Invalid composition type.' );
		}
		$this->table = $map[ $type ][0];
		$this->class  = $map[ $type ][1];
	}
	/**
	 * find.
	 *
	 * @param int $id id parameter.
	 *
	 * @return mixed find result.
	 *
	 * @since 1.0.0
	 */
	public function find( int $id ) {
		$row = DBHelper::get_by_id( $this->table, absint( $id ) );
		return is_array( $row ) ? new $this->class( $row ) : null;
	}
	/**
	 * for_project.
	 *
	 * @param int $id id parameter.
	 *
	 * @return array for_project result.
	 *
	 * @since 1.0.0
	 */
	public function for_project( int $id ): array {
		$class = $this->class;
		return array_map( static fn( array $row )=>new $class( $row ), DBHelper::get_all( $this->table, array( 'project_id' => absint( $id ) ), '*', 'created_at DESC' ) );
	}
	/**
	 * create.
	 *
	 * @param int $id id parameter.
	 * @param array $data data parameter.
	 *
	 * @return mixed create result.
	 *
	 * @since 1.0.0
	 */
	public function create( int $id, array $data ) {
		$now    = current_time( 'mysql', true );
		$record = DBHelper::insert(
			$this->table,
			array_merge(
				$data,
				array(
					'project_id' => absint( $id ),
					'created_by' => get_current_user_id(),
					'created_at' => $now,
					'updated_at' => $now,
				)
			)
		);
		return false === $record ? false : $this->find( (int) $record );
	}
	/**
	 * update.
	 *
	 * @param mixed $record record parameter.
	 * @param array $data data parameter.
	 *
	 * @return bool update result.
	 *
	 * @since 1.0.0
	 */
	public function update( $record, array $data ): bool {
		return DBHelper::update(
			$this->table,
			array_merge( $data, array( 'updated_at' => current_time( 'mysql', true ) ) ),
			array(
				'id'         => $record->get_id(),
				'project_id' => $record->get_project_id(),
			)
		);
	}
	/**
	 * delete.
	 *
	 * @param mixed $record record parameter.
	 *
	 * @return bool delete result.
	 *
	 * @since 1.0.0
	 */
	public function delete( $record ): bool {
		return DBHelper::delete(
			$this->table,
			array(
				'id'         => $record->get_id(),
				'project_id' => $record->get_project_id(),
			)
		);
	}
}
