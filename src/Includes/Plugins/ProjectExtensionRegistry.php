<?php
/**
 * Registry for ProjectPress project-domain extension points.
 *
 * @package ProjectPress
 */
namespace ProjectPress\Includes\Plugins;

use ProjectPress\Includes\Project\Core\Project;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class ProjectExtensionRegistry {
	/** @var array<string, array<string, mixed>> */
	private array $workspace_tabs = array();
	/** @var array<string, array<string, mixed>> */
	private array $related_entities = array();
	/** @var array<string, callable> */
	private array $serializers = array();

	public function add_workspace_tab( string $key, array $definition ): bool {
		$key = sanitize_key( $key );
		if ( '' === $key || ! isset( $definition['label'], $definition['render_callback'] ) || ! is_callable( $definition['render_callback'] ) ) { return false; }
		$this->workspace_tabs[ $key ] = array(
			'label' => sanitize_text_field( (string) $definition['label'] ),
			'render_callback' => $definition['render_callback'],
			'permission_callback' => isset( $definition['permission_callback'] ) && is_callable( $definition['permission_callback'] ) ? $definition['permission_callback'] : null,
			'priority' => (int) ( $definition['priority'] ?? 100 ),
		);
		return true;
	}

	public function add_related_entity( string $key, array $definition ): bool {
		$key = sanitize_key( $key );
		if ( '' === $key || empty( $definition['table'] ) ) { return false; }
		$this->related_entities[ $key ] = array(
			'table' => sanitize_key( (string) $definition['table'] ),
			'order_by' => sanitize_text_field( (string) ( $definition['order_by'] ?? 'created_at DESC' ) ),
			'create_callback' => isset( $definition['create_callback'] ) && is_callable( $definition['create_callback'] ) ? $definition['create_callback'] : null,
			'delete_callback' => isset( $definition['delete_callback'] ) && is_callable( $definition['delete_callback'] ) ? $definition['delete_callback'] : null,
		);
		return true;
	}

	public function add_project_serializer( string $key, callable $callback ): bool {
		$key = sanitize_key( $key ); if ( '' === $key ) { return false; } $this->serializers[ $key ] = $callback; return true;
	}

	public function workspace_tabs(): array { $tabs=$this->workspace_tabs; uasort($tabs,static fn($a,$b)=>$a['priority']<=>$b['priority']); return apply_filters('projectpress_extension_workspace_tabs',$tabs); }
	public function related_entities(): array { return apply_filters('projectpress_extension_related_entities',$this->related_entities); }
	public function extend_project_data( array $data, Project $project ): array { foreach($this->serializers as $key=>$callback){$data[$key]=call_user_func($callback,$project);} return apply_filters('projectpress_rest_project_data',$data,$project); }
}
