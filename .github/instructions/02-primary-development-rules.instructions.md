# 2. Primary Development Rules

## 2.1 Preserve the Existing Architecture

Before adding a class, helper, service, manager or storage system:

1. Inspect the existing repository for an equivalent component.
2. Extend the established component when appropriate.
3. Follow the existing namespace and directory conventions.
4. Do not introduce duplicate bootstrap, database, settings, REST, asset or extension systems.
5. Do not move existing framework classes unless the task specifically requires a structural migration.
6. Replace remaining `PluginName`, `pluginname`, `PLUGINNAME` placeholders only where the surrounding code is ready to become ProjectPress-specific.

ProjectPress-specific functionality should remain separate from reusable template infrastructure wherever practical.

## 2.2 Work Incrementally

Do not attempt to generate the complete plugin in one change.

Implement work in controlled stages:

1. Confirm the relevant existing architecture.
2. Create or update the domain model.
3. Connect storage through the authorised persistence layer.
4. Add the required service or repository logic.
5. Add permissions and validation.
6. Add the administrative or project interface.
7. Add REST integration if required.
8. Add tests or validation checks.
9. Update documentation.

Each stage must leave the plugin in a syntactically valid and internally consistent state.
