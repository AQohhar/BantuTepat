# Phase 0 release-to-coding gate — v1.2

Status: **candidate specification, NOT approved / not implementation evidence**. Product Owner and leads sign off before cross-module coding. Source hierarchy: v1.2 policy enums/transitions, state contract, combined OpenAPI, auth ADR and v1.2 documents override original archived sources; no silent changes; ADR for any change.

| Gate | Status | Owner / evidence required |
|---|---|---|
| Role count, routes, permission spelling, case and appeal status | Updated specification | Product + FE/BE approve |
| Missing API families | Combined OpenAPI JSON, 63 operations | BE+FE approve schemas, generate client/validators, validate contract tests; freeze internal service contracts before integration |
| ERD delta | Conceptual Draw.io + delta migration template | BE/DB integrate full Prisma baseline; verify FK/types/enums, subtype constraints, append-only privileges and test migration on disposable Postgres |
| Auth | Managed Supabase Auth + BFF candidate ADR | BE+QA test cookie/CSRF/AAL2, revoke, role scope and SDK integration |
| Deployment | Modular monolith plus optional worker | OPS prove host availability, TLS, capacity, backup, smoke test |
| Assets | License inventory template | FE fill every asset, attribution and provenance; reject unlicensed/template UI |
| Competition dates | Prelim deadline as printed 18 Dec 2026 23:59 WIB; printed final 20 Jan 2025 inconsistent | Team obtain written confirmation; do not guess correction |

## Order and stop conditions
1. Review and approve ADRs, contract freeze, full schema/DTO and automated consistency checks. No parallel FE/BE work on unresolved API interfaces.
2. P0 core only: auth/permissions, program publishing, two case flows, revision, appeal, complaint, audit, private files, tracking, transparency and in-app notification. E2E/security/hosted smoke all pass; P0 internal recommendation is not forwarding success.
3. P1 simulator isolated in backend, worker/outbox, anomaly, RAG, WA only after P0 gate. P2 disabled (`FACE_FEATURE_ENABLED=false`, OCR off); keep manual identity path available. Do not claim AI/WA live if mock or fallback.
4. Pre-submission: original UI + asset inventory, README, GitHub, hosted URL, demo video viewer link; no fabricated result or real citizen data.

Actual implementation progress and hosting budget are unknown; 26–27 Nov freeze and 17 Dec internal cut-off are planning assumptions. No task/test is marked passed merely because documentation exists.

## TASK-01 repository gate

See `TASK01_REPOSITORY_GATE.md` and `MANIFEST.sha256`. All Phase 0 artifacts must be committed and reviewed before TASK-02–40. Static checks are not sign-off. Full Prisma baseline, internal M2M contracts, generated clients/validators, runtime security tests and competition date confirmation are still pending.
