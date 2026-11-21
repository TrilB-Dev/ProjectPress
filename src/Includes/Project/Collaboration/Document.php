<?php
namespace ProjectPress\Includes\Project\Collaboration;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class Document extends ProjectRecord{public function get_title():string{return(string)$this->get('title','');}public function get_content():string{return(string)$this->get('content','');}}
