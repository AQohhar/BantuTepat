# TASK-01 — repository & freeze gate (v1.2)

Do not start TASK-02–40 until the team signs off TASK-01. TASK-02 may implement the approved database foundation; this is not proof of a migration or runtime pass.

1. Copy 01–07, README, 03_ERD.drawio, all `phase0/` and source archive into the project docs (e.g. `docs/BantuTepat_PRD_Package_v1.2/`). Retain directory layout and links. Do not commit secrets, real citizen data or generated provider tokens.
2. Run `python3 phase0/validate_contract.py` at package root (requires Python 3 + jsonschema). Verify manifest hashes. Run TypeScript policy typecheck, review API coverage and source diff; record outputs.
3. Commit the entire package (including machine contract, enum/transitions, auth, SQL delta, asset inventory, validation script, manifest). Check `git status`, `git diff --cached --name-only` and after commit `git ls-files` against the manifest. Record commit SHA and PR review. This package has no access to the team's actual repository; these steps are required, not completed.
4. FE/BE/PO approve API DTO choices, auth ADR and policy; generate typed client/runtime validators from the combined OpenAPI; test mock/client and negative cases. Do not create a second handwritten DTO authority. Freeze internal service contracts before P1 integration. Integrate/test full Prisma baseline and privileges as foundation work before feature implementation.
5. Record approvers, date, commit SHA and exceptions in the team ADR. If validation fails, artifact is missing or contract is unresolved, TASK-01 remains BLOCKED. No silent deviation.

| Evidence | Owner | Status |
|---|---|---|
| Package checks and manifest | BE | Static check supplied; rerun in repo |
| Git commit SHA and PR | Team lead | Pending |
| API/DTO & policy sign-off | FE/BE/PO | Pending |
| Generated client and runtime contract tests | FE/BE | Pending |
| Auth provider ADR | BE/QA | Pending |
| Rulebook date confirmation | Team lead | Pending |
