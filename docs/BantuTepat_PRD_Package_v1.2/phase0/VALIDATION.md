# Static validation — v1.2

- PASS: 63 API operations exactly match Markdown inventory (§16.1–16.3, §16.7 and auth); 87 JSON Schema declarations validated against Draft 2020-12 metaschema; schema references, operation IDs, path parameters and declared security schemes checked.
- PASS: all 17 original OpenAPI operations unchanged; all archived source files byte-identical to v1.1; ERD Draw.io unchanged.
- PASS: TypeScript policy declarations checked with `tsc --noEmit --strict --target ES2022`.
- PASS: PRD header and resubmit diagram regression static checks; ASSIGN_APPEAL permission aligned with API.
- No full OpenAPI specification linter, generated client, SDK integration, migration, graphical diagram rendering, runtime security or E2E tests performed. No repository commit or sign-off performed. Internal service §16.4 remains inventory. DTOs are proposed contract choices.
