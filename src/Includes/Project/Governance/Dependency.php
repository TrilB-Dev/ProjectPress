<?php
namespace ProjectPress\Includes\Project\Governance;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class Dependency extends ProjectRecord{public function get_name():string{return(string)$this->get('name','');}public function get_status():string{return(string)$this->get('status','open');}}
