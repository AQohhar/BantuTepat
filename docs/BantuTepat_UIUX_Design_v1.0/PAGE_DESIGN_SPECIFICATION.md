# PAGE_DESIGN_SPECIFICATION.md — BantuTepat v1.0
**42 layar/rute kanonik berdasarkan PRD v1.2 §8.** Semua kontrak API di bawah relatif terhadap `/api/v1`; braces OpenAPI bukan route App Router. G01–G13 merujuk `UI_CONTRACT_GAPS.md`. Semua layar dibaca bersama state global dan komponen. Desain visual tidak menambah hak, endpoint, fitur, atau transisi. Item yang belum ada kontrak harus dibekukan sebelum implementasi layar tersebut; jangan memakai data dummy dalam release. Istilah English pada spesifikasi teknis bukan label UI.

## 1. Page templates / wireframe low-fidelity
Blok berikut menunjukkan urutan konten, bukan mockup final; jarak, warna dan font mengacu token. Bentuk panel mengikuti komponen, bukan template siap pakai.

### Public — beranda desktop / mobile
```text
┌──────────────────────────────────────────────────────────────┐
│ BantuTepat   Beranda  Program  Panduan  Transparansi  Bantuan │
│                                                  Masuk Daftar│
├──────────────────────────────────────────────────────────────┤
│ Label prototipe layanan                                      │
│ Temukan jalur bantuan             ┌───────────────────────┐  │
│ yang tepat                        │ Dua jalur → satu tiket│  │
│ Ringkasan manfaat                 │ diagram garis orisinal│  │
│ [Lihat program] [Pelajari cara]   └───────────────────────┘  │
│ Dua jalur layanan                                            │
│ [Usulan data]                    [Pendaftaran program]        │
│ Program tersedia                                             │
│ [Card]                [Card]                 [Card]           │
│ Cara pengajuan → Statistik agregat → Footer                  │
└──────────────────────────────────────────────────────────────┘
Mobile: logo/menu → headline → CTA → diagram → kartu layanan
        → program list → alur → statistik → footer.
```
### Private — warga
```text
┌──────────────┬───────────────────────────────────────────────┐
│ BantuTepat   │ Ringkasan                          🔔 Akun   │
│ Scope: Warga │ Selamat datang                         [Buat] │
│ Ringkasan    │ ┌───────────────────────────────────────────┐ │
│ Pengajuan    │ │ Tindakan diperlukan (jika ada)           │ │
│ Program      │ │ Status + penjelasan + aksi sah           │ │
│ Pengaduan    │ └───────────────────────────────────────────┘ │
│ Profil       │ Pengajuan terakhir         Notifikasi        │
│              │ [CaseCard]                 [Safe message]     │
│ Bantuan      │ [CaseCard]                 [Safe message]     │
│              │ Program publik                                │
└──────────────┴───────────────────────────────────────────────┘
Mobile: topbar → action-needed → primary CTA → case cards →
notifikasi → program → bottom nav (bukan pada wizard).
```
### Private — petugas, review dan auditor (berbeda fokus)
```text
Operator:   [Scope] [Antrean assigned]      [Status tugas]
            [Filter status] → [Tabel kasus] → [Buka verifikasi]
Verifikator:[Scope] [Tiket + status + versi + guard]
            ┌──────────────────────┬──────────────────────┐
            │ Ringkasan & bukti    │ Tindakan berizin     │
            │ Aturan / flag / AI   │ Alasan, dasar aturan │
            │ Riwayat proses       │ Bukti, konfirmasi    │
            └──────────────────────┴──────────────────────┘
Manager:    [Buat program] → [Daftar draft/versi/periode]
Auditor:    [KPI + definisi/waktu] → [Audit & filter] (read-only)
Admin:      [Mandat pengguna] → [Wilayah] → [Kesehatan sistem]
Mobile review: scope → status → bukti → timeline → keputusan.
```
### Wizard / detail
```text
Breadcrumb → h1 → penjelasan → Langkah 2 dari 5 (teks)
[Ringkasan error] (fokus saat submit gagal; di atas form)
[Label] [Input]  → helper → error inline bila invalid
[Field group]    → privacy notice bila relevan
[Kembali] [Simpan draf] [Lanjut / Kirim]
Success: tiket aktual → status → tindakan selanjutnya → detail
Detail: header tiket → next action/reason → timeline aktual → bukti
```
Jumlah langkah bergantung schema/version; tidak mengubah langkah bisnis. Review sebelum submit. Tidak tampilkan completion % dari state case. Untuk form panjang dan tabel, lihat RESPONSIVE_GUIDELINES.md.

## 2. State machine UI (semua layar)
| State | Perilaku umum |
|---|---|
| Initial | Render shell aman, cek sesi/izin untuk privat, siapkan default filter/field; tidak fetch PII sebelum authorized. |
| Loading | Skeleton sesuai bentuk konten, aria-busy; mutation spinner hanya aksi terdampak. |
| Success | Read = siap; mutation = server-confirmed response, inline confirmation dan next step. |
| Empty | Hanya setelah hasil kosong sah; beda dengan belum dimuat atau error. |
| Error | Retry GET aman; mutation outcome tidak jelas retry dengan key sama; 409 refresh, 422 field; error publik generik. |
| Disabled | Tidak diberi permission → sembunyikan aksi; guard status/feature off → penjelasan sesuai konteks; pending → disable submit. |
| Validation | Error summary dan inline, preserve input; read-only N/A bila tidak ada parameter/filter. |
| Permission denied | 401 login aman; 403/404 object pesan sama; bersihkan view/scope/cache; tidak bocorkan keberadaan objek. |

Kode halaman stabil untuk task/QA. Setiap spesifikasi di bawah memuat delapan state khusus.

## 3. Inventaris rute
| ID | Route | Layar | Role/konteks | Prioritas |
|---|---|---|---|---|
| PUB-01 | `/` | Beranda | Publik | P0 |
| PUB-02 | `/program` | Katalog program | Publik | P0 |
| PUB-03 | `/program/[slug]` | Detail program | Publik | P0 |
| PUB-04 | `/panduan` | Panduan layanan | Publik | P0 |
| PUB-05 | `/transparansi` | Transparansi publik | Publik | P0 |
| PUB-06 | `/bantuan` | Pusat bantuan | Publik | P0 FAQ / P1 RAG+WA |
| AUTH-01 | `/login` | Masuk | Publik pre-auth | P0 |
| AUTH-02 | `/daftar` | Daftar akun warga | Publik pre-auth | P0 |
| AUTH-03 | `/lupa-password` | Pemulihan kata sandi | Publik pre-auth / recovery session | P0 |
| PUB-07 | `/privasi` | Kebijakan privasi | Publik | P0 |
| PUB-08 | `/syarat-layanan` | Syarat layanan | Publik | P0 |
| PUB-09 | `/aksesibilitas` | Aksesibilitas | Publik | P0 |
| CIT-01 | `/dashboard` | Dashboard warga | CITIZEN | P0 |
| CIT-02 | `/pengajuan/baru` | Pilih jenis pengajuan | CITIZEN | P0 |
| CIT-03 | `/pengajuan/usulan` | Usulan data sosial ekonomi | CITIZEN | P0 |
| CIT-04 | `/program/[slug]/daftar` | Pendaftaran program | CITIZEN | P0 |
| CIT-05 | `/pengajuan-saya` | Pengajuan saya | CITIZEN | P0 |
| CIT-06 | `/pengajuan-saya/[caseId]` | Detail pengajuan | CITIZEN | P0 |
| CIT-07 | `/pengajuan-saya/[caseId]/perbaiki` | Perbaikan pengajuan | CITIZEN | P0 |
| CIT-08 | `/pengajuan-saya/[caseId]/sanggah` | Sanggahan | CITIZEN | P0 |
| CIT-09 | `/pengaduan` | Pengaduan layanan | CITIZEN | P0 |
| CIT-10 | `/feedback/[caseId]` | Feedback pelayanan | CITIZEN | P0 |
| CIT-11 | `/profil` | Profil | CITIZEN | P0 |
| CIT-12 | `/verifikasi-identitas` | Verifikasi identitas | CITIZEN | P0 manual / P2 face |
| REG-01 | `/wilayah` | Dashboard wilayah | REGIONAL_PROPOSER / VILLAGE_OPERATOR | P0 |
| REG-02 | `/wilayah/usulkan` | Pengusulan warga | REGIONAL_PROPOSER / VILLAGE_OPERATOR dengan mandat | P0 |
| REG-03 | `/wilayah/kasus` | Daftar kasus wilayah | REGIONAL_PROPOSER / VILLAGE_OPERATOR | P0 |
| REG-04 | `/wilayah/verifikasi/[id]` | Verifikasi operator | VILLAGE_OPERATOR assigned | P0 / P1 AI |
| AGY-01 | `/instansi` | Dashboard instansi | AGENCY_VERIFIER / PROGRAM_MANAGER | P0 |
| AGY-02 | `/instansi/review` | Antrean review kasus | AGENCY_VERIFIER | P0 |
| AGY-03 | `/instansi/review/[id]` | Review kasus instansi | AGENCY_VERIFIER assigned independen | P0 / P1 AI+simulator |
| AGY-04 | `/instansi/program` | Manajemen program | PROGRAM_MANAGER | P0 |
| AGY-05 | `/instansi/program/baru` | Buat program | PROGRAM_MANAGER | P0 |
| AGY-06 | `/instansi/program/[id]/edit` | Edit versi program | PROGRAM_MANAGER | P0 |
| AGY-07 | `/instansi/program/review-publikasi` | Tinjauan publikasi | Pemegang program.publish independen | P0 |
| AGY-08 | `/instansi/sanggahan` | Manajemen sanggahan | AGENCY_VERIFIER assigned / coordinator grant | P0 |
| ADM-01 | `/administrasi/pengawasan` | Pengawasan / dashboard auditor / monitoring | AUDITOR | P0 / P1 downtime if available |
| ADM-02 | `/administrasi/audit` | Audit log | AUDITOR (scope), SUPER_ADMIN limited technical log only if grant | P0 |
| ADM-03 | `/administrasi/pengaduan` | Penanganan pengaduan | AUDITOR / handler authorized as matrix | P0 |
| ADM-04 | `/administrasi/pengguna` | Manajemen pengguna | SUPER_ADMIN | P0 |
| ADM-05 | `/administrasi/wilayah` | Manajemen wilayah | SUPER_ADMIN dengan territory.manage | P0 |
| ADM-06 | `/administrasi/integrasi` | Integrasi dan kesehatan sistem | SUPER_ADMIN (integration.manage); knowledge grants if allowed | P0 health / P1 simulator, knowledge, WA/RAG |

## 4. Spesifikasi layar terperinci

### PUB-01 — Beranda
**Route:** `/` · **Konteks:** Publik · **Prioritas:** P0 · **Trace:** FR-01, FR-12

**Data/API:** GET /public/programs; GET /public/stats.

**Layout & hierarki:** Hero: “Temukan jalur bantuan yang tepat” + Lihat program; diagram dua jalur; dua kartu layanan; program terbit; alur 3 langkah (Ajukan, Pantau, Tindak lanjut); statistik aman; footer disclaimer.

**Interaksi:** Lihat program → /program; Pelajari cara pengajuan → /panduan; guest tidak mengirim data.

| State | Desain dan tindakan |
|---|---|
| Initial | Hero statis siap, fetch section. |
| Loading | Skeleton hanya program/statistik. |
| Success / ready | Program dan agregat aktual dengan waktu. |
| Empty | Belum ada program terbit; panduan tetap ada. |
| Error | Section gagal: Coba lagi, hero tetap. |
| Disabled | Tidak ada CTA daftar langsung tanpa program. |
| Validation | N/A; navigasi tanpa input. |
| Permission denied | Konten publik tetap; tidak ada bagian privat. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### PUB-02 — Katalog program
**Route:** `/program` · **Konteks:** Publik · **Prioritas:** P0 · **Trace:** FR-01

**Data/API:** GET /public/programs; GET /public/territories.

**Layout & hierarki:** Header + search; filter wilayah, cara daftar, status; grid ProgramCard 3 desktop/1 mobile; pagination cursor.

**Interaksi:** Lihat detail → /program/[slug]; reset filter; tipe program belum ada parameter (G01).

| State | Desain dan tindakan |
|---|---|
| Initial | Filter default, mulai request. |
| Loading | Grid skeleton, filter tetap. |
| Success / ready | Cards dari items, cursor aktual. |
| Empty | Belum ada program / Tidak ada hasil sesuai filter. |
| Error | Retry GET; lookup wilayah gagal tampil pesan, bukan label tekaan. |
| Disabled | Next jika cursor null; filter wilayah jika lookup gagal. |
| Validation | Search 1–255 bila diisi; enum/UUID valid. |
| Permission denied | Hanya public programs; draft tidak bocor. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### PUB-03 — Detail program
**Route:** `/program/[slug]` · **Konteks:** Publik · **Prioritas:** P0 · **Trace:** FR-01, FR-05

**Data/API:** GET /public/programs/{slug}.

**Layout & hierarki:** Breadcrumb; status/mode + judul instansi; kolom isi aturan & sumber, area dan periode; aside "Cara mendaftar"; versi; label simulasi. G02 untuk detail yang belum DTO.

**Interaksi:** SELF/BOTH dan open: Daftar sekarang bila server mengizinkan preview; login bila guest. OFFICER_ONLY: Lihat prosedur melalui petugas; NO_PUBLIC_REGISTRATION: informasi; closed: baca aturan; BOTH jelaskan dua jalur. Jangan gunakan can_apply sebagai keputusan akhir.

| State | Desain dan tindakan |
|---|---|
| Initial | Slug siap; request. |
| Loading | Skeleton header/detail/aside. |
| Success / ready | Aturan aktual dan next_step, CTA sesuai mode. |
| Empty | Tidak ada aturan yang tersedia: beri batasan dan jangan ajukan jika kontrak belum lengkap. |
| Error | Retry atau kembali katalog. |
| Disabled | Tutup/arsip/preview blocked: penjelasan, no submit. |
| Validation | Slug invalid → safe not found; precheck sebenarnya di submit. |
| Permission denied | 404/403 identik; draft tidak tampil. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### PUB-04 — Panduan layanan
**Route:** `/panduan` · **Konteks:** Publik · **Prioritas:** P0 · **Trace:** FR-01, FR-12

**Data/API:** GET /public/faq.

**Layout & hierarki:** Intro dua jalur; perbandingan singkat; stepper vertikal tiap jalur; accordion FAQ; bagian revisi vs sanggah vs pengaduan.

**Interaksi:** Lihat program / Mulai pengajuan (login jika perlu); FAQ keyboard accessible.

| State | Desain dan tindakan |
|---|---|
| Initial | Konten panduan static siap; FAQ request. |
| Loading | Skeleton accordion. |
| Success / ready | FAQ terverifikasi, langkah jelas. |
| Empty | FAQ belum tersedia, panduan dasar tetap. |
| Error | FAQ retry; panduan dasar tetap. |
| Disabled | N/A untuk panduan statis; link privat perlu login. |
| Validation | N/A; no form. |
| Permission denied | Publik, tautan privat hanya redirect aman. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### PUB-05 — Transparansi publik
**Route:** `/transparansi` · **Konteks:** Publik · **Prioritas:** P0 · **Trace:** FR-12, FR-14

**Data/API:** GET /public/stats; GET /public/territories.

**Layout & hierarki:** Intro definisi data; waktu pembaruan; grid StatCard dari metrics; penjelasan metode/suppression dan sumber; label simulasi. Chart hanya jika DTO seri disetujui (G03).

**Interaksi:** Baca metodologi; filter wilayah/periode setelah G03 ditutup; tidak ada drilldown individu.

| State | Desain dan tindakan |
|---|---|
| Initial | Request agregat, tidak ada angka awal. |
| Loading | Skeleton metric tanpa angka. |
| Success / ready | Nilai aktual, suppressed ditulis “Disembunyikan untuk privasi”. |
| Empty | Belum ada agregat aman; bukan 0. |
| Error | Retry; jangan pakai data lama sebagai terbaru. |
| Disabled | Filter/ekspor tidak tersedia jika kontrak belum mendukung. |
| Validation | Rentang waktu valid jika G03 disetujui. |
| Permission denied | Hanya agregat; tiada data privat meskipun URL dimanipulasi. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### PUB-06 — Pusat bantuan
**Route:** `/bantuan` · **Konteks:** Publik · **Prioritas:** P0 FAQ / P1 RAG+WA · **Trace:** FR-12, FR-16, FR-17

**Data/API:** GET /public/faq; POST /web-chat (P1).

**Layout & hierarki:** FAQ sebagai isi utama, kategori jika sumber mendukung; kontak resmi terverifikasi; RAG inline panel P1 (input pertanyaan umum, jawaban + sitasi + label batasan); WA handoff hanya kanal tersedia/mock jelas.

**Interaksi:** Baca FAQ; Tanya panduan jika P1 aktif; link resmi aman; tidak meminta NIK/tiket/dokumen.

| State | Desain dan tindakan |
|---|---|
| Initial | FAQ request, chat idle. |
| Loading | FAQ skeleton; chat status “Menyiapkan jawaban”. |
| Success / ready | FAQ/jawaban bersumber, fallback ditandai. |
| Empty | FAQ belum tersedia / chat belum dimulai. |
| Error | FAQ retry; LLM down → FAQ, tidak menghambat core. |
| Disabled | Chat/WA off jika flag mati; no dummy link. |
| Validation | Pertanyaan sesuai batas DTO, blok PII sesuai kontrak; jangan tampil error provider mentah. |
| Permission denied | Guest boleh FAQ/chat umum; permintaan case privat diarahkan login. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### AUTH-01 — Masuk
**Route:** `/login` · **Konteks:** Publik pre-auth · **Prioritas:** P0 · **Trace:** FR-02

**Data/API:** GET /auth/csrf; POST /auth/login; POST /auth/mfa/enroll; POST /auth/mfa/verify; GET /me.

**Layout & hierarki:** Centered panel 440px; email, kata sandi + show/hide; lupa kata sandi; panel MFA jika challenge, enrollment secret hanya di sesi terbatas; no role picker.

**Interaksi:** Masuk; lanjut OTP/challenge; arahkan dashboard valid scope setelah sesi verified; staff AAL2 wajib.

| State | Desain dan tindakan |
|---|---|
| Initial | Email/password kosong; CSRF request. |
| Loading | Spinner pada tombol, field tetap. |
| Success / ready | Session sukses → redirect; MFA_REQUIRED → challenge UI, belum dashboard. |
| Empty | Tidak ada data akun ditampilkan; form kosong adalah initial. |
| Error | 401 generic; jaringan retry; CSRF expired refresh challenge. |
| Disabled | Submit pending; MFA belum lengkap; no privileged menu. |
| Validation | Email valid; login password 1–128; code sesuai schema; pesan umum untuk auth. |
| Permission denied | Sesi terbatas hanya MFA; scope tidak valid → safe no-access. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### AUTH-02 — Daftar akun warga
**Route:** `/daftar` · **Konteks:** Publik pre-auth · **Prioritas:** P0 · **Trace:** FR-02

**Data/API:** GET /auth/csrf; POST /auth/register.

**Layout & hierarki:** Panel email, kata sandi, konfirmasi lokal, notice versi + privasi; petugas melalui undangan terpisah, tanpa selector role.

**Interaksi:** Daftar akun; login link; 202 pesan generik cek email tanpa mengungkap akun.

| State | Desain dan tindakan |
|---|---|
| Initial | Field kosong. |
| Loading | Submitting spinner. |
| Success / ready | “Jika alamat dapat digunakan, ikuti petunjuk yang dikirim.” |
| Empty | N/A; form initial kosong. |
| Error | Generic error, retry; rate limit pesan aman. |
| Disabled | Pending/CSRF belum siap. |
| Validation | Password 12–128, konfirmasi cocok, notice versi, email valid. |
| Permission denied | Tidak menawarkan staff self-signup; tidak mengungkap akun existing. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### AUTH-03 — Pemulihan kata sandi
**Route:** `/lupa-password` · **Konteks:** Publik pre-auth / recovery session · **Prioritas:** P0 · **Trace:** FR-02

**Data/API:** GET /auth/csrf; POST /auth/recovery; GET /auth/callback; POST /auth/reset-password.

**Layout & hierarki:** Email request panel; callback server PKCE redirect aman; mode reset hanya session recovery verified, password baru + konfirmasi.

**Interaksi:** Kirim petunjuk; Simpan kata sandi baru; kembali login setelah 204.

| State | Desain dan tindakan |
|---|---|
| Initial | Request email / valid recovery context. |
| Loading | Spinner, callback status. |
| Success / ready | 202 pesan generik / reset berhasil. |
| Empty | N/A; input awal kosong. |
| Error | Tautan invalid/expired: minta ulang; network retry. |
| Disabled | Reset tidak tampil tanpa recovery context. |
| Validation | Email, password 12–128, confirmation; tidak blok paste. |
| Permission denied | Invalid recovery session tidak boleh reset; generic pesan. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### PUB-07 — Kebijakan privasi
**Route:** `/privasi` · **Konteks:** Publik · **Prioritas:** P0 · **Trace:** FR-02, FR-19

**Data/API:** Konten versioned yang disetujui, tanpa endpoint baru.

**Layout & hierarki:** Reading column 680–760; tujuan, kategori, retensi demo, hak subjek, kontak sah, status prototipe; tanggal versi.

**Interaksi:** Kembali ke layanan; tautan kebijakan terkait.

| State | Desain dan tindakan |
|---|---|
| Initial | Dokumen versi tersedia saat SSR. |
| Loading | N/A jika statis; route loading fallback. |
| Success / ready | Konten disetujui lengkap. |
| Empty | Belum ada versi disetujui: blocker sebelum signup/submit. |
| Error | Fallback error dan bantuan; jangan klaim kepatuhan. |
| Disabled | CTA consent tidak ada; kebijakan hanya baca. |
| Validation | N/A; tanpa form. |
| Permission denied | Selalu publik; tidak ada data subjek nyata. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### PUB-08 — Syarat layanan
**Route:** `/syarat-layanan` · **Konteks:** Publik · **Prioritas:** P0 · **Trace:** FR-01, FR-02

**Data/API:** Konten versioned yang disetujui.

**Layout & hierarki:** Reading column; ruang lingkup demo, kewajiban, batas keputusan, penggunaan layanan, kanal bantuan.

**Interaksi:** Kembali ke layanan/bantuan.

| State | Desain dan tindakan |
|---|---|
| Initial | Konten SSR. |
| Loading | N/A jika statis; route fallback. |
| Success / ready | Versi disetujui. |
| Empty | Konten belum disetujui: blocker release. |
| Error | Error konten, coba lagi. |
| Disabled | Tidak ada acceptance toggle kecuali kontrak notice terkait. |
| Validation | N/A. |
| Permission denied | Publik; tidak meminta identitas. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### PUB-09 — Aksesibilitas
**Route:** `/aksesibilitas` · **Konteks:** Publik · **Prioritas:** P0 · **Trace:** NFR-06

**Data/API:** Konten static disetujui.

**Layout & hierarki:** Reading column; cara navigasi keyboard, zoom, bantuan akses, keterbatasan yang diketahui; target AA bukan sertifikasi.

**Interaksi:** Hubungi kanal bantuan yang tersedia.

| State | Desain dan tindakan |
|---|---|
| Initial | Konten SSR. |
| Loading | N/A jika statis. |
| Success / ready | Konten dan keterbatasan jujur. |
| Empty | Jika belum ada kontak, pusat bantuan tetap tersedia. |
| Error | Error dan link bantuan. |
| Disabled | Jangan klaim audit lulus sebelum diuji. |
| Validation | N/A. |
| Permission denied | Publik. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### CIT-01 — Dashboard warga
**Route:** `/dashboard` · **Konteks:** CITIZEN · **Prioritas:** P0 · **Trace:** FR-04, FR-05, FR-09, FR-13

**Data/API:** GET /me; GET /cases; GET /notifications; GET /public/programs.

**Layout & hierarki:** Salam singkat; action-needed cards dari kasus berizin; CTA “Buat pengajuan”; pengajuan terakhir; notifikasi; program publik (bukan rekomendasi kelayakan). Desktop 7/5, mobile next action dahulu.

**Interaksi:** Buat pengajuan; lihat detail, perbaiki bila actions sah; notifikasi baca.

| State | Desain dan tindakan |
|---|---|
| Initial | Session valid, load independent regions. |
| Loading | Skeleton per region. |
| Success / ready | Kasus dan notifikasi aktual; no total dari halaman parsial. |
| Empty | Belum ada pengajuan → mulai; notifikasi kosong terpisah. |
| Error | Retry per region; jangan hilangkan kasus karena notifikasi gagal. |
| Disabled | Revisi tidak tersedia bila guard/kontrak tak lengkap; no fake counts. |
| Validation | N/A; tanpa form. |
| Permission denied | 401 login; role salah 403 aman; no private cache. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### CIT-02 — Pilih jenis pengajuan
**Route:** `/pengajuan/baru` · **Konteks:** CITIZEN · **Prioritas:** P0 · **Trace:** FR-04, FR-05

**Data/API:** GET /me; GET /public/programs.

**Layout & hierarki:** Dua kartu pilihan dengan penjelasan “Usulkan/perbaiki data sosial ekonomi” vs “Daftar program tertentu”; masing-masing tujuan, kapan dipakai, next step.

**Interaksi:** Usulan data → /pengajuan/usulan; Pendaftaran → /program. Tidak memaksa memilih program untuk usulan data.

| State | Desain dan tindakan |
|---|---|
| Initial | Session valid, opsi sesuai permission. |
| Loading | Skeleton program availability bila fetch. |
| Success / ready | Dua jalur jelas. |
| Empty | Program kosong: jalur usulan data tetap tersedia. |
| Error | Katalog gagal: retry atau usulan data. |
| Disabled | Jalur program nonavailable diberi penjelasan; tidak membuat draft tanpa aksi. |
| Validation | Pilihan hanya tipe yang sah. |
| Permission denied | Sembunyikan opsi tanpa hak; generic denial bila URL langsung. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### CIT-03 — Usulan data sosial ekonomi
**Route:** `/pengajuan/usulan` · **Konteks:** CITIZEN · **Prioritas:** P0 · **Trace:** FR-04, FR-19

**Data/API:** POST /data-proposals/drafts; PATCH /data-proposals/drafts/{id}; POST /data-proposals/{id}/submit; POST /cases/{id}/attachments; GET /cases/{id}.

**Layout & hierarki:** Wizard: jenis usulan (Belum terdata/Perbaikan/Perubahan kondisi), profil/kontak minimum & wilayah, kondisi/reason, berkas relevan, ringkasan/pernyataan, tiket. Schema field dan draft resume G04/G05.

**Interaksi:** Simpan draf; Lanjut/Kembali; Kirim pengajuan; sukses nomor tiket, tracking. Tidak ada program_version_id.

| State | Desain dan tindakan |
|---|---|
| Initial | Form belum terisi; buat draft hanya saat aksi sah. |
| Loading | Skeleton profile/schema, spinner save/submit. |
| Success / ready | Draf tersimpan atau PRECHECKED + ticket_no setelah server; bukan penerima. |
| Empty | Tidak ada profil prefill → isi minimum sesuai schema; tidak menebak. |
| Error | Draft/save/network retry; input tetap di memori; idempotency key sama bila outcome tidak jelas. |
| Disabled | Kirim menunggu required validation/scan menurut rule; tidak ada face wajib. |
| Validation | Field schema; 422 summary + inline; 409 reload snapshot. |
| Permission denied | Owner only; role lain ke route representasi; no PII leak. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### CIT-04 — Pendaftaran program
**Route:** `/program/[slug]/daftar` · **Konteks:** CITIZEN · **Prioritas:** P0 · **Trace:** FR-05, FR-19

**Data/API:** GET /public/programs/{slug}; POST /programs/{id}/applications/drafts; PATCH /program-applications/drafts/{id}; POST /program-applications/{id}/submit; POST /cases/{id}/attachments.

**Layout & hierarki:** Ringkasan program & cara daftar/periode/wilayah/versi → subjek & alamat dasar → kriteria dinamis → dokumen → review/pernyataan → tiket. G02/G04/G05.

**Interaksi:** Daftar hanya SELF/BOTH dan rule sah; Simpan draf; Kirim; jika blocked tampil jalur koreksi/petugas dari server.

| State | Desain dan tindakan |
|---|---|
| Initial | Fetch program/rule version, jangan create sebelum user mulai. |
| Loading | Skeleton rule/form; save spinner. |
| Success / ready | Tiket aktual PRECHECKED dan next_step. |
| Empty | Program tidak buka/tidak tersedia: kembali detail; tidak membuat tiket. |
| Error | Network retry; 409 version changed minta review ulang. |
| Disabled | Mode officer-only, closed, precheck blocked; no submit. |
| Validation | 422 mode/territory/time/duplicate/field; tidak ada tiket atau klaim ditolak. |
| Permission denied | Session/subject scope valid; 403 safe. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### CIT-05 — Pengajuan saya
**Route:** `/pengajuan-saya` · **Konteks:** CITIZEN · **Prioritas:** P0 · **Trace:** FR-09

**Data/API:** GET /cases.

**Layout & hierarki:** PageHeader + CTA, status dan tipe filter, CaseCard mobile / table desktop (tiket/draf, jenis, status, diperbarui, detail).

**Interaksi:** Lihat detail; buat pengajuan; cursor next/prev.

| State | Desain dan tindakan |
|---|---|
| Initial | Filter default. |
| Loading | Cards/table skeleton. |
| Success / ready | Only owned case rows; no false totals. |
| Empty | Belum ada pengajuan / filter kosong. |
| Error | Retry, pertahankan filter. |
| Disabled | Next disabled saat cursor null. |
| Validation | Enum filter valid, cursor opaque. |
| Permission denied | Case owner/mandat server; no cross-user. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### CIT-06 — Detail pengajuan
**Route:** `/pengajuan-saya/[caseId]` · **Konteks:** CITIZEN · **Prioritas:** P0 · **Trace:** FR-08, FR-09, FR-10, FR-19

**Data/API:** GET /cases/{id}; GET /cases/{id}/attachments/{attachmentId}/download; GET /appeals/{id} jika ID sah.

**Layout & hierarki:** Header tipe/tiket/status; panel “Langkah berikutnya”; DecisionReason/RevisionRequest jika data sah; timeline aktual; ringkasan jawaban dan lampiran authorized. G04/G06/G07.

**Interaksi:** Perbaiki/sanggah/feedback hanya available_actions + guard; draft lanjutkan sesuai tipe bila route resume disepakati; unduh CLEAN; back list.

| State | Desain dan tindakan |
|---|---|
| Initial | Validate opaque caseId, session; fetch. |
| Loading | Skeleton status/timeline. |
| Success / ready | Case detail, safe_message dan status; reason tidak ditebak. |
| Empty | Timeline kosong → belum ada riwayat; lampiran kosong → tidak ada. |
| Error | Retry; signed URL expired minta ulang. |
| Disabled | Aksi status tidak sah nonaktif dengan alasan aman; no fake reason/deadline. |
| Validation | ID invalid safe 404; version stale refetch. |
| Permission denied | 403/404 generic; jangan render snapshot privat sebelum check. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### CIT-07 — Perbaikan pengajuan
**Route:** `/pengajuan-saya/[caseId]/perbaiki` · **Konteks:** CITIZEN · **Prioritas:** P0 · **Trace:** FR-09, FR-19

**Data/API:** GET /cases/{id}; POST /cases/{id}/attachments; POST /cases/{id}/resubmit.

**Layout & hierarki:** Banner alasan/field/tenggat server (G06); ringkasan sebelumnya read-only; field yang diminta, bukti, diff sebelum/sesudah, pernyataan; tiket asli di header.

**Interaksi:** Kirim perbaikan; berhasil UNDER_REVIEW pada tiket sama; batal kembali detail; tidak create draft baru.

| State | Desain dan tindakan |
|---|---|
| Initial | Fetch revision request dan versi. |
| Loading | Skeleton reason/field. |
| Success / ready | Resubmitted → revalidated atomic, ticket sama. |
| Empty | Tidak ada field revisi karena guard: kembali detail; metadata hilang = blocker, bukan empty. |
| Error | Network retry key sama; 409 refresh, retain transient inputs bila aman. |
| Disabled | Deadline/state tidak sah; disable submit, penjelasan. |
| Validation | 422 tanpa revision/status change, fokus error summary. |
| Permission denied | Owner/mandate dan case.revise.own; revoked scope deny. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### CIT-08 — Sanggahan
**Route:** `/pengajuan-saya/[caseId]/sanggah` · **Konteks:** CITIZEN · **Prioritas:** P0 · **Trace:** FR-10

**Data/API:** GET /cases/{id}; POST /cases/{id}/appeals; GET /appeals/{id}.

**Layout & hierarki:** Keputusan yang disengketakan + alasan dan masa sanggah; reason textarea dan evidence refs authorized; konfirmasi; hasil/tiket sanggahan terpisah pada layar ini atau detail case terkait. G07.

**Interaksi:** Kirim sanggahan bila eligible; lihat status dari appeal id; kanal resmi bila tidak tersedia; jangan mengulang seluruh form awal.

| State | Desain dan tindakan |
|---|---|
| Initial | Fetch decision metadata/eligibility. |
| Loading | Skeleton reason/period. |
| Success / ready | Tiket appeal APPEAL_SUBMITTED; status terpisah dan hasil riwayat lama tetap. |
| Empty | Tidak ada keputusan yang eligible → penjelasan. |
| Error | Retry idempotent; 409 decision changed review ulang. |
| Disabled | Masa berakhir/tidak eligible; no submit. |
| Validation | challenged_review_id valid, reason 1–2000, evidence authorized. |
| Permission denied | Owner dan case.appeal.own; tidak membocorkan keputusan lain. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### CIT-09 — Pengaduan layanan
**Route:** `/pengaduan` · **Konteks:** CITIZEN · **Prioritas:** P0 · **Trace:** FR-11

**Data/API:** GET /complaints; GET /complaints/{id}; POST /complaints.

**Layout & hierarki:** Header penjelasan beda sanggahan; list pengaduan sendiri; form kategori, deskripsi, terkait case opsional, kerahasiaan; detail inline/drawer scoped.

**Interaksi:** Kirim pengaduan; lihat tiket/status/hasil; tidak mengubah case.

| State | Desain dan tindakan |
|---|---|
| Initial | Load list, form idle. |
| Loading | List skeleton; submit spinner. |
| Success / ready | Tiket RECEIVED, riwayat pengaduan sendiri. |
| Empty | Belum ada pengaduan → buat jika ingin. |
| Error | Retry list/submit; no duplicate. |
| Disabled | Submitting; related case tidak valid tidak bisa dipilih. |
| Validation | Kategori dan deskripsi 1–2000 sesuai schema, confidentiality explicit. |
| Permission denied | Guest login; owner only, redacted confidential. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### CIT-10 — Feedback pelayanan
**Route:** `/feedback/[caseId]` · **Konteks:** CITIZEN · **Prioritas:** P0 · **Trace:** FR-11

**Data/API:** GET /cases/{id}; POST /cases/{id}/feedback.

**Layout & hierarki:** Panel ringan setelah layanan; skor 1–5 radio dengan label; komentar opsional; jelas tidak mengubah hasil.

**Interaksi:** Kirim masukan; lewati/kembali ke detail.

| State | Desain dan tindakan |
|---|---|
| Initial | Load case/action, no preselected score. |
| Loading | Skeleton; submit spinner. |
| Success / ready | Konfirmasi “Masukan diterima.” |
| Empty | Belum eligible → jelaskan; bukan form aktif. |
| Error | Retry aman, jangan ubah hasil case. |
| Disabled | Tidak eligible/feedback sudah dikirim bila server menyatakan. |
| Validation | Skor integer 1–5; komentar max 2000. |
| Permission denied | Owner dan policy feedback, deny objek lain. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### CIT-11 — Profil
**Route:** `/profil` · **Konteks:** CITIZEN · **Prioritas:** P0 · **Trace:** FR-02

**Data/API:** GET /me; POST /auth/logout; auth MFA/recovery sesuai policy.

**Layout & hierarki:** Identitas minimal display_name, role/scope, keamanan akun, privasi; household/profile edit sesuai kebutuhan MS tetapi gap G08.

**Interaksi:** Keluar; pemulihan akun yang sah; verifikasi identitas manual; jangan buat edit/save tanpa endpoint.

| State | Desain dan tindakan |
|---|---|
| Initial | Fetch /me. |
| Loading | Skeleton profil. |
| Success / ready | Identitas minimal dan scope valid. |
| Empty | Tidak ada data tambahan → “Belum tersedia”. |
| Error | Retry; logout ambiguous jangan klaim sukses. |
| Disabled | Edit data/sesi/household belum ada kontrak; no dummy action. |
| Validation | Jika action auth, validasi sesuai AUTH-01/03. |
| Permission denied | Akun sendiri; no staff role self-change. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### CIT-12 — Verifikasi identitas
**Route:** `/verifikasi-identitas` · **Konteks:** CITIZEN · **Prioritas:** P0 manual / P2 face · **Trace:** FR-02, FR-18

**Data/API:** POST /identity-checks/manual-request; POST /identity-checks/face hanya jika P2.

**Layout & hierarki:** Jelaskan tujuan, tidak menentukan kelayakan; manual request reason sebagai default. P2 jika approved: opt-in dan consent, capture quality/PAD jujur, manual alternatif terlihat.

**Interaksi:** Ajukan pemeriksaan manual; P2 nonmatch/indeterminate → manual; tidak meminta biometrik saat off.

| State | Desain dan tindakan |
|---|---|
| Initial | Manual form idle; feature flag server. |
| Loading | Submit spinner; P2 processing jika aktif. |
| Success / ready | MANUAL_REQUIRED atau hasil face sesuai enum, tanpa eligibility claim. |
| Empty | Belum mengajukan; status history belum tersedia (G08). |
| Error | Retry aman; face gagal tetap manual. |
| Disabled | FACE_FEATURE_ENABLED=false default; tidak render capture. |
| Validation | Reason 1–2000; P2 consent/reference valid sesuai kontrak. |
| Permission denied | Owner/session; private no raw storage. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### REG-01 — Dashboard wilayah
**Route:** `/wilayah` · **Konteks:** REGIONAL_PROPOSER / VILLAGE_OPERATOR · **Prioritas:** P0 · **Trace:** FR-06, FR-07, FR-13

**Data/API:** GET /me; GET /officer/cases (operator); GET /cases (represented bila scope kontrak).

**Layout & hierarki:** RT: CTA usulkan warga, mandat sah, daftar kasus didampingi. Operator: antrean assigned, status pemeriksaan, next task. Scope label terlihat; metrics tidak dihitung dari page parsial.

**Interaksi:** RT usulkan; operator lihat daftar/verifikasi; tidak menggabung aksi role tanpa scope.

| State | Desain dan tindakan |
|---|---|
| Initial | Fetch scope + list sesuai role. |
| Loading | Queue skeleton. |
| Success / ready | Antrean atau kasus didampingi aktual. |
| Empty | Tidak ada kasus/mandat; instruksi jalur sah. |
| Error | Retry per region. |
| Disabled | Tidak ada mandat/scope → no create; operator no final approve. |
| Validation | N/A; tanpa form. |
| Permission denied | Staff AAL2 + role/scope; deny lintas wilayah. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### REG-02 — Pengusulan warga
**Route:** `/wilayah/usulkan` · **Konteks:** REGIONAL_PROPOSER / VILLAGE_OPERATOR dengan mandat · **Prioritas:** P0 · **Trace:** FR-06, FR-19

**Data/API:** POST /data-proposals/drafts atau POST /programs/{id}/applications/drafts; PATCH draft; POST submit; POST attachment.

**Layout & hierarki:** Scope strip; pilih subjek dari mandate lookup (G09); dasar representasi read-only; pilih jalur; wizard sesuai CIT-03/04 dengan actor≠subject; review mandat.

**Interaksi:** Usulkan atas nama warga; simpan draf; kirim; nomor tiket; tidak mencari semua warga.

| State | Desain dan tindakan |
|---|---|
| Initial | Load grant sah; form kosong. |
| Loading | Skeleton grant/form, submit spinner. |
| Success / ready | Tiket atas nama subjek sesuai grant. |
| Empty | Tidak ada grant aktif → minta proses mandat sah, tidak ada form. |
| Error | Retry; grant revoked segera stop. |
| Disabled | Grant expired, scope invalid, mode tidak sah; no submit. |
| Validation | subject_user_id/grant_id cocok, rules & evidence valid. |
| Permission denied | case.create.represented + scope dan mandate; no self-approve. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### REG-03 — Daftar kasus wilayah
**Route:** `/wilayah/kasus` · **Konteks:** REGIONAL_PROPOSER / VILLAGE_OPERATOR · **Prioritas:** P0 · **Trace:** FR-06, FR-07

**Data/API:** GET /officer/cases (assigned operator); GET /cases (represented sesuai scope).

**Layout & hierarki:** Queue table/cards; status filter yang ada di API; tiket/jenis/status/waktu; detail action. RT list didampingi, operator antrean pemeriksaan.

**Interaksi:** Buka kasus/verifikasi bila assigned; RT read-only detail representasi perlu G09; tidak assign diri.

| State | Desain dan tindakan |
|---|---|
| Initial | Fetch scoped list. |
| Loading | Queue skeleton. |
| Success / ready | Rows sesuai scope. |
| Empty | Tidak ada kasus / filter kosong. |
| Error | Retry dan scope context. |
| Disabled | No cursor/assignment invalid. |
| Validation | Enum status dan opaque cursor. |
| Permission denied | Staff scope; no global region search. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### REG-04 — Verifikasi operator
**Route:** `/wilayah/verifikasi/[id]` · **Konteks:** VILLAGE_OPERATOR assigned · **Prioritas:** P0 / P1 AI · **Trace:** FR-07, FR-15, FR-19

**Data/API:** GET /cases/{id}; GET attachment download; POST /officer/cases/{id}/verify; POST /officer/cases/{id}/request-revision.

**Layout & hierarki:** PageHeader ticket/scope; left snapshot data, evidence dan rule flags, right ReviewPanel; timeline; P1 advisory AI panel G06; no final review.

**Interaksi:** Minta perbaikan (reason, fields, deadline); Ajukan rekomendasi (reason, evidence); confirm, refetch. No approve final.

| State | Desain dan tindakan |
|---|---|
| Initial | Fetch assignment/evidence/available_actions. |
| Loading | Skeleton panels. |
| Success / ready | Rekomendasi PENDING_APPROVAL atau REVISION_REQUIRED aktual. |
| Empty | Tidak ada bukti wajib → jelaskan dan minta perbaikan sesuai policy. |
| Error | 409 reload; AI unavailable manual; download error safe. |
| Disabled | Not assigned, already changed, missing reason; no action. |
| Validation | required_fields, deadline_at, expected_version, evidence IDs; 422. |
| Permission denied | Assigned scope dan case.verify.assigned; no self-review bypass. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### AGY-01 — Dashboard instansi
**Route:** `/instansi` · **Konteks:** AGENCY_VERIFIER / PROGRAM_MANAGER · **Prioritas:** P0 · **Trace:** FR-03, FR-08, FR-10

**Data/API:** GET /me; GET /agency/reviews; program list/appeal list gap G10/G11.

**Layout & hierarki:** Verifier: pending review queue, appeal link jika grant, scope. Manager: daftar draft/versi/period dan publish review bila grant; no case decision. Layout 7/5 atau table-first.

**Interaksi:** Buka review / kelola program; tidak memberi CTA publikasi default.

| State | Desain dan tindakan |
|---|---|
| Initial | Load scope dan role data. |
| Loading | Queue skeleton. |
| Success / ready | Role-specific queue actual. |
| Empty | Belum ada tugas; no fake metrics. |
| Error | Retry per panel; gap diberi status internal bukan dummy. |
| Disabled | No assignment/permission → no action. |
| Validation | N/A. |
| Permission denied | Staff scope + AAL2, separation of duties. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### AGY-02 — Antrean review kasus
**Route:** `/instansi/review` · **Konteks:** AGENCY_VERIFIER · **Prioritas:** P0 · **Trace:** FR-07, FR-08

**Data/API:** GET /agency/reviews; POST /officer/cases/{id}/assign (coordinator).

**Layout & hierarki:** Table ticket/type/status/updated; scope strip; assignment control hanya jika explicit grant dan assignee lookup G09; no unsupported search/filter.

**Interaksi:** Lihat review; assign operator jika grant dan candidate eligible, no self assignment.

| State | Desain dan tindakan |
|---|---|
| Initial | Fetch queue. |
| Loading | Table skeleton. |
| Success / ready | Rows scope, action sesuai guard. |
| Empty | Antrean kosong. |
| Error | Retry list; 409 assignment refresh. |
| Disabled | No grant/assignee list missing → no assignment control. |
| Validation | Assignment reason, eligible assignee, version. |
| Permission denied | case.review.assigned / case.assign scoped; no global queue. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### AGY-03 — Review kasus instansi
**Route:** `/instansi/review/[id]` · **Konteks:** AGENCY_VERIFIER assigned independen · **Prioritas:** P0 / P1 AI+simulator · **Trace:** FR-08, FR-14, FR-15, FR-19

**Data/API:** GET /cases/{id}; GET attachment download; POST /agency/cases/{id}/review.

**Layout & hierarki:** Left evidence, snapshot, rule citations, timeline; right decision form without default; decision consequences. G06. P1 forwarding state distinct, no manual fake ACK.

**Interaksi:** Tindak lanjut internal, minta pemeriksaan ulang, tidak dapat diproses, tidak terpilih hanya program; reason/rule/evidence + confirm. Show simulator pending/failure status only.

| State | Desain dan tindakan |
|---|---|
| Initial | Fetch case and guard. |
| Loading | Panel skeleton. |
| Success / ready | Server-confirmed result and reason, P0 not forwarded. |
| Empty | Evidence tidak tersedia → recheck bila sah; no fake evidence. |
| Error | 409 reload, AI unavailable manual, no optimistic result. |
| Disabled | Maker==checker, stale version, state changed; no action. |
| Validation | Action enum, reason/rule_ref/evidence, expected_version. |
| Permission denied | Independent reviewer, assignment, jurisdiction; generic denial. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### AGY-04 — Manajemen program
**Route:** `/instansi/program` · **Konteks:** PROGRAM_MANAGER · **Prioritas:** P0 · **Trace:** FR-03

**Data/API:** GET internal list gap G10; POST /agency/programs/{id}/archive.

**Layout & hierarki:** Header Buat program; table name/code, lifecycle, version, period, action; distinguish published vs open; scope agency.

**Interaksi:** Buat draf / edit version / archive closed if grant; archive preserves snapshots; no fake list from public catalog.

| State | Desain dan tindakan |
|---|---|
| Initial | Scope + internal list when approved. |
| Loading | Skeleton rows. |
| Success / ready | Internal programs from scoped read model. |
| Empty | Belum ada program after actual list. |
| Error | Retry; missing contract is release blocker. |
| Disabled | Archive only CLOSED and program.archive; editing locked review. |
| Validation | Archive reason/expected_version. |
| Permission denied | Only agency scope; public catalog not substitute. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### AGY-05 — Buat program
**Route:** `/instansi/program/baru` · **Konteks:** PROGRAM_MANAGER · **Prioritas:** P0 · **Trace:** FR-03

**Data/API:** POST /agency/programs/drafts; PATCH /agency/programs/{id}/draft; POST /agency/programs/{id}/submit-publication; GET /public/territories.

**Layout & hierarki:** Wizard identity/instansi → mode + address basis + territory → dates → rules/source + required documents → optional quota/SLA/appeal → review. G10 for schema/read and policy fields.

**Interaksi:** Simpan draf (boleh belum lengkap), Ajukan tinjauan setelah validated; no publish own.

| State | Desain dan tindakan |
|---|---|
| Initial | Load scope/territories, fields blank. |
| Loading | Skeleton lookup; save spinner. |
| Success / ready | Draft actual version / pending review confirmed. |
| Empty | No territory or agency mandate → no publish. |
| Error | Retry, 409 reload; draft preserved. |
| Disabled | No grant; submit until required fields server valid. |
| Validation | Unique code, slug, date start<=end, typed rules, hierarchy, quotas/SLA, documents. |
| Permission denied | program.draft.create scoped; no cross-agency. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### AGY-06 — Edit versi program
**Route:** `/instansi/program/[id]/edit` · **Konteks:** PROGRAM_MANAGER · **Prioritas:** P0 · **Trace:** FR-03

**Data/API:** PATCH /agency/programs/{id}/draft; POST /agency/programs/{id}/submit-publication; GET internal detail gap G10.

**Layout & hierarki:** Same wizard with version badge and immutable published snapshot/returned reason; diff where data available; publication review locks draft.

**Interaksi:** Simpan perubahan draf; ajukan kembali; versi baru hanya mekanisme server yang disetujui; no mutate published.

| State | Desain dan tindakan |
|---|---|
| Initial | Fetch scoped draft/version. |
| Loading | Skeleton. |
| Success / ready | Server-confirmed draft update and version. |
| Empty | Tidak ada editable draft → read-only/return list. |
| Error | 409 compare latest and re-enter; no overwrite. |
| Disabled | Pending review/published immutable; no edit without new-version contract. |
| Validation | Same program validations, expected_version. |
| Permission denied | Agency scope and ownership/permission. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### AGY-07 — Tinjauan publikasi
**Route:** `/instansi/program/review-publikasi` · **Konteks:** Pemegang program.publish independen · **Prioritas:** P0 · **Trace:** FR-03

**Data/API:** POST /agency/programs/{id}/publish; POST /agency/programs/{id}/return; GET internal review queue/detail gap G10.

**Layout & hierarki:** Queue list + selected draft detail; compare version, mode, dates, territory, rule source, documents; maker info scoped; reason field and review actions.

**Interaksi:** Publikasikan / Kembalikan untuk perbaikan setelah review; no self-approve; show actual lifecycle.

| State | Desain dan tindakan |
|---|---|
| Initial | Load queue and selected draft. |
| Loading | Skeleton queue/detail. |
| Success / ready | Server lifecycle result; public page update via invalidation. |
| Empty | Tidak ada draft pending review. |
| Error | 409 refresh; retry idempotent. |
| Disabled | Maker==reviewer or stale version; no publish. |
| Validation | Reason and expected_version; validate required config server. |
| Permission denied | program.publish explicit, independent reviewer; generic denial. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### AGY-08 — Manajemen sanggahan
**Route:** `/instansi/sanggahan` · **Konteks:** AGENCY_VERIFIER assigned / coordinator grant · **Prioritas:** P0 · **Trace:** FR-10

**Data/API:** GET /appeals/{id}; POST /agency/appeals/{id}/assign; POST /agency/appeals/{id}/review; GET queue gap G11.

**Layout & hierarki:** Separate appeal queue; selected ticket, challenged decision snapshot, reason/evidence, history; compare previous decision and appeal; form options Uphold/Recheck/Correct localized.

**Interaksi:** Assign only coordinator, independent reviewer; Pertahankan keputusan / Minta pemeriksaan ulang / Koreksi keputusan; reason/evidence + confirm; parent recheck atomic.

| State | Desain dan tindakan |
|---|---|
| Initial | Load queue/detail scoped. |
| Loading | Skeleton queue/panel. |
| Success / ready | Appeal status and parent recheck actual; original preserved. |
| Empty | No assigned appeal. |
| Error | Retry; no eligible reviewer → escalate via approved channel (no invented endpoint). |
| Disabled | Previous decision maker or self assignment invalid, no action. |
| Validation | expected_version, reason, evidence, valid assignee; 422. |
| Permission denied | appeal.review.independent / appeal.assign; no leak of confidential evidence. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### ADM-01 — Pengawasan / dashboard auditor / monitoring
**Route:** `/administrasi/pengawasan` · **Konteks:** AUDITOR · **Prioritas:** P0 / P1 downtime if available · **Trace:** FR-13, FR-15

**Data/API:** GET /internal/metrics.

**Layout & hierarki:** KPI cards only actual metrics, definition and as_of; backlog/aging/SLA only if available; audit link, complaint queue if grant. Monitoring is section here, not a new route. G03.

**Interaksi:** Lihat audit / pengaduan authorized; filter/detailed drilldown only supported contract.

| State | Desain dan tindakan |
|---|---|
| Initial | Fetch internal metrics scoped. |
| Loading | Skeleton KPI. |
| Success / ready | Metric values with provenance. |
| Empty | Belum ada data aman / no metric available. |
| Error | Retry; no fake trend. |
| Disabled | No unsupported filter, chart, export. |
| Validation | N/A until approved filter schema. |
| Permission denied | metrics.read.internal scoped; no cross-agency rollup unauthorized. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### ADM-02 — Audit log
**Route:** `/administrasi/audit` · **Konteks:** AUDITOR (scope), SUPER_ADMIN limited technical log only if grant · **Prioritas:** P0 · **Trace:** FR-13

**Data/API:** GET /audit/events.

**Layout & hierarki:** Header read-only; date range/object id filters; native table event time/action/object/ref scoped; expandable AuditTimeline only safe fields; no edit/delete.

**Interaksi:** Apply/reset filters; inspect safe event detail; cursor navigation.

| State | Desain dan tindakan |
|---|---|
| Initial | Fetch scoped events. |
| Loading | Table skeleton. |
| Success / ready | Events actual immutable, redacted. |
| Empty | Tidak ada event untuk filter. |
| Error | Retry; no raw payload on errors. |
| Disabled | No edit/delete; cursor null. |
| Validation | Date from<=to, UUID object_id valid. |
| Permission denied | audit.read.scoped; technical log not full audit by default. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### ADM-03 — Penanganan pengaduan
**Route:** `/administrasi/pengaduan` · **Konteks:** AUDITOR / handler authorized as matrix · **Prioritas:** P0 · **Trace:** FR-11

**Data/API:** GET /complaints; GET /complaints/{id}; POST /complaints/{id}/triage; POST /complaints/{id}/transition.

**Layout & hierarki:** Scoped queue and detail; confidentiality marker, category/status, outcome and action form; no linked case data unless independently authorized.

**Interaksi:** Triase → assign → mulai → selesaikan/rujuk → tutup per transition; reason/destination/outcome when required; G09 assignees.

| State | Desain dan tindakan |
|---|---|
| Initial | Load queue/detail. |
| Loading | Skeleton; pending action. |
| Success / ready | New state after response, outcome communicated as workflow. |
| Empty | Queue empty. |
| Error | Retry or 409 reload; no confidentiality leak. |
| Disabled | Missing grant/assignment, incorrect state. |
| Validation | DTO discriminated action fields, version, reason. |
| Permission denied | complaint.manage.assigned/complaint.assign; separate from case decision. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### ADM-04 — Manajemen pengguna
**Route:** `/administrasi/pengguna` · **Konteks:** SUPER_ADMIN · **Prioritas:** P0 · **Trace:** FR-02

**Data/API:** POST /admin/users/{id}/roles; POST /admin/representation-grants; POST /admin/representation-grants/{id}/approve; GET /me; read/invite gaps G09/G12.

**Layout & hierarki:** User directory scoped once available; roles/scopes/expiry, pending approval and mandate; form change reason, approval reference; step-up MFA.

**Interaksi:** Grant/revoke with independent approval, fresh AAL2, create/approve representation grant; invite only when contract exists.

| State | Desain dan tindakan |
|---|---|
| Initial | Load authorized directory and scope. |
| Loading | Skeleton table; reauth modal. |
| Success / ready | Confirmed role/grant change; audit link if authorized. |
| Empty | No users/grants in scope (actual data only). |
| Error | 409 or MFA expired → reauthenticate; retry safe. |
| Disabled | Self grant, expired approval, missing lookup; no grant action. |
| Validation | Role enum excludes Guest; territory/agency/valid_until, reason, approval_ref; grant basis/evidence. |
| Permission denied | users.manage_roles / representation grants scoped; no raw tokens/PII directory leaks. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### ADM-05 — Manajemen wilayah
**Route:** `/administrasi/wilayah` · **Konteks:** SUPER_ADMIN dengan territory.manage · **Prioritas:** P0 · **Trace:** FR-02

**Data/API:** GET /public/territories (public lookup only); POST /admin/territories; internal read gap G12.

**Layout & hierarki:** Hierarchy list / nested disclosure, breadcrumb selected parent; create form parent_id, level, code, name; scope details only if authorized.

**Interaksi:** Tambah wilayah if grant; no edit/delete absent endpoint; parent selection from approved lookup.

| State | Desain dan tindakan |
|---|---|
| Initial | Load hierarchy. |
| Loading | Skeleton tree. |
| Success / ready | Persisted hierarchy from real authorized data. |
| Empty | No territory in authorized scope. |
| Error | Retry; server conflict reason. |
| Disabled | No permission or missing parent lookup, no create. |
| Validation | Hierarchy level parent compatibility, unique code, required fields. |
| Permission denied | territory.manage scoped; no silent global access. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.

### ADM-06 — Integrasi dan kesehatan sistem
**Route:** `/administrasi/integrasi` · **Konteks:** SUPER_ADMIN (integration.manage); knowledge grants if allowed · **Prioritas:** P0 health / P1 simulator, knowledge, WA/RAG · **Trace:** FR-14, FR-15, FR-16, FR-17

**Data/API:** GET /admin/integrations/health; POST /admin/knowledge-docs; POST /admin/knowledge-docs/{id}/approve; knowledge list gap G13.

**Layout & hierarki:** Health rows name/status, last checked only if server data; simulation label; P1 knowledge section with source draft and independent approval if grant; no token/config exposure. No separate simulator admin UI unless approved Master Spec routing clarified.

**Interaksi:** Refresh health; P1 create/approve public knowledge version; no toggle/retry service control absent endpoint.

| State | Desain dan tindakan |
|---|---|
| Initial | Load health, flags from server; knowledge only P1. |
| Loading | Skeleton health list. |
| Success / ready | Actual UP/DOWN/DISABLED/DEGRADED; knowledge response actual. |
| Empty | No integration status yet → unknown, not healthy. |
| Error | Retry; service down affects relevant UI only. |
| Disabled | P1/P2 flags off, no approval if same maker; no dummy toggles. |
| Validation | Knowledge DTO + source provenance, version/reason. |
| Permission denied | integration.manage; knowledge permissions separate; no credentials. |

**Acceptance desain:** urutan konten sesuai hierarki di atas; seluruh state teruji dengan synthetic fixture; 360 px tidak overflow; action mengikuti scope/guard dan hasil server; tidak ada placeholder yang terlihat sebagai data nyata.
