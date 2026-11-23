<?php

declare(strict_types=1);
namespace ProjectPress\Tests\Unit;
use PHPUnit\Framework\TestCase;use ProjectPress\Includes\Core\Schema;use ProjectPress\Includes\Core\WP\Database;use ProjectPress\Includes\Project\Governance\DecisionLog;use ProjectPress\Includes\Project\Governance\Dependency;use ProjectPress\Includes\Project\Governance\Issue;use ProjectPress\Includes\Project\Governance\Risk;
final class PhaseNineGovernanceTest extends TestCase{
 public function testGovernanceTablesAreRegistered():void{Schema::register_tables();$tables=Database::get_core_tables();foreach(array('risks','issues','dependencies','decisions')as$table){$this->assertArrayHasKey($table,$tables);}}
 public function testGovernanceModelsRemainProjectScoped():void{$models=array(new Risk(array('project_id'=>7,'title'=>'Risk')),new Issue(array('project_id'=>7,'title'=>'Issue')),new Dependency(array('project_id'=>7,'title'=>'Dependency')),new DecisionLog(array('project_id'=>7,'title'=>'Decision')));foreach($models as$model){$this->assertSame(7,$model->get_project_id());}}
}
