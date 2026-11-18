# 12. Administrative Interfaces

Administrative interfaces must follow the existing ProjectPress and WordPress architecture.

Each interface should have a clear responsibility, load only required assets, use existing asset registration/build systems and helpers, use services instead of direct SQL, check permissions before protected output, escape dynamic output, handle missing/invalid records gracefully, use consistent notices/errors and remain compatible with localisation.

Do not create a second admin framework solely for ProjectPress.
