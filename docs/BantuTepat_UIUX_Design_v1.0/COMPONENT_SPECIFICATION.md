# COMPONENT_SPECIFICATION.md — BantuTepat v1.0
Komponen murni presentational sejauh mungkin; container/adapters mengambil data dan capability dari server. Semua props memakai tipe hasil generate dari OpenAPI dan shared policy, bukan enum duplikat. `onAction` hanya diberikan bila tindakan memang tersedia. Semua komponen menerima `className` terbatas untuk layout, bukan mengubah semantic color. Gunakan primitives accessible buatan tim atau headless library yang telah dilisensikan, bukan template dashboard.

## 1. Katalog komponen reusable
| Komponen | Props / slots utama (konseptual) | Anatomi & perilaku | State, aksesibilitas, aturan |
|---|---|---|---|
| PublicHeader | items, currentPath, authContext, onMenu | brand mark + nav + Masuk/Daftar atau dashboard; desktop bar 72; mobile 64 | nav landmark, aria-current page, drawer focus; status sesi dari server; tidak ada role privat di guest |
| PrivateSidebar | activeScope, allowedItems, currentPath | brand, label kelompok, scope ringkas, nav, bantuan; 248px; active item bg brand-selected + garis kiri | menu hanya permission sah; link keyboard, aria-current; logout di menu akun bukan aksi mutasi GET |
| MobileNavigation | items, activePath, variant | drawer semua role; bottom nav warga max 4 item + menu bila perlu; label tetap | drawer trap/restore; tidak ada bottom bar di wizard/review; safe-area; tidak menutupi fokus |
| PageHeader | breadcrumb?, title, description?, actions?, meta? | breadcrumb → h1 → deskripsi dan satu primary CTA; desktop actions kanan | h1 satu per halaman; wrap di mobile; aksi forbidden tidak dirender, disabled reason terlihat |
| StatCard | label, value:number/null, unit?, description?, asOf?, suppressed? | label → angka tabular → definisi/tanggal | suppressed = "Disembunyikan untuk privasi"; null = "Belum tersedia"; 0 hanya data asli; tanpa fake trend, klik hanya bila drilldown tersedia |
| ProgramCard | PublicProgram, territoryLabels?, href | status + nama program → instansi → periode, wilayah, cara daftar → "Lihat detail" | status/mode pakai label; jangan tampilkan kuota tak ada DTO; link tunggal, jangan nested interactive; skeleton ukuran tetap |
| CaseCard | CaseSummary, permittedActions?, href | tipe + tiket/draft → status → tanggal → tindakan sah | tidak menghitung eligibility; draft no ticket; mobile list; jika CTA tidak didukung hanya detail |
| StatusBadge | domain, state | icon 16 + label + tone; label wrap, noninteractive | exhaustive typed mapping §3; fallback netral "Status belum dikenali" + dev test; bukan keputusan kelayakan |
| CaseTimeline | TimelineItem[], currentState, disclosure? | daftar ol chronological, titik+garis, judul/status, timestamp, safe_message; latest diberi teks "Terakhir" | event aktual saja; tidak buat estimasi/progress %; tidak render raw actor/PII; empty message |
| DataTable | rows, columns, getRowId, nextCursor, onNext, mobileView | table native, th scope, caption, row link/aksi; cursor pagination; sort/filter bila API | header tetap bila area scroll; aria-sort hanya jika sort tersedia; tidak row click-only; tidak fake totals; mobile card atau scroll untuk audit |
| SearchFilter | schema controls, values, onApply, onReset, pending | input search jika API; select status/mode/wilayah; filter chips, reset; mobile sheet | label, count hanya aktual, reset cursor saat filter berubah; jangan filter satu halaman lalu klaim seluruh dataset; filter privat tidak di URL bila berisi PII |
| FormWizard | steps, currentStep, values, fieldErrors, onBack, onNext, onSave, onSubmit | stepper label "Langkah x dari n"; field group, footer; review sebelum kirim | field errors aria-describedby; summary fokus; local-only transient values, save ke server; version/idempotency; warning navigasi sebelum data belum disimpan hilang |
| DocumentUploader | caseId, allowedTypes, maxBytes, attachmentRefs, onUpload, onDownload | pilih file native, drop enhancement, daftar file dan status; private quarantine | PDF/JPEG/PNG ≤10 MiB sesuai kontrak; server validasi; pending tidak dapat diunduh; rejected beri instruksi; download CLEAN via signed URL ≤60s; metadata scan refresh butuh gap G04 |
| ReviewPanel | evidence, rules, reasons, allowedActions, expectedVersion | header status, snapshot ringkas, flags, evidence, form keputusan; desktop 7/5 split | AI advisory, tidak preselect outcome; pilih action eksplisit, alasan/evidence/rule ref sesuai DTO, confirm; 409 reload; maker-checker server |
| AuditTimeline | AuditEvent[], redaction | waktu, aktor jika sah, action, object ref masked, metadata aman | read-only; jangan tampilkan raw payload/token/PII; beda dari timeline warga; tanpa update/delete |
| ConfirmationDialog | title, description, impactSummary, confirmLabel, cancelLabel, pending, onConfirm | header, ringkasan dampak, batal + aksi spesifik | role=dialog/alertdialog sesuai konteks; focus batal untuk aksi destruktif; trap/restore; jangan tutup saat commit belum jelas; no double submit |
| EmptyState | title, body, action?, variant | ikon opsional, heading, teks, link aksi | hanya setelah GET sukses kosong; filter no results vs belum ada data; no CTA jika forbidden |
| ErrorState | title, safeMessage, retry?, reference? | inline/panel; action retry atau bantuan | role alert bila baru terjadi; no raw stack/PII; 403/404 generic; mutation ambiguous retry key sama |
| LoadingState | variant, count?, label | skeleton bentuk konten (card/table/timeline/form); spinner pada aksi | aria-busy region, sr-only status; jangan focus skeleton; reduced motion; no fake values |

## 2. Primitives tambahan yang sudah dimandatkan Master Spec
| Komponen | Spesifikasi |
|---|---|
| Button / Link / IconButton | variant primary/secondary/tertiary/destructive; native type=button default, submit eksplisit; link untuk navigasi; pending aria-busy dan disable; reason visible di luar disabled button; hit target 44 |
| Field / Select / Textarea / RadioGroup / Checkbox | label di atas, required text + native required bila sesuai, helper sebelum error, error terkait id; native select bila sederhana; radio untuk ≤4 opsi; checkbox pernyataan tidak prechecked; long text wrap; don't block paste |
| PrivacyNotice | ringkasan tujuan, tautan `/privasi`, versi notice bila API; tidak menyamakan persetujuan privasi dengan persetujuan keputusan; tidak auto-centang |
| PolicyCitation | judul sumber, penerbit, tanggal/versi bila tersedia, link safe; sumber simulasi diberi label; jangan merender HTML LLM tanpa sanitasi |
| DecisionReason | hasil administratif + alasan + dasar aturan + next step, tanggal aktual; tanpa red/green-only; jika reason DTO belum ada tandai gap, jangan isi dengan teks fiktif |
| RevisionRequest | daftar field yang diminta, alasan, tenggat, CTA; tidak menghitung deadline dari client; fokus field pertama saat revisi; tiket tetap |
| RoleGuard | route boundary server-side, loading session, 401 login, 403/404 generic; client hanya presentation; permission pada /me bukan bukti objek berwenang |
| ServiceDegradedBanner | satu inline banner per layanan terdampak, pesan fallback manual; tidak global blocking untuk AI/WA/LLM; icon+teks |
| NotificationPanel | topbar bell + daftar title/safe_body, waktu, unread; GET recipient only dan POST read; jangan menciptakan link case dari data yang tidak ada; mobile full sheet |
| Dialog / Drawer / Dropdown / Tooltip | headless accessible primitives; dialog portal, inert background, focus trap/restore, Escape bila aman; dropdown keyboard/typeahead; tooltip dismissible, bukan label wajib; mobile drawer safe-area |
| InlineAlert | success/info/warning/danger icon+heading+body; inline errors persisten, toast hanya penguat non-critical; role alert hanya untuk pesan baru penting |

## 3. Status dictionary (domain-specific)
Sumber enum `phase0/packages/policy/enums.ts`, program lifecycle PRD §8.2. Jangan gunakan satu map global untuk `CLOSED` (case vs complaint vs program). Tone adalah semantic appearance, bukan state machine baru. Label detail tidak boleh menutupi safe_message. Ikon: neutral FileText/Archive, info Clock/Info, warning AlertTriangle, danger CircleX, success CheckCircle2.

| Case state | Label UI | Tone / makna |
|---|---|---|
| DRAFT | Draf | neutral |
| SUBMITTED | Pengajuan terkirim | info |
| PRECHECKED | Pemeriksaan awal sistem selesai | info; bukan layak |
| UNDER_REVIEW | Sedang diperiksa petugas | info |
| REVISION_REQUIRED | Perlu perbaikan | warning |
| RESUBMITTED | Perbaikan terkirim | info; state transien jika atomic |
| PENDING_APPROVAL | Menunggu tinjauan instansi | info |
| RECOMMENDED_FOR_FORWARD | Direkomendasikan untuk tindak lanjut | info; P0 internal |
| FORWARDING_PENDING | Menunggu penerusan (simulasi) | info |
| FORWARDING_FAILED | Penerusan simulasi terkendala | warning; bukan kesalahan warga |
| FORWARDED_SIMULATED | Diteruskan (simulasi) | success; ACK saja |
| CLOSED_UNPROCESSABLE | Tidak dapat diproses | danger |
| NOT_SELECTED | Tidak terpilih | danger; program saja |
| CLOSED | Proses ditutup | neutral; alasan hasil tetap terlihat |

| Appeal state | Label | Tone |
|---|---|---|
| APPEAL_SUBMITTED | Sanggahan terkirim | info |
| APPEAL_REVIEW | Sanggahan sedang ditinjau | info |
| UPHELD | Keputusan sebelumnya dipertahankan | neutral |
| RECHECK_REQUIRED | Pemeriksaan ulang diperlukan | warning |
| DECISION_CORRECTED | Koreksi keputusan diperlukan | info; parent recheck, bukan hasil baru otomatis |
| APPEAL_CLOSED | Proses sanggahan ditutup | neutral |

| Complaint state | Label | Tone |
|---|---|---|
| RECEIVED | Pengaduan diterima | info |
| TRIAGED | Pengaduan diklasifikasikan | info |
| ASSIGNED | Petugas ditugaskan | info |
| IN_HANDLING | Sedang ditangani | info |
| RESOLVED | Pengaduan ditangani | success |
| REFERRED | Dirujuk | info; bukan bukti transmisi eksternal |
| CLOSED | Pengaduan ditutup | neutral |

| Program state | Label | Tone |
|---|---|---|
| DRAFT | Draf | neutral |
| PENDING_PUBLICATION_REVIEW | Menunggu tinjauan publikasi | info |
| RETURNED_FOR_REVISION | Perlu perbaikan program | warning |
| APPROVED | Disetujui untuk publikasi | info |
| PUBLISHED | Dipublikasikan | info; belum tentu buka |
| OPEN | Pendaftaran dibuka | success |
| CLOSED | Pendaftaran ditutup | neutral |
| ARCHIVED | Diarsipkan | neutral |

Mode pendaftaran: SELF = "Daftar mandiri", OFFICER_ONLY = "Melalui petugas", BOTH = "Mandiri atau melalui petugas", NO_PUBLIC_REGISTRATION = "Tidak membuka pendaftaran umum". Jenis: DATA_PROPOSAL = "Usulan data", PROGRAM_APPLICATION = "Pendaftaran program". Upload PENDING = "Sedang diperiksa" info; CLEAN = "Berkas lolos pemeriksaan keamanan" success (bukan kelengkapan administratif); REJECTED = "Berkas tidak dapat digunakan" danger. Integrasi UP = "Tersedia", DOWN = "Tidak tersedia", DISABLED = "Tidak aktif", DEGRADED = "Terganggu"; AI_PENDING/AI_UNAVAILABLE = "Pemeriksaan otomatis tertunda/belum tersedia". Jangan menyebut skor AI sebagai tingkat kelayakan.

## 4. Pola interaksi
- Search katalog: debounce 300 ms atau submit eksplisit; abort stale GET, reset cursor. Tampilkan filter aktif; apabila lookup wilayah gagal, jangan tampilkan nama tekaan. Pagination cursor "Berikutnya" dan "Sebelumnya" (stack cursor di memori), no total page bila total tidak diberikan.
- Form: onBlur validasi setelah touched, onSubmit semua field; error summary + anchor ke field; backend field errors adalah otoritas. Simpan draft eksplisit; tampilkan "Draf tersimpan" hanya setelah response. Pada 409 jangan overwrite; refresh snapshot, user meninjau lagi. File input dan password tidak dipersist browser. Tidak memberi auto-submit saat enter di field multi-step tanpa review.
- Review: action menu/radio sesuai server; tampilkan konsekuensi, isi alasan, cek versi dan bukti, konfirmasi aksi; hasil baru dari refetch setelah response. `NOT_SELECT` hanya program. No AI preselected, no bulk approve.
- Upload: jangan preview blob PENDING pada petugas. Preview lokal milik pengunggah sebelum upload dapat opsional untuk image via object URL yang direvoke; PDF tampil nama/size, tidak embed untrusted content. Jangan menyediakan tombol hapus persisten sampai endpoint disepakati; file lokal boleh dibuang sebelum upload.
- Notification: success inline + polite live region; network error persisten; jangan toast satu-satunya bukti submit. UI tidak menebak nomor tiket atau waktu SLA.

## 5. Component QA checklist
Keyboard tab order; accessible name dan focus visible; disabled explanation; contrast token; 360px text wrapping; 200% zoom; loading, empty, network, 401/403/409/422; no nested interactive; no console PII; data synthetic; unit test mapping exhaustive terhadap enum bersama; story/fixture hanya dev/test dengan label simulasi. Komponen tidak memanggil endpoint internal M2M dan tidak memiliki authorization final. Tidak ada tombol dummy pada release.
