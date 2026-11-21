<?php
namespace ProjectPress\Includes\Project\Composition;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class Provenance extends ProjectRecord{public function get_source():string{return(string)$this->get('source','');}public function get_reference():string{return(string)$this->get('reference','');}}
