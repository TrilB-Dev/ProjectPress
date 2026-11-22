<?php
namespace ProjectPress\Includes\Project\Tracking;
use ProjectPress\Includes\Functions\Helpers\DBHelper;
if(!defined('ABSPATH')){exit;}
final class ActivityRepository{public function for_project(int $project_id,int $limit=0):array{$rows=DBHelper::get_all('activity',array('project_id'=>absint($project_id)),'*','created_at DESC');return$limit>0?array_slice($rows,0,$limit):$rows;}}
