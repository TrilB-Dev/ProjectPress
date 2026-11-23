<?php

declare( strict_types=1 );
namespace ProjectPress\Tests\Unit;
use PHPUnit\Framework\TestCase;use ProjectPress\API\Routes;use ProjectPress\Includes\Project\Core\Project;
final class RestRoutesTest extends TestCase{
 public function testNamespaceIsVersioned():void{$this->assertSame('projectpress/v1',Routes::NAMESPACE);}
 public function testProjectModelProvidesRestSafeScalarRepresentation():void{$data=(new Project(array('id'=>5,'name'=>'REST Project','key'=>'REST','status'=>'active')))->to_array();$this->assertSame(5,$data['id']);$this->assertSame('REST Project',$data['name']);$this->assertSame('REST',$data['key']);$this->assertIsArray($data);}
 public function testPhaseNineRestHandlersAreAvailable():void{foreach(array('requirements_index','requirement_create','requirement_update','requirement_delete','features_index','feature_create','feature_update','feature_delete','stories_index','story_create','story_update','story_delete')as$method){$this->assertTrue(method_exists(Routes::class,$method),$method.' handler must exist.');}}
}
