<?php

declare( strict_types=1 );

namespace ProjectPress\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ProjectPress\Includes\Project\Integration\ProjectExtensionRegistry;

final class ProjectExtensionRegistryTest extends TestCase {
    public function testWorkspaceTabRegistrationRequiresRenderableDefinition(): void {
        $registry = ProjectExtensionRegistry::get_instance();
        $this->assertFalse( $registry->add_workspace_tab( 'invalid', array( 'label' => 'Invalid' ) ) );
        $this->assertTrue( $registry->add_workspace_tab( 'example', array( 'label' => 'Example', 'render_callback' => static function (): void {}, 'priority' => 25 ) ) );
        $tabs = $registry->workspace_tabs();
        $this->assertArrayHasKey( 'example', $tabs );
        $this->assertSame( 'Example', $tabs['example']['label'] );
        $this->assertSame( 25, $tabs['example']['priority'] );
        $this->assertIsCallable( $tabs['example']['render_callback'] );
    }

    public function testRelatedEntityRegistrationRequiresTableAndSanitisesIt(): void {
        $registry = ProjectExtensionRegistry::get_instance();
        $this->assertFalse( $registry->add_related_entity( 'invalid', array() ) );
        $this->assertTrue( $registry->add_related_entity( 'example', array( 'table' => 'Example Records' ) ) );
        $entities = $registry->related_entities();
        $this->assertSame( 'examplerecords', $entities['example']['table'] );
    }
}
