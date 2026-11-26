# BantuTepat — Documentation Package v1.2

Paket tujuh deliverable untuk Product Owner, UI/UX, frontend, backend, AI engineer, QA, dan Codex. Semua dokumen bersifat requirement/design, belum menjadi bukti aplikasi berjalan.

| Output | Dokumen | Isi |
|---|---|---|
| PRD | [01_PRD.md](01_PRD.md) | Visi, scope, prioritas, arsitektur, delivery, risk, demo |
| Requirements | [02_Requirements.md](02_Requirements.md) | 20 FR, 12 NFR, user stories, backlog, KPI |
| ERD | [03_ERD.md](03_ERD.md), [03_ERD.drawio](03_ERD.drawio) | Data dictionary, diagram konseptual dan constraint |
| API contract | [04_API_Contract.md](04_API_Contract.md) | Endpoint public/authenticated/internal; OpenAPI gabungan dengan DTO (63 operasi) |
| Role matrix | [05_Role_Matrix.md](05_Role_Matrix.md) | 7 role terautentikasi + 1 akses publik (Guest), permissions, tugas, batasan |
| Workflow | [06_Workflow.md](06_Workflow.md) | Alur program/usulan/pendaftaran, state machines, appeal, complaint, AI/WA dan sequence |
| Acceptance criteria | [07_Acceptance_Criteria.md](07_Acceptance_Criteria.md) | FR/NFR acceptance, 48 baseline + 12 supplemental scenarios, QA dan DoD |

## Cara pakai
1. Baca PRD (01) terlebih dahulu; dokumen 02–07 adalah ekstrak tematik terstruktur dari PRD/master, bukan versi kebijakan yang berdiri sendiri.
2. Baca seluruh bahan asli di `sources/`. Diagram asli memiliki 30 nomor (31 halaman); D11 dipisah menjadi dua.
3. Freeze Open Decisions §20 PRD sebelum membuat schema final, OpenAPI, dan implementasi lintas modul. Jika berbeda, catat ADR; jangan diam-diam memilih kontrak baru.
4. P0 terlebih dahulu, P1 sesudah core stabil, P2 opsional. Jalankan acceptance test dan simpan bukti. Data demo sintetis, bukan layanan resmi pemerintah; AI tidak memutus kelayakan.

## Batasan paket
- ERD masih konseptual; schema Prisma/SQL belum difinalisasi. OpenAPI kandidat mencakup 63 operasi public/BFF/authenticated; §16.4 internal service belum memiliki kontrak mesin penuh. Perlu persetujuan fase 0.
- Belum ada verifikasi kode aplikasi, benchmark, maupun hasil tes. Target performa dan milestone pada PRD adalah usulan internal.
- Rulebook menyebut deadline pengumpulan 18 Desember 2026 pukul 23.59 WIB; ada ketidakkonsistenan tahun final pada naskah. Konfirmasi dengan panitia, jangan mengubah sumber.
- Dokumen asli disalin tanpa mengubah substansinya. Semua usulan di PRD tetap draft sampai disepakati tim.

## Phase 0 addendum (v1.2)

- `phase0/STATE_CONTRACT.md` + `phase0/packages/policy/{enums,transitions}.ts`: canonical state and role inventory.
- `phase0/openapi.phase0.json`: OpenAPI 3.1 gabungan (63 operasi: 17 existing + 37 legacy + 9 auth); indeks `phase0/API_COVERAGE.md`.
- `phase0/AUTH_SECURITY.md`: provider, session/CSRF/MFA decision candidate.
- `phase0/packages/db/phase0_delta.sql`: migration template, not complete Prisma baseline.
- `phase0/PHASE0_GATE.md`, `phase0/ASSET_LICENSE_INVENTORY.csv`: readiness and license evidence.

v1.2 overrides conflicting historical source content in `sources/`; sources unchanged. Sign-off, full Prisma schema and internal service OpenAPI, generated client and integration validation remain required.

## TASK-01 — repository gate

Ikuti `phase0/TASK01_REPOSITORY_GATE.md`; jalankan `python3 phase0/validate_contract.py` dari direktori paket. Commit paket lengkap, bukan hanya Markdown. `sources/` adalah arsip historis. Tidak ada commit aktual yang dilakukan oleh penyusun dokumen.
