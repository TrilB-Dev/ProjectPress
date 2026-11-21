<?php
namespace ProjectPress\Includes\Project\Composition;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class Licence extends ProjectRecord{public function get_identifier():string{return(string)$this->get('identifier','');}public function get_name():string{return(string)$this->get('name','');}}
