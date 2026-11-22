<?php
/** Project workspace provider contract. @package ProjectPress */
namespace ProjectPress\Includes\Project\Integration;
if(!defined('ABSPATH')){exit;}
interface ProjectWorkspaceProviderInterface{
 /** @return array<string,array<string,mixed>> */
 public function get_project_workspace_tabs():array;
}
