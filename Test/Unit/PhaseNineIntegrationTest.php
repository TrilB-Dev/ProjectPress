<?php

declare(strict_types=1);
namespace ProjectPress\Tests\Unit;
use PHPUnit\Framework\TestCase;
final class PhaseNineIntegrationTest extends TestCase{
 public function testPhaseNineWorkspaceIntegrationsAreRegistered():void{$source=file_get_contents(dirname(__DIR__,2).'/src/Admin/Manager/Projects/ProjectManager.php');foreach(array("'planning'=>'Planning'","'governance'=>'Governance'","'quality'=>'Quality'","'delivery'=>'Delivery'","'people-communication'=>'People & Communication'","'reporting'=>'Reporting'","'composition'=>'Composition'")as$tab)$this->assertStringContainsString($tab,$source);}
 public function testPhaseNineRestBootstrapsAreRegistered():void{$source=file_get_contents(dirname(__DIR__,2).'/src/Plugin.php');foreach(array('GovernanceRoutes','QualityRoutes','DeliveryRoutes','PeopleCommunicationRoutes','ReportingRoutes','CompositionRoutes')as$route)$this->assertStringContainsString($route,$source);}
 public function testSpdxSpecificImplementationIsNotBootstrappedInCore():void{$source=file_get_contents(dirname(__DIR__,2).'/src/Plugin.php');$this->assertStringNotContainsString('SPDXRoutes',$source);$this->assertStringNotContainsString('SpdxRoutes',$source);}
}
