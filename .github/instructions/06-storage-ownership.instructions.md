# 6. Storage Ownership

Each value must have one authoritative owner.

Examples:

- Primary project identity belongs to the projects table.
- WordPress user identity belongs to WordPress users.
- Project membership belongs to approved ProjectPress relationship storage.
- Post-based content belongs to its WordPress post.
- Classification identity belongs to the relevant taxonomy term.
- Plugin-wide configuration belongs to the existing settings system.
- Canonical fallback values belong to `Defaults.php`.

Avoid synchronising duplicate values unless there is a documented compatibility or performance requirement.

If duplication is required, define the authoritative source, synchronisation timing, conflict resolution and stale-value repair behaviour.
