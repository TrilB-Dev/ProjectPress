# ProjectPress Extensions

ProjectPress supports both bundled modules discovered from its configured plugin directory and separately installed WordPress plugins.

## Registering an external extension

Hook `projectpress_register_plugin` and pass a `PluginInterface` implementation to the supplied `Plugins` registry.

```php
add_action( 'projectpress_register_plugin', static function ( $plugins ) {
    $plugins->register_plugin_instance( MyProjectPressExtension::get_instance() );
} );
```

The extension can implement the existing settings, database, shortcode, assets, admin, REST, frontend and localisation provider interfaces.

## Project-domain extensions

Implement `ProjectExtensionProviderInterface` for direct access to `ProjectExtensionRegistry`, or use the narrower provider interfaces:

- `ProjectWorkspaceProviderInterface` adds tabs to individual project workspaces.
- `ProjectRelatedEntityProviderInterface` publishes related project entities for integration code.

A workspace tab definition requires `label` and a callable `render_callback`. It may also provide `permission_callback` and `priority`.

A related-entity definition requires a registered ProjectPress table key. It can provide `order_by`, `create_callback` and `delete_callback`.

The registry also supports project serializers through `add_project_serializer()`. Serializer output is appended to project REST representations under the serializer key.

## Integration hooks

The extension layer exposes these filters:

- `projectpress_extension_workspace_tabs`
- `projectpress_extension_related_entities`
- `projectpress_rest_project_data`

Existing project lifecycle actions remain available for create, update, archive, restore and delete operations.
