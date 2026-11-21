<?php

declare( strict_types=1 );

namespace ProjectPress\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ProjectPress\Includes\Project\Core\ProjectQuery;

final class ProjectQueryTest extends TestCase {
    public function testPaginationIsClampedAndOrderDefaultsToDescending(): void {
        $query = new ProjectQuery( [ 'page' => 0, 'per_page' => 999, 'order' => 'bad' ] );
        $this->assertSame( 1, $query->get( 'page' ) );
        $this->assertSame( 100, $query->get( 'per_page' ) );
        $this->assertSame( 'DESC', $query->get( 'order' ) );
    }

    public function testSupportedArgumentsAreNormalised(): void {
        $query = new ProjectQuery( [ 'status' => 'In_Progress', 'owner_id' => '15', 'order' => 'asc' ] );
        $this->assertSame( 'in_progress', $query->get( 'status' ) );
        $this->assertSame( 15, $query->get( 'owner_id' ) );
        $this->assertSame( 'ASC', $query->get( 'order' ) );
    }
}
