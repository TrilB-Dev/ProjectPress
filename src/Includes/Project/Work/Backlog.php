<?php
namespace ProjectPress\Includes\Project\Work;
if(!defined('ABSPATH')){exit;}
final class Backlog{
 public static function open(array $items):array{return array_values(array_filter($items,static function($item){$status=is_object($item)&&method_exists($item,'get_status')?$item->get_status():(string)($item['status']??'');return!in_array($status,array('done','completed','cancelled','rejected'),true);}));}
 public static function from_tasks(array $tasks):array{return self::open($tasks);}
}
