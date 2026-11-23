<?php
namespace ProjectPress\Includes\Project\Planning;
if(!defined('ABSPATH')){exit;}
final class GanttChart{public static function rows(array $items):array{$rows=array_map(static fn($item)=>is_object($item)&&method_exists($item,'to_array')?$item->to_array():(array)$item,$items);usort($rows,static function(array $a,array $b):int{$ad=(string)($a['target_date']??$a['due_date']??$a['target_end_date']??'9999-12-31');$bd=(string)($b['target_date']??$b['due_date']??$b['target_end_date']??'9999-12-31');return strcmp($ad,$bd);});return array_values($rows);}}
