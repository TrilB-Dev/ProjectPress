<?php

declare( strict_types=1 );

namespace ProjectPress\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ProjectPress\Includes\Project\Integration\ProjectExtensionRegistry;
use ProjectPress\Includes\Project\Core\Project;

final class ProjectExtensionRegistryTest extends TestCase {
    public function testWorkspaceTabsArePrioritised(): void {
        $registry = ProjectExtensionRegistry::get_instance();
        $registry->add_workspace_tab( 'later', [ 'label' => 'Later', 'render_callback' => static function (): void {}, 'priority' => 50 ] );
        $registry->add_workspace_tab( 'earlier', [ 'label' => 'Earlier', 'render_callback' => static function (): void {}, 'priority' => 10 ] );
        $keys = array_keys( $registry->workspace_tabs() );
        $this->assertLessThan( array_search( 'later', $keys, true ), array_search( 'earlier', $keys, true ) );
    }

    public function testProjectSerialiserAddsExtensionData(): void {
        $registry = ProjectExtensionRegistry::get_instance();
        $registry->add_project_serializer( 'qa', static fn ( Project $project ): array => [ 'project_id' => $project->get_id() ] );
        $data = $registry->extend_project_data( [ 'id' => 9 ], new Project( [ 'id' => 9, 'name' => 'QA' ] ) );
        $this->assertSame( [ 'project_id' => 9 ], $data['qa'] );
    }
}
