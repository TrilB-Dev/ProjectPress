<?php
namespace ProjectPress\Includes\Project\Core;
use ProjectPress\Includes\Functions\Helpers\PermissionHelper;
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class ProjectPermissions {
 public function can_view(Project $project,?int $user_id=null):bool{$user_id=$user_id??PermissionHelper::user_id();$allowed='public'===$project->get_visibility()||$user_id===$project->get_owner_id()||user_can($user_id,'projectpress_projects_view')||user_can($user_id,'manage_options');return (bool)apply_filters('projectpress_user_can_view_project',$allowed,$user_id,$project);}
 public function can_edit(Project $project,?int $user_id=null):bool{$user_id=$user_id??PermissionHelper::user_id();$allowed=$user_id>0&&($user_id===$project->get_owner_id()||user_can($user_id,'projectpress_projects_edit')||user_can($user_id,'manage_options'));return (bool)apply_filters('projectpress_user_can_edit_project',$allowed,$user_id,$project);}
 public function can_create():bool{return PermissionHelper::can_any(array('projectpress_projects_create','manage_options'));}
 public function can_archive(Project $project,?int $user_id=null):bool{return $this->can_edit($project,$user_id);}
 public function can_restore(Project $project,?int $user_id=null):bool{return $this->can_edit($project,$user_id);}
 public function can_delete(Project $project):bool{return PermissionHelper::can_any(array('projectpress_projects_delete','manage_options'));}
}
