<?php
namespace ProjectPress\Includes\Project\Planning;if(!defined('ABSPATH')){exit;}final class GanttChart{public static function rows(array $items):array{return array_values(array_map(static fn($item)=>is_object($item)&&method_exists($item,'to_array')?$item->to_array():(array)$item,$items));}}
