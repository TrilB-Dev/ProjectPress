<?php

declare( strict_types=1 );
namespace ProjectPress\Tests\Unit;
use PHPUnit\Framework\TestCase;use ProjectPress\Includes\Core\Schema;use ProjectPress\Includes\Core\WP\Database;
final class UpgradePathTest extends TestCase{
 public function testCurrentSchemaContainsAllProjectOwnedTablesNeededForDeleteLifecycle():void{Schema::register_tables();$registered=array_keys(Database::get_core_tables());foreach(array('activity','tasks','milestones','discussions','notes','files','project_settings') as$table){$this->assertContains($table,$registered);}}
 public function testRepeatedSchemaRegistrationDoesNotAddDuplicateTableKeys():void{Schema::register_tables();Schema::register_tables();$keys=array_keys(Database::get_core_tables());$this->assertSame($keys,array_values(array_unique($keys)));}
}
