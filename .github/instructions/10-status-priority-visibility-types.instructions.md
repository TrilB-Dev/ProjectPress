# 10. Status, Priority, Visibility and Type Values

Statuses, priorities, visibility values and project types must not be implemented as unrelated free-text strings across the plugin.

Use central definitions supplied by the appropriate ProjectPress domain configuration or `Defaults.php`.

Every supported value should have a stable machine identifier, translatable label, validation rule, fallback where appropriate, optional display configuration and documented lifecycle.

Interfaces, services, repositories and REST schemas must use the same identifiers.
