<?php
namespace ProjectPress\Includes\Project\Collaboration;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class Attachment extends ProjectRecord{public function get_attachment_id():int{return absint($this->get('attachment_id',0));}public function get_label():string{return(string)$this->get('file_label','');}}
