<?php
namespace ProjectPress\Includes\Project\Work;if(!defined('ABSPATH')){exit;}final class Backlog{public static function from_tasks(array $tasks):array{return array_values(array_filter($tasks,static function($task){$status=is_object($task)&&method_exists($task,'get_status')?$task->get_status():(string)($task['status']??'');return!in_array($status,array('done','completed'),true);}));}}
