<?php
namespace ProjectPress\Includes\Project\Workflow;if(!defined('ABSPATH')){exit;}final class Kanban{public static function columns(array $items):array{$columns=array();foreach($items as$item){$row=is_object($item)&&method_exists($item,'to_array')?$item->to_array():(array)$item;$status=(string)($row['status']??'todo');$columns[$status][]=$row;}return$columns;}}
