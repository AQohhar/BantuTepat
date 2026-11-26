# BantuTepat — API Contract

**Versi 1.2 | 26 November 2026 | Draft requirements, bukan bukti implementasi**

Acuan: Master Specification §16, §20.4; PRD §11, §20. Baca `01_PRD.md` untuk scope, prioritas dan Open Decisions. Penomoran bagian yang diambil dari master dipertahankan. Kontrak v1.2 adalah kandidat freeze untuk persetujuan tim; sumber historis tidak mengungguli kontrak ini.

## 11. API contract dan transaksi

Frontend dan backend wajib menggunakan shared DTO/enum (packages/contracts, packages/policy), bukan string status bebas. Sebelum implementasi feature, definisikan OpenAPI/JSON Schema, DTO input/output, pagination, error, action permission, transition guard, dan versioning. API berikut diambil dari MS §16; endpoint internal terpisah dari API publik. Endpoint §16.1–16.3, §16.7 dan auth memiliki DTO dalam kontrak mesin gabungan Phase 0 (63 operasi). §16.4 tetap kontrak internal terpisah.

## 16. API CONTRACTS — FRONTEND, BACKEND, INTERNAL SERVICES

**Base:** `/api/v1`. Seluruh API authenticated memakai cookie sesi BFF `__Host-bt_session` HttpOnly/Secure/SameSite=Lax; provider Supabase Auth server-side (lihat `phase0/AUTH_SECURITY.md`). Cek CSRF untuk mutasi bila menggunakan cookie auth. Response error standar: `request_id`, `code`, `message`, `field_errors`, `retryable` tanpa stack trace.

### 16.1 API publik

| Verb | Path | Tujuan | Response penting |
|---|---|---|---|
| GET | `/public/programs` | katalog dengan filter `territory`, `mode`, `status`, `search`, paging | program cards publik |
| GET | `/public/programs/:slug` | detail program + rules versi publik | `can_apply` hanya preview; recheck saat submit |
| GET | `/public/territories` | hirarki wilayah | codes, parent_id |
| GET | `/public/stats` | statistik aman | agregasi + suppression |
| GET | `/public/faq` | FAQ statis | verified entries |
| POST | `/web-chat` | LLM RAG web-only | jawaban + citations; fallback |
| GET | `/health` | public liveness minimal | status service tidak expose secrets |

### 16.2 API masyarakat dan pengajuan

| Verb | Path | Permission / pembatasan | Output |
|---|---|---|---|
| GET | `/me` | authenticated | akun + active roles/scopes |
| POST | `/data-proposals/drafts` | `case.create.self` / represented | draft id |
| PATCH | `/data-proposals/drafts/:id` | owner/mandated proposer | updated draft |
| POST | `/data-proposals/:id/submit` | owner/mandate + idempotency | case/ticket + status |
| POST | `/programs/:id/applications/drafts` | mode+territory+auth | application draft |
| POST | `/program-applications/:id/submit` | same actor, status precheck | case/ticket |
| GET | `/cases` | scoped cases | paged sanitized list |
| GET | `/cases/:id` | per-object access | case + private timeline + actions |
| POST | `/cases/:id/resubmit` | can revise + case revision | same ticket, new revision |
| POST | `/cases/:id/appeals` | appeal eligibility, ownership | appeal ticket |
| GET | `/appeals/:id` | creator/assigned reviewer | appeal status |
| POST | `/complaints` | authenticated only; `complaint.create` | complaint ticket |
| POST | `/cases/:id/feedback` | case subject & post-result | feedback id |
| POST | `/identity-checks/face` | opt-in, policy enabled | match/manual outcome |
| POST | `/identity-checks/manual-request` | authenticated | manual ticket |

### 16.3 API instansi, wilayah, administrasi

| Verb | Path | Permission | Keterangan |
|---|---|---|---|
| POST | `/agency/programs/drafts` | `program.draft.create` + agency | create program |
| PATCH | `/agency/programs/:id/draft` | `program.draft.update` + version | edit draft only |
| POST | `/agency/programs/:id/submit-publication` | `program.submit_review` | queue publication review |
| POST | `/agency/programs/:id/publish` | `program.publish` + different actor | publish |
| POST | `/agency/programs/:id/return` | publisher permission | reasoned return |
| GET | `/officer/cases` | `case.read.assigned` + scope | work queue |
| POST | `/officer/cases/:id/verify` | `case.verify.assigned` | check/recommend/revise |
| POST | `/officer/cases/:id/request-revision` | assigned + reason | revision notice |
| GET | `/agency/reviews` | `case.review.assigned` | review queue |
| POST | `/agency/cases/:id/review` | reviewer independent | forward/recheck/close with reason |
| POST | `/agency/appeals/:id/review` | independent appeal reviewer | upheld/recheck/corrected |
| GET | `/audit/events` | `audit.read.scoped` | filters + redaction |
| GET | `/internal/metrics` | scope authorized | operational aggregates |
| POST | `/admin/users/:id/roles` | `users.manage_roles` + proof | invite/revoke scope |
| GET | `/admin/integrations/health` | `integration.manage` | health no secrets |

### 16.4 Internal service API (server-to-server only)

| Verb | Path/service | Payload | Respons |
|---|---|---|---|
| POST | `AI /v1/anomaly/assess` | case ID, anonymized feature vector, version | model flag/score/version |
| POST | `AI /v1/face/verify` | authenticated one-time reference + sample | MATCH/NO_MATCH/MANUAL_REQUIRED |
| POST | `AI /v1/rag/answer` | sanitized public question + retrieval refs | cited response/fallback |
| GET | `GOV /v1/synthetic-households/:key` | demo key, service auth | synthetic record |
| POST | `GOV /v1/synthetic-forwarding` | case ID, mapping payload | ACK/pending/error |
| GET | `WA /webhook` | provider challenge | verification token handshake |
| POST | `WA /webhook` | signed incoming events | acknowledge quickly, queued processing |

**Service auth:** signed short-lived machine-to-machine token or protected private networking and secret header backed by secret manager; rotate; validate audience/expiry; strict CORS. No internal endpoint exposed via browser without protection. Consider egress allowlist if hosting supports it.

### 16.5 Example submit request/response

```http
POST /api/v1/program-applications/49e7.../submit
Idempotency-Key: 5c1aa12d-9d...
Content-Type: application/json
```

```json
{
  "acknowledge_accuracy": true,
  "application_revision": 1,
  "expected_version": 1,
  "consent_notice_version": "privacy-v1"
}
```

```json
{
  "case_id": "d4d5f5c2-ef6f-49d8-a2e1-9bfe816e3b9d",
  "ticket_no": "BT-P-2026-0007",
  "case_type": "PROGRAM_APPLICATION",
  "status": "PRECHECKED",
  "version": 2,
  "simulation_only": true,
  "next_step": "Petugas akan memeriksa pengajuan.",
  "request_id": "req_93f"
}
```

### 16.6 Error contract (sample)

```json
{
  "request_id": "req_97a",
  "code": "REGION_NOT_ELIGIBLE_FOR_APPLICATION",
  "message": "Program ini hanya membuka pengajuan untuk wilayah sasaran yang disebutkan.",
  "field_errors": {"territory_id": "Periksa kembali wilayah yang sesuai persyaratan program."},
  "retryable": false,
  "actions": ["REVIEW_ADDRESS_DATA", "VIEW_PROGRAM_RULES", "CONTACT_HELPDESK"]
}
```

**Standard HTTP mapping:** 400 malformed; 401 unauthenticated; 403 no permission/scope; 404 object absent or hide unauthorized existence; 409 state/version/duplicate; 422 validation; 429 rate-limited; 503 dependent service unavailable (only endpoints truly depending on that service). Jangan sampai AI/WA down menyebabkan semua pengajuan 503.

---

### 16.7 Endpoint Phase 0 — kontrak tambahan v1.2

Semua path relatif terhadap `/api/v1`; kurung kurawal merupakan parameter OpenAPI (setara `:id`). Detail DTO machine-readable: `phase0/openapi.phase0.json` (gabungan 63 operasi; lihat `phase0/API_COVERAGE.md`). P1 knowledge endpoints diimplementasikan setelah P0.

| Method | Path | Permission | Guard | Request | Response |
| --- | --- | --- | --- | --- | --- |
| PATCH | /program-applications/drafts/{id} | case.create.self / case.create.represented | Owner/mandate; DRAFT; expected_version | DraftUpdate | DraftResult |
| POST | /cases/{id}/attachments | case.attachment.write | Owner/mandate or assigned officer with evidence permission; state allows; multipart private quarantine | AttachmentUpload | AttachmentResult |
| GET | /cases/{id}/attachments/{attachmentId}/download | case.attachment.read | Per-object scope; CLEAN; short TTL; audit; no public bucket | None | SignedDownload |
| POST | /officer/cases/{id}/assign | case.assign | Authorized scoped coordinator; operator active; no self assignment; expected_version | Assignment | CaseResult |
| GET | /complaints/{id} | complaint.read.own / complaint.manage.assigned | Owner or assigned handler; redact confidential fields | None | ComplaintResult |
| GET | /complaints | complaint.read.own / complaint.manage.assigned | Scoped list; queue only for scoped triage; pagination | None | ComplaintList |
| POST | /complaints/{id}/triage | complaint.manage.assigned | Authorized triage; RECEIVED; confidentiality | Triage | ComplaintResult |
| POST | /complaints/{id}/transition | complaint.manage.assigned / complaint.assign | Action-specific permission; state and assignment; no self assignment | ComplaintTransition | ComplaintResult |
| POST | /agency/programs/{id}/archive | program.archive | Agency scope; closed program only; preserve published snapshots | VersionReason | ProgramResult |
| GET | /notifications | notification.read.own | Recipient only; pagination | None | NotificationList |
| POST | /notifications/{id}/read | notification.read.own | Recipient only; idempotent read | Empty | NotificationResult |
| POST | /admin/representation-grants | representation.manage | Scoped admin; legal basis/evidence; no self grant; independent approval | GrantCreate | GrantResult |
| POST | /admin/representation-grants/{id}/approve | representation.approve | Independent reviewer; verify evidence and scope | VersionReason | GrantResult |
| POST | /admin/territories | territory.manage | Authorized admin; hierarchy validation; audit | TerritoryCreate | TerritoryResult |
| POST | /admin/knowledge-docs | knowledge.draft.create | P1; public verified source only; not auto-approved | KnowledgeCreate | KnowledgeResult |
| POST | /admin/knowledge-docs/{id}/approve | knowledge.approve | P1; independent reviewer; pending public doc version only | VersionReason | KnowledgeResult |
| POST | /agency/appeals/{id}/assign | appeal.assign | Scoped coordinator; different from original decision maker; no self assignment | Assignment | AppealResult |

Kontrak upload: multipart `file` PDF/JPEG/PNG max 10 MiB, server MIME sniffing, SHA-256, private quarantine, reject executable/SVG, scan sebelum download; `PENDING`, `CLEAN`, `REJECTED`. Signed URL GET max 60 detik, authorization dan audit sebelum mint, `Cache-Control: no-store`, URL tidak dilog. Draft menggunakan ID case yang sudah ada (ticket_no null sampai submit). Attachment tidak mengubah status; upload di DRAFT/REVISION_REQUIRED atau oleh petugas assigned pada UNDER_REVIEW dengan alasan. Sanggahan menggunakan evidence references yang sudah terotorisasi; attachment tambahan hanya lewat scoped evidence flow, bukan edit snapshot lama.

Complaint guest disabled, daftar dan detail selalu scoped; triage dan assignment tidak memberi akses otomatis pada kasus terkait. Mutasi status menggunakan `expected_version` dan `Idempotency-Key`. `GET` list `limit` default 20 max 100, cursor opaque, stable `(created_at,id)` order. Endpoint `POST /complaints` yang sudah ada hanya authenticated dengan `complaint.create`. Auth routes dan session contract di `phase0/AUTH_SECURITY.md`.

### 20.4 Atomic write + outbox pattern

**Satu transaksi database pada perubahan status penting:**

1. Kunci case row menggunakan transaksi `SELECT ... FOR UPDATE` atau optimistic `version` check.
2. Validasi actor/role/assignment, state transition, rules, reason dan evidence.
3. Tambahkan revision/decision baru; set status terbaru; tambahkan `audit_events` dan `notification_outbox`.
4. Commit. Baru sesudah commit, worker mengirim notifikasi atau memanggil service eksternal, dengan retry/idempotency.
5. Client menerima respons `request_id`, `ticket_no`, `version`, dan status. Duplicate request (idempotency key sama) harus mengembalikan hasil commit yang sama, bukan kasus baru.

Gunakan kolom `version` + unique `(actor_scope, idempotency_key)` dalam tabel `idempotency_keys`; payload sama mengulang respons, payload berbeda dengan key sama menghasilkan `409`. Implementasikan idempotency untuk `POST submit`, perubahan review, penerusan simulasi, dan WA webhook.

## Status kontrak dan pekerjaan sebelum freeze

OpenAPI 3.1 gabungan `phase0/openapi.phase0.json` mencakup seluruh §16.1–16.3, §16.7 dan 9 auth operations (63 operasi). Lihat indeks `phase0/API_COVERAGE.md`. §16.4 internal service tetap inventory terpisah; generated client, validator runtime, schema Prisma lengkap dan integration tests belum selesai. Jangan coding paralel sebelum sign-off. Lihat `phase0/PHASE0_GATE.md`.
