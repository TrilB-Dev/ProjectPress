<?php

declare(strict_types=1);
namespace ProjectPress\Tests\Unit;
use PHPUnit\Framework\TestCase;use ProjectPress\Includes\Project\Core\ProjectPlan;use ProjectPress\Includes\Project\Planning\GanttChart;use ProjectPress\Includes\Project\Planning\RoadMap;use ProjectPress\Includes\Project\Requirements\Requirement;use ProjectPress\Includes\Project\Work\Feature;use ProjectPress\Includes\Project\Work\Task;use ProjectPress\Includes\Project\Work\UserStory;use ProjectPress\Includes\Project\Workflow\Kanban;use ProjectPress\Includes\Project\Workflow\Workflow;
final class PhaseNinePlanningTest extends TestCase{
 public function testPlanComposesCanonicalWork():void{$plan=new ProjectPlan(8,array(),array(new Task(array('project_id'=>8,'status'=>'todo'))),array(new Requirement(array('project_id'=>8,'status'=>'draft'))),array(new Feature(array('project_id'=>8,'status'=>'planned'))),array(new UserStory(array('project_id'=>8,'status'=>'backlog'))));$this->assertSame(8,$plan->get_project_id());$this->assertCount(4,$plan->planning_items());}
 public function testPlanningViewsReuseCanonicalStatuses():void{$items=array(array('title'=>'A','status'=>'todo','due_date'=>'2026-12-02'),array('title'=>'B','status'=>'in_progress','due_date'=>'2026-12-01'));$this->assertArrayHasKey('todo',Kanban::columns($items));$this->assertArrayHasKey('in_progress',RoadMap::group_by_status($items));$this->assertSame('B',GanttChart::rows($items)[0]['title']);}
 public function testWorkflowTransitionsAreExplicit():void{$workflow=Workflow::project_work();$this->assertTrue($workflow->can_transition('todo','in_progress'));$this->assertFalse($workflow->can_transition('done','todo'));}
}
