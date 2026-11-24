# ProjectPress New Element Integration Audit

This audit records the integration status after the Project domain expansion and Phase 9 implementation.

## Integration rule

A user-facing ProjectPress entity is complete when it is deliberately mapped through the appropriate architecture:

`Domain model -> repository/storage -> service/validation -> permissions -> REST/API where required -> ProjectPress UI -> tests`

Read models and aggregates derive from canonical persisted records rather than introducing duplicate sources of truth.

## Storage boundaries

ProjectPress retains its hybrid storage architecture:

- custom ProjectPress tables for independently queryable application entities and relationships;
- WordPress posts/meta for authored content;
- WordPress taxonomies/term meta for classification;
- WordPress attachments for uploaded files;
- WordPress comments for discussion replies;
- WordPress users for canonical user identities.

## Workspace persistence audit

The earlier `WorkspaceSections.php` direct-persistence concern is no longer representative of the active workspace architecture. Current project workspace controllers delegate to owning repositories:

- `WorkspaceContent` uses `ContentRepository`, `ActivityRepository`, and `ProjectSettingsRepository`.
- `Collaboration` uses `DocumentRepository` and `AttachmentRepository`.
- task, milestone, requirement, feature, story, governance, quality, delivery, people/communication, reporting and composition sections use their domain repository/service boundaries.

The workspace controllers retain nonce and `ProjectPermissions` checks around mutations.

## Domain classification and integration

### Planning and work

Requirements, features, user stories, tasks and milestones are persistent project entities. Planning/backlog/reporting views derive from those canonical records. `ProjectPlan` remains an aggregate/read model and does not require a `project_plans` table.

### Collaboration

Documents/notes/discussions remain authored-content concerns, attachment ownership remains with WordPress, and ProjectPress stores project relationships/metadata around those WordPress entities.

### People and communication

WordPress users and the existing ProjectPress team/member model remain authoritative for identity/membership. Timesheets and project notifications are independently queryable project entities.

### Governance and quality

Risks, issues, dependencies, decisions, bugs and test cases are project-scoped persistent entities with repository/service, permission, API/workspace and deletion-lifecycle integration.

### Delivery

Sprints, releases, deployments and environments are project-scoped persistent entities integrated through the owning delivery repository/service and workspace/API layers.

### Reporting

Reporting is a presentation/read model derived from canonical repositories. It does not create parallel reporting persistence.

### Composition and SBOM

Components, packages, assets, licences, attribution, provenance, relationships and generic SBOM records form the format-neutral ProjectPress composition foundation. The snapshot API derives a compositional view from these canonical records.

SPDX-specific import, export and generation are intentionally outside ProjectPress core and belong in the separate SPDX extension.

### Tracking and workflow

Activity is a tracked project record exposed through `ActivityRepository`. Workflow/read-model classes should continue to derive from canonical project entities unless an independently queryable persistence requirement is explicitly approved.

## API/UI completion

Phase 9 workspace and REST integration is registered for the approved user-facing domains. Permission enforcement continues through `ProjectPermissions`, and mutation controllers use owning repositories/services rather than adding a second persistence mechanism.

## Verification

Focused Phase 9 tests cover planning, governance, quality, delivery, people/communication, reporting and composition. Consolidation coverage validates workspace registration, REST bootstrap registration and the absence of SPDX-specific REST bootstrap code from core.

The repository validation contract is documented in `PHASE_9_VALIDATION.md` and the Composer scripts.

## Remaining release-candidate work

Phase 9 architecture/integration is considered closed by this audit. Release-candidate QA remains a separate verification activity: run the documented local test/lint contract in a supported WordPress test environment, resolve failures, exercise representative workspace flows, and validate upgrade behaviour before release tagging.
