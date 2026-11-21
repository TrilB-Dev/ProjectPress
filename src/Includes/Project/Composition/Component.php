<?php
namespace ProjectPress\Includes\Project\Composition;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class Component extends ProjectRecord{public function get_name():string{return(string)$this->get('name','');}public function get_version():string{return(string)$this->get('version','');}}
