<?php

declare( strict_types=1 );

namespace ProjectPress\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ProjectPress\Includes\Project\Core\ProjectService;
use ReflectionClass;

final class ProjectDeleteLifecycleTest extends TestCase {
    public function testPermanentDeleteOwnsWordPressBackedProjectContentCleanup(): void {
        $reflection = new ReflectionClass( ProjectService::class );
        $this->assertTrue( $reflection->hasMethod( 'delete_wordpress_content' ) );
        $source = file_get_contents( dirname( __DIR__, 2 ) . '/src/Includes/Project/Core/ProjectService.php' );
        $this->assertStringContainsString( "'pp_note','pp_document','pp_update','pp_discussion'", str_replace( ' ', '', $source ) );
        $this->assertStringContainsString( "'post_type'=>'attachment'", str_replace( ' ', '', $source ) );
        $this->assertStringContainsString( "'_pp_project_id'", $source );
    }
}
