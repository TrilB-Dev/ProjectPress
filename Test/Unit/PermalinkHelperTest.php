<?php
declare(strict_types=1);
namespace ProjectPress\Tests\Unit;
use PHPUnit\Framework\TestCase;use ProjectPress\Includes\Functions\Helpers\PermalinkHelper;
final class PermalinkHelperTest extends TestCase{public function testCustomAliasesAreNormalizedForReusablePatterns():void{$aliases=array('%project%'=>'%object%','%project_category%'=>'%object_category%','%project_tag%'=>'%object_tag%','%project_page%'=>'%object_slug%');$pattern='%root%/%project_category%/%project_tag%/%project_page%';$this->assertSame('%root%/%object_category%/%object_tag%/%object_slug%',PermalinkHelper::sanitize_pattern($pattern,$aliases));}public function testUnknownTokensRemainAvailableForExtensionDefinitions():void{$pattern='%custom_root%/%custom_category%/%custom_page%';$this->assertSame($pattern,PermalinkHelper::sanitize_pattern($pattern));}}
