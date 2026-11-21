<?php
/** Project-domain extension provider contracts. @package ProjectPress */
namespace ProjectPress\Includes\Plugins;
if ( ! defined( 'ABSPATH' ) ) { exit; }

interface ProjectExtensionProviderInterface {
	/** Register project workspace, related-entity and serializer extensions. */
	public function register_project_extensions( ProjectExtensionRegistry $registry ): void;
}

interface ProjectWorkspaceProviderInterface {
	/** @return array<string, array<string, mixed>> */
	public function get_project_workspace_tabs(): array;
}

interface ProjectRelatedEntityProviderInterface {
	/** @return array<string, array<string, mixed>> */
	public function get_project_related_entities(): array;
}
