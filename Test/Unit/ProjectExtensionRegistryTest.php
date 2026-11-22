<?php

declare( strict_types=1 );

namespace ProjectPress\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ProjectPress\Includes\Project\Integration\ProjectExtensionRegistry;

final class ProjectExtensionRegistryTest extends TestCase {
    public function testWorkspaceTabRegistrationRequiresRenderableDefinition(): void {
        $registry = ProjectExtensionRegistry::get_instance();
        $this->assertFalse( $registry->add_workspace_tab( 'invalid', array( 'label' => 'Invalid' ) ) );
        $this->assertTrue( $registry->add_workspace_tab( 'example', array( 'label' => 'Example', 'render_callback' => static function (): void {} ) ) );
        $tabs = $registry->workspace_tabs();
        $this->assertArrayHasKey( 'example', $tabs );
        $this->assertSame( 'Example', $tabs['example']['label'] );
        $this->assertIsCallable( $tabs['example']['render_callback'] );
    }

    public function testRelatedEntityRegistrationRequiresTable(): void {
        $registry = ProjectExtensionRegistry::get_instance();
        $this->assertFalse( $registry->add_related_entity( 'invalid', array() ) );
        $this->assertTrue( $registry->add_related_entity( 'example', array( 'table' => 'example_records' ) ) );
        $entities = $registry->related_entities();
        $this->assertSame( 'example_records', $entities['example']['table'] );
    }
}
