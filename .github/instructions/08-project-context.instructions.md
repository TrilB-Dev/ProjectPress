# 8. Project Context

Individual project interfaces must use a consistent project-context resolver.

The resolver should:

1. Accept an approved project identifier.
2. Resolve the project record.
3. Reject invalid or missing projects.
4. Check whether the current user can access the project.
5. Make the validated project available to the manager, controller or service.

Do not repeatedly implement project-loading logic in individual screens. Commands and REST requests must apply the same project-resolution and permission principles as administrative screens.
