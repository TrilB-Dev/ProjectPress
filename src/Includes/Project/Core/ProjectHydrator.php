<?php
namespace ProjectPress\Includes\Project\Core;
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class ProjectHydrator {
 public function hydrate(array $row):Project{return new Project($row);}
 public function extract(Project $project):array{$d=$project->to_array();return array('project_name'=>$d['name'],'project_slug'=>$d['slug'],'project_key'=>$d['key'],'project_description'=>$d['description'],'parent_id'=>$d['parent_id'],'owner_id'=>$d['owner_id'],'status'=>$d['status'],'priority'=>$d['priority'],'visibility'=>$d['visibility'],'start_date'=>$d['start_date'],'target_end_date'=>$d['target_end_date'],'actual_end_date'=>$d['actual_end_date'],'created_by'=>$d['created_by'],'updated_by'=>$d['updated_by']);}
}
