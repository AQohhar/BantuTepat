# AUTH-01 — Authentication & session (kandidat freeze)

**Pilihan rancangan:** Supabase Auth (managed email/password, email verification/recovery, TOTP MFA); PostgreSQL app roles independent of provider claims. Next.js BFF same-origin; browser tidak menyimpan access/refresh token di localStorage. Gunakan provider SDK server-side, validasi issuer/audience/expiry dan identity mapping `auth_provider_ref`. Tidak membuat custom password hash sendiri. Pilihan provider perlu disetujui tim dan diuji terhadap environment hosting.

## Session and CSRF
- Setelah login verified dan MFA bila diwajibkan, server membuat random session token 256-bit; simpan SHA-256 token di `sessions`; provider tokens disimpan encrypted at rest, server-only. Set `__Host-bt_session` Secure; HttpOnly; SameSite=Lax; Path=/; tanpa Domain. Idle 30 menit, absolute 12 jam (usulan); refresh provider token server-side, revoke bila refresh gagal. Rotate token saat login, MFA, privilege elevation; logout menghapus cookie + revoke session; revoke roles dibaca ulang pada setiap mutasi dan invalidasi sesi terdampak.
- Synchronizer token: GET `/api/v1/auth/csrf` mengeluarkan token random 256-bit terikat session/pre-auth challenge, simpan hash server-side; response no-store; client mengirim `X-CSRF-Token` pada setiap mutasi termasuk login/register/recovery/logout. Compare constant-time, cek Origin sama persis dengan APP_ORIGIN; tolak bila tidak cocok/absen pada browser mutation; CORS default deny. Pre-auth challenge short-lived 10 menit, cookie `__Host-bt_pre_auth` HttpOnly Secure SameSite=Lax Path=/ tanpa Domain; rotate saat session terbentuk. Tidak ada side effect di GET kecuali session challenge dan audit akses download. Webhook tidak memakai cookie/CSRF: verify signature, timestamp/replay and idempotency.
- Staff (REGIONAL_PROPOSER, VILLAGE_OPERATOR, AGENCY_VERIFIER, PROGRAM_MANAGER, AUDITOR, SUPER_ADMIN): AAL2/TOTP wajib sebelum akses dashboard/task; citizen default AAL1, step-up bila operasi berisiko. Role grant/revoke memerlukan AAL2 fresh dalam 5 menit + audit. Signup hanya CITIZEN; staff invite terpisah dan persetujuan scope. No role trust dari client/provider user metadata. Recovery/verification hanya callback allowlist, rate-limited, generic messages; no account enumeration. Provider MFA enrollment/challenge dilakukan server-side, no secret in logs.

## Auth endpoint inventory (relative /api/v1)
| Method/path | Request / response | Guard |
|---|---|---|
| GET /auth/csrf | token, expires_at | pre-auth/session; no-store |
| POST /auth/register | email,password,notice_version → 202 generic | CSRF, rate limit, CITIZEN only, verify email |
| POST /auth/login | email,password → session or MFA_REQUIRED challenge | CSRF, rate limit, generic 401 |
| POST /auth/mfa/enroll | challenge → TOTP enrollment data | authenticated limited session, no privileged access |
| POST /auth/mfa/verify | challenge_id, code → rotated session | CSRF, expiry, rate-limit, provider AAL2 |
| POST /auth/recovery | email → generic 202 | CSRF, rate limit |
| GET /auth/callback | provider verification/recovery exchange → safe redirect | one-time token/PKCE state, no open redirect |
| POST /auth/reset-password | password → 204 | recovery session only, CSRF, revoke existing sessions |
| POST /auth/logout | empty → 204 | CSRF, revoke |

All nine auth endpoint schemas are included in `openapi.phase0.json`; SDK integration, runtime validators and security testing still need implementation. No actual session security or provider availability has been verified.

## DTO v1.2 clarifications (candidate)

Password registration/reset 12–128 characters; login accepts 1–128 to accommodate provider accounts. CSRF token refreshed after session/challenge rotation. Login MFA_REQUIRED creates limited session cookie; AAL2 verify rotates session; callback uses provider PKCE code/state, allowlisted local redirect. No token, TOTP QR or signed URL may enter logs. All auth responses no-store, including errors. Register/recovery 202 generic; logout/reset 204. Provider settings and exact callback behavior require SDK integration testing.
