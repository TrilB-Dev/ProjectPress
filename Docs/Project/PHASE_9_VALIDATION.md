# Phase 9 Integration and Validation

Phase 9 completes the expanded ProjectPress project-domain integration using the established architecture.

## Integration contract

User-facing project entities follow the owning path:

`domain model -> repository/storage -> service/validation -> ProjectPermissions -> REST/UI -> focused tests`

Read models such as reporting and composition snapshots derive from canonical persisted records rather than introducing duplicate sources of truth.

## Integrated areas

- Planning and work: requirements, features, user stories, backlog/planning views, tasks and milestones.
- Governance: risks, issues, dependencies and decisions.
- Quality: bugs and test cases.
- Delivery: sprints, releases, deployments and environments.
- People and communication: existing teams/members remain authoritative; project timesheets and notifications add project-scoped records.
- Reporting: derives project totals and status breakdowns from canonical repositories.
- Composition: components, packages, assets, licences, attribution, provenance, relationships and generic SBOM records.

## Composition and SBOM boundary

ProjectPress core owns the format-neutral composition model and snapshot API. It does not own SPDX-specific import, export or generation. Format-specific behaviour belongs in an extension using the ProjectPress extension/integration points.

## Persistence and deletion

Project-scoped application entities use registered ProjectPress tables where independently queryable persistence is required. Permanent project deletion removes project-owned application records transactionally. WordPress remains authoritative for WordPress users and other WordPress-owned entities.

## API and workspace

Phase 9 project features are exposed through project-scoped workspace controllers and REST routes where appropriate. Access is mediated by `ProjectPermissions` and mutation flows use owning services/repositories.

## Validation coverage

Focused unit coverage exists for Phase 9 schema/integration areas including planning, governance, quality, delivery, people/communication, reporting and composition. Existing repository-level tests additionally cover project permissions, queries, extension registration, REST routes, deletion lifecycle and upgrade paths.

The repository's local validation commands are defined by Composer:

- `composer test`
- `composer test:unit`
- `composer lint`
- `composer lint:docs`
- `composer check`

CI is not a blocking requirement during this development phase; these commands remain the explicit local validation contract.

## Release-candidate follow-up

Before a release candidate, run the local validation contract in a supported WordPress test environment and resolve any runtime, coding-standard, documentation or upgrade-path failures. Validate the project workspace end-to-end with representative records before tagging a release.
