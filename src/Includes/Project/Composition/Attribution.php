<?php
namespace ProjectPress\Includes\Project\Composition;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class Attribution extends ProjectRecord{public function get_name():string{return(string)$this->get('name','');}public function get_notice():string{return(string)$this->get('notice','');}}
