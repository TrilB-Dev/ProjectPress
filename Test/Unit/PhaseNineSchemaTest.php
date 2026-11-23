<?php

declare(strict_types=1);
namespace ProjectPress\Tests\Unit;
use PHPUnit\Framework\TestCase;
use ProjectPress\Includes\Core\Schema;
use ProjectPress\Includes\Core\WP\Database;
final class PhaseNineSchemaTest extends TestCase{
 public function testRequirementsFeaturesAndStoriesAreRegistered():void{Schema::register_tables();$tables=Database::get_core_tables();foreach(array('requirements','features','user_stories')as$table){$this->assertArrayHasKey($table,$tables);}}
}
