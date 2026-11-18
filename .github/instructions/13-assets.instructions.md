# 13. Assets

Use the existing source and compiled asset architecture. Compiled assets belong in `src/Assets/dist/`.

Do not manually edit generated distribution files when the source file exists.

When an interface needs JavaScript or CSS:

1. Add or update the appropriate source entry.
2. Register/enqueue it through the existing asset system.
3. Scope it to the relevant ProjectPress page.
4. Rebuild assets.
5. Confirm unrelated WordPress screens are unaffected.
