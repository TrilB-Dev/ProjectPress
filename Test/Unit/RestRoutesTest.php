<?php

declare( strict_types=1 );
namespace ProjectPress\Tests\Unit;
use PHPUnit\Framework\TestCase;use ProjectPress\API\Routes;use ProjectPress\Includes\Project\Core\Project;
final class RestRoutesTest extends TestCase{
 public function testNamespaceIsVersioned():void{$this->assertSame('projectpress/v1',Routes::NAMESPACE);}
 public function testProjectModelProvidesRestSafeScalarRepresentation():void{$data=(new Project(array('id'=>5,'name'=>'REST Project','key'=>'REST','status'=>'active')))->to_array();$this->assertSame(5,$data['id']);$this->assertSame('REST Project',$data['name']);$this->assertSame('REST',$data['key']);$this->assertIsArray($data);}
}
