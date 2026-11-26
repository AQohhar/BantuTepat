# API coverage and DTO index — v1.2

Candidate schema decisions; review with FE/BE. Canonical machine contract: `openapi.phase0.json`. All paths relative to `/api/v1`.

| Section | Method | Path | Request DTO | Response DTO |
|---|---|---|---|---|
| 16.1 | GET | /public/programs | — | PublicProgramPage |
| 16.1 | GET | /public/programs/{slug} | — | PublicProgramDetail |
| 16.1 | GET | /public/territories | — | TerritoryPage |
| 16.1 | GET | /public/stats | — | PublicStats |
| 16.1 | GET | /public/faq | — | FaqList |
| 16.1 | POST | /web-chat | ChatRequest | ChatResult |
| 16.1 | GET | /health | — | HealthResult |
| 16.2 | GET | /me | — | Me |
| 16.2 | POST | /data-proposals/drafts | DraftCreate | DraftResult |
| 16.2 | PATCH | /data-proposals/drafts/{id} | DraftUpdate | DraftResult |
| 16.2 | POST | /data-proposals/{id}/submit | SubmitRequest | SubmitTicketResult |
| 16.2 | POST | /programs/{id}/applications/drafts | DraftCreate | DraftResult |
| 16.2 | POST | /program-applications/{id}/submit | SubmitRequest | SubmitTicketResult |
| 16.2 | GET | /cases | — | CasePage |
| 16.2 | GET | /cases/{id} | — | CaseDetail |
| 16.2 | POST | /cases/{id}/resubmit | ResubmitRequest | ResubmitTicketResult |
| 16.2 | POST | /cases/{id}/appeals | AppealCreate | AppealTicket |
| 16.2 | GET | /appeals/{id} | — | AppealTicket |
| 16.2 | POST | /complaints | ComplaintCreate | ComplaintResult |
| 16.2 | POST | /cases/{id}/feedback | FeedbackCreate | IdResult |
| 16.2 | POST | /identity-checks/face | FaceRequest | IdentityResult |
| 16.2 | POST | /identity-checks/manual-request | ManualRequest | ManualResult |
| 16.3 | POST | /agency/programs/drafts | ProgramDraftCreate | ProgramWriteResult |
| 16.3 | PATCH | /agency/programs/{id}/draft | ProgramDraftUpdate | ProgramWriteResult |
| 16.3 | POST | /agency/programs/{id}/submit-publication | VersionReason | ProgramWriteResult |
| 16.3 | POST | /agency/programs/{id}/publish | VersionReason | ProgramWriteResult |
| 16.3 | POST | /agency/programs/{id}/return | VersionReason | ProgramWriteResult |
| 16.3 | GET | /officer/cases | — | CasePage |
| 16.3 | POST | /officer/cases/{id}/verify | VerifyRequest | TicketResult |
| 16.3 | POST | /officer/cases/{id}/request-revision | RevisionRequest | TicketResult |
| 16.3 | GET | /agency/reviews | — | CasePage |
| 16.3 | POST | /agency/cases/{id}/review | ReviewRequest | TicketResult |
| 16.3 | POST | /agency/appeals/{id}/review | AppealReviewRequest | AppealTicket |
| 16.3 | GET | /audit/events | — | AuditPage |
| 16.3 | GET | /internal/metrics | — | InternalMetrics |
| 16.3 | POST | /admin/users/{id}/roles | RoleChange | RoleChangeResult |
| 16.3 | GET | /admin/integrations/health | — | IntegrationHealth |
| AUTH | GET | /auth/csrf | — | CsrfResult |
| AUTH | POST | /auth/register | RegisterRequest | GenericAccepted |
| AUTH | POST | /auth/login | LoginRequest | LoginResult |
| AUTH | POST | /auth/mfa/enroll | MfaEnroll | MfaEnrollment |
| AUTH | POST | /auth/mfa/verify | MfaVerify | LoginSession |
| AUTH | POST | /auth/recovery | RecoveryRequest | GenericAccepted |
| AUTH | GET | /auth/callback | — | — |
| AUTH | POST | /auth/reset-password | ResetPassword | — |
| AUTH | POST | /auth/logout | — | — |

17 pre-existing §16.7 operations are preserved in the same OpenAPI. §16.4 internal service contracts remain separate and must be frozen before their integration.

## Candidate DTO choices to approve

- DTO field names use snake_case and UUID IDs, version counters, UTC date-time, cursor pagination; no runtime contract or client has been generated.
- Draft answers are versioned dynamic forms, not arbitrary persisted data. Server validates permitted keys/types against the selected rules snapshot; never mass-assign. Program draft may be incomplete; publication must validate all mandatory fields, rules and jurisdiction.
- `expected_version` is required on submit; resubmit returns `UNDER_REVIEW` only after successful atomic revalidation. Case and ticket identity never change.
- Public `can_apply` is a preview, never a guarantee. Public stats require suppression; private DTOs require scope/redaction.
- `POST /officer/cases/{id}/verify` recommends only; revision uses its dedicated endpoint. For case-review actions refer to shared transitions.
- Auth password minimum and BFF cookie/challenge response schemas are candidate decisions documented in AUTH_SECURITY. Dynamic rules, staff invitations, internal M2M and provider callback integration require separate implementation validation.
- P2 face remains disabled by default; the schema does not activate the feature.
