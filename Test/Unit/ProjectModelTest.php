<?php

declare( strict_types=1 );

namespace ProjectPress\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ProjectPress\Includes\Project\Core\Project;

final class ProjectModelTest extends TestCase {
    public function testProjectSerialisesCanonicalFields(): void {
        $project = new Project( [
            'id' => 42,
            'name' => 'Project Alpha',
            'slug' => 'project-alpha',
            'key' => 'ALPHA',
            'description' => 'Test project',
            'owner_id' => 7,
            'status' => 'active',
            'priority' => 'high',
            'visibility' => 'private',
            'start_date' => '2026-11-01 12:00:00',
        ] );

        $data = $project->to_array();
        $this->assertSame( 42, $data['id'] );
        $this->assertSame( 'Project Alpha', $data['name'] );
        $this->assertSame( 'ALPHA', $data['key'] );
        $this->assertSame( 7, $data['owner_id'] );
        $this->assertSame( 'high', $data['priority'] );
        $this->assertSame( '2026-11-01 12:00:00', $data['start_date'] );
    }

    public function testInvalidOrEmptyDatesBecomeNull(): void {
        $project = new Project( [ 'name' => 'Dates', 'start_date' => '', 'target_end_date' => 'not-a-date' ] );
        $this->assertNull( $project->get_start_date() );
        $this->assertNull( $project->get_target_end_date() );
    }
}
