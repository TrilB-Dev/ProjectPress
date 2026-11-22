<?php
/** Direct project-extension provider contract. @package ProjectPress */
namespace ProjectPress\Includes\Project\Integration;
if(!defined('ABSPATH')){exit;}
interface ProjectExtensionProviderInterface{
 public function register_project_extensions(ProjectExtensionRegistry $registry):void;
}
