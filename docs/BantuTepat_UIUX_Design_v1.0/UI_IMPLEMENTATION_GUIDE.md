# UI_IMPLEMENTATION_GUIDE.md — Codex execution plan
Target PRD v1.2, Design v1.0. Tidak ada perubahan terhadap domain, state, izin, database atau API oleh dokumen desain ini. Implementasi tidak dimulai dengan menyalin template atau memasang semua fitur sekaligus.

## 1. Phase 0: kontrak dan setup
1. Commit PRD v1.2 dan artefak phase0 ke repo sesuai TASK-01; letakkan enam dokumen desain, token, gap dan validation di `docs/design/`. Simpan master spec sebagai sumber historis. Review dan sign-off UI-D01–D07. Konfirmasi gap G01–G13 dengan BE; freeze kontrak scoped sebelum layar yang membutuhkan data dirilis. P0 blockers tidak boleh dianggap resolved karena placeholder. Jangan menggunakan file v1.0/v1.1 untuk enum/route.
2. Baca lockfile existing, pilih Next.js App Router + React/TypeScript strict dan Tailwind versi repo. Contoh tokens.css v4; bila v3 mapping manual. Install hanya dependency yang dibutuhkan dan sudah diverifikasi lisensi; catat versi di `phase0/ASSET_LICENSE_INVENTORY.csv`. Original composition, not template. Font self-host hanya jika file dan lisensi tersedia, fallback system bekerja. Jangan menyalin logo pemerintah.
3. Generate typed client + runtime validation dari canonical OpenAPI setelah kontrak dibekukan. Shared policy package menyediakan enum/action domain; UI label map terpisah. Jangan menciptakan `GUEST` enum DB. Server components atau BFF/domain call server-only, browser hanya `/api/v1`. Implementasi modular monolith + worker sesuai PRD. No client service credentials.
4. Build layout, tokens, component stories/fixtures (synthetic, dev/test only), error boundaries, permission guards, auth/session. Verify keyboard, focus, responsive before wiring. Do not expose fixtures in release build.

## 2. Struktur repo yang disarankan (kompatibel MS §23/PRD §17)
```text
bantutepat/
  docs/design/                         # dokumen ini + tokens.css reference
  apps/web/src/
    app/
      layout.tsx                       # lang=id, font, skip link
      globals.css                      # Tailwind import + canonical tokens
      (public)/layout.tsx
      (public)/page.tsx                # /
      (public)/program/page.tsx
      (public)/program/[slug]/page.tsx
      (public)/panduan/page.tsx
      (public)/transparansi/page.tsx
      (public)/bantuan/page.tsx
      (public)/privasi/page.tsx
      (public)/syarat-layanan/page.tsx
      (public)/aksesibilitas/page.tsx
      (auth)/login/page.tsx
      (auth)/daftar/page.tsx
      (auth)/lupa-password/page.tsx
      (citizen)/layout.tsx
      (citizen)/dashboard/page.tsx
      (citizen)/pengajuan/baru/page.tsx
      (citizen)/pengajuan/usulan/page.tsx
      (citizen)/program/[slug]/daftar/page.tsx
      (citizen)/pengajuan-saya/page.tsx
      (citizen)/pengajuan-saya/[caseId]/page.tsx
      (citizen)/pengajuan-saya/[caseId]/perbaiki/page.tsx
      (citizen)/pengajuan-saya/[caseId]/sanggah/page.tsx
      (citizen)/pengaduan/page.tsx
      (citizen)/feedback/[caseId]/page.tsx
      (citizen)/profil/page.tsx
      (citizen)/verifikasi-identitas/page.tsx
      (territory)/wilayah/layout.tsx
      (territory)/wilayah/page.tsx
      (territory)/wilayah/usulkan/page.tsx
      (territory)/wilayah/kasus/page.tsx
      (territory)/wilayah/verifikasi/[id]/page.tsx
      (agency)/instansi/layout.tsx
      (agency)/instansi/page.tsx
      (agency)/instansi/review/page.tsx
      (agency)/instansi/review/[id]/page.tsx
      (agency)/instansi/sanggahan/page.tsx
      (agency)/instansi/program/page.tsx
      (agency)/instansi/program/baru/page.tsx
      (agency)/instansi/program/[id]/edit/page.tsx
      (agency)/instansi/program/review-publikasi/page.tsx
      (admin)/administrasi/layout.tsx
      (admin)/administrasi/pengawasan/page.tsx
      (admin)/administrasi/audit/page.tsx
      (admin)/administrasi/pengaduan/page.tsx
      (admin)/administrasi/pengguna/page.tsx
      (admin)/administrasi/wilayah/page.tsx
      (admin)/administrasi/integrasi/page.tsx
      api/v1/...                       # BFF routes, contract only
      not-found.tsx                    # safe public generic
      error.tsx                        # safe error, no internals
    components/
      layout/  PublicHeader.tsx PrivateSidebar.tsx MobileNavigation.tsx PageHeader.tsx
      ui/      Button.tsx Field.tsx Select.tsx Dialog.tsx StatusBadge.tsx
               EmptyState.tsx ErrorState.tsx LoadingState.tsx InlineAlert.tsx
      program/ ProgramCard.tsx
      case/    CaseCard.tsx CaseTimeline.tsx ReviewPanel.tsx RevisionRequest.tsx
               DecisionReason.tsx FormWizard.tsx DocumentUploader.tsx
      data/    StatCard.tsx DataTable.tsx SearchFilter.tsx AuditTimeline.tsx
      auth/    RoleGuard.tsx PrivacyNotice.tsx
      shared/  ConfirmationDialog.tsx PolicyCitation.tsx ServiceDegradedBanner.tsx NotificationPanel.tsx
    lib/
      api/                             # typed same-origin adapter, csrf, error normalization
      ui/labels.ts                     # exhaustive domain enum maps
      ui/navigation.ts                 # allowed routes + permission tags
      ui/format.ts                     # id-ID date/number; server timezone
    server/                            # auth, scope & domain logic per PRD
  packages/contracts/                 # frozen OpenAPI/Zod/types/client
  packages/policy/                    # enum/transitions single source of truth
  packages/ui/                        # optional shared primitives; no duplicate component tree
```
Route groups do not add URL segments. `/program/[slug]` public and `/program/[slug]/daftar` citizen resolve different concrete paths; build verify no duplicate route. `/administrasi` bare root not specified as feature page; optional server redirect to first authorized canonical child only after approval (otherwise no route). Shared layouts must not fetch private data in public routes. Add `loading.tsx`/`error.tsx` per group as appropriate, with local Suspense boundaries. Program draft resume via existing route only once query/ID contract approved (G05). Detail of RT represented case needs G09; do not give RT citizen owner route permission implicitly.

## 3. Reference examples React / Tailwind
Examples below are snippets, not tested production components. Adapt imports to actual repo paths, use generated types and audited primitives. Do not add dependencies solely for these examples.

### 3.1 Global CSS
```css
@import "tailwindcss";
@import "./tokens.css";
html { color-scheme: light; }
body { font-family: var(--bt-font); background: var(--bt-canvas); color: var(--bt-ink); }
:focus-visible { outline: 2px solid var(--bt-focus); outline-offset: 2px; }
```
Copy token reference to canonical runtime stylesheet; docs copy must match (CI hash or generate from same source). Keep font fallback until license approved; if next/font local, map CSS variable to `--bt-font` without loading remote assets.

### 3.2 Button primitive
```tsx
import type { ButtonHTMLAttributes } from 'react';
type Variant = 'primary' | 'secondary' | 'tertiary' | 'destructive';
const variants: Record<Variant, string> = {
  primary: 'bg-primary text-on-primary hover:bg-primary-hover active:bg-primary-pressed',
  secondary: 'border border-control-line bg-panel text-primary hover:bg-brand-soft',
  tertiary: 'bg-transparent text-primary hover:bg-brand-soft',
  destructive: 'bg-danger text-on-primary hover:bg-danger-pressed',
};
type Props = ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: Variant; pending?: boolean; pendingLabel?: string;
};
export function Button({variant='primary', pending=false, pendingLabel='Memproses…',
  disabled=false, type='button', children, className='', ...rest}: Props) {
  return <button {...rest} type={type} disabled={disabled || pending}
    aria-busy={pending || undefined}
    className={`inline-flex min-h-11 items-center justify-center gap-2 rounded-control px-4 py-2 text-ui font-semibold
    focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2
    disabled:cursor-not-allowed disabled:bg-disabled-bg disabled:text-disabled-text ${variants[variant]} ${className}`}>
    {pending ? pendingLabel : children}
  </button>;
}
```
Use live region outside disabled button for asynchronous result; confirm dialogs need focus restoration. Do not rely on aria-disabled alone for actual mutation. For pending and disabled state, ensure class conflicts are merged deterministically in actual implementation. Never wrap button in link.

### 3.3 Typed case badge (labels separated from business state)
```tsx
import type { CaseState } from '@bantutepat/policy'; // adapt package name
import { FileText, Clock, AlertTriangle, CheckCircle2, CircleX } from 'lucide-react';
type Tone = 'neutral'|'info'|'warning'|'success'|'danger';
const label = {
 DRAFT: ['Draf','neutral'], SUBMITTED: ['Pengajuan terkirim','info'],
 PRECHECKED: ['Pemeriksaan awal sistem selesai','info'],
 UNDER_REVIEW: ['Sedang diperiksa petugas','info'],
 REVISION_REQUIRED: ['Perlu perbaikan','warning'], RESUBMITTED: ['Perbaikan terkirim','info'],
 PENDING_APPROVAL: ['Menunggu tinjauan instansi','info'],
 RECOMMENDED_FOR_FORWARD: ['Direkomendasikan untuk tindak lanjut','info'],
 FORWARDING_PENDING: ['Menunggu penerusan (simulasi)','info'],
 FORWARDING_FAILED: ['Penerusan simulasi terkendala','warning'],
 FORWARDED_SIMULATED: ['Diteruskan (simulasi)','success'],
 CLOSED_UNPROCESSABLE: ['Tidak dapat diproses','danger'], NOT_SELECTED: ['Tidak terpilih','danger'],
 CLOSED: ['Proses ditutup','neutral'],
} as const satisfies Record<CaseState, readonly [string, Tone]>;
const toneClass: Record<Tone,string> = {
 neutral:'bg-neutral-bg text-neutral-ink', info:'bg-info-bg text-info',
 warning:'bg-warning-bg text-warning', success:'bg-success-bg text-success', danger:'bg-danger-bg text-danger',
};
const icons = {neutral: FileText, info: Clock, warning: AlertTriangle, success: CheckCircle2, danger: CircleX};
export function CaseStatusBadge({state}:{state:CaseState}) {
 const [text,tone] = label[state] ?? ['Status belum dikenali','neutral'];
 const Icon = icons[tone];
 return <span className={`inline-flex max-w-full items-center gap-1.5 rounded-status px-2.5 py-1 text-caption font-semibold ${toneClass[tone]}`}>
  <Icon aria-hidden="true" className="size-4 shrink-0"/><span className="min-w-0 break-words">{text}</span>
 </span>;
}
```
Implement other domains with their own exhaustive maps. Unknown enum from unvalidated response must not enable actions; production DTO validation should reject or safely degrade. Badge does not determine permissions.

### 3.4 Layout composition (server boundary)
```tsx
// Illustrative layout body; session and allowed nav already resolved server-side.
export function PrivateShell({sidebar, topbar, children}: {
 sidebar: React.ReactNode; topbar: React.ReactNode; children: React.ReactNode;
}) {
 return <div className="min-h-dvh bg-canvas text-ink desktop:grid desktop:grid-cols-[248px_minmax(0,1fr)]">
  <a href="#main" className="sr-only focus:not-sr-only focus:fixed focus:z-50 focus:bg-panel focus:p-3">Lewati ke konten utama</a>
  <aside className="hidden desktop:block desktop:sticky desktop:top-0 desktop:h-dvh overflow-y-auto border-r border-line bg-panel">{sidebar}</aside>
  <div className="min-w-0">
   <header className="sticky top-0 z-20 bg-panel border-b border-line">{topbar}</header>
   <main id="main" tabIndex={-1} className="mx-auto w-full max-w-[1440px] px-4 py-6 tablet:px-6 desktop:px-8 desktop:py-8">{children}</main>
  </div>
 </div>;
}
```
`topbar` includes mobile drawer navigation. Pass only sanitized nav/view props; no access token or raw data into client props. Use semantic landmarks; ensure sticky topbar does not cover focused anchor (`scroll-padding-top` and `scroll-margin-top`).

### 3.5 Field example
```tsx
<label htmlFor="reason" className="block text-ui font-medium">Alasan <span>(wajib)</span></label>
<textarea id="reason" name="reason" required maxLength={2000}
 aria-invalid={Boolean(errors.reason)} aria-describedby="reason-help reason-error"
 className="mt-2 min-h-28 w-full rounded-control border border-control-line bg-panel p-3 text-body aria-invalid:border-danger" />
<p id="reason-help" className="mt-2 text-ui text-muted">Jelaskan alasan berdasarkan dokumen dan aturan yang diperiksa.</p>
<p id="reason-error" className="mt-1 text-ui text-danger">{errors.reason ?? ''}</p>
```
For dynamic forms generate label/field from approved schema, not arbitrary answers. Native validation should complement, not replace server error handling. Do not display backend stack trace. For 422 move focus to error summary with links to field IDs. Avoid losing data on retry; use controlled form state where appropriate.

## 4. Data binding and permissions
- Read: server-authenticated loader validates DTO; no-store for private. Public SSR/cache only approved published data; invalidate on publication/version changes. GET filters validated, abort stale requests; cursor opaque. Avoid passing private filters in URL/history/log.
- `GET /me` supplies valid scope/permission list; resolve label via safe lookup; AAL2 staff gate server. RoleGuard only controls rendering; each API checks owner/assignment/mandate/jurisdiction/state/maker-checker. No `isAdmin => allow all`.
- `CaseDetail.available_actions` is untyped string array in v1.2; freeze vocabulary G06 before action buttons. UI action availability requires permission AND server capability AND relevant feature flag, not string inference from status. If no capability contract, don't wire action. Current action response must be refetched after mutation, including notification and list invalidation.
- Auth: same-origin secure cookie `__Host-bt_session`, no localStorage tokens. `GET /auth/csrf` synchronizer token in memory; use `X-CSRF-Token` for protected browser mutations per auth contract; refresh on rotation. Public endpoints follow canonical OpenAPI and security policy; `/web-chat` currently public, review CSRF/rate-limit policy without silently changing contract. No access/refresh tokens returned to browser. CSRF token never in logs. Staff MFA after login challenge; generic recovery/register messaging. `POST /auth/logout` then clear private view/cache and redirect login.
- Mutations: validate DTO, use `expected_version` where schema requires, `Idempotency-Key` where contract requires; key belongs to logical operation and payload, reuse only same payload after ambiguous timeout; changed payload uses new key after state refresh. Button pending state prevents multiple click but server is source of idempotency. Persist new revision only once; no fake ticket. Upload gets case_version from response; use latest approved version, not arbitrary +1.
- Errors: parse canonical `Error` schema (`request_id`, `code`, `message`, `field_errors`, `retryable`, `actions`); allowlisted actions localized, never raw error HTML. 400 malformed; 401 login; 403/404 generic; 409 conflict/duplicate read latest; 422 error summary; 429 delay based on server if provided; 503 only dependent service. Retry GET safely; no automatic retries of ambiguous mutations except explicit idempotent contract. 204 no JSON parse.
- Attachments: never log signed URLs or render them as persistent links. Mint on click after scope/CLEAN check, use short TTL/no-store, safe download handling. No unauthorized preview. Cancel in-flight request on unmount, clear file data after use. No browser persistence of PII.
- AI / simulator: render safe status only from server. AI advisory with model/source if DTO approved; pending/unavailable manual fallback, no eligibility score UI. RAG safe text and citations; no HTML/raw prompt. WA menu handoff only; no LLM on WA; label mock. P2 disabled default, no biometric prompt by default.

## 5. Implementation sequence (not replacement for PRD task IDs)
| Stage | Deliverable | Gate |
|---|---|---|
| UI-00 Foundation | Tokens, layouts, primitives, route inventory, labels, fixtures, auth client | TASK-01 commit, PRD/FE/BE sign-off, no open blocker for current screen |
| UI-01 Public + auth | PUB-01…09 + AUTH-01…03 | published-only catalog, accessible auth+MFA, privacy content, suppression and fallback |
| UI-02 Citizen P0 | CIT-01…12 | two independent flows, drafts/attachments/revision/appeal/complaint/feedback; no duplicate ticket; manual fallback |
| UI-03 Regional + agency P0 | REG-01…04, AGY-01…08 | assigned queue, maker-checker, reasoned review, program version & publication, appeal independence |
| UI-04 Administration P0 | ADM-01…06 | scoped audit/metrics/complaint/grants, no unauthorized data, health without secrets |
| UI-05 Innovation P1 | in-page AI, simulator, RAG, WA, knowledge | P0 E2E/security passed; feature flags & fallback; P1 contracts approved |
| UI-06 Optional P2 | identity face/OCR only if gates met | P0/P1 stable, explicit approval, consent and manual alternative |
| UI-07 Release | visual QA, responsive, accessibility, performance, security, demo evidence | no dummy controls; all PRD acceptance tests; originals/license inventory complete |

UI implementation can prepare fixture-based components while blocked contract is reviewed, but cannot claim live completion. Do not commit secrets or publish mock API. Ensure no changes to PRD/contract without ADR and sign-off.

## 6. QA and acceptance checklist (design-specific, supplementary to PRD T-01…60)
| ID | Check / measurable acceptance |
|---|---|
| UI-T01 | All 42 routes exist or are explicitly blocked by approved gap, navigation no orphan/dummy link; Indonesian copy. |
| UI-T02 | Tokens used across all role dashboards, 7 roles + guest; screenshots reviewed at 360/390/430/768/1280/1440. |
| UI-T03 | No body horizontal overflow at 360/320; localized table scroll; zoom 200% usable; safe-area bottom bar. |
| UI-T04 | Keyboard only: navigation, drawer, filters, form wizard, confirmation, upload, data table, focus restore. |
| UI-T05 | axe automated no critical/serious and manual WCAG target: labels, contrast, error association, status announcement, focus not obscured. |
| UI-T06 | Every screen's 8 states validated by fixtures/test, N/A justified; no fake stats, empty distinct from error, partial failure isolated. |
| UI-T07 | Login/register/recovery generic response, staff AAL2, no session/token in localStorage, 401 redirect safe. |
| UI-T08 | Cross-user/region/agency direct URL + forged mutation denied server; generic 403/404, no flash of PII. |
| UI-T09 | Program SELF/OFFICER_ONLY/BOTH/NO_PUBLIC_REGISTRATION & closed mode display appropriate CTA; no false eligibility. |
| UI-T10 | Data proposal independent program; program wizard versioned dynamic rules; required docs, 422, 409. |
| UI-T11 | Attachment scan pending/rejected/no download; clean signed download scoped, expired retry; no cached PII. |
| UI-T12 | Submit repeated click/ambiguous response same logical key one ticket; resubmit same case/ticket, revision atomic, invalid 422 no change. |
| UI-T13 | Case status and appeals all enum labels exhaustive; correct next action, decision reason/appeal deadline server-backed; no invented progress. |
| UI-T14 | RT mandate expiry, operator assignment, verifier independence; maker-checker blocked despite UI tampering. |
| UI-T15 | Program draft immutable published version, independent publication, archive only closed. |
| UI-T16 | Complaint confidential, separate ticket; feedback no case state changes. |
| UI-T17 | Public suppression not zero, no private data drilldown; internal audit read-only, scoped and redacted. |
| UI-T18 | Admin fresh AAL2/grant approval, no self-grant, no global case access; integration secrets never rendered. |
| UI-T19 | AI unavailable → manual; P0 no false forwarded; P1 ACK only; RAG fallback FAQ, no private prompt; P2 default off. |
| UI-T20 | Asset license inventory, original visual review, performance p95 target measured in deployed environment; no claim before results. |

Recommended tests: component unit/story fixtures, Playwright E2E with synthetic role accounts, accessibility axe + manual keyboard and screen reader, contract tests generated from OpenAPI, browser network throttling, visual diff for viewport and state. Each PR records screen IDs, FR/T-ID references, test evidence, accessibility and security checks. Visual snapshot changes reviewed, never auto-approved. Data fixtures are test-only; never expose real identity.

## 7. Codex handoff prompt
```text
Implement BantuTepat UI/UX against PRD v1.2 and docs/design/DESIGN.md.
Read all 6 design docs, tokens.css, UI_CONTRACT_GAPS.md, shared policy,
OpenAPI, role matrix, workflows and acceptance criteria before coding.
Do not change domain logic, route URLs, role permissions, enum states,
API contracts or feature flags. Use approved design tokens and original
components. All visible interface labels Bahasa Indonesia. P0 first.
If required data/API is missing, record the exact gap and block live wiring;
use synthetic fixtures only for development/test, not production. Do not
invent endpoints or fake a successful action. Implement one coherent stage
at a time, run typecheck/test/build/accessibility checks, and report screen
IDs, changed files, open blockers and actual test outcomes.
```
