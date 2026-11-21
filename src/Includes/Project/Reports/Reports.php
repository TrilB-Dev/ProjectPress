<?php
namespace ProjectPress\Includes\Project\Reports;if(!defined('ABSPATH')){exit;}final class Reports{public static function count_by(array $rows,string $field):array{$counts=array();foreach($rows as$row){$row=is_object($row)&&method_exists($row,'to_array')?$row->to_array():(array)$row;$key=(string)($row[$field]??'unknown');$counts[$key]=($counts[$key]??0)+1;}return$counts;}}
