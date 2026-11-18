# 4. Authoritative Database Architecture

All ProjectPress database rules, table registration, schema declarations and database defaults must be managed through:

- `src/Includes/Core/WP/Database.php`
- `src/Includes/Core/Schema.php`
- `src/Includes/Core/Defaults.php`

These files form the authoritative database layer. No other class may create an independent schema manager, table registry, upgrade runner or default-data installation system.

## 4.1 Database.php

`Database.php` is responsible for registering core custom tables, resolving complete table names, applying the WordPress database prefix, coordinating creation/update operations, enforcing table-registration rules, providing registered table information, supporting installation/upgrades, coordinating database-version checks where required, and providing approved database entry points for repositories and services.

New ProjectPress tables must be registered through this class. Do not create tables directly from activation hooks, domain models, admin controllers, REST controllers, AJAX handlers, request callbacks, templates, project managers or project screens.

## 4.2 Schema.php

`Schema.php` is responsible for declaring each ProjectPress custom table, columns and SQL types, primary/unique keys, indexes, nullable/default values, and schema definitions expected by `Database.php`.

All schema changes must be made here and applied through the database-management workflow. Do not scatter SQL schema strings throughout the plugin. Schema definitions must not contain business logic, request handling or UI behaviour.

## 4.3 Defaults.php

`Defaults.php` is responsible for canonical default values and initial database data, including default project values, statuses, priorities, visibility/state values, storage settings, required initial records and safe fallback values.

Default data must be safe to apply repeatedly and must not overwrite user-modified records during normal upgrade operations.

## 4.4 Database Change Workflow

When a storage change is required:

1. Update `Schema.php`.
2. Update `Database.php` registration or management rules if required.
3. Update `Defaults.php` if canonical defaults change.
4. Update the database/schema version through the established mechanism.
5. Add an upgrade/migration when existing data must be transformed.
6. Update affected repositories/services.
7. Update validation, REST schemas and interfaces.
8. Verify clean installation and upgrade from the previous schema.
9. Document the change.

Avoid destructive schema operations unless explicitly required and migration behaviour has been defined.
