<?php
namespace ProjectPress\Includes\Project\Planning;if(!defined('ABSPATH')){exit;}final class RoadMap{public static function group_by_status(array $items):array{$groups=array();foreach($items as$item){$row=is_object($item)&&method_exists($item,'to_array')?$item->to_array():(array)$item;$status=(string)($row['status']??'unscheduled');$groups[$status][]=$row;}return$groups;}}
