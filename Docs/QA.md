# ProjectPress Quality Assurance

Phase 8 validates the initial ProjectPress build rather than adding another product feature layer.

## Automated checks

Composer exposes these commands:

- `composer test` runs PHPUnit using `phpunit.xml.dist`.
- `composer test:unit` runs the unit suite.
- `composer lint` runs PHPCS using `phpcs.xml.dist`.
- `composer lint:docs` checks WordPress documentation standards across PHP source and tests.
- `composer check` runs the test and lint commands together.

## Phase 8 test areas

The QA pass should cover:

1. Database installation, repeated schema synchronisation and upgrade paths.
2. Project model, query, repository and deletion behaviour.
3. Project service lifecycle and permission boundaries.
4. REST project CRUD, task/member/related-entity routes, schemas and errors.
5. Phase 7 extension registrations and project REST serializers.
6. Existing admin and project workspace behaviour.

## Manual verification

Before a release candidate:

- Activate ProjectPress on a clean WordPress installation and confirm all registered tables are created.
- Run the database synchroniser again and confirm it is safe to repeat.
- Create, edit, archive, restore and permanently delete a project.
- Exercise every project workspace tab.
- Exercise authenticated REST routes with allowed and denied users.
- Register one bundled extension and one separately installed WordPress extension.
- Run `composer check` and resolve failures before release.

## Legacy-template cleanup

Existing tests and documentation that still reference the previous template/plugin identity should be migrated to `ProjectPress` as part of Phase 8. They should not be treated as authoritative ProjectPress coverage until migrated.
