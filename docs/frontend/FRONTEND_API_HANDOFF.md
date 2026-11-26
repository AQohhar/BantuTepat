# Frontend API dependency matrix dan backend handoff

Status: OpenAPI v1.2 adalah kandidat kontrak dari dokumen, belum disetujui/diintegrasikan pada project. Semua path relatif pada /api/v1 sebagaimana API coverage. Endpoint backend/internal-service tidak boleh dipanggil langsung dari browser.

## Dasar integrasi

- Sumber kontrak kandidat: docs/BantuTepat_PRD_Package_v1.2/phase0/openapi.phase0.json dan API_COVERAGE.md. Package menyatakan 63 operasi API dan 87 schema; validasi statis tercatat di VALIDATION.md. Itu bukan bukti sign-off atau runtime integration project.
- Frontend Laravel memakai Laravel HTTP Client hanya dari server dan mengambil base URL dari BACKEND_API_URL dengan base path /api/v1. Jangan hardcode production URL.
- Auth/session, cookie, CSRF, callback, MFA, Origin, header forwarding, same-origin deployment, dan pemilik BFF harus disepakati sebelum membuat client auth. AUTH_SECURITY.md mengusulkan Supabase + Next.js BFF, sedangkan prompt frontend terbaru menetapkan Laravel; jangan menganggap sesi Laravel/Sanctum kompatibel otomatis.
- Jangan membuat DTO tandingan. Setelah kontrak disetujui, gunakan client/validator yang dihasilkan dari OpenAPI sesuai pipeline tim.
- Mock API hanya mode local/test dengan data sintetis dan response shape yang sama. Production tidak mengaktifkan mock atau mengganti outage menjadi sukses.
- Tidak ada token di localStorage. Tidak ada akses langsung ke PostgreSQL, internal M2M, AI service, Government Simulator, atau private object storage dari browser.

## Matrix kebutuhan layar ke API

| Area layar | Operasi kandidat yang diperlukan | Gap/keputusan yang menghalangi |
|---|---|---|
| PUB-01 Beranda | GET /public/programs; GET /public/stats | G03 untuk data agregat yang lebih lengkap. Angka hanya dari server dan diberi waktu/sumber jika tersedia. |
| PUB-02 Katalog | GET /public/programs; GET /public/territories | G01: filter jenis belum ada; G02 untuk join territory label. Jangan mengklaim filter lokal sebagai hasil global. |
| PUB-03 Detail program | GET /public/programs/{slug} | G02: detail rules, address basis, quota/documents/SLA/appeal window belum cukup pada DTO kandidat. |
| PUB-04 Panduan | GET /public/faq | Konten FAQ harus disetujui; FAQ statis tetap tersedia jika API gagal. |
| PUB-05 Transparansi | GET /public/stats; GET /public/territories | G03: time range/series/unit/method/territory filtering; suppression dilakukan server. |
| PUB-06 Bantuan | GET /public/faq; POST /web-chat (P1) | Chat hanya pertanyaan umum dengan sumber; security/CSRF/rate limits POST /web-chat dikonfirmasi. WA adalah static handoff, bukan LLM. |
| AUTH-01—03 | GET /auth/csrf; POST /auth/register; POST /auth/login; POST /auth/mfa/enroll; POST /auth/mfa/verify; POST /auth/recovery; GET /auth/callback; POST /auth/reset-password; POST /auth/logout; GET /me | Auth/session ADR belum disetujui untuk BFF Laravel. Signup hanya CITIZEN, staff melalui undangan; session dan challenge tidak menjadi auth custom lokal. |
| CIT-01 Dashboard | GET /me; GET /cases; GET /notifications; GET /public/programs; POST /notifications/{id}/read | G06 untuk action/revision/reason; paginasi/no-store dan region error terpisah. |
| CIT-02 Pilih pengajuan | GET /me; GET /public/programs | Capability dan jalur berdasar program/permission response; bukan keputusan eligibility. |
| CIT-03 Usulan data | POST /data-proposals/drafts; PATCH /data-proposals/drafts/{id}; POST /data-proposals/{id}/submit; POST /cases/{id}/attachments; GET /cases/{id} | G04 metadata/scan file; G05 field schema/draft resume; G06 reason/next action. Tidak ada program_version_id. |
| CIT-04 Pendaftaran program | GET /public/programs/{slug}; POST /programs/{id}/applications/drafts; PATCH /program-applications/drafts/{id}; POST /program-applications/{id}/submit; POST /cases/{id}/attachments | G02 schema/detail program; G04 attachments; G05 dynamic field definitions/revision. Precheck bukan keputusan penerima. |
| CIT-05—07 Tracking/perbaikan | GET /cases; GET /cases/{id}; POST /cases/{id}/resubmit; attachment download | G04 scan state; G05 schema/version; G06 structured revision/reason/action; owner/scope guard backend. |
| CIT-08 Sanggahan | GET /cases/{id}; POST /cases/{id}/appeals; GET /appeals/{id} | G07: challenged review reference, eligibility/window, linked ticket dan decision summary; tidak ada keputusan baru yang ditebak. |
| CIT-09 Pengaduan | GET /complaints; GET /complaints/{id}; POST /complaints | Read/create candidate ada; confidentiality/redaction dan linked-case authorization tetap perlu tes. |
| CIT-10 Feedback | GET /cases/{id}; POST /cases/{id}/feedback | Server menentukan eligibility; feedback tidak mengubah status case. |
| CIT-11 Profil | GET /me; POST /auth/logout | G08: profile update, household/session/identity history belum punya kontrak; profil read-only hingga endpoint disetujui. |
| CIT-12 Verifikasi | POST /identity-checks/manual-request; POST /identity-checks/face hanya P2 | G08 untuk history/read; face off secara default, consent dan manual alternative wajib. |
| REG-01—03 Wilayah | GET /me; GET /officer/cases; GET /cases | G09: grants/subjek berizin, scoping represented case, list clarity dan subject lookup. Tidak ada pencarian warga umum. |
| REG-02 Usulan terwakilkan | POST data-proposal/program-app drafts dan submit; POST /cases/{id}/attachments | Mandat/scope dan actor-subject berbeda; grant lookup G09; API authorization yang menjadi sumber kebenaran. |
| REG-04 Verifikasi operator | GET /cases/{id}; attachment download; POST /officer/cases/{id}/verify; POST /officer/cases/{id}/request-revision | G06: assignment, evidence, reason, required fields, deadline, expected actions, AI panel P1. Operator tidak menyetujui final. |
| AGY-01—03 Review instansi | GET /agency/reviews; GET /cases/{id}; attachment download; POST /agency/cases/{id}/review; POST /officer/cases/{id}/assign; GET /appeals/{id}; POST /agency/appeals/{id}/review; POST /agency/appeals/{id}/assign | G06 reasoned decision/capability; G07 appeal linkage; G09 eligible assignee lookup; G11 appeal queue/list. Assignment/review selalu scoped dan maker-checker. |
| AGY-04—07 Program | POST /agency/programs/drafts; PATCH /agency/programs/{id}/draft; POST submit-publication/publish/return; POST archive | G10: scoped internal list/detail/version, publication queue dan create-new-version contract. GET public programs bukan pengganti read model management. |
| AGY-08 Appeal queue | GET /appeals/{id}; POST /agency/appeals/{id}/review; POST /agency/appeals/{id}/assign | G11: list/queue dan eligible reviewer lookup. |
| ADM-01 Monitoring | GET /internal/metrics; GET /admin/integrations/health | G03: filter/time/window/method; scope internal; health metadata as-of belum ada. |
| ADM-02 Audit | GET /audit/events | Filter schema/redaction/scoped query disepakati; audit read-only. |
| ADM-03 Complaint handling | GET /complaints; GET /complaints/{id}; POST /complaints/{id}/triage; POST /complaints/{id}/transition | G09 handler eligibility; object-level confidentiality; Gaps complaint transitions/field vocabulary dicek terhadap machine contract. |
| ADM-04 Pengguna | POST /admin/users/{id}/roles; POST /admin/representation-grants; POST /admin/representation-grants/{id}/approve | G09/G12: user, grant, invitation, approval read model/lookup. No self-grant; fresh AAL2 sesuai candidate auth contract. |
| ADM-05 Wilayah | GET /public/territories; POST /admin/territories | G12: authorized internal list/detail/edit; public lookup bukan daftar admin. |
| ADM-06 Integrasi | GET /admin/integrations/health; P1 knowledge create/approve | G13: knowledge list/read, flags projection, as-of metadata. Jangan tampilkan credential atau buat toggle/retry yang belum ada endpoint. |

## Perilaku response dan error yang diminta

Gunakan error contract kandidat yang berisi request_id, code, message, field_errors, retryable, dan actions yang di-allowlist. Pemetaan HTTP: 400 input rusak; 401 login/session; 403/404 pesan objek generik; 409 konflik/version/duplicate dan muat ulang state; 422 ringkasan field error; 429 rate limit; 503 hanya bila layanan yang diminta bergantung pada dependency yang gagal. Jangan tampilkan stack trace/provider error mentah.

Mutasi submit/review mengikuti Idempotency-Key dan expected_version hanya saat kontrak menyatakannya. Timeout mutation tidak otomatis membuat tiket baru; client mempertahankan form transient dan merefresh state sesuai policy. 204 tidak diparse sebagai JSON. Response privat menggunakan no-store.

## P1 dan internal service

API browser tetap /api/v1. RAG, anomaly model, Government Simulator, worker/outbox, Face service, dan WhatsApp provider memakai internal service contract/server adapter; tidak ada kredensial M2M di Blade/JavaScript. Frontend hanya menampilkan state hasil adapter/backend: AI flag advisory, forwarding pending/failure/ACK berbeda, label simulasi konsisten. P2 face hanya saat feature flag dan approval aktif.

## Handoff yang perlu dikonfirmasi backend

1. Konfirmasi siapa pemilik BFF dan cookie/session saat UI Laravel same-origin terhadap API Next.js/FastAPI.
2. Bekukan OpenAPI gabungan, auth/CSRF/session ADR, error/permission/action vocabulary, DTO runtime validation, and generated client strategy.
3. Putuskan setiap G01—G13 dengan screen ID terdampak, owner, minimal DTO/endpoint, privacy/scoping, approval, tests, dan target delivery. Tidak menambah endpoint sepihak.
4. Sediakan dev/staging URL, CORS/Origin model, HTTPS, timeout/rate limits, synthetic accounts/data, and failure fixtures.
5. Tegaskan attachment quarantine/download, private storage, short-lived URL logging, AAL2 staff, and revoke/session-expiry behavior.
6. Pisahkan endpoint UI yang tersedia dari internal service inventory; beri status P0/P1/P2 dan feature flags yang aman.
