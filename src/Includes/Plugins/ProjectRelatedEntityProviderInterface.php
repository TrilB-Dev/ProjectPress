<?php
/** Project related-entity provider contract. @package ProjectPress */
namespace ProjectPress\Includes\Plugins;
if(!defined('ABSPATH')){exit;}
interface ProjectRelatedEntityProviderInterface{
 /** @return array<string,array<string,mixed>> */
 public function get_project_related_entities():array;
}
