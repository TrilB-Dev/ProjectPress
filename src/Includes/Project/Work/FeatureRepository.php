<?php
namespace ProjectPress\Includes\Project\Work;
use ProjectPress\Includes\Functions\Helpers\DBHelper;
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class FeatureRepository {
 public function find( int $id ): ?Feature { $row = DBHelper::get_by_id( 'features', absint( $id ) ); return is_array( $row ) ? new Feature( $row ) : null; }
 public function for_project( int $project_id ): array { return array_map( static fn( array $row ): Feature => new Feature( $row ), DBHelper::get_all( 'features', array( 'project_id' => absint( $project_id ) ), '*', 'sort_order ASC,created_at ASC' ) ); }
 public function create( int $project_id, array $data ) { $now=current_time('mysql',true); $id=DBHelper::insert('features',array_merge($data,array('project_id'=>absint($project_id),'created_by'=>get_current_user_id(),'created_at'=>$now,'updated_by'=>get_current_user_id(),'updated_at'=>$now))); return false===$id?false:$this->find((int)$id); }
 public function update( Feature $feature, array $data ): bool { return DBHelper::update('features',array_merge($data,array('updated_by'=>get_current_user_id(),'updated_at'=>current_time('mysql',true))),array('id'=>$feature->get_id(),'project_id'=>$feature->get_project_id())); }
 public function delete( Feature $feature ): bool { return DBHelper::delete('features',array('id'=>$feature->get_id(),'project_id'=>$feature->get_project_id())); }
}
