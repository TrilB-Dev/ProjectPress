<?php
/** Runtime integration for project extension providers. @package ProjectPress */
namespace ProjectPress\Includes\Plugins;
if(!defined('ABSPATH')){exit;}
final class ProjectExtensions{
 private static bool $registered=false;
 public static function register():void{if(self::$registered)return;self::$registered=true;$registry=ProjectExtensionRegistry::get_instance();foreach(Plugins::get_instance()->get_registered_plugins() as$plugin){if($plugin instanceof ProjectExtensionProviderInterface){$plugin->register_project_extensions($registry);}if($plugin instanceof ProjectWorkspaceProviderInterface){foreach($plugin->get_project_workspace_tabs() as$key=>$definition){$registry->add_workspace_tab((string)$key,(array)$definition);}}if($plugin instanceof ProjectRelatedEntityProviderInterface){foreach($plugin->get_project_related_entities() as$key=>$definition){$registry->add_related_entity((string)$key,(array)$definition);}}}do_action('projectpress_project_extensions_registered',$registry,Plugins::get_instance());}
 public static function project_data(array $data,$project):array{return $project instanceof \ProjectPress\Includes\Project\Core\Project?ProjectExtensionRegistry::get_instance()->extend_project_data($data,$project):$data;}
}
