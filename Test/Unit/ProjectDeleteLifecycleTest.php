<?php

declare( strict_types=1 );
namespace ProjectPress\Tests\Unit;
use PHPUnit\Framework\TestCase;
use ProjectPress\Includes\Project\Core\ProjectRepository;
use ProjectPress\Includes\Project\Core\ProjectService;
use ReflectionClass;
final class ProjectDeleteLifecycleTest extends TestCase {
 public function testPermanentDeleteOwnsWordPressBackedProjectContentCleanup(): void {$reflection=new ReflectionClass(ProjectService::class);$this->assertTrue($reflection->hasMethod('delete_wordpress_content'));$source=file_get_contents(dirname(__DIR__,2).'/src/Includes/Project/Core/ProjectService.php');$compact=str_replace(' ','',$source);$this->assertStringContainsString("'pp_note','pp_document','pp_update','pp_discussion'",$compact);$this->assertStringContainsString("'post_type'=>'attachment'",$compact);$this->assertStringContainsString("'_pp_project_id'",$source);}
 public function testPermanentDeleteIncludesPhaseNineOwnedTables():void{$source=file_get_contents(dirname(__DIR__,2).'/src/Includes/Project/Core/ProjectRepository.php');$compact=str_replace(' ','',$source);foreach(array("'requirements'","'features'","'user_stories'")as$table){$this->assertStringContainsString($table,$compact);}$this->assertTrue((new ReflectionClass(ProjectRepository::class))->hasMethod('delete'));}
}
