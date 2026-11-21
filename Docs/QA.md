# ProjectPress Quality Assurance

Phase 8 validates the initial ProjectPress implementation across the database, domain layer, REST API, extensions, upgrades and documentation.

## Automated commands

- `composer test` runs the configured PHPUnit suite.
- `composer test:unit` runs the unit suite.
- `composer lint` runs PHPCS using `phpcs.xml.dist`.
- `composer lint:docs` checks WordPress documentation standards across PHP source and tests.
- `composer check` runs the configured test and lint commands together.

## Current automated coverage

The ProjectPress unit suite now includes coverage for:

- Project model serialisation and date handling.
- Project query sanitisation and pagination boundaries.
- Core database schema registration and repeated registration.
- Project permission boundaries for public, owner and unprivileged access.
- REST namespace and project representation expectations.
- Upgrade-path assertions for Phase 5 project-owned tables.
- Phase 7 extension workspace ordering and project serializer output.
- Migrated reusable core tests for cron jobs, editors, menus, permalinks, plugin settings, shortcodes and taxonomies.

## Release-candidate manual verification

Before release:

1. Activate ProjectPress on a clean WordPress installation and confirm the schema is installed.
2. Run schema synchronisation repeatedly and confirm no duplicate table registrations or destructive changes.
3. Create, edit, archive, restore and permanently delete projects.
4. Exercise Overview, Details, Members, Tasks, Milestones, Discussions, Notes, Files, Activity and project Settings.
5. Exercise authenticated REST CRUD and related-entity routes with allowed and denied users.
6. Verify permanent project deletion removes project-owned Phase 5 rows.
7. Register one bundled ProjectPress extension and one separately installed WordPress extension.
8. Run `composer check` and resolve all failures before release.

## Notes

The PHPUnit tests provide isolated coverage using the repository test bootstrap. Database installation, `dbDelta`, WordPress REST dispatch and full upgrade migrations still require the WordPress integration environment described by the manual release checks above.
