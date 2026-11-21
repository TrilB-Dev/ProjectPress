<?php

declare( strict_types=1 );
namespace ProjectPress\Tests\Unit;
use PHPUnit\Framework\TestCase;
use ProjectPress\Includes\Core\Schema;
use ProjectPress\Includes\Core\WP\Database;

final class DatabaseSchemaTest extends TestCase {
 public function testCoreSchemaRegistersPhaseFiveTables(): void { Schema::register_tables(); $tables=Database::get_core_tables(); foreach(array('projects','project_term_relationships','teams','team_members','project_teams','project_fields','project_field_values','activity','tasks','milestones','discussions','notes','files','project_settings') as$table){$this->assertArrayHasKey($table,$tables);} }
 public function testSchemaRegistrationIsIdempotent(): void { Schema::register_tables(); $first=array_keys(Database::get_core_tables()); Schema::register_tables(); $this->assertSame($first,array_keys(Database::get_core_tables())); }
 public function testEveryRegisteredSchemaProducesCreateTableSql(): void { Schema::register_tables(); foreach(Database::get_core_tables() as$name=>$schema){$sql=$schema('wp_projectpress_'.$name,'DEFAULT CHARSET=utf8mb4');$this->assertStringStartsWith('CREATE TABLE wp_projectpress_'.$name,$sql);$this->assertStringContainsString('PRIMARY KEY',$sql);} }
}
