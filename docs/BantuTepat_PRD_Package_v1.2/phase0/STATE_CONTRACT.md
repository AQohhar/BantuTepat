# State contract v1.2 — kandidat freeze

### 12.1–12.2 Status kasus (kanonik, kedua tipe)

`DRAFT → SUBMITTED → PRECHECKED → UNDER_REVIEW → PENDING_APPROVAL → RECOMMENDED_FOR_FORWARD`.

- `SUBMITTED → PRECHECKED` mencatat hasil pemeriksaan valid yang sudah dilakukan server saat submit. Data proposal memeriksa kelengkapan dan mandat, bukan eligibility program. Submit invalid tetap DRAFT/REVISION_REQUIRED dan 422; tidak menciptakan tiket baru. Kedua event dicatat dalam transaksi submit; respons akhir `PRECHECKED`.
- Revisi: `UNDER_REVIEW → REVISION_REQUIRED → RESUBMITTED → UNDER_REVIEW` (resubmission + revalidation atomik; response `UNDER_REVIEW`). Recheck: `PENDING_APPROVAL → UNDER_REVIEW`.
- P0 berhenti pada `RECOMMENDED_FOR_FORWARD` (hasil internal); P1 `RECOMMENDED_FOR_FORWARD → FORWARDING_PENDING → FORWARDED_SIMULATED` atau `FORWARDING_FAILED → FORWARDING_PENDING` untuk retry. `CLOSED_UNPROCESSABLE` tersedia bagi kedua tipe; `NOT_SELECTED` hanya program dengan aturan dan kewenangan sah.
- `CLOSED` hanya dari hasil terminal yang diizinkan dalam tabel; kegagalan forwarding tidak menjadi sukses atau ditutup diam-diam. Status hasil sebelum penutupan tetap tersimpan pada decision/timeline.

### 12.3 Status sanggahan

`APPEAL_SUBMITTED → APPEAL_REVIEW → UPHELD | RECHECK_REQUIRED | DECISION_CORRECTED → APPEAL_CLOSED`. Objek terpisah. Appeal hanya terhadap `CLOSED_UNPROCESSABLE` atau `NOT_SELECTED` yang eligible; satu aktif per kasus; waktu sanggah berasal dari policy versi. Koreksi/recheck membuka tugas baru, tidak menimpa keputusan lama. Tidak ada reopening bebas setelah CLOSED.

### 12.4 Status pengaduan/feedback

`RECEIVED → TRIAGED → ASSIGNED → IN_HANDLING → RESOLVED | REFERRED → CLOSED`. Guest tidak submit pengaduan dalam MVP. Feedback `SUBMITTED`, tidak mengubah eligibility.

### 12.5 Invariant transition

Single source of truth: `phase0/packages/policy/transitions.ts` dan `phase0/STATE_CONTRACT.md`. Default deny; setiap mutasi memeriksa aktor, izin, scope, assignment, mandat aktif, maker-checker, status dan expected version. Audit/decision/revision/outbox atomik. Tidak ada PATCH status generik. SYSTEM bukan role akun; hanya service internal yang terotorisasi. Semua tabel di bawah/diagram mengikuti kontrak ini.

| Action | From | To | Actor / permission | Guard | Case type |
| --- | --- | --- | --- | --- | --- |
| SUBMIT | DRAFT | SUBMITTED | case.create.self / case.create.represented | Owner/active mandate; rules valid; idempotency; version; no duplicate active ticket | both |
| PRECHECK | SUBMITTED | PRECHECKED | SYSTEM | Persist validation snapshot; program rules or data completeness; no eligibility decision; pre-submit failure returns 422 | both |
| ASSIGN | PRECHECKED | UNDER_REVIEW | case.assign | Scoped operator; active task unique; no self-review bypass | both |
| REQUEST_REVISION | UNDER_REVIEW | REVISION_REQUIRED | case.verify.assigned | Assigned operator; reason, fields/evidence, deadline | both |
| RESUBMIT | REVISION_REQUIRED | RESUBMITTED | case.revise.own | Owner/mandate; same ticket; new immutable revision; deadline | both |
| REVALIDATE | RESUBMITTED | UNDER_REVIEW | SYSTEM | Revalidate rules and revision, restore task; invalid submit returns 422 before commit | both |
| RECOMMEND | UNDER_REVIEW | PENDING_APPROVAL | case.verify.assigned | Assigned operator; evidence and reason; creates independent review task | both |
| RECHECK | PENDING_APPROVAL | UNDER_REVIEW | case.review.assigned | Independent reviewer; reason and task | both |
| RECOMMEND_FORWARD | PENDING_APPROVAL | RECOMMENDED_FOR_FORWARD | case.review.assigned | Independent reviewer; rule/evidence/next step; P0 result only | both |
| QUEUE_FORWARD | RECOMMENDED_FOR_FORWARD | FORWARDING_PENDING | SYSTEM | P1 simulator enabled; persist outbox and correlation ID; no success claim | both |
| ACK_FORWARD | FORWARDING_PENDING | FORWARDED_SIMULATED | SYSTEM | Verified simulator ACK matches case, version and correlation; synthetic only | both |
| FAIL_FORWARD | FORWARDING_PENDING | FORWARDING_FAILED | SYSTEM | Timeout/failure recorded; no ACK; preserve reason | both |
| RETRY_FORWARD | FORWARDING_FAILED | FORWARDING_PENDING | SYSTEM | P1 enabled; bounded retry with same logical correlation/idempotency; no duplicate | both |
| CLOSE_UNPROCESSABLE | PENDING_APPROVAL | CLOSED_UNPROCESSABLE | case.review.assigned | Independent reviewer; reason, evidence, rule and appeal policy | both |
| NOT_SELECT | PENDING_APPROVAL | NOT_SELECTED | case.review.assigned | Authorized program-specific rule only; reason/evidence; no official benefit determination | program |
| CLOSE | RECOMMENDED_FOR_FORWARD | CLOSED | SYSTEM | P0 only; internal result delivered in web inbox, appeal window expired or no appeal allowed; no active appeal/forward job | both |
| CLOSE | FORWARDED_SIMULATED | CLOSED | SYSTEM | Result in inbox; appeal window expired or not eligible; no active appeal | both |
| CLOSE | CLOSED_UNPROCESSABLE | CLOSED | SYSTEM | Result in inbox; appeal window expired or not eligible; no active appeal | both |
| CLOSE | NOT_SELECTED | CLOSED | SYSTEM | Result in inbox; appeal window expired or not eligible; no active appeal | program |
| APPEAL_RECHECK | CLOSED_UNPROCESSABLE | UNDER_REVIEW | SYSTEM | Independent appeal decision RECHECK_REQUIRED or DECISION_CORRECTED; preserve prior decision; new task | both |
| APPEAL_RECHECK | NOT_SELECTED | UNDER_REVIEW | SYSTEM | Independent appeal decision RECHECK_REQUIRED or DECISION_CORRECTED; preserve prior decision; new task | program |

## Appeal

| Action | From | To | Actor / permission | Guard |
| --- | --- | --- | --- | --- |
| ASSIGN_APPEAL | APPEAL_SUBMITTED | APPEAL_REVIEW | appeal.assign | Reviewer assigned by authorized coordinator; different from all prior decision makers; no self assignment |
| UPHOLD | APPEAL_REVIEW | UPHELD | appeal.review.independent | Reason and evidence, prior decision preserved |
| RECHECK | APPEAL_REVIEW | RECHECK_REQUIRED | appeal.review.independent | Atomic parent APPEAL_RECHECK and new task |
| CORRECT | APPEAL_REVIEW | DECISION_CORRECTED | appeal.review.independent | Correction rationale; parent APPEAL_RECHECK, no automatic new outcome |
| CLOSE | UPHELD | APPEAL_CLOSED | SYSTEM | Result available in inbox |
| CLOSE | RECHECK_REQUIRED | APPEAL_CLOSED | SYSTEM | Result in inbox and linked new task exists |
| CLOSE | DECISION_CORRECTED | APPEAL_CLOSED | SYSTEM | Result in inbox and linked new task exists |

## Complaint

| Action | From | To | Actor / permission | Guard |
| --- | --- | --- | --- | --- |
| TRIAGE | RECEIVED | TRIAGED | complaint.manage.assigned | Scoped triage; category and confidentiality |
| ASSIGN | TRIAGED | ASSIGNED | complaint.assign | Scoped handler, no self assignment |
| START | ASSIGNED | IN_HANDLING | complaint.manage.assigned | Assigned handler |
| RESOLVE | IN_HANDLING | RESOLVED | complaint.manage.assigned | Outcome and reason |
| REFER | IN_HANDLING | REFERRED | complaint.manage.assigned | Referral destination and reason; no actual external transmission claim |
| CLOSE | RESOLVED | CLOSED | complaint.manage.assigned | Outcome communicated via inbox |
| CLOSE | REFERRED | CLOSED | complaint.manage.assigned | Referral communicated via inbox |

Creation: appeal POST creates APPEAL_SUBMITTED with parent decision reference, complaint POST creates RECEIVED; idempotency and eligibility required. Assignment/reassignment without state change is permitted only via explicit scoped action and audit, not generic status updates.
