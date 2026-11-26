# Rencana implementasi frontend BantuTepat

Status: FE-00 selesai dalam bentuk audit dan dokumen; tahap coding berikutnya menunggu persetujuan FE-01.

## Prinsip urutan

Gunakan project Laravel yang ada. Implementasi P0 lebih dulu dan mengikuti 42 URL kanonik di FRONTEND_ROUTE_MATRIX.md. OpenAPI v1.2, role matrix, state contract, serta UI/UX v1.0 menjadi baseline; materi lama yang menyebut Next.js sebagai frontend diadaptasi ke Laravel sesuai prompt terbaru. Jangan mengubah business logic, API, permission, role, status, workflow, atau desain produk. P1 mulai setelah gate P0. P2 tidak aktif tanpa persetujuan eksplisit dan semua gate keamanan.

## Rencana dan acceptance criteria per fase

| Fase | Deliverable | Acceptance criteria untuk menutup fase | Ketergantungan/gate |
|---|---|---|---|
| FE-00 — Repository Audit & Planning | Audit repository, arsitektur Laravel, roadmap, 42-route matrix, komponen, API dependencies, handoff, blockers, dan acceptance criteria. Salinan PRD v1.2 serta UI/UX v1.0 ada dalam docs proyek. | Dokumen dapat direview; status, asumsi, gap, dan hal yang belum diverifikasi disebut jelas. Tidak ada code feature yang berubah. | Selesai untuk turn ini. Review pengguna/tim sebelum FE-01. TASK-01 PRD masih belum lulus karena tidak ada Git checkout dan sign-off kontrak belum ada. |
| FE-01 — Laravel Frontend Foundation | Struktur view modular, Tailwind/Vite, Alpine, tokens, typography/icon setup, API client dasar, error model, env backend, development mock adapter. | Vite build berhasil; routes/view skeleton tidak memakai React/Vue/Livewire/Inertia; token menjadi sumber canonical; API base berasal dari environment; mock hanya dev/test; endpoint client tidak mengarang kontrak; tidak ada domain schema/model baru. | Persetujuan FE-01, kontrak dan auth ADR disepakati, Git repository tersedia, PHP/npm toolchain tersedia. |
| FE-02 — Design System | Public/private layouts, navigasi role, UI primitives, shared Blade components dan screen-state patterns. | Komponen yang diwajibkan tercatat di inventaris; layout desktop/mobile sesuai spec; label Indonesia; keyboard/focus, semantic form dan feedback konsisten; tidak ada template dashboard. | FE-01 selesai. Gaps yang menyangkut komponen dasar diputuskan sebelum wiring. |
| FE-03 — Public Website & Authentication | PUB-01—PUB-09 dan AUTH-01—AUTH-03; katalog, detail, panduan, transparansi, bantuan FAQ, kebijakan, login/daftar/pemulihan. | Sembilan route publik dan tiga route auth kanonik tersedia; program hanya versi publik; filter hanya yang didukung API; metrik suppressed tidak ditampilkan sebagai nol; auth memakai mekanisme kontrak, CSRF/MFA dan pesan generik; tidak ada demo success untuk aksi backend. | FE-02; endpoint publik/auth, legal copy, dan session/cookie ADR disetujui. P1 chatbot/WA tetap off pada fase ini. |
| FE-04 — Citizen Dashboard | CIT-01—CIT-12: dua jenis pengajuan, tracking, perbaikan, sanggahan, pengaduan, feedback, profil, verifikasi manual. | Warga hanya melihat objek miliknya/mandat sah; tipe DATA_PROPOSAL terpisah dari PROGRAM_APPLICATION; nomor tiket dan status hanya dari server; tiket yang direvisi tetap sama; alasan/tenggat dari response; appeal, complaint, feedback menjadi alur berbeda; face UI tidak tampil saat flag mati. | FE-03; gap G02, G04—G08 ditutup untuk layar yang memakainya. |
| FE-05 — Regional Dashboard | REG-01—REG-04: dashboard scope, usulan terwakilkan, kasus wilayah, verifikasi operator. | Data berdasarkan mandat dan wilayah; subjek hanya dari lookup authorized; operator assigned-only, dapat minta revisi atau rekomendasi; tidak ada approval final atau pencarian seluruh warga; aksi di-refetch setelah backend confirm. | FE-02/FE-04; gap G06 dan G09 serta action assignment disetujui. |
| FE-06 — Agency Dashboard | AGY-01—AGY-08: review independen, sanggahan, program, versi dan publikasi. | Antrean hanya scoped; maker-checker berlaku; keputusan beralasan dengan rule/evidence/next step dari kontrak; tidak ada hasil bantuan resmi; pembuat program tidak memublikasikan versi sendiri; program terdahulu tetap terikat snapshot. | FE-02/FE-05; gap G06, G07, G09, G10, dan G11 ditutup. |
| FE-07 — Administration Dashboard | ADM-01—ADM-06: monitoring, audit, pengaduan, user/scope, wilayah, health integrasi. | Auditor read-only dan scoped; admin teknis tidak dapat memutus kasus; audit dan pengaduan sensitive ter-redact; role/grant memerlukan grant/approval yang sah; health tidak mengekspos secret; daftar admin memakai API internal authorized, bukan data publik atau fixture. | FE-02/FE-06; gap G03, G09, G12, dan G13 dibahas/ditutup sesuai prioritas. |
| FE-08 — P1 Feature Interfaces | Panel status Government Simulator, flag AI advisory, web RAG, dan WA static menu/handoff. | Hanya aktif dengan feature flag/server response dan adapter terkait; tidak ada klaim forwarding sebelum ACK; AI tidak menentukan kelayakan; LLM hanya FAQ publik dan fallback FAQ; WhatsApp tidak menggunakan LLM; outage tidak memblokir proses P0. | Semua acceptance P0 dan gate security/E2E lulus; API/service contracts P1 disetujui. |
| FE-09 — Backend Integration & Quality Assurance | API client terhubung ke environment backend, response mapping dan sesi diuji, kegagalan serta permission diuji. | Payload/DTO cocok OpenAPI yang dibekukan; 401/403/404/409/422/429/503 menjadi state aman; timeout dan idempotency benar; role/scope/object checks diverifikasi server-side; tidak ada token, PII, signed URL, atau mock success di production; laporan mencatat test yang benar-benar dijalankan. | Backend tersedia; seluruh ADR/gap integrasi disetujui. QA environment dan test accounts sintetis tersedia. |
| FE-10 — Final Polish, Production Build & Handoff | Responsive/accessibility polish, asset license inventory, build/release notes dan dokumentasi setup. | Build Vite serta browser flow berhasil pada toolchain target; viewport 360/390/430/768/1280/1440 dan reflow 320/zoom diuji; Lighthouse/accessibility hanya dilaporkan dengan bukti aktual; semua 42 route memiliki state sesuai spec; tidak ada link/tombol dummy; README dan handoff lengkap. | FE-09 selesai; hosting dan konfigurasi backend disetujui; tidak ada blocker release terbuka. |

## Prioritas halaman

P0 meliputi portal publik, auth, semua halaman inti warga/wilayah/instansi/administrasi, tracking, revisi, sanggahan, pengaduan, feedback, transparansi, audit, berkas, dan FAQ statis. Bantuan P0 adalah FAQ, tanpa mengharuskan LLM. Face check manual tersedia bila diperlukan; biometrik 1:1 tetap P2 dan mati secara default.

P1 hanya mengaktifkan status integrasi simulator, AI advisory, chatbot RAG web, dan WA menu setelah P0 stabil. P2/OCR/face tidak masuk baseline deliverable tanpa approval dan bukti aman.

## Requirement tracing

Setiap issue/PR implementasi mencatat screen ID, FR/NFR, task, acceptance criteria, route, komponen, API operation, file berubah, hasil uji aktual, dan blocker. Gunakan G01—G13 dari UI_CONTRACT_GAPS sebagai ID rujukan. Status UI_READY, MOCK_TESTED, API_BLOCKED, API_INTEGRATED, dan VERIFIED harus berbeda; status tidak diturunkan dari keberadaan file atau mock.

## Hal yang belum dapat dijadwalkan

Durasi dan tanggal implementasi belum ditetapkan karena sign-off API/auth, akses backend, runtime PHP/npm, akses Git, serta estimasi tim belum tersedia. Tanggal target rulebook bukan estimasi kapasitas engineering.
