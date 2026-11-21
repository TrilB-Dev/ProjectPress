<?php

declare( strict_types=1 );

namespace {
    if ( ! defined( 'ARRAY_A' ) ) { define( 'ARRAY_A', 'ARRAY_A' ); }
    if ( ! defined( 'OBJECT' ) ) { define( 'OBJECT', 'OBJECT' ); }
    if ( ! class_exists( 'WP_Error' ) ) { final class WP_Error { private string $code; public function __construct(string $code){$this->code=$code;} public function get_error_code():string{return$this->code;} } }
    $GLOBALS['projectpress_test_taxonomies']=array();$GLOBALS['projectpress_test_terms']=array();$GLOBALS['projectpress_test_object_terms']=array();$GLOBALS['projectpress_test_filters']=array();$GLOBALS['projectpress_test_next_term_id']=1;
    $GLOBALS['wpdb']=new class {public string $prefix='wp_';public function get_results(string $query,$output=null):array{return array();}};
    if(!function_exists('sanitize_title')){function sanitize_title($title,$fallback_title='',$context='save'):string{$slug=strtolower(trim(preg_replace('/[^a-z0-9]+/','-',(string)$title),'-'));return''!==$slug?$slug:(string)$fallback_title;}}
    if(!function_exists('sanitize_key')){function sanitize_key($key):string{return strtolower(preg_replace('/[^a-z0-9_\-]/','',(string)$key));}}
    if(!function_exists('sanitize_text_field')){function sanitize_text_field($text):string{return trim((string)$text);}}
    if(!function_exists('absint')){function absint($value):int{return abs((int)$value);}}
    if(!function_exists('__')){function __($text,$domain=null):string{return(string)$text;}}
    if(!function_exists('taxonomy_exists')){function taxonomy_exists($taxonomy):bool{return isset($GLOBALS['projectpress_test_taxonomies'][(string)$taxonomy]);}}
    if(!function_exists('post_type_exists')){function post_type_exists($post_type):bool{return isset($GLOBALS['projectpress_test_post_types'][(string)$post_type]);}}
    if(!function_exists('register_term_meta')){function register_term_meta($taxonomy,$meta_key,$args=array()):void{$GLOBALS['projectpress_test_term_meta'][(string)$taxonomy][(string)$meta_key]=$args;}}
    if(!function_exists('maybe_unserialize')){function maybe_unserialize($value){return$value;}}
    if(!function_exists('is_wp_error')){function is_wp_error($thing):bool{return$thing instanceof WP_Error;}}
    if(!function_exists('register_taxonomy')){function register_taxonomy($taxonomy,$object_type,$args=array()){$GLOBALS['projectpress_test_taxonomies'][$taxonomy]=array('object_type'=>$object_type,'args'=>$args);return(object)array('name'=>$taxonomy);}}
    if(!function_exists('add_filter')){function add_filter($hook,$callback,$priority=10,$accepted_args=1):bool{$GLOBALS['projectpress_test_filters'][$hook][]=array('callback'=>$callback,'priority'=>$priority,'accepted_args'=>$accepted_args);return true;}}
    if(!function_exists('apply_filters')){function apply_filters($hook,$value,...$args){foreach($GLOBALS['projectpress_test_filters'][$hook]??array() as$filter){$filter_args=array_slice(array_merge(array($value),$args),0,$filter['accepted_args']);$value=$filter['callback'](...$filter_args);}return$value;}}
    if(!function_exists('term_exists')){function term_exists($term,$taxonomy='',$parent_term=null){foreach($GLOBALS['projectpress_test_terms'][$taxonomy]??array() as$stored){if((string)$stored->slug===(string)$term||(int)$stored->term_id===(int)$term){if(null===$parent_term||(int)$stored->parent===(int)$parent_term)return array('term_id'=>$stored->term_id);}}return 0;}}
    if(!function_exists('wp_insert_term')){function wp_insert_term($term,$taxonomy,$args=array()){$slug=$args['slug']??sanitize_title($term);$existing=term_exists($slug,$taxonomy);if($existing)return$existing;$id=$GLOBALS['projectpress_test_next_term_id']++;$GLOBALS['projectpress_test_terms'][$taxonomy][]=(object)array('term_id'=>$id,'slug'=>$slug,'name'=>$term,'parent'=>(int)($args['parent']??0));return array('term_id'=>$id);}}
    if(!function_exists('get_term')){function get_term($term,$taxonomy='',$output=OBJECT,$filter='raw'){foreach($GLOBALS['projectpress_test_terms'][$taxonomy]??array() as$stored){if((int)$stored->term_id===(int)$term)return$stored;}return null;}}
    if(!function_exists('wp_get_object_terms')){function wp_get_object_terms($object_ids,$taxonomies,$args=array()){$ids=$GLOBALS['projectpress_test_object_terms'][(int)$object_ids][(string)$taxonomies]??array();return'ids'===($args['fields']??'')?$ids:array();}}
}

namespace ProjectPress\Tests\Unit {
 use PHPUnit\Framework\TestCase;use ProjectPress\Includes\Core\Taxonomy;
 final class TaxonomyTest extends TestCase{
  protected function setUp():void{parent::setUp();$GLOBALS['projectpress_test_taxonomies']=array();$GLOBALS['projectpress_test_terms']=array();$GLOBALS['projectpress_test_object_terms']=array();$GLOBALS['projectpress_test_filters']=array();$GLOBALS['projectpress_test_term_meta']=array();$GLOBALS['projectpress_test_next_term_id']=1;}
  public function testCreateRegistersGenericTaxonomyDefinitions():void{$this->assertTrue(Taxonomy::create('projectpress_project_group',array('object_type'=>array('projectpress_project'),'hierarchical'=>true)));$this->assertTrue(Taxonomy::create('projectpress_project_tag',array('object_type'=>array('projectpress_project'),'hierarchical'=>false)));$this->assertContains('projectpress_project_group',Taxonomy::get_taxonomy_names());$this->assertContains('projectpress_project_tag',Taxonomy::get_taxonomy_names());$this->assertSame(array('projectpress_project'),$GLOBALS['projectpress_test_taxonomies']['projectpress_project_group']['object_type']);}
  public function testDynamicallyCreateNormalizesConfigurationAndSlug():void{$result=Taxonomy::dynamically_create(array('taxonomy'=>'Project Group','object_type'=>array('projectpress_project'),'hierarchical'=>true,'public'=>true));$this->assertTrue($result);$this->assertArrayHasKey('project_group',$GLOBALS['projectpress_test_taxonomies']);$this->assertSame(array('projectpress_project'),$GLOBALS['projectpress_test_taxonomies']['project_group']['object_type']);$this->assertTrue($GLOBALS['projectpress_test_taxonomies']['project_group']['args']['public']);}
  public function testCreateRejectsEmptyAndDuplicateValues():void{$this->assertFalse(Taxonomy::create('',array()));$this->assertTrue(Taxonomy::create('projectpress_project_group',array('object_type'=>array('projectpress_project'))));$this->assertFalse(Taxonomy::create('projectpress_project_group',array('object_type'=>array('projectpress_project'))));}
 }
}
