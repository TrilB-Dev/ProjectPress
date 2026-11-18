# 17. Validation Checklist

Before completing a change, verify:

- The implementation follows existing ProjectPress architecture.
- No duplicate database/settings system was introduced.
- Database rules are managed by `Database.php`.
- Schema definitions are managed by `Schema.php`.
- Canonical database defaults are managed by `Defaults.php`.
- Project backend code belongs under `src/Includes/Project/`.
- Global management interfaces belong under `src/Admin/Manager/ProjectManager/`.
- Individual project interfaces belong under `src/Admin/Manager/Projects/`.
- General settings interfaces belong under `src/Admin/Settings/`.
- SQL is prepared correctly.
- Permissions/nonces are checked.
- Input is sanitised/validated and output escaped.
- Existing helpers are reused where appropriate.
- REST behaviour follows existing API architecture.
- Assets use the existing build system.
- PHP syntax and formatting checks pass.
- Documentation reflects implementation.
