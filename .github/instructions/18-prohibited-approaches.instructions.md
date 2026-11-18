# 18. Prohibited Approaches

Do not:

- Create a second database manager or schema registry.
- Register ProjectPress tables from arbitrary services or admin pages.
- Store the authoritative project record as both a custom-table row and WordPress post.
- Create a separate user identity system.
- Put raw SQL inside views/templates.
- Put interface markup inside repositories.
- Put request handling inside schema classes.
- Duplicate existing helpers without reviewing them.
- Bypass existing settings architecture.
- Load ProjectPress assets across every WordPress admin page.
- Hard-code unvalidated status, priority or visibility values.
- Allow extensions to modify core files as their normal integration mechanism.
- Remove existing template functionality without checking ProjectPress dependencies.
- Regenerate unrelated files during a focused change.
