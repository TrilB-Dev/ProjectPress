<?php

declare( strict_types=1 );
namespace ProjectPress\Tests\Unit;
use PHPUnit\Framework\TestCase;use ProjectPress\Includes\Project\Core\Project;use ProjectPress\Includes\Project\Core\ProjectPermissions;
final class ProjectPermissionsTest extends TestCase{
 protected function setUp():void{parent::setUp();$GLOBALS['projectpress_test_user_id']=7;$GLOBALS['projectpress_test_caps']=array();}
 public function testPublicProjectCanBeViewedWithoutCapability():void{$project=new Project(array('id'=>1,'visibility'=>'public','owner_id'=>2));$this->assertTrue((new ProjectPermissions())->can_view($project,7));}
 public function testOwnerCanEditOwnProject():void{$project=new Project(array('id'=>1,'visibility'=>'private','owner_id'=>7));$this->assertTrue((new ProjectPermissions())->can_edit($project,7));}
 public function testUnprivilegedUserCannotEditAnotherUsersProject():void{$project=new Project(array('id'=>1,'visibility'=>'private','owner_id'=>8));$this->assertFalse((new ProjectPermissions())->can_edit($project,7));}
}
