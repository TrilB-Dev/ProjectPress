<?php

declare(strict_types=1);
namespace ProjectPress\Tests\Unit;
use PHPUnit\Framework\TestCase;
use ProjectPress\Includes\Core\Schema;
use ProjectPress\Includes\Core\WP\Database;
use ProjectPress\Includes\Project\Requirements\Requirement;
use ProjectPress\Includes\Project\Work\Backlog;
use ProjectPress\Includes\Project\Work\Feature;
use ProjectPress\Includes\Project\Work\UserStory;
final class PhaseNineSchemaTest extends TestCase{
 public function testRequirementsFeaturesAndStoriesAreRegistered():void{Schema::register_tables();$tables=Database::get_core_tables();foreach(array('requirements','features','user_stories')as$table){$this->assertArrayHasKey($table,$tables);}}
 public function testBacklogExcludesTerminalPhaseNineRecords():void{$items=array(new Requirement(array('status'=>'draft')),new Requirement(array('status'=>'rejected')),new Feature(array('status'=>'planned')),new Feature(array('status'=>'completed')),new UserStory(array('status'=>'backlog')),new UserStory(array('status'=>'cancelled')));$this->assertCount(3,Backlog::open($items));}
 public function testPhaseNineModelsPreserveProjectScope():void{$requirement=new Requirement(array('id'=>1,'project_id'=>9,'title'=>'Requirement'));$feature=new Feature(array('id'=>2,'project_id'=>9,'title'=>'Feature'));$story=new UserStory(array('id'=>3,'project_id'=>9,'title'=>'Story','points'=>5));foreach(array($requirement,$feature,$story)as$item){$this->assertSame(9,$item->get_project_id());}$this->assertSame(5,$story->get_points());}
}
