# BantuTepat — MASTER SPECIFICATION & IMPLEMENTATION BLUEPRINT

> **Dokumen induk untuk AI coding agent (Codex)** · Versi 3.0 FINAL BLUEPRINT · 19 November 2026  
> **Tujuan:** membangun aplikasi web BantuTepat yang benar-benar dapat dijalankan, diuji, di-deploy, dan didemokan pada **Techcomfest 2027 — Web Application Competition**, subtema **Smart Governance & Public Service**.  
> **Status dokumen:** spesifikasi lengkap dan lanjutan dari MASTER SPEC v2; bukan produk berjalan ataupun pernyataan integrasi resmi pemerintah.  
> **Bahasa UI:** Bahasa Indonesia. **Timezone data:** UTC di database; tampilkan Asia/Jakarta untuk demo layanan Indonesia.  
> **Ruang lingkup:** satu website multi-role, satu backend utama, satu DB utama, Government Data Simulator terisolasi, AI anomaly service, chatbot LLM+RAG hanya web, face verification 1:1 opsional-terbatas, WhatsApp bot **statis tanpa LLM**.

---

## PANDUAN MEMBACA DOKUMEN (WAJIB UNTUK CODEX)

**Dokumen ini merupakan kelanjutan langsung spesifikasi sebelumnya (v2) dan telah dilengkapi menjadi v3.** Seluruh diagram disimpan inline dalam blok `mermaid`, tidak bergantung pada file Draw.io eksternal. Semua contoh wilayah, nama akun, dokumen, data keluarga dan keputusan bersifat **simulasi** kecuali disebut sebagai referensi hukum/resmi.

| Rentang bagian | Isi | Kapan dipakai |
|---|---|---|
| **0–3** | instruksi Codex, tujuan, penilaian lomba, role & permission | sebelum menyentuh kode |
| **4–6** | peta frontend, modul backend, model database, arsitektur | saat desain awal/migrasi |
| **7–14** | master workflow, program, usulan data, pendaftaran, userflow, status, sanggahan, WhatsApp statis | saat membangun business logic |
| **15–18** | AI, face verification, LLM/RAG web, API contracts, sequence, security/privacy | saat integrasi service & hardening |
| **19–25** | spesifikasi frontend/backend rinci, policy, deployment, monorepo, CI/CD, QA, analytics | saat implementasi end-to-end |
| **26–28** | referensi hukum/teknis, milestone, storyboard demo | saat compliance review & presentasi |
| **29–32** | prompt eksekusi Codex, Definition of Done, indeks 30 diagram, risiko | saat menyerahkan ke coding agent dan final QA |

**Sumber otoritas:** (1) ketentuan lomba = *rulebook* yang diunggah pengguna; (2) kebijakan bansos sebenarnya = peraturan/ketentuan program resmi yang relevan; (3) untuk contoh fiktif dan detail engineering = rekomendasi desain dalam spesifikasi ini. Bila berbeda, **jangan** menggantikan peraturan dengan rekomendasi teknis.

---

## 0. INSTRUKSI WAJIB UNTUK CODEX — BACA SEBELUM CODING

**Gunakan dokumen ini sebagai single source of truth.** Implementasi harus mengikuti aturan **SHALL/MUST**; saran **SHOULD** diprioritaskan jika memungkinkan; **MAY** opsional. Jika bagian tidak bisa selesai, gunakan *feature flag*, graceful degradation, dan dokumentasikan kekurangan. Jangan mengarang integrasi pemerintah resmi, kredensial, data warga asli, hasil model, atau status penerima bansos resmi.

**PENTING: kode yang ditulis harus menjadi perangkat lunak nyata, bukan mockup layar tanpa fungsi.** Setiap tombol submit harus memanggil API yang benar, memvalidasi, menyimpan ke DB, dan mengembalikan state yang konsisten. Buat migrasi, seed, backend authorization, error handling, test, Docker Compose, `.env.example`, OpenAPI/docs, dan README deployment. Seluruh service disimpan dalam **monorepo**.

**Urutan kerja wajib (vertical slice):**

1. Buat struktur monorepo, desain sistem, DB schema/migration/seed dan autentikasi/RBAC dengan wilayah.
2. Implementasi program → publikasi berotorisasi → katalog + pembatasan wilayah/jalur.
3. Implementasi pengajuan warga/RT → validasi → kasus/tiket → pemeriksaan petugas → review independen → hasil/penolakan beralasan → perbaikan → sanggahan → feedback → audit.
4. Implementasi transparansi publik yang hanya menampilkan data agregat dan terlindungi.
5. Tambahkan Government Simulator, rules engine deterministik, dan AI anomaly detection dengan data sintetis.
6. Tambahkan LLM+RAG **hanya website**, dengan dokumen sumber dan mode fallback FAQ.
7. Tambahkan WA menu statis via provider adapter; sediakan WA sandbox/simulator jika nomor/akun Meta belum disetujui.
8. Tambahkan face verification 1:1 demo berizin hanya jika aman, dapat diuji, dan tidak mengganggu alur inti; sediakan fallback manual.
9. Uji otomatis, keamanan lintas-role, aksesibilitas, mobile, kinerja, failure mode, lalu deploy dan buat skrip video demo.

**Non-negotiable guardrails:**

- BantuTepat **bukan** DTSEN, Dukcapil, SIKS-NG, SP4N, bank penyalur, atau portal pemerintah resmi. Government Data Simulator hanya berisi **DATA SINTETIS** dan harus berlabel simulasi pada UI.
- `DATA_PROPOSAL` (usulan/koreksi kondisi sosial ekonomi) **berbeda** dari `PROGRAM_APPLICATION` (pengajuan program tertentu); status, aturan, dan hasilnya terpisah meskipun sama-sama memakai nomor tiket.
- Dua sumber pengusul (warga mandiri dan petugas/RT terotorisasi) **tidak** berarti ada dua database pengajuan.
- **Model AI tidak boleh menentukan kelayakan, menolak, menyetujui, menetapkan penerima, atau memperbarui DTSEN secara otomatis.** AI hanya memberi indikator yang bisa diperdebatkan dan diverifikasi manusia.
- Hasil administrasi seperti `FORWARDED_SIMULATED` **bukan** status `RESMI_DITERIMA`, `MASUK_DTSEN`, `BANTUAN_CAIR`.
- **Pengaduan pelayanan**, **sanggahan keputusan**, dan **feedback kepuasan** adalah entitas dan proses berbeda.
- Jika warga ditolak/hasil tidak dapat diproses, tampilkan **alasan, dasar rule/aturan, tanggal, pihak pemroses sesuai hak akses, jalur koreksi/sanggahan yang tersedia**. Jangan ada penolakan hitam-putih tanpa penjelasan.
- Face verification **tidak dijadikan gerbang wajib untuk warga** pada MVP. Tidak ada facial recognition 1:N/mass surveillance. Kegagalan wajah harus ada jalur manual.
- RT/RW/operator tidak otomatis memperoleh data seluruh penduduk; harus punya **akun terotorisasi, tugas, dan wilayah eksplisit**.
- LLM hanya untuk informasi umum/FAQ/RAG di situs web. **WhatsApp tidak menjalankan LLM**.
- Data rahasia/identitas/dokumen tidak masuk ke log, analytics publik, prompt LLM pihak ketiga, atau repository.

### 0.1 Definition of Done proyek

`DONE` bila: (a) aplikasi URL publik aktif; (b) minimal 2 program simulasi dengan aturan wilayah/jalur berbeda; (c) pengajuan warga dan pengusulan RT berjalan; (d) satu kasus bisa mencapai **hasil berhasil**, **perlu perbaikan**, **ditolak/tidak dapat diproses**, dan **sanggahan**; (e) audit/event timeline benar; (f) semua endpoint melakukan authorization; (g) AI anomaly memunculkan flag tanpa auto-deny; (h) FAQ RAG atau fallback berfungsi; (i) WA adapter punya simulasi end-to-end; (j) UI responsif; (k) README, seed akun, test, dan video demo jelas.

### 0.2 Larangan shortcut untuk Codex

DILARANG: hanya membuat landing page; memalsukan respons AI hardcoded seolah model aktif; mengasumsikan semua user adalah admin; menaruh secrets di browser; menyimpan gambar wajah di public bucket; endpoint admin tanpa pemeriksaan backend; membuat status penerima resmi palsu; menghubungkan database pemerintah sungguhan tanpa izin; memakai template website jadi yang dilarang rulebook. UI boleh menggunakan pustaka komponen/framework, tetapi **desain/tata letak harus orisinal** dan bukan template situs siap pakai.

---

## 1. VISI, MASALAH, AKTOR, KEPUTUSAN DESAIN

### 1.1 Masalah yang ditangani

1. Ketidakselarasan/keterlambatan pemutakhiran kondisi sosial ekonomi.
2. Sulitnya mengakses informasi program, syarat, wilayah, periode, dan metode pengajuan.
3. Kurangnya visibilitas proses verifikasi, alasan tindak lanjut, dan perubahan status.
4. Kesulitan mengajukan klarifikasi/sanggahan tanpa mengulang pengajuan dari nol.
5. Kebutuhan akuntabilitas petugas: siapa memeriksa, bagaimana keputusan terbentuk, dan apakah ada penyimpangan prosedural.
6. Keterbatasan akses layanan: web penuh + WhatsApp menu statis sebagai pintu masuk.

### 1.2 Lima pilar produk

| Pilar | Masalah | Respons produk | Bukti demo |
|---|---|---|---|
| **Accessibility** | Sulit akses layanan | katalog web, warga mandiri, petugas membantu, WA menu | pengajuan web + mulai WA |
| **Efficiency** | Proses berulang/manual | rules, validasi form, antrean, notifikasi, penyimpanan draf | perubahan status otomatis |
| **Accuracy / Assistance** | Data salah/tidak konsisten | duplikasi deterministik, AI anomaly flag, verifikasi manusia | kasus flagged & diverifikasi |
| **Transparency** | Warga tak tahu progres | timeline pribadi, alasan, program publik, statistik agregat | dashboard publik & personal |
| **Accountability** | Keputusan tak tertelusur | maker-checker, audit, banding, pengaduan | riwayat siapa-apa-kapan |

### 1.3 Objek bisnis utama — pemisahan wajib

| Objek | Arti | Sumber | Hasil yang benar |
|---|---|---|---|
| `PROGRAM` | Pengumuman program instansi dengan ketentuan | Pengelola + publisher instansi | Program terpublikasi, *bukan bantuan otomatis* |
| `DATA_PROPOSAL` | Usulan/koreksi data sosial ekonomi tanpa memilih program | Warga, RT, operator sesuai hak | Review dan siap diteruskan melalui mekanisme sah / tidak dapat diproses |
| `PROGRAM_APPLICATION` | Pendaftaran/pengusulan pada satu program spesifik | Warga/petugas sesuai mode | hasil tinjauan program, bukan penetapan resmi kecuali ada otoritas & integrasi |
| `APPEAL` | Keberatan atas hasil kasus | Subjek/pemohon terotorisasi | upheld/recheck/corrected, berdasar review |
| `COMPLAINT` | Pengaduan soal mutu, akses, petugas, dugaan pungli | Warga/guest sesuai kebijakan | ditriase/ditindaklanjuti/dirujuk |
| `FEEDBACK` | Survei kepuasan layanan | Pengguna selesai layanan | statistik agregat; tak memengaruhi hasil kasus |

### 1.4 Dua tipe deployment

- **Demo lomba:** seluruh warga, petugas, wilayah, foto referensi, program, dan data integrasi **fiktif/relawan berizin**. Status resmi **disimulasikan dan diberi label**.
- **Operasional pemerintahan nyata:** di luar scope lomba. Perlu kemitraan, izin/data-sharing, klasifikasi PSE, tata kelola keamanan, penilaian risiko dan perlindungan data; desain awal tidak otomatis menyatakan patuh hukum/terintegrasi.

---

## 2. PEMETAAN RESMI PENILAIAN TECHCOMFEST 2027

**Sumber:** berkas yang diberikan pengguna `Rulebook WEB APP Techcomfest 2027(1).pdf`, halaman tercetak 3–8 (PDF halaman 4–9), terutama tabel H. Kriteria Penilaian (halaman tercetak 6–7). Ini ringkasan isi rulebook, bukan inferensi.

| Bobot resmi | Kriteria | Hal yang mesti dibuktikan | Implementasi/artefak |
|---|---|---|---|
| **25%** | Kesesuaian Tema & Solusi | dampak pada efisiensi, transparansi, akuntabilitas, SDG 16 | narasi masalah → fitur → hasil; audit & keterbukaan |
| **30%** | Fungsionalitas & Kinerja Web | URL publik lancar, fitur berjalan, minim bug fatal | vertical slice end-to-end; automated E2E; graceful degradation |
| **25%** | Desain UI/UX & Estetika Visual | mudah navigasi, konsisten, responsif | design system orisinal, wizard form, accessibility, mobile |
| **20%** | Kualitas Demo Video | alur jelas, runtut, mudah dimengerti | rekam 4 skenario demo + catatan aksi dan hasil |

**Ketentuan yang memengaruhi engineering:** framework apa pun diperbolehkan; **website builder (mis. Wix/WordPress) dan penggunaan template website dilarang**; pemakaian AI diperbolehkan dengan tanggung jawab keamanan dan pemahaman source code; karya harus orisinal; hasil wajib berupa **tautan hosted app, video demo, GitHub**, dan jika frontend/backend menjadi beberapa proyek gunakan **monorepo**. Tenggat pengumpulan pada rulebook **18 Desember 2026 pukul 23.59 WIB**. Tim maksimal 3 orang. Jangan mengandalkan fitur mahal/rapuh yang mengancam demo.

### 2.1 KPI demo yang akan diukur (target internal, bukan klaim hasil)

| Indikator | Target uji/internal | Cara ukur |
|---|---|---|
| Skenario inti berhasil | 100% test P0 lulus | Playwright + API tests |
| Alur gagal/denied | 100% skenario P0 tercakup | test 403, 409, 422, 503 dan banding |
| Kebocoran data role | 0 temuan pada matriks test | test BOLA/IDOR lintas wilayah |
| Respons UI publik | p95 < 2,5 detik pada skenario demo normal | observasi pada hosting nyata; tidak menjamin SLA publik |
| API layanan inti | p95 < 1 detik pada seed dan beban ringan, tanpa AI eksternal | pengujian k6/Artillery ringan |
| LCP/Core Web Vitals | arahkan ke kategori Good jika mungkin | Lighthouse; laporkan hasil aktual |
| AI latency | dicatat, timeout dan fallback tersedia | integration test + log metriks |
| Keterbacaan & aksesibilitas | keyboard usable dan label form benar | axe/Lighthouse + manual |
| Demo total | 4 skenario wajib; tanpa restart manual | skrip E2E demonstrasi |

---

## 3. ROLE, MANDAT, HAK AKSES DAN BATASAN

**7 role yang punya akun + Guest.** `PUBLISH_PROGRAM` adalah permission khusus, bukan role kedelapan. Role bisa digabung pada satu akun hanya bila diberi mandat terpisah, tetapi konflik kepentingan maker-checker tetap dilarang per kasus/program.

### 3.1 Tabel role lengkap

| Kode role | Nama UI | Tugas | Yang boleh | Yang DILARANG / batasan | Dashboard |
|---|---|---|---|---|---|
| `GUEST` | Pengunjung | melihat informasi publik | katalog, aturan, FAQ web LLM, transparansi agregat, kanal kontak | tidak lihat data privat, tidak submit identitas | Publik |
| `CITIZEN` | Masyarakat | menggunakan layanan dirinya/rumah tangga sesuai hak | usul/koreksi, daftar mode SELF/BOTH, tracking sendiri, resubmit, appeal, complaint, feedback | tidak lihat/edit milik warga lain, tidak ubah status/hasil | Warga |
| `REGIONAL_PROPOSER` | RT/RW terotorisasi | mengusulkan/mendampingi warga | buat usulan dengan perwakilan yang sah, lihat kasus sesuai mandat, memberi klarifikasi | tidak setujui, tidak lihat semua data warga, tidak usulkan luar scope | Wilayah |
| `VILLAGE_OPERATOR` | Operator desa/kelurahan | verifikasi tingkat awal sesuai mandat | ambil kasus ditugaskan, cek kelengkapan, minta revisi, rekomendasi | tidak memberi penetapan nasional; tidak review kasus yang sama pada tingkat akhir | Wilayah |
| `AGENCY_VERIFIER` | Verifikator instansi/Dinsos | tinjau hasil petugas dan sanggahan sesuai otorisasi | approve forwarding internal, minta recheck, close unprocessable with reason, review appeal berbeda penangan | tidak memutus status penerima resmi di luar mandat; tidak jadi maker & checker kasus sama | Instansi |
| `PROGRAM_MANAGER` | Pengelola program | siapkan kebijakan program yang sudah ada dari instansi | create/edit draft, territory, mode, quota, ketentuan, submit publication review; publish hanya jika punya permission `PUBLISH_PROGRAM` | tidak buat program luar mandat; tidak publish sendiri tanpa otorisasi; tidak ubah hasil kasus | Instansi |
| `AUDITOR` | Pengawas/auditor | memantau kepatuhan dan kinerja | dashboard KPI, audit timeline, laporan dan eskalasi sesuai mandat | tidak manipulasi log, tidak menerima/menolak pengajuan tanpa kewenangan tambahan | Administrasi |
| `SUPER_ADMIN` | Super Admin Teknis | kelola user, izin dan keamanan | invite/revoke role, scope/masa berlaku, setting, health/operasional | **bukan pejabat penentu penerima**, tidak mengubah keputusan diam-diam, tidak bisa akses berkas privat tanpa mandat kasus | Administrasi |

**Group dashboard:** 4 + publik: `/dashboard` warga; `/wilayah` proposer/operator; `/instansi` verifier/program manager; `/administrasi` auditor/superadmin. Route UI hanya convenience. Izin selalu divalidasi ulang server-side.

### 3.2 Matriks hak akses (`A`=create; `R`=read assigned; `V`=verify/review; `P`=publish with separate permission; `-`=none)

| Kapabilitas | Guest | Warga | RT | Operator | Verifikator | Program mgr | Auditor | Super Admin |
|---|---|---|---|---|---|---|---|---|
| Baca katalog publik | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Chatbot RAG publik | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Data proposal milik sendiri | - | A/R | - | - | - | - | R* | - |
| Usulkan warga diberi mandat | - | - | A/R | A/R* | - | - | - | - |
| Pendaftaran program SELF | - | A/R | - | - | - | - | - | - |
| Pendaftaran via petugas | - | - | A/R* | A/R* | - | - | - | - |
| Verifikasi awal | - | - | - | V/R | - | - | R* | - |
| Review & tindak lanjut | - | - | - | - | V/R | - | R* | - |
| Draft program | - | - | - | - | - | A/R | R* | - |
| Publish program | - | - | - | - | - | P* | R* | - |
| Sanggahan kasus sendiri | - | A/R | A/R* | - | V/R* | - | R* | - |
| Audit read sesuai scope | - | Timeline sendiri | Timeline usulan | Timeline tugas | Timeline tugas | Timeline program | R | Access-log teknis |
| Admin user/role | - | - | - | - | - | - | - | A/R |

`*` = hanya bila mandat, kewenangan objek, instansi, wilayah, status, dan/atau assignment sesuai. Hak audit bersifat read-only. Publication reviewer harus **pengguna berbeda** dari pengusul draft, menggunakan permission eksplisit `PUBLISH_PROGRAM`.

### 3.3 Permission codes usulan

`program.read_public`, `program.draft.create`, `program.draft.update`, `program.submit_review`, `program.publish`, `program.archive`, `case.create.self`, `case.create.represented`, `case.read.own`, `case.read.assigned`, `case.revise.own`, `case.verify.assigned`, `case.review.assigned`, `case.appeal.own`, `appeal.review.independent`, `complaint.create`, `complaint.manage.assigned`, `audit.read.scoped`, `metrics.read.public`, `metrics.read.internal`, `users.manage_roles`, `integration.manage`, `ai.assessment.read.scoped`.

**Policy check** = `isAuthenticated && hasPermission && inJurisdiction && assignedOrOwner && statusAllowsAction && separationOfDuties && mandateActive`. Terapkan query filtering dan object check, bukan sekadar cek menu/route; semua mutasi terhubung user ID, agency ID, scope ID, version ID. Petugas yang mengajukan atas nama warga harus menyimpan `submitted_by_actor_id != subject_user_id` dan bukti dasar perwakilan.

### Diagram 01 — Role hierarchy dan hubungan tugas

```mermaid
flowchart TD
  G[Guest: katalog, FAQ, statistik] --> WEB["Satu Web BantuTepat"]
  C[Warga: pengajuan sendiri] --> WEB
  R["RT/RW: pengusul terotorisasi"] --> WEB
  O["Operator Desa: pemeriksaan awal"] --> WEB
  V["Verifikator Instansi: review"] --> WEB
  P["Pengelola Program: draft + publikasi berizin"] --> WEB
  AU["Auditor: audit dan laporan"] --> WEB
  SA["Super Admin: akses teknis"] --> WEB
  WEB --> AUTH["Auth + RBAC + Wilayah + Assignment"]
  AUTH --> PUB["Portal Publik"]
  AUTH --> WAR["Dashboard Warga"]
  AUTH --> WIL["Dashboard Wilayah"]
  AUTH --> INS["Dashboard Instansi"]
  AUTH --> ADM["Dashboard Administrasi"]
```

---

## 4. FRONTEND — INFORMATION ARCHITECTURE DAN HALAMAN

**Teknologi:** Next.js App Router + React + TypeScript + Tailwind. Gunakan komponen aksesibel yang dikustomisasi; desain sendiri, bukan template website jadi. Gunakan SSR/streaming untuk halaman publik yang perlu SEO, client components hanya untuk interaksi; validasi sisi client membantu UX tetapi **server selalu sumber kebenaran**.

### 4.1 Struktur route publik dan guest

| Route | Komponen/halaman | Fitur | Prioritas |
|---|---|---|---|
| `/` | Beranda | hero problem → solusi, CTA, statistik anonymized, alur, disclaimer simulasi | P0 |
| `/program` | Katalog program | search, filter wilayah/metode/status/jenis, list, empty state | P0 |
| `/program/[slug]` | Detail program | instansi, aturan, wilayah, periode, kuota jika sah, CTA kondisional, versi | P0 |
| `/panduan` | Panduan layanan | perbedaan usulan data vs daftar program, alur dan FAQ | P0 |
| `/transparansi` | Transparency Center | metrik agregat, rentang tanggal, per wilayah aman, sumber metadata | P0 |
| `/bantuan` | Pusat bantuan | FAQ statis, chatbot LLM+RAG widget, kontak, tautan kanal resmi | P1 |
| `/login`, `/daftar`, `/lupa-password` | Auth | sesi aman, recovery, kebijakan privasi, autentikasi tambahan petugas | P0 |
| `/privasi`, `/syarat-layanan`, `/aksesibilitas` | Kebijakan | tujuan data, retensi, hak subjek, status prototipe | P0 |

### 4.2 Route masyarakat (sesudah login)

| Route | Fitur | State penting | Prioritas |
|---|---|---|---|
| `/dashboard` | ringkasan kasus, notifikasi, program relevan | loading, empty, error | P0 |
| `/pengajuan/baru` | pilih `DATA_PROPOSAL` vs `PROGRAM_APPLICATION` | program yang valid tersedia | P0 |
| `/pengajuan/usulan` | wizard usulan/koreksi data | draft, invalid, resubmit | P0 |
| `/program/[slug]/daftar` | wizard aplikasi program | eligibility akses, dokumen, ringkasan | P0 |
| `/pengajuan-saya` | list pribadi & filter status | pagination aman | P0 |
| `/pengajuan-saya/[caseId]` | detail + timeline + alasan + dokumen izin | perbaikan, hasil, CTA banding | P0 |
| `/pengajuan-saya/[caseId]/perbaiki` | revisi dengan diff + resubmit | versi form dan deadline | P0 |
| `/pengajuan-saya/[caseId]/sanggah` | form keberatan + bukti | jika eligibility banding terpenuhi | P0 |
| `/pengaduan` | pengaduan layanan, bukan banding | ticket pengaduan terpisah | P0 |
| `/feedback/[caseId]` | survei pelayanan | opsional setelah selesai | P0 |
| `/profil` | akun, keamanan, keterkaitan rumah tangga | kontrol data dan sesi | P0 |
| `/verifikasi-identitas` | face demo atau manual fallback | flag `FACE_DEMO_ENABLED` | P2 |

### 4.3 Route pengelola dan admin

| Route | Role | Fungsi |
|---|---|---|
| `/wilayah` | RT/Operator | kasus wilayah sesuai assignment dan ringkasan |
| `/wilayah/usulkan` | RT/Operator | formulir atas nama warga, alasan perwakilan |
| `/wilayah/kasus` | RT/Operator | antrean yang diperbolehkan |
| `/wilayah/verifikasi/[id]` | Operator | bukti, rule flags, AI results, revisi/rekomendasi |
| `/instansi` | Verifikator/Program Manager | ringkasan instansi dan antrean |
| `/instansi/review` | Verifikator | kasus menunggu review final internal |
| `/instansi/review/[id]` | Verifikator | reasoned decision, maker-checker guard |
| `/instansi/sanggahan` | Verifikator terpisah | antrean appeal dan keputusan |
| `/instansi/program` | Program Manager | daftar draft/aktif/arsip sesuai instansi |
| `/instansi/program/baru` | Program Manager | wizard program, aturan dan wilayah |
| `/instansi/program/[id]/edit` | Program Manager | versioning, submit publish review |
| `/instansi/program/review-publikasi` | Pemegang `PUBLISH_PROGRAM` | approve/return program tanpa self-approve |
| `/administrasi/pengawasan` | Auditor | KPI kinerja, outlier proses, laporan |
| `/administrasi/audit` | Auditor | immutable event search; tanpa data lebih dari perlu |
| `/administrasi/pengaduan` | Auditor/penangan berotorisasi | triage pengaduan akses terbatas |
| `/administrasi/pengguna` | Super Admin | invitation, role, approval mandat, revoke |
| `/administrasi/wilayah` | Super Admin | master wilayah + scope, perubahan tercatat |
| `/administrasi/integrasi` | Super Admin | status koneksi & kesehatan, tanpa token terlihat |

### 4.4 Komponen UI reusable (gunakan design system sendiri)

`PublicHeader`, `PrivateSidebar`, `MobileNav`, `RoleSwitcherIfAuthorized`, `ProgramCard`, `TerritoryFilter`, `ProgramStatusBadge`, `ApplicationModeBadge`, `CaseCard`, `CaseTimeline`, `CaseStatusChip`, `CaseWizard`, `DocumentUploader`, `CorrectionDiff`, `ReviewChecklist`, `AIFlagPanel`, `ReasonCodeDisplay`, `AuditHistory`, `AppealForm`, `FeedbackDialog`, `TransparencyStats`, `RAGChatWidget`, `PrivacyNotice`, `SecuritySessionBanner`, `Skeleton`, `EmptyState`, `ErrorState`, `RetryAction`, `SimulationLabel`.

**Peraturan UX:**

- CTA jangan menjanjikan “pasti diterima”. Pakai “Ajukan usulan”, “Daftar program” jika dibuka, “Lihat persyaratan”, “Lihat hasil”.
- Indikator progress wizard (identitas/kondisi/bukti/ringkasan), autosave draft server-side, error per kolom, resume setelah gagal koneksi.
- Jika wilayah tidak cocok, tampilkan **kenapa** dan “Koreksi data wilayah jika salah”, bukan 404.
- Jika program tidak membuka pendaftaran, tampilkan mekanisme resmi; tidak ada CTA palsu.
- Jika status negatif, tampilkan alasan, versi aturan, tanggal, bukti yang boleh diakses, opsi sanggahan/rujukan.
- Mobile-first target 360/375/390 px; desktop 1280px; kontras cukup; form keyboard accessible; aria-live untuk error/hasil; ukuran tombol nyaman.
- Data sintetis wajib diberi penanda “Simulasi — Bukan layanan resmi pemerintah” di UI demo, tidak hanya footer.
- Notifikasi/email/WA gagal tidak membuat kasus hilang; pengguna selalu dapat membuka detail di web.

### Diagram 02 — Sitemap frontend lengkap

```mermaid
flowchart TD
  WEB["BantuTepat Web"] --> PUB["Area Publik"]
  WEB --> AUTH["Login / Daftar"]
  PUB --> HOME["Beranda"]
  PUB --> CAT["Katalog + Detail Program"]
  PUB --> INFO["Panduan + FAQ + Chatbot Web"]
  PUB --> TRANS["Transparansi Publik"]
  AUTH --> CIT["Dashboard Warga"]
  AUTH --> REG["Dashboard Wilayah"]
  AUTH --> INST["Dashboard Instansi"]
  AUTH --> ADM["Dashboard Administrasi"]
  CIT --> C1["Usulan Data / Daftar Program"]
  CIT --> C2["Pengajuan Saya + Timeline + Perbaikan"]
  CIT --> C3["Sanggahan + Pengaduan + Feedback"]
  REG --> R1["Usulkan Warga + Antrean"]
  REG --> R2["Verifikasi Awal + Klarifikasi"]
  INST --> I1["Program Management + Approval"]
  INST --> I2["Review Kasus + Sanggahan"]
  ADM --> A1["Monitoring + Audit"]
  ADM --> A2["Users + Role + Wilayah + Integrasi"]
```

---

## 5. BACKEND — MODUL, BOUNDARY, KEAMANAN ALUR

Gunakan **modular monolith** pada tahap lomba: Next.js Route Handlers sebagai BFF/API bisnis, service Python terpisah untuk inference AI, adapter Government Simulator terpisah, Postgres untuk state transaksional. Tidak perlu event-driven microservice kompleks. Gunakan **outbox + background worker** untuk tindakan non-kritis seperti notifikasi agar request utama tetap cepat.

### 5.1 Modul backend wajib

| Modul | Service | Tanggung jawab | Bukan tanggung jawabnya |
|---|---|---|---|
| Identity/Auth | BantuTepat API | sesi, reset, MFA admin/petugas, CSRF, login throttling | penetapan bantuan |
| RBAC & Jurisdiction | BantuTepat API | permission, scope, assignment, segregation of duties | aturan kelayakan ekonomi |
| Territory Reference | BantuTepat API | master provinsi/kab/kec/desa dan relasi | menggunakan GPS sebagai bukti tunggal |
| Program Catalog | BantuTepat API | baca program dan metadata publik | menyunting aturan pemerintah di luar kewenangan |
| Program Management | BantuTepat API | draft, version, rules, review, publish | memutus penerima otomatis |
| Proposal/Application | BantuTepat API | draft, submit, resubmit, kasus, attachments | memperbarui DTSEN asli |
| Validation/Rules | BantuTepat API | validasi deterministik, duplikasi, wilayah, mode, periode | auto reject substantif berdasar AI |
| Case Workflow | BantuTepat API | case state machine, assignment, pemeriksaan | mengganti pejabat penetapan resmi |
| Appeals/Complaints/Feedback | BantuTepat API | tiga workflow berbeda | keputusan tanpa review |
| Audit & Metrics | BantuTepat API | append-only events, agregasi statistik | publikasi NIK/nama individual |
| Notification | Outbox worker | email/WA transaksional, retry | mengubah status kasus saat pesan gagal |
| AI Orchestrator | BantuTepat API | masking, request inference, timeout, version capture | menyimpan secrets di frontend |
| AI Inference | FastAPI | tabular anomaly, face optional, retriever/LLM proxy | keputusan penerima |
| WA Gateway | BantuTepat API | webhook, state machine, template queue | chatbot LLM |
| Gov Integration Adapter | BantuTepat API | signed server-to-server calls, mock version, mapping schema | akses DTSEN/Dukcapil nyata tanpa izin |

### 5.2 Diagram 03 — Arsitektur high-level dan boundary keamanan

```mermaid
flowchart TB
  subgraph CLIENT["Clients / Channels"]
    W["Web Browser: Guest + 7 Role"]
    WA["WhatsApp Static Menu Bot"]
  end
  subgraph EDGE["Public HTTPS Edge"]
    CDN["Vercel: Next.js UI + Route Handlers"]
    WH["HTTPS Webhook WhatsApp + Signature Check"]
  end
  subgraph CORE["BantuTepat Business Backend"]
    AUTH["Auth + RBAC + Scope"]
    PROG["Program & Rules"]
    CASE["Case + Review + Appeals"]
    AUD["Audit + Aggregation"]
    NTF["Notification Outbox / Worker"]
    ORCH["AI Orchestrator"]
    GAD["Government Adapter"]
  end
  subgraph DATA["Private Managed Data"]
    DB[("PostgreSQL — primary DB")]
    OBJ[("Private Object Storage")]
  end
  subgraph SERVICES["Isolated Services"]
    AI["FastAPI CPU: Anomaly / Face 1:1 / RAG Adapter"]
    GOV["Government Simulator API — Synthetic Only"]
    LLM["External LLM API — Public FAQ Only"]
  end
  W -->|TLS| CDN
  WA --> WH
  WH --> AUTH
  CDN --> AUTH
  AUTH --> PROG
  AUTH --> CASE
  AUTH --> AUD
  CASE --> NTF
  CASE --> ORCH
  CASE --> GAD
  PROG --> DB
  CASE --> DB
  AUD --> DB
  CASE --> OBJ
  ORCH -->|Service Auth + Timeout| AI
  GAD -->|Service Auth| GOV
  AI -->|Sanitized RAG Question| LLM
  NTF -->|Provider API| WA
```

### 5.3 Diagram 04 — Backend transaction boundary dan event outbox

```mermaid
sequenceDiagram
  autonumber
  participant UI as Frontend
  participant API as BantuTepat API
  participant AUTH as RBAC/Rules
  participant DB as PostgreSQL
  participant OB as Outbox Worker
  participant AI as AI Service
  UI->>API: POST submit case (idempotency-key)
  API->>AUTH: authorize + validate + snapshot rules
  alt forbidden or invalid
    AUTH-->>API: deny with reason code
    API-->>UI: 403/409/422 + safe explanation
  else valid
    API->>DB: BEGIN
    API->>DB: INSERT case + revision + audit + outbox event
    API->>DB: COMMIT
    API-->>UI: 201 case_id + SUBMITTED
    OB->>DB: Poll unprocessed outbox
    OB->>AI: Request anomaly asynchronously
    alt AI available
      AI-->>OB: score + flags + model_version
      OB->>DB: Save assessment + event; never auto-deny
    else AI timeout
      OB->>DB: Save AI_UNAVAILABLE; retry limited
    end
    OB->>DB: queue notification (idempotent)
  end
```

### 5.4 Lifecycle event dan jobs

- Mutasi utama `case` dan `audit_event` **satu transaksi DB**. Append event setelah action terotorisasi, dengan `request_id`, `actor_id`, `entity_version`, `reason_code`.
- Event typed: `CASE_SUBMITTED`, `RULE_CHECK_COMPLETED`, `AI_CHECK_REQUESTED`, `AI_CHECK_COMPLETED`, `CASE_ASSIGNED`, `REVISION_REQUESTED`, `CASE_RESUBMITTED`, `REVIEW_COMPLETED`, `FORWARDING_SIMULATED`, `CASE_CLOSED`, `APPEAL_CREATED`, `APPEAL_REVIEWED`, `COMPLAINT_RECEIVED`, `PROGRAM_PUBLISHED`, `ROLE_GRANTED`, `ROLE_REVOKED`.
- Retry event delivery exponential backoff + max attempts + dead-letter diagnostics; **jangan duplikasi** akibat retry.
- Optimistic locking: `version` pada program/case. Konflik mutasi -> `409 CONFLICT`, reload dan tampilkan pesan.
- Logging: structured JSON, `request_id`, *masked* user identifiers; metrics tidak boleh mengandung dokumen, wajah, NIK, chat privat.

---

## 6. DATA MODEL DAN RELASI DATABASE

**Rekomendasi:** PostgreSQL managed + Prisma ORM untuk aplikasi Next.js. Database dikelola server-side; browser tidak mengakses schema administratif langsung. Jika memakai Supabase, tetap berlakukan RLS sesuai arsitektur akses; kunci elevated/service **tidak pernah masuk klien**.

### 6.1 Entitas wajib

| Tabel | Field penting (ringkas) | Indeks/constraint |
|---|---|---|
| `users` | id, email/phone verified, auth_provider_ref, status, created_at | unique normalized email; soft deactivate |
| `roles` | id, role_code | unique role_code |
| `permissions` | id, code | unique code |
| `user_role_scopes` | user_id, role_id, agency_id, territory_id, valid_from/to, approved_by | composite, expiry + revoked_at |
| `agencies` | id, name, agency_type, territory_scope, demo_flag | only synthetic in demo |
| `territories` | id, parent_id, level, code, name | unique code+level, hierarchical index |
| `households` | id, demo_household_key, self-reported attributes | data minimized |
| `person_household_links` | person_id, household_id, relation, verified_state | access proof |
| `representation_grants` | subject_id, representative_id, legal_basis_type, scope, expiry | revoke + evidence reference |
| `programs` | id, agency_id, slug, lifecycle_status, current_version_id | agency owner + unique slug |
| `program_versions` | id, program_id, version, name, policy_source, effective_at, mode, dates, address_basis, quota | immutable once published |
| `program_territories` | program_version_id, territory_id, include_descendants | composite unique |
| `program_criteria` | id, version_id, criterion_code, operator, value_json, source_document | strongly validated JSON schema |
| `cases` | id, ticket_no, type, subject_id, household_id, program_version_id nullable, submitter_id, source_channel, status, owner_agency_id, territory_id, version | unique ticket; type-conditional checks |
| `data_proposals` | case_id, change_type, reason, household_snapshot | 1:1 case type enforced |
| `program_applications` | case_id, program_version_id, household_snapshot, application_snapshot | 1:1 case type enforced |
| `case_revisions` | case_id, revision_no, snapshot_json, submitted_at, submitted_by | unique case+revision, immutable after submit |
| `attachments` | id, case_id, object_key, sha256, mime, size, uploader_id, scan_status, classification | private access only |
| `verification_tasks` | id, case_id, assignee_id, stage, status, due_at | unique active assignment per stage |
| `verification_reviews` | id, case_id, actor_id, stage, decision, reason_code, notes, evidence_ref, rule_version_id | maker-checker enforced |
| `ai_assessments` | id, case_id, input_schema_version, model_version, status, flags_json, score nullable, human_feedback | only internal |
| `identity_checks` | id, user_id, method, decision, model_version, checked_at, retention_until, manual_ref | NEVER public biometrics |
| `appeals` | id, parent_case_id, submitter_id, reason, status, assignee, resolved_at | unique active appeal policy |
| `appeal_decisions` | id, appeal_id, reviewer_id, decision, reason, timestamp | independent reviewer |
| `complaints` | id, related_case_id optional, confidential, category, owner, status, outcome | per-field access |
| `feedback` | id, case_id, citizen_id, score_1_5, category scores, comment | one feedback/version policy |
| `audit_events` | id, actor_id, entity_type, entity_id, action, before_hash, after_hash, reason_code, created_at, request_id | append-only; immutable role privileges |
| `notification_outbox` | id, event_id, channel, payload_ref, state, retries, next_attempt | unique event+channel+recipient |
| `wa_sessions` | id, phone_hash, wa_user_id_enc, state, selected_program_id, expires_at | TTL, max attempts |
| `integration_requests` | id, provider, correlation_id, request_meta_masked, status, retry_count | no raw identity secrets |
| `knowledge_docs` | id, title, official_url, doc_version, effective_date, approval_status, summary | versioned + approval |
| `knowledge_chunks` | id, knowledge_doc_id, chunk_text, embedding_ref, source_span | RAG public content only |
| `privacy_consents` | id, subject_id, purpose, legal_basis, captured_at, withdrawal_at | consent is not always sole lawful basis |

### 6.2 Constraints utama (implementasi DB + domain policy)

1. `cases.type='DATA_PROPOSAL'` -> `program_version_id IS NULL`; `PROGRAM_APPLICATION` -> non-null.
2. `cases.ticket_no` unik, tidak berubah saat revisi, recheck, atau sanggahan.
3. `program_versions` published **immutable**; perubahan versi baru.
4. `verification_reviews.actor_id` untuk tahap final harus beda dari aktor pemeriksa awal / proposer keputusan kasus sama (maker-checker).
5. Pendaftaran program hanya untuk `program_version_id` yang `OPEN` menurut waktu server dan metode sah (atau sesuai review jika officer-only).
6. `status` berubah hanya lewat transition service, tidak boleh diedit langsung via `PATCH case` generik.
7. Idempotency key untuk create/submit dan webhook. Unique active case per program/household **jangan** menghapus hak klarifikasi; duplikasi mengembalikan tiket aktif dengan proses keberatan.
8. Semua relasi territory bersifat hirarkis dan mencatat jenis dasar alamat; tidak otomatis menolak hanya karena GPS berbeda.
9. Soft-delete PII sesuai kebijakan retensi dan kewajiban penyimpanan, tanpa menghapus bukti audit yang wajib; untuk demo, data boleh di-reset via seed script admin lokal.
10. `audit_events` append-only dengan DB privileges yang membatasi UPDATE/DELETE; teknik chain hash opsional (bukan pengganti pengamanan log).

### Diagram 05 — ERD konseptual

```mermaid
erDiagram
    USERS ||--o{ USER_ROLE_SCOPES : has
    ROLES ||--o{ USER_ROLE_SCOPES : grants
    AGENCIES ||--o{ USER_ROLE_SCOPES : scope
    TERRITORIES ||--o{ USER_ROLE_SCOPES : scope
    AGENCIES ||--o{ PROGRAMS : owns
    PROGRAMS ||--o{ PROGRAM_VERSIONS : versioned
    PROGRAM_VERSIONS ||--o{ PROGRAM_TERRITORIES : targets
    TERRITORIES ||--o{ PROGRAM_TERRITORIES : included
    PROGRAM_VERSIONS ||--o{ PROGRAM_CRITERIA : defines
    USERS ||--o{ CASES : subject
    USERS ||--o{ CASES : submitted_by
    PROGRAM_VERSIONS o|--o{ CASES : applies_to
    HOUSEHOLDS ||--o{ CASES : subject_household
    CASES ||--o| DATA_PROPOSALS : typed_detail
    CASES ||--o| PROGRAM_APPLICATIONS : typed_detail
    CASES ||--o{ CASE_REVISIONS : snapshots
    CASES ||--o{ ATTACHMENTS : has
    CASES ||--o{ VERIFICATION_TASKS : assigned
    CASES ||--o{ VERIFICATION_REVIEWS : reviewed
    CASES ||--o{ AI_ASSESSMENTS : flagged
    CASES ||--o{ APPEALS : appealed
    CASES ||--o{ COMPLAINTS : related
    CASES ||--o{ FEEDBACK : rated
    USERS ||--o{ IDENTITY_CHECKS : checked
    USERS ||--o{ REPRESENTATION_GRANTS : delegated
    CASES ||--o{ AUDIT_EVENTS : logged
    NOTIFICATION_OUTBOX }o--|| USERS : recipient
    KNOWLEDGE_DOCS ||--o{ KNOWLEDGE_CHUNKS : chunks
```

---
## 7. MASTER BUSINESS WORKFLOW — SEMUA JALUR

### Diagram 06 — Master proses end-to-end (dari program sampai feedback)

```mermaid
flowchart TD
  START["Start — Guest / Warga / Petugas / Instansi"] --> ROLE{"Tujuan pengguna?"}
  ROLE -->|Kelola program| PM["Draft Program + Dasar Aturan + Wilayah"]
  PM --> PR["Publication Review oleh pihak berbeda"]
  PR --> PA{"Disetujui?"}
  PA -->|Tidak| PM
  PA -->|Ya| PC["Publish Katalog + Status / Periode"]
  ROLE -->|Usulan data| DP["Usulan / Koreksi Data — tanpa program"]
  ROLE -->|Daftar program| CAT["Buka katalog / detail program"]
  PC --> CAT
  CAT --> MODE{"Pendaftaran dibuka & jalur sah?"}
  MODE -->|Tidak| NO["Info mekanisme resmi, bukan tombol daftar"]
  MODE -->|Mandiri| SELF["Form warga"]
  MODE -->|Petugas| OFF["Form RT / operator sesuai mandat"]
  MODE -->|Keduanya| SELF
  MODE -->|Keduanya| OFF
  DP --> F["Form usulan data"]
  SELF --> V["Validasi backend"]
  OFF --> V
  F --> V
  V --> OK{"Valid?"}
  OK -->|Tidak| FIX["Error spesifik / perbaiki sebelum submit"]
  FIX --> V
  OK -->|Ya| SUB["Buat Case/Tiket + Snapshot Rules"]
  SUB --> CHECK["Rules + Government Simulator + AI Flag"]
  CHECK --> REV["Verifikasi Petugas"]
  REV --> REVQ{"Hasil pemeriksaan?"}
  REVQ -->|Perlu klarifikasi| NEED["Minta perbaikan / bukti + notifikasi"]
  NEED --> RESUB["Warga/pengusul resubmit — tiket sama"]
  RESUB --> V
  REVQ -->|Lengkap| FIN["Review Independen oleh Verifikator"]
  FIN --> OUT{"Tindak lanjut?"}
  OUT -->|Siap diteruskan| FOR["Simulasi penerusan / rujukan resmi"]
  OUT -->|Tidak dapat diproses| DEN["Penutupan beralasan + dasar aturan"]
  OUT -->|Cek ulang| REV
  FOR --> STAT["Update status + audit + notifikasi"]
  DEN --> STAT
  STAT --> SHOW["Warga baca hasil + timeline"]
  SHOW --> CH{"Keberatan?"}
  CH -->|Ya| APP["Tiket Sanggahan + Bukti Baru"]
  APP --> AREV["Review independen: upheld / recheck / corrected"]
  AREV --> STAT
  CH -->|Tidak| FB["Feedback pelayanan opsional"]
  FB --> KPI["Agregasi statistik publik & laporan auditor"]
```

**Penjelasan:** `NO_PUBLIC_REGISTRATION` tidak berarti warga dilarang melakukan *usulan data* terpisah melalui kanal yang sah. `DEN` adalah hasil administratif berdasar aturan dan manusia berwenang; AI **tidak** dapat langsung mengeluarkan `DEN`.

---

## 8. WORKFLOW PEMBUATAN PROGRAM (PEMERINTAH/INSTANSI)

### 8.1 Field konfigurasi program yang WAJIB

| Field | Makna | Contoh demo | Validasi |
|---|---|---|---|
| Pemilik program | instansi penyelenggara | Dinsos Kab. Semarang **fiktif** | harus sesuai mandate actor |
| Nama dan kode program | identitas unik | Bantuan Pangan Kab A / `BP-KABA-2026` | unique per agency/version |
| Dokumen/URL aturan | dasar kebijakan yang dapat dirujuk | dokumen **simulasi** | wajib terisi sebelum publish |
| Mode pendaftaran | `SELF`, `OFFICER_ONLY`, `BOTH`, `NO_PUBLIC_REGISTRATION` | `BOTH` | nilai enum sah dan dasar aturan |
| Cakupan | prov/kab/kec/desa atau nasional | Kab Semarang (demo) | subset kewenangan instansi |
| Jenis alamat | KTP/domisili sesuai aturan program | domisili (demo) | tidak berdasarkan GPS saja |
| Status program | draft, approval, live, closed | DRAFT | mesin status |
| Periode program | pengumuman & pendaftaran | 1–15 Des 2026 (demo) | start <= end; timezone konsisten |
| Kriteria | requirement data/kuota/peruntukan | contoh syarat dokumen | memiliki tipe, operator, dasar |
| Dokumen wajib | jenis, batas ukuran, retensi | dokumen pendukung fiktif | file validation dan private storage |
| Kuota | optional, bila sah | 100 (demo) | cap tidak menyiratkan auto-seleksi |
| Alur verifikasi | petugas wilayah → instansi | `VILLAGE_OPERATOR` → `AGENCY_VERIFIER` | mengikuti kebijakan program |
| Masa sanggah | rentang jika memang dibuka | 7 hari (hanya demo; configurable) | jangan klaim standar resmi seragam |
| SLA | target layanan per program | 3 hari kerja (demo) | konfigurasi, tidak klaim resmi |
| Persetujuan publikasi | mandatory reviewer | pemegang `program.publish` | maker != publisher |

### 8.2 Aturan program lifecycle

- `DRAFT`: boleh diubah oleh Program Manager berwenang.
- `PENDING_PUBLICATION_REVIEW`: pengelola submit; draft terkunci hingga keputusan reviewer.
- `RETURNED_FOR_REVISION`: dikembalikan dengan alasan; diajukan ulang.
- `APPROVED`: disetujui; publikasi sesuai waktu/tindakan pengesahan.
- `PUBLISHED`: tersedia untuk dibaca publik; belum tentu membuka pendaftaran.
- `OPEN`: `PUBLISHED` dan periode pendaftaran aktif serta mode != NO_PUBLIC_REGISTRATION.
- `CLOSED`: pengajuan baru tutup; pengajuan lama masih diproses.
- `ARCHIVED`: historis publik tetap bisa dirujuk; rule snapshot tidak hilang.

### Diagram 07 — Workflow Program Management

```mermaid
flowchart TD
  A["Program Manager login"] --> B["Create Draft + policy source"]
  B --> C["Atur cakupan, mode, tanggal, persyaratan, kuota"]
  C --> D["Preview dari perspektif warga berbagai wilayah"]
  D --> E["Submit for Publication Review"]
  E --> F["Publisher berbeda memeriksa mandat dan legal basis"]
  F --> G{"Sesuai aturan?"}
  G -->|Tidak| H["Return with reason"]
  H --> B
  G -->|Ya| I["Approve + version immutable"]
  I --> J["Publish katalog"]
  J --> K{"Buka pendaftaran umum?"}
  K -->|Tidak| L["Info saja + kanal usulan resmi"]
  K -->|Ya| M["Aktifkan CTA sesuai waktu + wilayah + mode"]
  M --> N["Pendaftaran masuk dan diproses"]
  N --> O["Periode tutup: no new application"]
  O --> P["Arsip + statistik publik aman"]
```

**Frontend wajib menguji:** program nasional vs kabupaten vs desa; `OFFICER_ONLY` bukan menampilkan tombol daftar untuk warga; `NO_PUBLIC_REGISTRATION` hanya info, bukan 403 diam-diam; periode belum buka/tutup; program versi revisi; pembuat draft gagal publish sendiri.

---

## 9. WORKFLOW USULAN DATA SOSIAL EKONOMI (TANPA PROGRAM)

### 9.1 Jalur pemohon

- **Mandiri melalui web:** warga mendaftar/login, membaca perlindungan data, membuat usulan/koreksi, mendapat tiket dan memantau.
- **Diwakili RT/Operator:** akun terotorisasi membuat usulan atas nama warga yang benar dalam scope wilayah serta mencatat dasar perwakilan; warga dapat melihat/mengoreksi kasus setelah keterkaitan dan akses dibuktikan.
- **WhatsApp statis:** hanya membuat draf awal tanpa identitas sensitif; selesaikan di web aman.

### 9.2 Form usulan data — minimum yang layak

`jenis_usulan: BELUM_TERDATA | PERBAIKAN | PERUBAHAN_KONDISI`, data kontak, identitas akun dengan pengumpulan minimum, household ID internal, wilayah administratif relevan, informasi anggota keluarga yang dibutuhkan, rentang/perubahan kondisi ekonomi yang relevan, alasan, dokumen pendukung yang benar-benar perlu, pernyataan kebenaran data dan privacy notice. Data biometrik **tidak perlu** dikumpulkan secara default.

### Diagram 08 — Data Proposal, sukses, revisi, tidak dapat diproses

```mermaid
flowchart TD
  A{"Siapa pengusul?"} -->|Warga| B["Form usulan mandiri"]
  A -->|RT/operator berwenang| C["Form usulkan warga + bukti mandat"]
  B --> D["Server: auth, owner, validasi"]
  C --> D
  D --> E{"Input memenuhi syarat teknis?"}
  E -->|Tidak| F["Simpan draft; error per field"]
  F --> D
  E -->|Ya| G["SUBMITTED: buat tiket"]
  G --> H["Gov Simulator fetch + rules + anomaly"]
  H --> I["Operator desa cek bukti/konsistensi"]
  I --> J{"Perlu klarifikasi?"}
  J -->|Ya| K["REVISION_REQUIRED + reason list"]
  K --> L["Warga/pengusul melengkapi"]
  L --> M["RESUBMITTED: nomor tiket sama"]
  M --> D
  J -->|Tidak| N["Reviewer instansi berbeda"]
  N --> O{"Keputusan administratif?"}
  O -->|Diteruskan| P["FORWARDED_SIMULATED — bukan DTSEN asli"]
  O -->|Tidak dapat diproses| Q["CLOSED_UNPROCESSABLE + alasan"]
  O -->|Review ulang| I
  P --> R["Timeline, audit, notifikasi"]
  Q --> R
  R --> S{"Sanggahan tersedia?"}
  S -->|Ya| T["Ajukan appeal dengan bukti"]
  T --> U["Independent appeal reviewer"]
  U --> R
  S -->|Tidak| V["Feedback / pengaduan / kanal resmi"]
```

**Batas keputusan:** masyarakat boleh membuat *usulan ke BantuTepat*, tetapi ini tidak sama dengan mengajukan langsung ke aplikasi resmi Kemensos; status penerusan nyata memerlukan integrasi resmi. Jangan mengubah kolom `dtsen_status` menjadi "accepted" dari AI/aksi aplikasi demo.

---

## 10. WORKFLOW PENDAFTARAN PROGRAM SPESIFIK

### 10.1 Penentuan cara daftar per program

| `registration_mode` | Warga mandiri | RT/Operator | UI warga |
|---|---|---|---|
| `SELF` | Bisa sesuai rules program | tidak, kecuali hak/perwakilan resmi khusus | “Daftar Sekarang” |
| `OFFICER_ONLY` | Tidak untuk jalur web langsung | Bisa sesuai penugasan | “Lihat prosedur melalui petugas” |
| `BOTH` | Bisa | Bisa | opsi jalur: ajukan sendiri / dibantu |
| `NO_PUBLIC_REGISTRATION` | Tidak | tidak via pendaftaran terbuka; bila ada proses internal resmi perlu workflow tersendiri | “Informasi Program & Mekanisme Resmi” |

### 10.2 Precheck **bukan keputusan kelayakan akhir**

- Program diterbitkan dan status open; tanggal sesuai waktu server; mode sesuai role.
- Pemohon/rumah tangga berada dalam wilayah sasaran menurut **basis alamat program** (data kependudukan/domisili sesuai aturan simulasi). Pastikan *teritori* bukan sekadar lokasi IP/GPS.
- Persyaratan format, field minimum, lampiran, satu pengajuan aktif bila sesuai aturan.
- Jika gagal precheck, tampilkan `blocked_reason_code` dan langkah koreksi (ubah informasi yang memang salah / pilih kanal sesuai program). **Jangan labeli "bansos ditolak"** karena kasus belum diajukan.
- Setelah submit, rules dan AI menandai masalah; petugas melakukan verifikasi, verifikator memutus tindak lanjut yang berwenang.
- Kuota habis: bukan otomatis kecurangan. Gunakan status `NOT_SELECTED` **hanya bila aturan resmi program memungkinkan seleksi, keputusan diberikan pihak berwenang, dan alasannya terdokumentasi**. Dalam demo jelas berlabel simulasi.

### Diagram 09 — Pendaftaran, batas wilayah & penolakan dua tahap

```mermaid
flowchart TD
  A["Warga memilih program di katalog"] --> B["Detail: penyelenggara, area, mode, periode, aturan"]
  B --> C{"Registration mode?"}
  C -->|NO_PUBLIC_REGISTRATION| N["Info + kanal resmi tanpa form"]
  C -->|OFFICER_ONLY| RT["RT/operator terotorisasi membuka form"]
  C -->|SELF atau BOTH| W["Warga membuka form"]
  W --> D["Precheck server: scope, time, duplicate, basic fields"]
  RT --> D
  D --> E{"Boleh submit?"}
  E -->|Tidak| F["Block sebelum submit: alasan + koreksi/rujukan"]
  E -->|Ya| G["Submit + case_id + rule snapshot"]
  G --> H["Rule Engine + Simulator + AI Flags"]
  H --> I["Petugas verifikasi bukti"]
  I --> J{"Perlu perbaikan?"}
  J -->|Ya| K["Minta revisi beralasan"]
  K --> L["Resubmit dengan tiket sama"]
  L --> D
  J -->|Tidak| M["Verifikator instansi review"]
  M --> O{"Hasil internal sesuai mandat"}
  O -->|Siap diteruskan| P["RECOMMENDED / FORWARDED_SIMULATED"]
  O -->|Tak dapat diproses| Q["CLOSED_UNPROCESSABLE + alasan"]
  O -->|Tidak terpilih menurut aturan| R["NOT_SELECTED + dasar & appeal path"]
  P --> S["Hasil + audit + notifikasi"]
  Q --> S
  R --> S
  S --> T{"Warga sanggah?"}
  T -->|Ya| U["Appeal independent reviewer"]
  U --> S
  T -->|Tidak| V["Feedback + case closed"]
```

---

## 11. USER FLOW DETIL PER PERSONA

### 11.1 Warga mandiri — contoh lengkap kasus **ditolak**

1. Guest melihat program bantuan Kab A; login warga dengan domisili Kab A.
2. Klik “Daftar Sekarang” (tersedia karena program `SELF/BOTH`, `OPEN`, dan wilayah sesuai).
3. Wizard 3–4 langkah: data pokok → kondisi yang relevan → berkas → review/konfirmasi.
4. Submit -> `BT-P-2026-0007`; tampilkan tiket dan `SUBMITTED`.
5. Server rule memeriksa; AI flag "perubahan pendapatan butuh konfirmasi"; petugas **tidak otomatis menolak**.
6. Petugas meminta dokumen tambahan dengan daftar alasan -> `REVISION_REQUIRED`.
7. Warga menambah bukti -> `RESUBMITTED` tiket tetap.
8. Petugas rekomendasikan kasus lengkap; verifikator melihat dokumen dan alasan serta mengambil keputusan berotorisasi.
9. Contoh demo: verifikator memutus `CLOSED_UNPROCESSABLE` karena kriteria administratif tertentu tidak dipenuhi **setelah pemeriksaan**; alasan, dasar program, tanggal, dan jalur keberatan tampak.
10. Warga klik “Ajukan Sanggahan”, masukkan alasan dan bukti yang belum diperiksa; muncul tiket appeal `BT-A-2026-001` terhubung kasus induk.
11. Reviewer appeal independen dapat `UPHELD`, `RECHECK_REQUIRED`, atau `DECISION_CORRECTED`; jika recheck, kasus induk kembali ke antrean dengan event jelas.
12. Warga melihat hasil appeal dan mengisi feedback kepuasan; auditor melihat metrik agregat dan event yang diizinkan.

**Contoh kata-kata UI hasil negatif (bukan auto-kecurangan):**

> "Pengajuan Anda belum dapat diproses lebih lanjut. Alasan: [ketentuan administratif spesifik]. Dasar: [nama aturan program + versi/tanggal]. Anda dapat [memperbaiki/mengajukan sanggahan/menghubungi petugas] sesuai ketentuan program. Hasil ini merupakan simulasi penanganan BantuTepat dan bukan keputusan bantuan resmi."

### 11.2 RT/RW mengusulkan warga

1. RT login, role + wilayah ditentukan administrator; *scope* tidak dipilih bebas oleh RT.
2. Klik `Usulkan Warga`, pilih program yang membuka `OFFICER_ONLY/BOTH` atau `Usulan Data`.
3. Isi identitas subjek dengan data minimum dan dasar mandat/perwakilan, lalu bukti.
4. Jika warga di luar wilayah petugas -> 403/422 beralasan tanpa mengungkap data lengkap warga.
5. Buat tiket `submitted_by=RT`, `subject=warga`, `source_channel=OFFICER`.
6. Warga diberi akses setelah keterkaitan diverifikasi dan dapat melihat status/menyanggah; RT hanya memantau kasus yang menjadi tugasnya.
7. Operator desa memeriksa, verifikator berbeda meninjau; RT tidak boleh auto-approve.

### 11.3 Operator desa/kelurahan

1. Buka antrean kasus **assigned/scope** dengan filter dan indikator AI.
2. Buka detail; lihat data seminimal mungkin, checklist dokumen, status rules/AI disertai provenance.
3. Lakukan verifikasi manual; boleh minta revisi, minta klarifikasi, atau membuat rekomendasi tindak lanjut.
4. Setiap tindakan wajib `reason_code`, catatan dan bukti rujukan; timeline tersimpan.
5. Tidak boleh memberikan final review pada kasus yang diperiksanya.

### 11.4 Verifikator instansi/Dinsos

1. Lihat `PENDING_APPROVAL`; ambil kasus dalam scope instansi/wilayah, bukan semua daerah.
2. Sistem validasi `reviewer != initial_verifier` dan konflik kepentingan lain.
3. Lihat snapshot rules, hasil manual dan AI flag (bukan menjadikan skor satu-satunya alasan).
4. Tindak lanjut: minta ulang, teruskan simulasi, atau tutup beralasan sesuai kebijakan; tidak menerbitkan klaim penerima resmi.
5. Sanggahan ditinjau reviewer lain yang independen dari keputusan awal.

### 11.5 Pengelola program

1. Buat program dengan dasar aturan, versi, cakupan dan mode yang ditugaskan.
2. Preview kondisi warga A/B, tanggal/territory edge cases.
3. Ajukan review; hanya publisher berbeda yang dapat menyetujui publikasi.
4. Setelah diterbitkan, semua kasus baru menyimpan rule snapshot versi yang berlaku.
5. Edit program yang telah publish harus menjadi **versi baru**, bukan menimpa histori.

### 11.6 Pengawas/Auditor

1. Masuk dashboard pengawasan terotorisasi.
2. Monitor waktu antrean, alasan penolakan, tingkat kasus revisi, indikator keterlambatan, distribusi per wilayah yang aman.
3. Drill down kasus **hanya sesuai mandat**, lihat event actor/timestamp/reason, tidak bisa mengedit event.
4. Kelola rujukan pengaduan, eskalasi potensi penyimpangan sesuai kewenangan.

### 11.7 Super Admin teknis

1. Undang petugas dan hubungkan role/scope dengan masa berlaku; tidak ada self-register admin.
2. Nonaktifkan pengguna, cabut mandat, rotasi rahasia integrasi, lihat health service.
3. Tidak boleh menggunakan panel admin untuk mengubah status bantuan atau melihat dokumen warga tanpa alasan akses yang sah.
4. Audit setiap privilege change dan akses khusus.

### Diagram 10 — Swimlane user flow (empat lapisan proses)

```mermaid
flowchart TB
  subgraph CIT["Warga / RT"]
    A["Buka katalog/usulan data"] --> B["Isi form + upload"]
    D["Terima tiket dan cek status"]
    REV["Perbaiki data jika diminta"]
    END["Hasil, sanggahan, feedback"]
  end
  subgraph SYS["Backend + AI"]
    C["Auth + rules + scope + save case"]
    AI["Anomaly flags + Simulator"]
    AUD["Update status, audit, notifikasi"]
  end
  subgraph OFF["Operator Wilayah"]
    O["Verifikasi manual + alasan"]
    Q{"Perlu perbaikan?"}
  end
  subgraph INS["Verifikator Instansi"]
    V["Review independen"]
    RES["Hasil: forward / unprocessable / recheck"]
  end
  B --> C --> D
  C --> AI --> O --> Q
  Q -->|Ya| REV --> C
  Q -->|Tidak| V --> RES --> AUD --> END
```

---

## 12. STATUS, TRANSISI, INVARIANT (IMPLEMENT STATE MACHINES)

**Catatan:** hindari `status` bertumpuk yang memiliki makna ambigu. Gunakan `case.type`, `case.state`, `review_stage`, `external_integration_state`, dan `appeal.state` terpisah.

### 12.1 Status usulan data

`DRAFT → SUBMITTED → UNDER_REVIEW → PENDING_APPROVAL → FORWARDED_SIMULATED | CLOSED_UNPROCESSABLE → CLOSED`

Cabang `UNDER_REVIEW → REVISION_REQUIRED → RESUBMITTED → UNDER_REVIEW`, `PENDING_APPROVAL → UNDER_REVIEW` jika recheck. `APPEAL` adalah objek tersendiri sesudah keputusan yang dapat disanggah.

### 12.2 Status pengajuan program

`DRAFT → SUBMITTED → PRECHECKED → UNDER_REVIEW → PENDING_APPROVAL → RECOMMENDED_FOR_FORWARD | FORWARDED_SIMULATED | NOT_SELECTED | CLOSED_UNPROCESSABLE → CLOSED`.

`NOT_SELECTED` hanya bila rule program dan pejabat berwenang mendukungnya; jangan jadikan istilah universal untuk data proposal.

### 12.3 Status sanggahan

`SUBMITTED → UNDER_APPEAL_REVIEW → UPHELD | RECHECK_REQUIRED | DECISION_CORRECTED → CLOSED`.

### 12.4 Status pengaduan/feedback

- Complaint: `RECEIVED → TRIAGED → ASSIGNED → IN_HANDLING → RESOLVED | REFERRED → CLOSED`.
- Feedback: `SUBMITTED` lalu immutable / dapat dikoreksi sesuai policy, tidak pernah mengubah eligibility.

### 12.5 Invariant transition

- Actor harus berhak di **status saat ini**, bukan hanya punya role.
- Tindakan `REQUEST_REVISION` perlu daftar field/bukti + alasan dan tenggat konfigurasi.
- Hanya pemohon/representatif yang berwenang bisa `RESUBMIT`.
- Tindakan `CLOSE_UNPROCESSABLE/NOT_SELECTED` perlu alasan terstruktur + referensi aturan + reviewer; AI flag bukan satu-satunya alasan.
- `FORWARDED_SIMULATED` hanya jika simulator memberi ACK; jika gagal, status `FORWARDING_PENDING`/`FORWARDING_FAILED` dan bisa retry; **jangan klaim diteruskan berhasil jika HTTP gagal**.
- `CLOSED` tidak menghapus timeline; pembukaan ulang melalui workflow formal dan audit.
- Sanggahan punya reviewer berbeda dan boleh memicu *recheck* atas kasus induk melalui event; tidak menghapus keputusan sebelumnya.

### Diagram 11 — State machine cases dan appeal

```mermaid
stateDiagram-v2
    [*] --> DRAFT
    DRAFT --> SUBMITTED: submit valid
    SUBMITTED --> UNDER_REVIEW: assign
    UNDER_REVIEW --> REVISION_REQUIRED: request fix
    REVISION_REQUIRED --> RESUBMITTED: subject responds
    RESUBMITTED --> UNDER_REVIEW: revalidate
    UNDER_REVIEW --> PENDING_APPROVAL: verification complete
    PENDING_APPROVAL --> UNDER_REVIEW: request recheck
    PENDING_APPROVAL --> FORWARDING_PENDING: forward approval
    FORWARDING_PENDING --> FORWARDED_SIMULATED: simulator ack
    FORWARDING_PENDING --> FORWARDING_FAILED: transient failure
    FORWARDING_FAILED --> FORWARDING_PENDING: authorized retry
    PENDING_APPROVAL --> CLOSED_UNPROCESSABLE: reasoned close
    PENDING_APPROVAL --> NOT_SELECTED: eligible program rule only
    FORWARDED_SIMULATED --> CLOSED: close handling
    CLOSED_UNPROCESSABLE --> CLOSED: notify result
    NOT_SELECTED --> CLOSED: notify result
    CLOSED --> [*]
```

```mermaid
stateDiagram-v2
    [*] --> APPEAL_SUBMITTED
    APPEAL_SUBMITTED --> APPEAL_REVIEW
    APPEAL_REVIEW --> UPHELD: decision unchanged
    APPEAL_REVIEW --> RECHECK_REQUIRED: more investigation
    APPEAL_REVIEW --> DECISION_CORRECTED: authorized correction
    UPHELD --> APPEAL_CLOSED
    RECHECK_REQUIRED --> APPEAL_CLOSED: linked task created
    DECISION_CORRECTED --> APPEAL_CLOSED: audited case update
    APPEAL_CLOSED --> [*]
```

---

## 13. SANGGAHAN, PENGADUAN, FEEDBACK, TRANSPARANSI DAN AKUNTABILITAS

### 13.1 Bedakan saluran hasil kasus

| Proses | Trigger | Siapa memutus/menangani | Hasil dan audit |
|---|---|---|---|
| **Perbaikan** | formulir kurang lengkap, perlu bukti | pemohon menambah, petugas cek ulang | resubmit tiket sama |
| **Sanggahan** | warga keberatan atas hasil administratif | reviewer independen sesuai kewenangan | uphold/recheck/correct + reason |
| **Pengaduan** | layanan lambat, pungutan, penyalahgunaan, akses bermasalah | petugas pengaduan/pengawas ditugaskan | received → handled/referred; kerahasiaan |
| **Feedback** | selesai layanan | tidak untuk memutus kasus | rating kepuasan dan tindak lanjut kualitas |

### 13.2 Desain pengalaman sanggahan

- Link hanya muncul jika program/jalur menyediakan mekanisme sesuai aturan, atau sediakan penjelasan kanal keberatan resmi bila BantuTepat tidak berwenang.
- Form mencakup `parent_case_id`, alasan kategori, uraian, evidence attachment; jangan mewajibkan seluruh formulir awal diulang.
- Review oleh user berbeda dari reviewer keputusan awal; jika tidak ada reviewer memenuhi separation-of-duties, **eskalasi**, jangan bypass.
- Hasil harus menjelaskan **apa yang diperiksa**, **apa yang berubah**, **mengapa**, serta tanggal/waktu.
- Kasus dan sanggahan tetap memiliki audit timeline serta keterkaitan historis.

### Diagram 12 — Penolakan, sanggahan, pengaduan dan feedback

```mermaid
flowchart TD
  A["Case selesai + hasil beralasan"] --> B["Warga melihat hasil privat"]
  B --> C{"Reaksi warga?"}
  C -->|Setuju| F["Feedback kepuasan opsional"]
  C -->|Keberatan hasil| D{"Jalur sanggahan tersedia?"}
  D -->|Ya| E["Form sanggah + bukti + tiket appeal"]
  D -->|Tidak| REF["Rujukan mekanisme resmi & helpdesk"]
  E --> H["Reviewer independen"]
  H --> I{"Keputusan appeal"}
  I -->|Upheld| J["Alasan keputusan dipertahankan"]
  I -->|Recheck| K["Tugas verifikasi ulang kasus induk"]
  I -->|Corrected| L["Perbarui hasil via aksi berotorisasi"]
  J --> M["Notifikasi + audit"]
  K --> M
  L --> M
  C -->|Masalah pelayanan| N["Pengaduan pelayanan / SP4N info"]
  N --> O["Triage, assign, resolve atau refer"]
  O --> M
  M --> F
  REF --> F
  F --> P["Agregasi KPI, tanpa PII publik"]
```

### 13.3 Transparency Center publik (P0)

Metrik aman: jumlah program per wilayah; total kasus per jenis, status, bulan; median durasi penanganan; jumlah kasus revisi/sanggahan yang ditangani; riwayat perubahan aturan program; metadata sumber aturan. **Jangan publikasikan** nama, NIK, alamat rinci, nomor telepon, foto wajah, dokumen, hasil AI individual, identitas pengadu, atau sel agregasi kecil yang memungkinkan identifikasi ulang. Gunakan ambang `k >= 10` sebagai **konfigurasi desain demo** (bukan angka wajib hukum) dan gabungkan/suppress kategori kecil.

### 13.4 Accountability Center internal (P0)

Metrik: backlog per assignment, aging, over-SLA terhadap SLA yang memang didefinisikan, frekuensi revisi, keputusan tanpa reason (harus 0), perubahan role, hasil appeal, audit read access, dan waktu AI downtime. Audit akses peristiwa sensitif, tetapi jangan gunakan AI anomaly untuk menuduh petugas/masyarakat korup tanpa investigasi. Kinerja statistik tidak menjadi basis otomatis menghukum petugas.

### Diagram 13 — Data visibility transparansi vs audit

```mermaid
flowchart TB
  DB[("Cases + Reviews + Audit + Programs")] --> ACL["Authorized Query / Row Filter"]
  ACL --> PUB["Aggregation + Small-Cell Suppression + PII Scrubber"]
  PUB --> OUT["PUBLIC: program, aggregate metrics, policy updates"]
  ACL --> OWN["SUBJECT: own case timeline, reasons, appeal"]
  ACL --> OFF["OFFICER: only assigned case data + AI flags"]
  ACL --> AUD["AUDITOR: permitted audit history + performance"]
  OUT -.-> NO["Never: identity lists, raw attachments, face data"]
```

---

## 14. WHATSAPP STATIC BOT — TIDAK PAKAI LLM

**Prinsip:** satu nomor layanan, **menu dan state machine deterministik**, data status/program dari API dinamis. Tidak ada jawaban open-domain generatif. Login dan dokumen sensitif diselesaikan melalui website. Pengiriman WA harus menggunakan Meta WhatsApp Business Platform yang sah (sandbox untuk development).

### 14.1 Menu dan respons

| Input | Menu | Action backend | Respons aman |
|---|---|---|---|
| `1` | Informasi Program | `GET /api/public/programs?filter=...` | nomor item, nama, area, periode, tautan web |
| `2` | Mulai pengajuan | pilih `DATA_PROPOSAL` / program | kirim tautan sekali pakai berumur pendek menuju web aman; jangan kirim dokumen ke WA |
| `3` | Cek status | masukkan tiket, lalu **verifikasi akun** via web | hanya status umum; detail privat setelah auth |
| `4` | Sanggahan | cari kasus terautentikasi | link halaman appeal dan panduan |
| `5` | Hubungi petugas | FAQ statis / handoff | kontak terverifikasi, jam layanan bila diketahui |
| `0` | Kembali | reset state | menu utama |

### 14.2 State machine WA

`INIT → MAIN_MENU → INFO_SELECT | APPLICATION_SELECT | STATUS_TICKET | APPEAL_GUIDE | HELP → (GENERATE_SECURE_LINK if needed) → MAIN_MENU`; sesi punya TTL, rate limit, reentrancy, idempotency untuk webhook duplicate.

### Diagram 14 — WhatsApp menu & web handoff

```mermaid
flowchart TD
  A["Pesan ke WA BantuTepat"] --> B["Verify webhook signature + dedupe"]
  B --> C["Load session/menu state"]
  C --> D{"Pilihan 1-5?"}
  D -->|1 Info| E["Get published programs API"]
  E --> F["Reply template pilihan program"]
  D -->|2 Pengajuan| G["Pilih jenis dan buat draft minimal"]
  G --> H["Secure one-time link ke form web"]
  D -->|3 Status| I["Minta case ticket"]
  I --> J["Verifikasi akun via web"]
  J --> K["Read scoped status, no PII in WA"]
  D -->|4 Sanggahan| L["Link sanggahan setelah auth"]
  D -->|5 Bantuan| M["Helpdesk/FAQ statis"]
  D -->|Lainnya| N["Error format + menu ulang"]
  F --> O["Conversation state persisted with TTL"]
  H --> O
  K --> O
  L --> O
  M --> O
  N --> O
```

### 14.3 WhatsApp notification flow

- Event `CASE_STATUS_CHANGED` -> outbox -> recipient opted-in/allowed -> provider adapter -> approved template jika dibutuhkan -> delivery status callback; jika gagal, simpan error+retry serta notifikasi tetap terlihat di web.
- Implementasi melalui SDK/API Meta yang berlaku, jangan meng-hardcode nomor aktif atau token ke repository. Gunakan provider mock `WA_PROVIDER=mock` untuk demo bila onboarding nomor belum selesai.
- Validasi tanda tangan webhook provider berdasarkan spesifikasi saat integrasi, replay/duplicate check, origin policy yang benar, throttling.
- **Nomor tiket saja bukan autentikasi** dan balasan nomor WhatsApp tidak otomatis membuktikan identitas pemohon.

---
## 15. AI ARCHITECTURE — TERPISAH DARI CORE BUSINESS RULES

**AI hanya asisten keputusan manusia.** Untuk MVP gunakan **1 Python FastAPI service CPU** dengan router `/anomaly`, `/face` (flag opsional), dan `/rag`. Akses AI diproksi melalui backend aplikasi; tidak ada frontend langsung memanggil AI service privat, dan tidak ada frontend langsung memanggil LLM provider dengan API key. Jika AI down, kasus **tetap dapat diajukan** dan petugas dapat memeriksa manual.

### 15.1 Matriks AI dan non-AI

| Modul | Kategori | Kapan berjalan | Masukan | Keluaran | Priority | Fallback |
|---|---|---|---|---|---|---|
| Eligibility/time/territory rules | **Deterministik, bukan AI** | browse form, submit, review | policy version, role, region, time | valid/error/reason | P0 | harus selalu aktif |
| Duplicate check | **Deterministik** | submit | household/case keys internal | kemungkinan duplikat dan referensi tiket aman | P0 | manual review |
| Anomaly Detection | ML tabular | setelah submit/resubmit | fitur konsistensi yang minim & berizin | score, flags, version | P1 utama | `AI_UNAVAILABLE`, review manual |
| LLM + RAG | LLM eksternal via API | widget bantuan **web** | pertanyaan umum + retrieved public docs | jawaban + sitasi + batasan | P1 | FAQ statis |
| Face verification 1:1 | model vision CPU | verifikasi risiko tertentu, opt-in demo | selfie/live capture + referensi berizin + quality | match/uncertain + manual path | P2 | verifikasi manual |
| Passive liveness/PAD | anti-spoofing | bagian face 1:1 | capture liveness yang dibenarkan | PAD result | P2 | manual, bukan auto reject bansos |
| OCR | vision/text | unggah dokumen relevan | image scan aman | extracted fields | P2/tunda | isi manual |

### 15.2 AI anomaly detection — rancangan teknis dan evaluasi

**Input minimal:** fitur numerik/boolean yang relevan dan tidak berlebihan; mis. ukuran rumah tangga, jumlah tanggungan, rentang pendapatan self-report, delta pendapatan terhadap riwayat sintetis, status pekerjaan, selisih/konsistensi antarformulir, jumlah kasus aktif keluarga (rule), dan indikator perubahan data. Hindari memasukkan atribut sensitif yang tidak relevan atau proxy diskriminatif. **Tidak boleh** menggunakan latar etnis, agama, wajah, atau penampilan sebagai prediktor kelayakan.

**Model awal:** Isolation Forest / unsupervised baseline di scikit-learn. Anomali bukan bukti salah sasaran. Bandingkan dengan baseline rules, perhitungan precision/recall/F1 pada label anomali sintetis jika ada (label merupakan skenario yang dibuat, bukan *ground truth* kelayakan), FPR, review workload per 100 kasus, latency dan stabilitas multi-seed. Jangan menulis “akurasi 99%” tanpa eksperimen.

**Format data inference hasil:**

```json
{
  "case_id": "BT-P-2026-0007",
  "assessment_id": "uuid",
  "module": "tabular_anomaly",
  "model_version": "iforest-0.1.0",
  "feature_schema_version": "1.0",
  "status": "COMPLETED",
  "score": -0.083,
  "flagged_for_review": true,
  "flags": [
    {"code": "INCOME_CHANGE_RECHECK", "source": "MODEL", "message": "Perubahan kondisi ekonomi perlu dikonfirmasi"},
    {"code": "POSSIBLE_ACTIVE_DUPLICATE", "source": "RULE", "message": "Ada usulan serupa yang masih aktif"}
  ],
  "eligibility_decision": null,
  "auto_rejection": false
}
```

**Penting:** penjelasan model harus sesuai metode. Jika menggunakan Isolation Forest, daftar flag `INCOME_CHANGE_RECHECK` boleh berasal dari rule/data diff; jangan mengklaim model memberi penjelasan kausal/SHAP bila tidak dihitung. `source` wajib `RULE` atau `MODEL` agar petugas tidak menganggap semuanya “keputusan AI”.

### Diagram 15 — AI anomaly pipeline, human decision dan fallback

```mermaid
flowchart TD
  CASE["Case SUBMITTED/RESUBMITTED"] --> VAL["Deterministic validation + duplicates"]
  VAL --> GOV["Government Simulator (optional synthetic reference)"]
  GOV --> FEAT["Feature extraction + schema/version"]
  VAL --> FEAT
  FEAT --> AI{"AI service available?"}
  AI -->|Ya| MODEL["Isolation Forest inference"]
  MODEL --> FLAGS["Score + model flags + separate rules"]
  AI -->|Tidak| FALL["AI_UNAVAILABLE + manual-review queue"]
  FLAGS --> SAVE["Save assessment, version, provenance"]
  FALL --> SAVE
  SAVE --> HUMAN["Operator verification + evidence"]
  HUMAN --> REVIEW["Independent reviewer + reasoned outcome"]
  REVIEW --> AUDIT["Audit + case tracking"]
```

### 15.3 LLM + RAG web-only

**Tujuan:** menolong pengunjung/warga memahami syarat, wilayah, waktu, jalur pengajuan, prosedur sanggahan, dan arti status secara umum. **Tidak** boleh membuat keputusan, mengaku sebagai pemerintah, mengungkap status privat tanpa auth, menulis perubahan pada case, atau menjawab dengan dokumen yang belum disetujui.

**Pipeline:** admin knowledge mengimpor dokumen/URL resmi + tanggal berlaku → internal reviewer menyetujui dokumen → split/chunk → index/search (Postgres full-text cukup untuk MVP; embeddings/pgvector opsional) → user query sanitization → retrieve top-k → batas context, sitasi setiap klaim normatif → call external LLM via server API → response with citations/confidence disclaimer. Kalau dokumen tidak memadai, jawab “Informasi belum tersedia dalam dokumen terverifikasi” + link resmi. Gunakan model API yang tersedia sesuai anggaran, env config `LLM_PROVIDER`, `LLM_MODEL`, `LLM_API_KEY`.

**Guardrails:** tidak mengirim NIK, alamat, catatan pengajuan pribadi, token/secret, ataupun prompt internal ke LLM provider; input/output rate limit, moderation/profanity, max tokens, timeout, spend cap, logging tanpa isi chat sensitif, prompt-injection resistance dengan memperlakukan dokumen retrieval sebagai DATA bukan instruksi. Tidak menyimpan chat tanpa kebutuhan/notice; session TTL.

### Diagram 16 — Web chatbot LLM + RAG (tidak melewati WhatsApp)

```mermaid
sequenceDiagram
    autonumber
    participant U as Guest/Warga pada Website
    participant WEB as Chat Widget
    participant API as BantuTepat /api/web-chat
    participant RET as Approved Knowledge Retriever
    participant LLM as External LLM Provider
    U->>WEB: Tanya syarat / program / prosedur
    WEB->>API: POST {message, session_id}
    API->>API: Rate limit + sanitize + no private PII
    API->>RET: Search only approved public docs
    alt found official/synthetic approved documents
       RET-->>API: passages + source URLs + versions
       API->>LLM: constrained context + cited question
       LLM-->>API: draft answer
       API->>API: validate sources & filter sensitive output
       API-->>WEB: answer + links + uncertainty notice
    else no source / provider down
       API-->>WEB: FAQ fallback / official link
    end
    WEB-->>U: Jawaban dengan sumber
```

### 15.4 Face verification: aman dan tetap bisa di-deploy

**Kebutuhan nyata:** Face **verification 1:1** memeriksa kecocokan dua representasi dari identitas yang sudah mempunyai dasar referensi tepercaya. Untuk prototipe, gunakan referensi **relawan yang secara eksplisit menyetujui demo atau synthetic portrait yang sesuai untuk uji teknis**. Jangan mengklaim foto KTP unggahan sendiri adalah verifikasi Dukcapil/e-KYC sah. Tidak ada pencarian wajah massal 1:N, face attribute inference, atau penyimpanan galeri wajah penduduk.

**Opsi implementasi CPU:** Python 3.11, FastAPI, OpenCV/Pillow untuk preprocessing/quality (jika perlu), ONNX Runtime CPU untuk lightweight face embedder, comparator cosine similarity yang dikalibrasi menggunakan data uji demo, dan PAD/liveness ringan **hanya bila diuji**; quality and PAD failure = `MANUAL_REVIEW` (bukan penolakan bansos). Model harus diverifikasi **lisensi deployment/commercial-use** sebelum digunakan; lisensi kode dan bobot model dapat berbeda. Hindari asumsi bahwa semua bobot InsightFace/ArcFace bebas digunakan di layanan publik.

**Rencana operasional:**

1. `FACE_FEATURE_ENABLED=false` secara default, UI punya jalur manual.
2. Web minta izin dan informasi pemrosesan spesifik; referensi disediakan melalui proses demo yang berizin (enrollment tidak otomatis dari KTP user).
3. Capture wajah via browser HTTPS; batasi ukuran; kirim ke backend via HTTPS dan forward ke AI service authenticated. Jangan simpan gambar mentah di log/analytics.
4. Face service: validate MIME/size → quality → liveness/PAD bila tersedia → embed → 1:1 compare → hasil `MATCH`, `NO_MATCH`, `INDETERMINATE`, `MANUAL_REQUIRED`.
5. SIMPAN hanya hasil minimal, versi model, waktu, dasar pemrosesan, masa retensi; **default buang temporary image/embedding** setelah request (atau penyimpanan terenkripsi dan terikat ke kebijakan bila benar-benar diperlukan).
6. Pisahkan storage privat, akses staf sangat terbatas, enkripsi at rest/in transit, retensi minimum, delete request pathway.
7. Verifikasi identitas manual selalu tersedia; kegagalan wajah/warna kulit/lighting/camera tidak otomatis menyatakan warga tidak layak menerima bantuan.
8. Evaluasi `FMR` (false match rate), `FNMR` (false non-match rate), kegagalan deteksi/liveness, latency pada hardware hosting nyata, dan bias antar kondisi uji. Tidak membuat klaim e-KYC/anti-spoofing sempurna.

**Penilaian risiko privasi:** data biometrik termasuk data pribadi spesifik pada UU 27/2022, dan pemrosesan berisiko tinggi dapat memerlukan penilaian dampak pelindungan data pribadi; konsultasi/persetujuan penyelenggara serta aturan terkait diperlukan sebelum pemakaian nyata. Jika belum siap, **jangan deploy face module secara publik sebagai verifikasi wajib**. Tampilkan demo yang tidak menyimpan biometrik dengan relawan berizin dan hak untuk berhenti/menarik persetujuan bila relevan.

### Diagram 17 — Face 1:1, PAD dan alternative manual

```mermaid
flowchart TD
  A["Identity check diminta"] --> B{"FACE_FEATURE_ENABLED + lawful basis?"}
  B -->|Tidak| M["Manual verification process"]
  B -->|Ya| C["User notice + opt-in + HTTPS camera"]
  C --> D["Referensi 1:1 yang sah / demo berizin"]
  D --> E["Image quality & size checks"]
  E --> F{"PAD/Liveness feasible and passed?"}
  F -->|Tidak / uncertain| M
  F -->|Ya| G["CPU ONNX face embedder + 1:1 comparison"]
  G --> H{"Result confidence?"}
  H -->|Match threshold| I["Record minimal verification evidence"]
  H -->|No match / inconclusive| M
  I --> J["Continue account action; never approve bansos"]
  M --> K["Authorized human review, no automatic bansos denial"]
  J --> Z["Retention/minimization + audit"]
  K --> Z
```

### 15.5 What is NOT supported

- Face recognition 1:N mencari warga pada database nasional.
- Automatic rejection due to face mismatch or AI risk score.
- LLM natural language “menilai kelayakan” dari foto kemiskinan/rumah.
- Crawling DTSEN/Dukcapil publik seolah API terbuka.
- Inferensi atribut sensitif atau pembuatan ranking warga yang tidak diatur program.

---

## 16. API CONTRACTS — FRONTEND, BACKEND, INTERNAL SERVICES

**Base:** `/api/v1`. Seluruh API authenticated memakai cookie session `HttpOnly`/Secure/SameSite atau mekanisme session server setara. Cek CSRF untuk mutasi bila menggunakan cookie auth. Response error standar: `request_id`, `code`, `message`, `field_errors`, `retryable` tanpa stack trace.

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
| POST | `/complaints` | authenticated/allowed guest per policy | complaint ticket |
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
  "consent_notice_version": "privacy-v1"
}
```

```json
{
  "case_id": "d4d5f5c2-ef6f-49d8-a2e1-9bfe816e3b9d",
  "ticket_no": "BT-P-2026-0007",
  "case_type": "PROGRAM_APPLICATION",
  "status": "SUBMITTED",
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

## 17. FRONTEND ↔ BACKEND SEQUENCE DIAGRAMS

### Diagram 18 — Warga submit, AI async, petugas dan verifikator

```mermaid
sequenceDiagram
    autonumber
    actor C as Masyarakat
    participant W as Next.js Frontend
    participant A as BantuTepat API
    participant R as Rules+RBAC
    participant DB as PostgreSQL
    participant AI as FastAPI AI
    actor O as Operator
    actor V as Verifikator
    C->>W: Buka program; isi pengajuan
    W->>A: GET program and requirements
    A-->>W: mode/area/period/version
    C->>W: Submit
    W->>A: POST application submit + idempotency
    A->>R: Validate role/territory/period
    alt precheck failed
      R-->>A: reason code
      A-->>W: 422 with corrective action
      W-->>C: Tampilkan alasan sebelum submit
    else valid
      A->>DB: INSERT case + audit + outbox (transaction)
      A-->>W: 201 case_id/ticket_no
      W-->>C: Tiket dan status SUBMITTED
      A->>AI: Async anomaly check, not blocking submit
      AI-->>A: Flags or timeout
      A->>DB: Save AI status/result with provenance
      O->>A: Open assigned case
      A-->>O: Case + rules + AI flags
      O->>A: Verify + reason
      A->>DB: Save review + audit
      V->>A: Final internal review; independent actor
      A->>R: Authorization + separation-of-duties
      A->>DB: Save decision + audit + notification outbox
      C->>W: Lihat detail kasus
      W->>A: GET /cases/:id
      A-->>W: status + reason + timeline + appeal option
      W-->>C: Tampilkan hasil
    end
```

### Diagram 19 — Penolakan administratif, sanggahan dan hasil revisi

```mermaid
sequenceDiagram
    autonumber
    actor C as Pemohon
    participant UI as Frontend
    participant API as Backend
    participant DB as PostgreSQL
    actor RV as Reviewer Awal
    actor AV as Reviewer Appeal Independen
    RV->>API: Close with reason and rule reference
    API->>DB: Validate state + store decision + audit
    API-->>UI: update detail result
    UI-->>C: Hasil tidak dapat diproses + alasan
    C->>UI: Klik Ajukan Sanggahan
    UI->>API: POST appeal + evidence references
    API->>DB: Check ownership, window, duplicate appeal
    DB-->>API: appeal_id
    API-->>UI: appeal ticket
    AV->>API: Assigned appeal review
    API->>API: Ensure AV != RV and within mandate
    alt decision upheld
      AV->>API: UPHELD + reason
    else needs recheck
      AV->>API: RECHECK_REQUIRED + task
    else corrected
      AV->>API: DECISION_CORRECTED + rationale
    end
    API->>DB: Commit appeal outcome + case event + notification
    C->>UI: Baca hasil sanggahan
    UI->>API: GET appeal scoped
    API-->>UI: outcome + explanation
    UI-->>C: Hasil + feedback opportunity
```

### Diagram 20 — Privacy-aware authentication/authorization

```mermaid
sequenceDiagram
    autonumber
    actor U as User
    participant FE as Browser
    participant API as Auth/API
    participant RB as RBAC/Jurisdiction Engine
    participant DB as Database
    U->>FE: Login
    FE->>API: Session request (TLS)
    API->>DB: Validate credential/session & MFA policy
    DB-->>API: User + active scopes
    API-->>FE: Secure HttpOnly session cookie
    U->>FE: Open /agency/cases/:id
    FE->>API: GET case id (session)
    API->>RB: Permission + territory + ownership + assignment
    RB->>DB: Scoped object lookup
    alt forbidden
      RB-->>API: deny
      API-->>FE: 403/404 safe response
    else permitted
      RB-->>API: allowed fields only
      API-->>FE: sanitized case detail
    end
```

---

## 18. SECURITY, PRIVACY, THREAT MODEL DAN AUDIT

**Prinsip:** security by design, privacy by default, least privilege, deny-by-default, server-side authorization, immutable/versioned evidence, no automatic adverse decision by model. Ini **security baseline prototipe**, bukan sertifikasi keamanan untuk sistem pemerintah.

### 18.1 Threat model dan mitigasi prioritas

| Risiko | Contoh ancaman | Kontrol P0/P1 | Test |
|---|---|---|---|
| IDOR/BOLA | warga A `GET /cases/B` | object authorization + scope di API/DB | test 403/404 cross-user |
| Broken function authorization | warga panggil endpoint `/publish` | permission per action, bukan hanya route guard | test escalation |
| Privilege escalation | RT mengubah scope sendiri | admin invitation + approval+expiry+audit | test revoke/expiry |
| Maker-checker bypass | operator menyetujui kasus sendiri | `actor_id != previous_actor_id` enforced | test self-review forbidden |
| Horizontal multi-tenant leak | Kab A baca Kab B | agency/territory bound scope queries | test cross-jurisdiction |
| Mass assignment | client kirim `status=APPROVED` | explicit DTO allowlist + domain transition | test forbidden attributes |
| SQLi/XSS/CSRF | input/HTML/requests | Prisma parametrized, sanitization, CSRF on cookie mutating routes, CSP | scanner + payload tests |
| Credential compromise | brute force / reused login | rate limit, MFA officer, session rotation, secure cookies | auth tests |
| Sensitive file leakage | PDF/face exposed public bucket | signed URL short TTL, private store, type checks, AV scanning optional | direct URL/access tests |
| Unsafe LLM | prompt injection, source spoofing | approved docs only, no tools/access to case DB, PII filter | adversarial RAG tests |
| AI model abuse | spoof photo, false match | liveness quality/threshold eval + manual review | PAD/face test dataset |
| WA spoof/replay | fake webhook, duplicated message | signature verification, timestamp, idempotency | replay tests |
| Public stats reidentification | unique subgroup count=1 | small-cell suppress, aggregate, rate limit | sample leak test |
| Secret leakage | token di client/repo | env server, secret manager, secret scanning | repo scan |
| Third-party outage | LLM/WA/AI unavailable | circuit breaker, timeout, fallback manual/FAQ | 503 fault injection |
| Disputed action | keputusan ditimpa diam-diam | audit append-only + version history + reviewer | audit integrity test |

**Acuan security review:** OWASP API Security Top 10 2023, khususnya BOLA dan Broken Function Level Authorization. Link ada pada bagian referensi.

### 18.2 Checklist implementasi keamanan minimum

- [ ] HTTPS seluruh endpoint, HSTS bila environment siap, secure cookies; tidak menyimpan token di `localStorage` untuk sesi sensitif.
- [ ] Session management (logout, revoke, max idle, rotate on privilege change), MFA untuk akun instansi/admin jika dapat dikonfigurasi.
- [ ] CSRF mitigation untuk mutasi dengan cookie session, input size constraints, schema validation (Zod/Pydantic), output encoding, safe redirect policy.
- [ ] Backend RBAC+scope+assignment untuk setiap operasi CRUD; khusus query list dan download lampiran.
- [ ] Database role least privilege; RLS bila menggunakan Supabase Data API; secret/elevated key server-only. Jika DB diakses via Prisma server, gunakan dedicated constrained DB user dan authorization di service layer.
- [ ] Audit event append-only dan proteksi privileges; tidak menyimpan NIK, password, face image di log.
- [ ] Upload: allowlist file MIME+magic bytes, ukuran kecil terkonfigurasi, anti path traversal, quarantine/scanning, private bucket signed URL berumur pendek.
- [ ] Data minimization, purpose limitation, retention rules, privacy notice, pengaturan hak subjek, backup terenkripsi, restore test.
- [ ] Rate limit per IP/session/user/service; anti-bot di route kritis, status ticket lookup tidak enumerate.
- [ ] CSP security headers, dependabot/lockfile, supply-chain scan, lint, SAST, secret scan GitHub.
- [ ] Face service: strict opt-in, 1:1, licensed model, liveness validity, no persistent biometrics by default, manual fallback.
- [ ] LLM provider: server-only key, quota, no private case data in prompts, provider retention/DPA ditinjau sebelum data riil.
- [ ] Jika ada pelanggaran/kebocoran: incident response minimal, logging akses, revoke credentials, jalur penanganan internal.

### 18.3 Data classification dan retensi desain demo

| Jenis data | Kategori | Lokasi | Retensi demo | Publik? |
|---|---|---|---|---|
| Program & aturan | publik setelah publish | DB + knowledge docs | versi historis | Ya |
| Statistik teragregasi | publik setelah agregasi | query/materialized view | periodik | Ya |
| Profil/nomor kontak | personal | DB private | hanya selama demo/perlu | Tidak |
| Kondisi ekonomi/dokumen | sensitif menurut konteks | DB + private object storage | minimum, delete after demo | Tidak |
| Wajah/embeddings | biometrik spesifik | transient AI processing | **tidak disimpan default** | Tidak |
| Audit aktivitas | internal restricted | DB append-only | sesuai kebijakan proyek dan kebutuhan pembuktian | Tidak |
| Chatbot web pertanyaan umum | public-question content | memory/session terbatas | TTL | Tidak perlu dipublikasikan |
| WA session | metadata terbatas | hashed/encrypted DB | TTL jam/hari configurable | Tidak |

**Kebijakan produksi** tidak boleh disimpulkan dari angka retensi demo. Tetapkan jadwal retensi resmi setelah kajian dasar hukum dan persyaratan instansi.

### Diagram 21 — Security trust boundaries (fokus PII dan biometrik)

```mermaid
flowchart TB
  PUB["Public Internet"] --> EDGE["TLS + Rate Limit + WAF / Secure Headers"]
  EDGE --> BFF["Next.js Backend: Auth, CSRF, RBAC, Input Validation"]
  BFF --> PSQL[("Private Postgres + scoped queries")]
  BFF --> ST[("Private File Storage + short-lived signed URL")]
  BFF --> RAG["Sanitized FAQ Query -> LLM API"]
  BFF --> F["One-time Face Request -> CPU AI Service"]
  F --> TEMP["Transient image processing only"]
  TEMP --> OUT["Minimal status + model version"]
  BFF --> AU["Restricted Append-Only Audit"]
  BFF --> GOV["Synthetic Government API over service auth"]
  NOTE["Never send NIK, case file, face image to external LLM"] -.-> RAG
```

---

## 19. FRONTEND IMPLEMENTATION SPEC — HALAMAN, INTERAKSI, EMPTY/ERROR STATES

**Batas frontend:** menampilkan data dan aksi yang diizinkan, mengumpulkan input, menangani draf/unggahan, membaca hasil API. Frontend **tidak** mengesahkan kewenangan, menghitung status resmi, menerapkan rules sebagai sumber keputusan, mengakses database secara bebas, atau menyimpan kunci AI. Backend **selalu** mengulang seluruh validasi penting.

### 19.1 Inventaris screen dan state fungsional

| Screen/komponen | Isi utama | Aksi nyata yang wajib bekerja | Loading/empty/error/fallback |
|---|---|---|---|
| `/` landing publik | nilai produk, cara kerja, program aktif, transparansi ringkas | ke katalog, pedoman, login, bantuan | jika statistik mati tampil pesan statistik sementara tak tersedia |
| `/program` katalog | daftar, wilayah, metode, syarat utama, periode, source | pencarian, pagination, filter, detail | hasil 0: reset filter; gagal API: retry |
| `/program/[slug]` | syarat lengkap, pemilik program, dasar kebijakan, wilayah, metode, periode, kuota sesuai aturan | `Daftar` bila valid; `Lihat jalur resmi` bila tertutup | disabled dengan alasan, bukan tombol dead-end |
| `/bantuan`/FAQ | petunjuk, sumber dokumen, kontak | tautan sumber, chatbot web | FAQ statis saat API LLM down |
| `/transparansi` | agregat yang aman, data diperbarui terakhir, filters | filter wilayah/periode; unduh ringkasan jika aman | suppress small groups; no NIK/nama |
| `/dashboard` warga | tiket, status, langkah selanjutnya, notifikasi | lihat detail, buat usulan, daftar program | empty state ajakan membuat pengajuan |
| `/pengajuan/data/baru` | wizard kondisi ekonomi, alasan perubahan, dokumen secukupnya | simpan draft, prev/next, submit | field errors, upload retry, draft autosave pilihan |
| `/pengajuan/program/[id]` | wizard program versi tertentu, alamat sasaran, syarat | preview, save draft, submit | out-of-scope: pesan & jalur koreksi |
| `/pengajuan/[id]` | timeline, alasan, revision request, review, bukti, audit pribadi | unggah revisi, sanggahan, feedback | 403/404 aman; last-updated |
| `/sanggahan/[id]` | keputusan yang disengketakan, form alasan & bukti | submit appeal, track appeal | window expired: penjelasan & pengaduan |
| `/pengaduan` | masalah pelayanan, kategori, privasi | submit laporan & nomor tracking | flag laporan rahasia, status tidak bocor |
| `/wilayah/usulkan` | pengusulan warga representatif | pilih subjek terotorisasi, isi form | validasi representation grant/scope |
| `/wilayah/kasus` | antrean sesuai area, jumlah belum ditangani | buka, minta revisi, rekomendasi | unauthorized region ditolak |
| `/instansi/review` | case review, bukti, AI flags, decision log | review ulang, penerusan simulasi, penolakan beralasan | server enforce maker-checker |
| `/instansi/program` | daftar program & versi & publikasi | buat draft, edit, submit review, publish dengan permission | draft version conflict 409 |
| `/pengawasan` | SLA, anomaly, audit, internal performance | filter audit, drilldown scoped | log restricted; redaction |
| `/admin/pengguna` | user role scope dan masa berlaku | invite, revoke, set scope; audit | privilege changes require reauth/MFA |

**Render empat keadaan minimal setiap halaman:** `loading`, `empty`, `error`, `ready`. Setiap form memiliki `validation errors`, `network retry`, `success confirmation`, `double-submit guard`. Untuk endpoint bergantung AI, tambahkan `AI_PENDING/AI_UNAVAILABLE` yang **tidak** menghentikan proses manual.

### 19.2 Wizard pengajuan warga — UI step-by-step

1. **Pilih jenis layanan:** `usulan data` atau `pendaftaran program`; jelaskan bedanya dalam bahasa awam.
2. **Kenali batasan:** tampilkan penerbit program, cakupan wilayah, asal aturan, tanggal, metode pendaftaran; minta user menyatakan telah membaca ringkasan.
3. **Profil subjek dan rumah tangga:** ambil dari profil bila sah; izinkan pembetulan dengan jejak revisi. Jangan tampilkan data keluarga lain tanpa representasi sah.
4. **Informasi relevan:** perubahan kondisi, rentang penghasilan bila diperlukan, jumlah tanggungan/pekerjaan; field berdasarkan `program_version.criteria`, jangan hardcode universal.
5. **Dokumen relevan:** hanya berkas yang memang diperlukan; preview, batas ukuran/format, hapus sebelum kirim; private storage.
6. **Ringkasan + pernyataan kebenaran + privacy notice:** baca semua field; pilih `submit` sekali; server buat `ticket_no`.
7. **Berhasil terkirim:** tampilkan tiket, status awal, estimasi **jika ada SLA terkonfigurasi**, tindakan lanjut, tautan tracking. Jangan mengklaim program pasti diterima.

**Aturan UX penolakan:** bedakan (a) *blocked precheck* sebelum tiket dibuat, (b) permintaan revisi setelah tiket, (c) *hasil administratif tidak dapat diproses* setelah review, (d) *tidak terpilih* jika aturan program sah dan otoritas memungkinkan, (e) error layanan sementara. Kelimanya perlu teks/tindakan berbeda.

### Diagram 22 — Frontend application navigation dan branching

```mermaid
flowchart TD
  G["Guest buka BantuTepat"] --> PUB["Beranda - Katalog - Transparansi - FAQ"]
  PUB --> CHAT["Chat LLM + RAG di WEB SAJA"]
  PUB --> PDETAIL["Detail Program + Syarat + Wilayah + Metode"]
  PDETAIL --> METHOD{"Pendaftaran program dibuka?"}
  METHOD -->|Tidak| INFO["Panduan usulan/koreksi atau kanal resmi"]
  METHOD -->|Ya| LOGIN["Login/Register"]
  PUB --> LOGIN
  LOGIN --> ROLES{"Permission + role aktif"}
  ROLES -->|Masyarakat| C["Dashboard Warga"]
  ROLES -->|Pengusul wilayah| R["Dashboard RT/RW"]
  ROLES -->|Operator| O["Dashboard Desa/Kelurahan"]
  ROLES -->|Verifikator/Pengelola| A["Dashboard Instansi"]
  ROLES -->|Pengawas/Admin| Z["Dashboard Pengawasan/Teknis"]
  C --> DRAFT["Wizard Usulan Data / Pendaftaran"]
  DRAFT --> PREVIEW["Ringkasan dan Konfirmasi"]
  PREVIEW --> SUBMIT["POST Submit -> API"]
  SUBMIT --> CASE["Tiket + Timeline + Perbaikan/Sanggahan"]
  R --> PROPOSE["Usulkan Warga Terotorisasi"]
  O --> VERIFY["Verifikasi Kasus"]
  A --> REVIEW["Review Program dan Kasus"]
  Z --> AUDIT["Audit, Statistik, Role Management"]
  PROPOSE --> SUBMIT
  VERIFY --> CASE
  REVIEW --> CASE
```

### Diagram 23 — Frontend form error states dan penyelesaian

```mermaid
stateDiagram-v2
  [*] --> LOADING
  LOADING --> READY: data available
  LOADING --> ERROR: GET failed
  ERROR --> LOADING: retry
  READY --> EDITING: user changes form
  EDITING --> VALIDATING: next/submit
  VALIDATING --> EDITING: invalid fields
  VALIDATING --> SAVING: valid data
  SAVING --> EDITING: save failed + retry
  SAVING --> DRAFT_SAVED: draft persisted
  DRAFT_SAVED --> EDITING: resume edit
  DRAFT_SAVED --> SUBMITTING: submit
  SUBMITTING --> SUCCESS: 2xx case issued
  SUBMITTING --> EDITING: 422 field errors
  SUBMITTING --> CONFLICT: 409 duplicate/version
  SUBMITTING --> RETRYABLE_ERROR: 5xx/network
  CONFLICT --> EDITING: resolve / inspect existing ticket
  RETRYABLE_ERROR --> SUBMITTING: retry with same idempotency key
  SUCCESS --> [*]
```

### 19.3 Design system, aksesibilitas, performa

- **Orisinalitas:** desain sendiri; jangan import landing page atau dashboard template siap pakai. Komponen boleh dibuat dari primitives/shadcn-ui/Radix dengan komposisi, ilustrasi, dan style kustom jika sesuai aturan dan lisensi; jangan copy template website.
- **Visual:** navigasi sederhana, warna netral bernuansa institusi publik, satu CTA utama tiap halaman, progress stepper, status dengan label+ikon (jangan bergantung warna), panduan teks Indonesia ringkas.
- **Accessibility:** target WCAG 2.2 AA sebagai sasaran desain (belum sertifikasi). Fokus keyboard, label field yang eksplisit, pesan error terasosiasi, caption, kontras, input mobile, screen reader dan visual tanpa animasi berlebihan.
- **Performa:** server-render konten publik (katalog/FAQ), pagination list, kompresi asset, lazy-load grafik/AI chat, optimasi font/image, cache katalog sesuai invalidasi, tanpa cache PII pada CDN.
- **Konsistensi:** buat komponen `ProgramCard`, `StatusBadge`, `DecisionReason`, `AuditTimeline`, `RevisionRequest`, `CaseWizard`, `PolicyCitation`, `PrivacyNotice`, `RoleGuard`, `ServiceDegradedBanner`.

---

## 20. BACKEND IMPLEMENTATION DETAIL — RULES, TRANSACTIONS, AUTHORIZATION

### 20.1 Pembagian tanggung jawab backend (jangan campur)

| Komponen | Fungsi | Dilarang |
|---|---|---|
| `auth-service` | session, login, revoke, MFA petugas, user context | percaya role dari client/localStorage |
| `authorization-service` | role+permission+agency+jurisdiction+object ownership+assignment | cek role saja tanpa objek/wilayah |
| `program-service` | draft, versi immutable, publish review, rules config | edit program published in place |
| `case-service` | draft, submit, revision, status transition, attachments | `PATCH status` langsung dari client |
| `policy-engine` | rules deterministik program/wilayah/metode/periode | menyerahkan kriteria hukum pada model AI/LLM |
| `verification-service` | tugas operator, review, reason, evidence | actor=reviewer kasusnya sendiri |
| `appeal-service` | jendela keberatan, review independen, hasil | overwrite keputusan lama tanpa audit |
| `complaint-service` | pengaduan layanan/triage/rujukan | mencampur pengaduan dengan penentuan penerima |
| `notification-worker` | outbox, email/in-app/WA, retry | mengirim data sensitif tanpa autentikasi |
| `ai-adapter` | preprocess minim, call AI, validasi response | memblokir submit saat AI down |
| `government-adapter` | synthetic simulator; status request/outbox | mengaku telah update DTSEN resmi |
| `audit-service` | append-only event logging & masking | menaruh PII mentah pada log |
| `analytics-service` | agregasi statistik aman, KPI, SLA | expose row-level dataset pada public API |

### 20.2 Policy evaluation yang deterministik (pseudocode)

```ts
// Kontrak domain: hanya keputusan dapat/tidak dapat mengajukan di kanal ini,
// BUKAN keputusan layak/tidak layak menerima bansos.
function evaluateSubmission(ctx: SessionContext, p: ProgramVersion, subject: Subject) {
  require(p.lifecycle === 'PUBLISHED');
  require(p.applicationWindow.isOpenAt(serverNow()));
  require(ctx.hasPermission('case.create') && ctx.isAuthorizedFor(subject));
  require(p.allowsChannel(ctx.submissionMode));
  const territory = verifiedOrReviewedAddress(subject, p.addressBasis);
  if (!territory) return REVIEW_REQUIRED('ADDRESS_NOT_VERIFIED');
  if (!p.targetTerritories.contains(territory)) {
    return BLOCK_NEW_SUBMISSION('OUTSIDE_SCOPE', ['FIX_ADDRESS', 'VIEW_RULES']);
  }
  if (hasConflictingActiveCase(subject, p.id)) {
    return REQUIRES_CLARIFICATION('POSSIBLE_DUPLICATE');
  }
  return CAN_SUBMIT(); // NOT equal to BENEFICIARY_APPROVED
}
```

**Caution:** kebijakan wilayah dapat memakai KTP, domisili, tempat sekolah, atau kriteria lain sesuai program; `verifiedOrReviewedAddress` harus mengikuti `address_basis` versi program. Ketidakpastian alamat atau kesalahan sumber bukan alasan permanen untuk menolak hak mengajukan; sediakan klarifikasi. Untuk ketentuan *tanpa pendaftaran umum*, `allowsChannel` hanya benar bagi kanal otoritas sesuai aturan.

### 20.3 Rule configuration (data-driven, berversi)

```json
{
  "program_code": "PANGAN-DEMO-KAB-A",
  "agency_id": "agency_demo_kab_a",
  "policy_version": 3,
  "source_type": "SYNTHETIC_DEMO",
  "application_mode": "BOTH",
  "geographic_scope": {
    "territory_ids": ["KAB_A"],
    "include_descendants": true,
    "address_basis": "DOMICILE_OR_REVIEWED"
  },
  "registration_period": {
    "timezone": "Asia/Jakarta",
    "opens_at": "2026-12-01T00:00:00+07:00",
    "closes_at": "2026-12-15T23:59:00+07:00"
  },
  "data_requirements": ["household_size", "income_band", "reason"],
  "allow_appeal": true,
  "appeal_days": 7,
  "max_active_application_per_household": 1,
  "publication_requires_two_person_approval": true
}
```

**Catatan:** seluruh tanggal/nilai adalah contoh program **fiktif**, bukan syarat resmi PKH/Sembako. Jangan menampilkan contoh angka ini sebagai kebijakan pemerintah nyata. Jika ada ketentuan kuota, jadwal verifikasi, ambang pendapatan atau dokumen, gunakan `policy_source` dan versi yang benar bagi program tersebut.

### Diagram 24 — Backend policy decision flow dan otorisasi

```mermaid
flowchart TD
  REQ["POST /program-applications/:id/submit"] --> AUTH{"Authenticated, own subject or valid grant?"}
  AUTH -->|Tidak| DENY["401 / 403 - audit denied attempt"]
  AUTH -->|Ya| LOAD["Load immutable program version"]
  LOAD --> MODE{"Channel + role allowed?"}
  MODE -->|Tidak| REJECT_MODE["403 + available official channel"]
  MODE -->|Ya| DATE{"Within server-side program window?"}
  DATE -->|Tidak| CLOSED["409 + schedule and reason"]
  DATE -->|Ya| TERR{"Territory verified and in scope?"}
  TERR -->|No, verified outside| REGION["422 + scope reason and correction options"]
  TERR -->|Unknown| ADDR["Hold draft for address clarification"]
  TERR -->|Yes| DUP{"Conflicting active application?"}
  DUP -->|Yes| CLARIFY["409 / clarification + existing ticket safe ref"]
  DUP -->|No| TX["DB transaction: case + revision + audit + outbox"]
  TX --> ACK["201 ticket SUBMITTED"]
  ACK --> ASYNC["Enqueue simulator/rules/AI enrichment"]
  ASYNC --> HUMAN["Operator verification"]
```

### 20.4 Atomic write + outbox pattern

**Satu transaksi database pada perubahan status penting:**

1. Kunci case row menggunakan transaksi `SELECT ... FOR UPDATE` atau optimistic `version` check.
2. Validasi actor/role/assignment, state transition, rules, reason dan evidence.
3. Tambahkan revision/decision baru; set status terbaru; tambahkan `audit_events` dan `notification_outbox`.
4. Commit. Baru sesudah commit, worker mengirim notifikasi atau memanggil service eksternal, dengan retry/idempotency.
5. Client menerima respons `request_id`, `ticket_no`, `version`, dan status. Duplicate request (idempotency key sama) harus mengembalikan hasil commit yang sama, bukan kasus baru.

Gunakan kolom `version` + unique `(actor_id, idempotency_key)` dalam tabel `idempotency_keys`; payload sama mengulang respons, payload berbeda dengan key sama menghasilkan `409`. Implementasikan idempotency untuk `POST submit`, perubahan review, penerusan simulasi, dan WA webhook.

### Diagram 25 — Async events, outbox dan pemulihan gangguan

```mermaid
sequenceDiagram
  autonumber
  participant UI as Website
  participant API as Backend
  participant DB as PostgreSQL
  participant WORK as Outbox Worker
  participant AI as AI Inference
  participant WA as WhatsApp Provider
  UI->>API: Submit data valid + idempotency key
  API->>DB: BEGIN; case + revision + audit + outbox
  DB-->>API: COMMIT / ticket number
  API-->>UI: 201 SUBMITTED
  loop poll safe queue
    WORK->>DB: Claim job (SKIP LOCKED / lease)
    DB-->>WORK: assessment / notification work item
    alt AI available
      WORK->>AI: assess minimized input + M2M auth
      AI-->>WORK: flag + version
      WORK->>DB: Save AI assessment + mark done
    else AI unavailable
      WORK->>DB: Mark retry / AI_UNAVAILABLE + manual queue
    end
    alt WA notification permitted & provider available
      WORK->>WA: approved template or permitted reply
      WA-->>WORK: message id
      WORK->>DB: record delivery/send outcome
    else WA unavailable
      WORK->>DB: retry & keep notification in web inbox
    end
  end
```

### 20.5 Job handling dan kegagalan terduga

| Job | Trigger | Timeout/retry | Fallback |
|---|---|---|---|
| `ANOMALY_ASSESS` | case submit/resubmit | finite timeout; max retries; DLQ style state | petugas tetap bisa review manual |
| `GOV_REFERENCE_LOOKUP` | sesudah submit | bounded retries; mark `SIMULATOR_UNAVAILABLE` | manual data review; tidak klaim telah sinkron |
| `FORWARD_SIMULATED` | reviewer authorizes forward | idempotent + audit ACK/FAIL | `FORWARDING_PENDING/FAILED`, retry |
| `WA_DELIVERY` | status changed + permission/template valid | provider rate limit/backoff | inbox website tetap tersedia |
| `APPEAL_ASSIGN` | appeal submitted | assign different reviewer | supervisor manual assignment |
| `SLA_ESCALATE` | schedule based on program rules | idempotent recurring cron | hanya flag, bukan mengubah hasil perkara |
| `PUBLIC_STATS_REFRESH` | after changes/on schedule | incremental/cached; suppress small cells | last good snapshot + timestamp |

---

## 21. FACE VERIFICATION + LLM DEPLOYMENT CONTRACT — STANDAR TEKNIS

### 21.1 Face 1:1 bukan face recognition massal

**Input:** `verification_request_id`, referensi yang terdaftar secara sah untuk subjek yang sama, foto capture baru melalui sesi HTTPS, opsi PAD; request ukuran file dibatasi. **Output:** `MATCH/NO_MATCH/INCONCLUSIVE/MANUAL_REQUIRED`, `model_version`, `inference_duration_ms`, `pad_status` jika diuji, dan `reason_code` minim. Tidak mengembalikan embedding/raw image ke browser.

**Desain dua-mode:**

- `FACE_MODE=disabled`: fitur disembunyikan; `manual verification` aktif.
- `FACE_MODE=demo_opt_in`: hanya akun relawan simulasi, feature flag, notice/persetujuan yang relevan, private endpoint, ephemeral processing. Jangan gunakan foto warga/identitas pemerintah asli.
- **Mode produksi nyata:** tidak diaktifkan sampai kajian dasar pemrosesan data biometrik, uji ketahanan PAD, lisensi model, evaluasi akurasi/fairness, mekanisme penghapusan dan persetujuan instansi berwenang selesai. **Tidak** ada jaminan bahwa implementasi lomba otomatis layak digunakan pemerintahan.

### 21.2 Model serving minimum (CPU)

- Gunakan model ONNX *ringan* yang dapat dijalankan dengan ONNX Runtime CPU; pilih bobot dengan lisensi yang mengizinkan tujuan deployment. **Lisensi source code berbeda dengan lisensi bobot model**; sebagian model pra-latih wajah membatasi penggunaan komersial.
- Lakukan *warm start* model satu kali pada startup; jangan `load_model()` setiap request.
- Batasi resolusi dan `max request bytes`, `max concurrent inference`, CPU threads, timeout, request queue; hindari OOM. Target awal uji lokal **2 vCPU / 4 GB RAM** sebagai titik ukur (bukan kebutuhan pasti); lakukan benchmark pada model dan provider akhir sebelum menetapkan ukuran server/biaya.
- Simpan file bobot terverifikasi dengan checksum & version; jangan auto-download model tak diverifikasi setiap startup di server publik.
- Anti-spoofing/liveness tidak boleh diklaim aman hanya karena ada library. Jika komponen PAD belum diuji, tampilkan `PAD_NOT_AVAILABLE` dan gunakan jalur manual. **Jangan menyatakan bypass liveness = sukses.**
- Terapkan endpoint `GET /healthz` (liveness), `GET /readyz` (model loaded), `POST /v1/face/verify` private, dan metrik agregat tanpa gambar/wajah.

### 21.3 LLM melalui API eksternal

- Gunakan `LLM_PROVIDER=...`, `LLM_MODEL=...`, `LLM_API_KEY=...` **di server/secret manager**. Tidak ada API key di frontend, log, GitHub, atau WhatsApp webhook.
- Semua jawaban yang menyatakan aturan harus menyertakan referensi resmi yang sudah dikurasi. Retrieval awal boleh menggunakan full-text Postgres tanpa biaya embedding; peningkatan ke `pgvector` setelah diperlukan.
- Batasi kuota penggunaan per akun/IP, maksimal panjang pertanyaan dan output, allowed domains untuk referensi, timeout, observability biaya token dan fallback FAQ.
- Tidak melakukan tool-calling untuk edit data, membaca identitas individu, melakukan submit atau mengeksekusi database melalui LLM.
- Jika tidak ada sumber atau hasil verifikasi sitasi gagal, **jangan membuat kutipan palsu**; jawab berbasis FAQ statis atau informasikan bahwa sumber belum tersedia.

### 21.4 Failure matrix AI dan prioritas layanan

| Layanan | Jika mati/gagal | Yang tetap berfungsi |
|---|---|---|
| Face CPU crash/OOM | manual verification, modal pemberitahuan, log service incident | login, submit, operator review, audit |
| LLM API timeout/quota | FAQ statis, fallback sumber resmi, chat non-generatif | semua transaksi bantuan |
| Anomaly inference timeout | AI_UNAVAILABLE, task review manual | submit, pemeriksaan, tindak lanjut |
| Government Simulator fail | `PENDING` dengan error terukur, tombol retry admin/petugas | status kasus internal, review bukti |
| WhatsApp API unavailable | notifikasi inbox web; outbox retry | seluruh fitur website |
| PostgreSQL unavailable | mode error layanan terukur, **jangan** klaim submit berhasil | halaman publik cache non-sensitif dapat tampil sementara |

### Diagram 26 — AI service isolation dan fallback keseluruhan

```mermaid
flowchart TD
  WEB["Next.js Website + Backend API"] --> CORE["Authenticated business actions"]
  WEB --> RAG["Web Chat -> RAG orchestrator"]
  CORE --> DB[("Private PostgreSQL")]
  CORE --> RULES["Deterministic Rules Engine"]
  CORE --> ANOM{"Anomaly Service Healthy?"}
  CORE --> FACE{"FACE enabled + opt-in + healthy?"}
  ANOM -->|Yes| IF["CPU tabular ML"]
  ANOM -->|No| MA["Manual Case Review + AI_UNAVAILABLE"]
  FACE -->|Yes| FV["CPU ONNX 1:1 + PAD if validated"]
  FACE -->|No| FM["Manual Identity Verification"]
  RAG --> APPROVED["Approved Public Knowledge only"]
  APPROVED --> LLM{"External LLM API available?"}
  LLM -->|Yes| ANSWER["Cited answer"]
  LLM -->|No| FAQ["Static curated FAQ"]
  IF --> STAFF["Human officer review"]
  MA --> STAFF
  FV --> STAFF
  FM --> STAFF
```

---

## 22. DEPLOYMENT ARCHITECTURE — REALISTIS UNTUK LOMBA

### 22.1 Pilihan utama (hosting terpisah)

| Komponen | Pilihan | Catatan implementasi |
|---|---|---|
| Frontend+backend/BFF | **Vercel (Next.js)** | public URL/HTTPS; API app; no persistent background work in request handlers |
| Primary database | **Supabase Postgres** atau managed Postgres setara | private credentials, backups, indexes, auth layer; RLS jika akses Supabase Data API digunakan |
| File upload | private object storage Supabase/S3-compatible | signed URLs short TTL, access per case, malware/file validation |
| AI inference | **Render/Fly.io/Railway container CPU** | FastAPI persistent, 1:1 face optional, tabular model, authenticated service route |
| Worker | Render background worker atau cron/polling service setara | outbox, notification, AI jobs; gunakan always-on untuk reliabilitas |
| Government Data Simulator | service container kecil atau modul API terisolasi | synthetic DB, authenticated adapter, README penegasan `NOT GOVERNMENT SYSTEM` |
| Web LLM | provider LLM API via backend only | API key server; rate limit; FAQ mode |
| WhatsApp | Meta WhatsApp Cloud API | nomor/akun dan webhook terverifikasi; fallback mock untuk demo |
| Monitoring | provider logs + minimal uptime check + structured logs | no sensitive payloads in logs |

**Kenapa AI Face terpisah:** model vision butuh warm model, RAM, disk/binary, dan latency yang tidak cocok diasumsikan selalu tersedia dalam fungsi web singkat. Vercel memang mendukung Functions termasuk Python, tetapi untuk keamanan operasi demo dan kontrol kapasitas CPU/RAM, **gunakan container AI terpisah**. Jangan menyatakan face model pasti jalan pada paket gratis; ukur penggunaan memori, ukuran file, cold start, kuota, serta ketentuan masing-masing provider terlebih dahulu.

**Catatan biaya:** harga vendor dan paket gratis berubah; hindari klaim gratis untuk layanan always-on, nomor WhatsApp produksi, LLM token, penyimpanan, worker, dan traffic. Catat biaya estimasi setelah ukuran kebutuhan serta provider final dipilih. Jika budget sangat kecil, prioritas **P0 website+DB+audit** lebih penting dibandingkan nomor WA/face live.

### Diagram 27 — Deployment produksi demo dengan trust boundary

```mermaid
flowchart TB
  USERS["Browser Pengunjung / Warga / Petugas"] --> EDGE["HTTPS CDN / Vercel Next.js"]
  WAUSER["WhatsApp user"] --> META["Meta Cloud API"]
  META --> WEBHOOK["HTTPS webhook on backend + signature check"]
  EDGE --> BFF["Next.js Backend API + RBAC + Policy"]
  WEBHOOK --> BFF
  BFF --> DB[("Managed PostgreSQL private")]
  BFF --> BUCKET["Private encrypted file bucket"]
  BFF --> MOCK["Authenticated Synthetic Gov API"]
  BFF --> LLM["External LLM API - server key only"]
  BFF --> OUTBOX["Transactional Outbox in DB"]
  OUTBOX --> WORK["Always-on Worker / scheduler"]
  WORK --> AI["Private FastAPI CPU service"]
  WORK --> META
  WORK --> DB
  AI --> MODEL["Versioned checked ONNX / IForest weights"]
  STAFF["Admin operations"] --> MONITOR["Restricted monitoring / audit"]
  BFF --> MONITOR
  WORK --> MONITOR
  AI --> MONITOR
```

### 22.2 Alternatif biaya sederhana untuk demo

1. **Next.js + PostgreSQL + Python AI dalam satu VPS/container host** menggunakan Docker Compose, tetapi aplikasi web dan AI tetap proses/container berbeda. Gunakan HTTPS reverse proxy, private DB, resource limits, proses worker, backup, firewall. Satu VPS murah dapat habis RAM saat memuat model wajah; uji sebelum lomba.
2. **Jika belum tersedia nomor WhatsApp produksi**, gunakan `WA_PROVIDER=mock` dengan UI simulasi webhook/percakapan berlabel *DEMO*, bukan mengaku nomor resmi aktif.
3. **Jika model wajah terlalu berat**, `FACE_MODE=disabled` dan tunjukkan opsi verifikasi manual. Untuk demo opsional, jalankan *face CPU service* hanya pada relawan dan pastikan restart/health aman.
4. **Jika API LLM tidak tersedia**, aktifkan FAQ RAG retrieval + jawaban statis sehingga navigasi/help tetap berfungsi.

### 22.3 Pengaturan domain, TLS dan CORS

- `APP_PUBLIC_URL=https://<domain>` (untuk contoh hanya placeholder, Codex **tidak** boleh menganggap domain sudah dimiliki).
- `API_INTERNAL_URL` bersifat server-only. Bila frontend dan BFF di Next.js sama origin, gunakan path relatif `/api/v1` agar menghindari CORS yang tidak perlu.
- `AI_SERVICE_URL` hanya server-to-server, dengan autentikasi kuat. Jika Render public network tidak bisa sepenuhnya private di arsitektur host campuran, gunakan HTTPS + token internal + rate limit + allowed caller policy; **jangan percaya CORS sebagai kontrol keamanan server-to-server**.
- `SIMULATOR_URL` private/server-only; tandai data `synthetic: true`.
- Webhook WA memiliki rute HTTPS publik khusus yang menolak signature tidak sah dan *tidak* dijadikan kanal login otomatis.

### 22.4 Env contract untuk Codex (.env.example TANPA nilai rahasia)

```dotenv
NODE_ENV=development
APP_PUBLIC_URL=http://localhost:3000
DATABASE_URL=postgresql://bantutepat:CHANGE_ME@localhost:5432/bantutepat
SESSION_SECRET=CHANGE_ME_32_BYTES_MINIMUM
ENCRYPTION_KEY=CHANGE_ME
GOV_SIMULATOR_MODE=synthetic
GOV_SIMULATOR_URL=http://simulator:8001
GOV_SIMULATOR_TOKEN=CHANGE_ME
AI_SERVICE_URL=http://ai:8000
AI_SERVICE_TOKEN=CHANGE_ME
ANOMALY_ENABLED=true
FACE_MODE=disabled
FACE_MODEL_PATH=/models/face-verified.onnx
FACE_MODEL_SHA256=PLACEHOLDER_CHECKSUM
FACE_ALLOW_DEMO_SUBJECTS_ONLY=true
LLM_PROVIDER=mock
LLM_MODEL=SET_PROVIDER_MODEL
LLM_API_KEY=
RAG_MODE=approved_fts
WA_PROVIDER=mock
WA_ACCESS_TOKEN=
WA_PHONE_NUMBER_ID=
WA_VERIFY_TOKEN=
WA_APP_SECRET=
WA_WEBHOOK_URL=http://localhost:3000/api/v1/wa/webhook
STORAGE_PROVIDER=local_private
STORAGE_ENDPOINT=
STORAGE_BUCKET=private-attachments
STORAGE_ACCESS_KEY=
STORAGE_SECRET_KEY=
OUTBOX_POLL_INTERVAL_SECONDS=5
OBSERVABILITY_REDACT_PII=true
```

**Jangan gunakan `.env.example` sebagai `.env` produksi dengan `CHANGE_ME`.** Codex harus membuat dokumentasi setup secret yang cukup rinci dan memastikan `ENV` diperiksa pada startup. Vercel web+API memakai secret tersendiri, worker dan AI memakai service credentials berbeda. Hindari `NEXT_PUBLIC_*` untuk secrets.

### 22.5 Local Docker Compose target (disyaratkan pada Codex)

Buat `docker-compose.yml` dengan service: `db`, `web`, `ai`, `gov-simulator`, `worker` (opsional `minio` untuk private object storage lokal). Jangan expose port DB/AI/simulator ke interface publik; gunakan network internal, healthcheck, named volume DB, dan image pinned. Bila membuat demo fallback WA, route `/demo/wa` hanya aktif `NODE_ENV != production` atau di lingkungan demo terlindungi.

**Perintah yang harus tersedia:**

```bash
cp .env.example .env   # isi seluruh CHANGE_ME dengan rahasia lokal yang aman
pnpm install
pnpm db:migrate
pnpm db:seed:demo
pnpm dev
# ATAU
# docker compose up --build -d
# docker compose exec web pnpm db:migrate
# docker compose exec web pnpm db:seed:demo
```

Script di atas adalah **kontrak target untuk implementasi Codex**. File kode dan Docker Compose belum dibuat oleh dokumen ini; jangan menganggap command tersebut sudah dapat dijalankan sebelum Codex membangunnya.

### 22.6 Checklist deployment & rollback

1. Config secrets server, managed DB & bucket private; jangan gunakan database dev untuk demo publik.
2. Migrasi staging/test dengan data fiktif, backup sebelum migrasi, verifikasi rollback/recovery plan. Jangan melakukan destructive migration pada produksi tanpa backup.
3. Deploy AI CPU, cek `healthz` dan `readyz`, RAM at startup, inference test, timeout/out-of-memory restart, model hash.
4. Deploy simulator, jalankan integration contract test `GET synthetic record` + `POST forward simulated` + idempotency.
5. Deploy backend & web, jalankan seed demo secara terkendali, pastikan API auth, cookies, scope, private storage, rate limits, CSP.
6. Deploy worker dan tes outbox events; uji provider WA mock dan LLM quota/fallback.
7. Jalankan smoke-test memakai akun guest/warga/RT/operator/verifikator/program/pengawas/admin; cross-region access denial.
8. Monitor log, error rate, status DB, worker queue; jangan logging payload mentah. Label semua data simulasi secara eksplisit.
9. Sediakan rollback release tag dan versi model, plus DB strategy yang sesuai migrasi; tidak cukup hanya `git revert` untuk migrasi irreversible.

---

## 23. REPOSITORY STRUCTURE, CODING CONTRACT, CI/CD

### 23.1 Monorepo yang Codex harus buat

```text
bantutepat/
├── apps/
│   └── web/                         # Next.js App Router, public+all role dashboards, BFF API
│       ├── src/app/                 # route groups /public /citizen /territory /agency /admin
│       ├── src/components/          # custom UI design system
│       ├── src/lib/                 # validators, auth client, safe API client, labels
│       ├── src/server/              # server-side domain services, authz and RBAC
│       └── tests/                   # UI + integration
├── services/
│   ├── ai/                          # FastAPI, anomaly inference, optional face ONNX
│   │   ├── app/api/                 # internal endpoints
│   │   ├── app/models/              # model loading + schemas
│   │   ├── app/security/            # M2M auth, content validation
│   │   ├── tests/
│   │   └── Dockerfile
│   ├── government-simulator/        # synthetic-only integration + failure toggles
│   └── worker/                      # outbox, retries, notifications and AI scheduling
├── packages/
│   ├── db/                          # Prisma schema or equivalent, migrations, seeds
│   ├── contracts/                   # Zod/JSON Schema/OpenAPI; generated typed client
│   ├── policy/                      # shared enum defs, status transition specs
│   ├── ui/                          # optional reusable primitives, original compositions
│   └── config/                      # lint, tsconfig, formatter
├── data/
│   ├── synthetic/                   # synthetic household/program seeds only
│   └── public-knowledge/            # curated approved source metadata/FAQ, no private PII
├── scripts/                          # seed, smoke, model benchmark, demo reset
├── docs/                             # architecture, ADR, privacy, API, test evidence
├── .github/workflows/                # CI lint, tests, build, audit, deploy
├── .env.example
├── docker-compose.yml
├── package.json
├── pnpm-workspace.yaml
└── README.md
```

*Gunakan versi dependency yang dikunci lockfile dan dokumentasikan minimum runtime; jangan mengasumsikan versi terbaru tanpa uji kompatibilitas. Format folder contoh dapat disesuaikan selama boundary fungsi tetap sama.*

### 23.2 Tooling minimum yang disarankan

| Area | Stack baseline | Catatan |
|---|---|---|
| Frontend | Next.js + TypeScript + Tailwind | desain mandiri, bukan template website |
| Validation | Zod shared schema + backend Zod/server DTO | schema security tetap di server |
| DB | Postgres + Prisma/Drizzle (pilih satu) | migration, tx, indexes |
| Auth | Auth.js/managed auth dengan adapter aman | MFA petugas, object-level authorization |
| Testing UI | Vitest/RTL + Playwright | mock service/provider untuk CI |
| API contracts | OpenAPI + Zod/JSON Schema | generated clients/contract tests |
| AI | FastAPI + Pydantic + scikit-learn + ONNX Runtime CPU | ephemeral face; pins/hashes |
| Worker | TS/Node or Python | Postgres outbox; idempotency |
| Hosting | Vercel + Managed Postgres + CPU container provider | alternatif VPS Compose |

### Diagram 28 — CI/CD, gate keamanan, dan deploy

```mermaid
flowchart TD
  GIT["GitHub Monorepo PR"] --> CI["CI: lint + typecheck + unit tests"]
  CI --> DB["DB migration & policy tests"]
  DB --> SEC["Secrets scan + dependency audit + authorization tests"]
  SEC --> BUILD["Next.js + FastAPI + Worker build"]
  BUILD --> E2E["Playwright E2E with synthetic seed + WA/LLM mock"]
  E2E --> REVIEW{"All gates passed?"}
  REVIEW -->|No| FIX["Fix PR, re-run checks"]
  FIX --> CI
  REVIEW -->|Yes| STAGE["Deploy staging + smoke tests"]
  STAGE --> APPROVE["Human release approval + demo checklist"]
  APPROVE --> PROD["Deploy hosted demo"]
  PROD --> HEALTH["Health checks, logs, queue, latency"]
  HEALTH -->|Regression| ROLLBACK["Rollback application/model safely; DB recovery plan"]
  HEALTH -->|Healthy| DEMO["Record demo video + provide URL + repo"]
```

### 23.3 Engineering rules and acceptance per PR

- Setiap endpoint baru: DTO/schema validation, permission gate, scoped DB query, structured errors, audit event bila mutasi, tests.
- Setiap status baru: definisi transisi formal, reason code, UI label dan guards, API contract, integration tests.
- Setiap integrasi eksternal: mock adapter, timeout, fallback, retry, idempotency, service health, failure tests.
- Setiap fitur AI: model version+feature schema+metric, input minimization, no auto adverse decision, manual path, API test.
- Setiap halaman: responsive, loading/empty/error/success, keyboard, feedback user, no misleading government claims.
- Jangan menambahkan dummy CTA yang tidak berfungsi. Jika modul masih P2, sembunyikan di produksi demo atau beri status `Demo/Belum Tersedia` yang jelas.

---

## 24. TEST STRATEGY, QA, SECURITY SCENARIOS DAN ACCEPTANCE TESTS

### 24.1 Test levels

| Lapisan | Tool/metode | Wajib |
|---|---|---|
| Unit | Vitest/Jest untuk rules; pytest untuk AI | policy, transition, scoring, fallback |
| DB/Integration | Test Postgres migrations + transaction fixtures | unique/version constraints, outbox, audit |
| API contract | Supertest/HTTP client+OpenAPI | schema, status, rate limit, error contract |
| Security | matrix RBAC/agency/jurisdiction, session & CSRF, upload & BOLA | negative authorization tests |
| Frontend E2E | Playwright mobile+desktop | 4/5 skenario utuh sampai DB |
| AI eval | data sintetis disjoint + failure/latency tests | avoid unsupported effectiveness claims |
| Face module opt-in | FMR/FNMR, uncertain/manual, PAD quality failure, RAM/CPU test | hanya jika face live diaktifkan |
| Accessibility | keyboard flow, labels, contrast, axe-core scan | P0 flows keyboard usable |
| Deployment | smoke test hosted URL, worker, AI health, simulator, fallback | run against staged hosted build |

### 24.2 Test matrix P0/P1/P2 (test IDs stabil)

| Test ID | Skenario | Ekspektasi |
|---|---|---|
| T-01 | Guest lihat katalog | info publik tampil tanpa login |
| T-02 | Guest akses detail kasus warga | 401/404 aman, tanpa data pribadi |
| T-03 | Warga daftar/login | sesi secure, role warga |
| T-04 | Warga mengajukan usulan data valid | nomor tiket DATA_PROPOSAL, status SUBMITTED, audit+outbox |
| T-05 | Warga mengajukan program mandiri yang OPEN | nomor tiket PROGRAM_APPLICATION terpisah |
| T-06 | Program officer-only, warga mencoba submit | ditolak di backend dengan alasan jalur, UI tidak menipu |
| T-07 | Di luar wilayah dan basis alamat verified | tidak bisa submit program itu, tampil jalan koreksi |
| T-08 | Alamat belum dapat dipastikan | minta klarifikasi, bukan ditolak permanen |
| T-09 | Program sebelum pembukaan/sesudah penutupan | alasan tanggal, tidak mengirim baru |
| T-10 | Program tanpa pendaftaran umum | tidak tampil tombol daftar; jalur resmi terlihat |
| T-11 | RT mengusulkan warga wilayah tugas | diterima bila representasi sah, tercatat actor+subject |
| T-12 | RT mengusulkan wilayah lain | deny; jangan baca profil/warga wilayah lain |
| T-13 | RT mencoba approve hasil | 403 dan event keamanan |
| T-14 | Operator memeriksa kasus | verification record, reason, audit |
| T-15 | Operator minta dokumen perbaikan | status REVISION_REQUIRED, pemohon lihat detail |
| T-16 | Pemohon revisi kasus | same ticket, new revision, AI reevaluate asynchronously |
| T-17 | Reviewer sama dengan verifier awal | 403/409 maker-checker enforced |
| T-18 | Reviewer independen meneruskan kasus | simulated ACK -> FORWARDED_SIMULATED, no official claim |
| T-19 | Simulator API gagal | pending/failed + retry, tidak klaim forward sukses |
| T-20 | Reviewer tutup kasus tidak dapat diproses | alasan/rule/doc version wajib tampil; tanpa itu ditolak |
| T-21 | Penolakan program karena aturan khusus | tidak ada auto reject oleh model AI |
| T-22 | Warga ajukan sanggahan | appeal ticket, independent reviewer, saved evidence |
| T-23 | Sanggahan setelah waktu sesuai program | tampil ketentuan/kanal lain, tidak silently fail |
| T-24 | Appeal corrected | decision lama tetap pada history, new outcome audited |
| T-25 | Warga isi feedback negatif | tidak mengubah case status/eligibility |
| T-26 | Warga ajukan complaint pelayanan | ticket COMPLAINT, triage berbeda dari APPEAL |
| T-27 | Pengelola edit published version | versi lama immutable, buat draft versi berikut |
| T-28 | Publisher sama dengan author draft | deny jika kebijakan maker-checker publikasi berlaku |
| T-29 | Staff instansi A baca kasus instansi B | deny, termasuk file download |
| T-30 | Admin teknis mencoba menolak warga | deny, admin tidak punya decision permission default |
| T-31 | AI anomaly membubuhkan flag | score+version+source flag; bukan automatic denial |
| T-32 | AI server down | submit tetap selesai; manual review tersimpan |
| T-33 | LLM chat pertanyaan resmi | jawaban bersitasi sumber approved, no fabricated laws |
| T-34 | LLM down/no source | FAQ fallback, label keterbatasan |
| T-35 | WhatsApp menu statis | tidak memanggil LLM, menu/state konsisten |
| T-36 | WA message duplikat/replay | diproses idempotent sekali |
| T-37 | WA cek status hanya tiket tanpa auth | informasi privat tidak dibuka |
| T-38 | Face opt-in gagal match/PAD | manual route, bukan penolakan bansos |
| T-39 | Public stats kelompok berjumlah 1 | suppression, tidak dapat infer identitas |
| T-40 | HTTP submit diulang saat network error | satu tiket untuk satu idempotency key |
| T-41 | File tipe berbahaya/disguised MIME | ditolak; private object key tidak leak |
| T-42 | CSRF/session revoked/expired | mutasi diblokir, user diarahkan login ulang |
| T-43 | Webhook WA signature invalid | 401/403 sesuai protokol dan tidak diproses |
| T-44 | LLM prompt injection di dokumen sumber | retrieval diperlakukan data, tidak keluar dari approved references |
| T-45 | Health endpoint umum | tidak membocorkan secret/token/path private |
| T-46 | Backup/restore staging | data kasus + audit + referensi berkas terpulihkan |
| T-47 | Pertanyaan publik memuat NIK | input difilter, no PII forwarding to model provider |
| T-48 | Tidak ada koneksi pada submit | UI retry yang tidak menyebabkan duplikasi |

### 24.3 Contoh BDD/Gherkin untuk kasus ditolak dan sanggahan

```gherkin
Feature: Keputusan pengajuan dengan alasan dan sanggahan
  Scenario: Pengajuan mandiri tidak dapat diproses setelah verifikasi
    Given terdapat program demo aktif dengan pendaftaran mandiri
    And warga memiliki pengajuan SUBMITTED
    And operator sudah meminta perbaikan yang tidak terpenuhi
    When reviewer independen menetapkan CLOSED_UNPROCESSABLE
    Then backend mewajibkan reason_code dan rujukan aturan program
    And status warga menunjukkan alasan yang mudah dipahami
    And timeline menyimpan actor, time, dan riwayat revisi
    And warga yang berhak melihat aksi sanggahan bila program mengizinkan

  Scenario: Hasil sanggahan mengoreksi keputusan awal
    Given warga memiliki hasil CLOSED_UNPROCESSABLE dan appeal aktif
    When reviewer appeal yang berbeda mengubah hasil menjadi DECISION_CORRECTED
    Then keputusan awal tetap ada pada audit history
    And outcome terbaru di case ditampilkan dengan alasan
    And pemberitahuan tercatat di outbox
    And hasil model AI tidak dijadikan alasan tunggal keputusan
```

### 24.4 KPI target internal sebelum kirim lomba

| KPI target (bukan hasil terukur) | Syarat ukur |
|---|---|
| 100% P0 test cases `T-01` s.d. `T-30` yang relevan lulus | attach evidence CI+E2E |
| Tidak ada 401/403 bypass lintas role/wilayah pada suite keamanan | negative tests wajib |
| Semua pengajuan, revisi, review, appeal, publish memiliki audit | DB assertions |
| Zero duplicate ticket pada retry idempotency | e2e test network interruption |
| Katalog publik dapat digunakan pada mobile 360 px | Playwright mobile screenshots + keyboard |
| AI/LLM/WA unavailable tidak merusak proses utama | chaos/failure tests |
| LCP / responsivitas sesuai anggaran yang ditentukan setelah baseline | Lighthouse+RUM; jangan klaim tanpa pengukuran |
| 4-5 demo skenario selesai tanpa mengedit DB manual | test run recorded |

---

## 25. TRANSPARENCY, AUDIT, ANALYTICS, METRICS DAN INCIDENT RESPONSE

### 25.1 Statistik yang boleh dipublikasikan

| Metrik publik | Agregasi | Perlindungan |
|---|---|---|
| Program aktif | count per provider/region | only approved public program |
| Usulan masuk | count monthly/territory | minimum group size; suppress small cells |
| Perlu perbaikan / dalam review / selesai | count by status | no case level data |
| Median waktu pelayanan | duration from submitted to handled | data sufficient; explain methods |
| Pengaduan selesai | aggregated ratios | no victim/complainant identity |
| Jumlah kasus diteruskan (simulasi) | count separated from official outcomes | label **SIMULASI** besar |

Metrik “bansos tersalurkan” **jangan** ditampilkan bila simulator hanya menirukan proses pengusulan. Jika kelak ada penyaluran resmi, data berasal dari instansi berwenang dengan sumber, tanggal dan definisi angka. Gunakan latensi/perbedaan cakupan angka yang jelas, bukan klaim *real time pemerintah*.

### 25.2 Audit event schema yang disarankan

```json
{
  "event_id": "uuid",
  "request_id": "req_...",
  "actor_type": "USER|SERVICE",
  "actor_id": "user_or_service_uuid",
  "actor_role_at_time": "OPERATOR_DESA",
  "action": "CASE_REQUEST_REVISION",
  "resource_type": "CASE",
  "resource_id": "case_uuid",
  "old_state": "UNDER_REVIEW",
  "new_state": "REVISION_REQUIRED",
  "reason_code": "DOCUMENT_MISSING",
  "policy_version_id": "program_version_uuid",
  "territory_scope_id": "territory_uuid",
  "timestamp_utc": "2026-11-19T10:00:00Z",
  "metadata_redacted": true
}
```

Simpan *before/after hash* atau struktur perubahan yang telah dimask, bukan seluruh dokumen PII. Audit berbasis peran, dengan larangan edit/hapus reguler, review akses dan kontrol retensi sesuai kebutuhan kebijakan. Jika kasus yang belum terotorisasi menyangkut dugaan penyimpangan, gunakan akses auditor independen yang benar, bukan membuka data ke publik.

### 25.3 Observability minimum

- Correlation ID yang sama dari frontend/API/outbox/AI/WA/gov simulator, tanpa NIK di ID.
- Logs: `request_id`, service, endpoint, status code, duration_ms, error code, hashed user id bila perlu. **Tidak** logging full payload form, foto, token, transkrip privat.
- Metrics: API response time, error rate, rate limit hits, DB connection pool, outbox pending/retries, AI model ready/latency/oom, LLM tokens/cost, WA delivery state, appeal backlog, program submission counts.
- Alerts untuk DB unavailable, AI repeated OOM, high error rate, queue stuck, unexpected status transition, cross-scope attempts meningkat.
- Public `/healthz` minimal liveness; private `/readyz` untuk readiness/dependency, tanpa secrets.
- Incident runbook: isolate/revoke API token, block abuse, disable face feature, switch FAQ mode, roll back, preserve log bukti, beri pemberitahuan sesuai kebutuhan hukum/kontrak/risiko.

### Diagram 29 — Transparansi publik vs pengawasan internal

```mermaid
flowchart TD
  CASE["Cases, decisions, corrections, complaints"] --> AUDIT["Restricted audit events"]
  CASE --> AGG["Aggregate query / safe statistics engine"]
  AUDIT --> REVIEW["Auditor dashboard with scoped access"]
  AGG --> SUPPRESS["Threshold suppression + noise policy if appropriate"]
  SUPPRESS --> PUBLIC["Public Transparency Center"]
  CASE --> OWN["Owner-specific case timeline"]
  OWN --> AUTH["Authenticated citizen view"]
  AUDIT -.-> PRIVATE["Never publish raw audit/PII"]
  PRIVATE -.-> PUBLIC
```

---

## 26. LEGAL / REGULATORY REFERENCE, RULEBOOK TRACEABILITY, SCOPE CAVEATS

**Peringatan:** daftar berikut adalah referensi awal perancangan, **bukan opini hukum atau sertifikasi kepatuhan**. Validasi ulang peraturan, pedoman teknis, aturan setiap program bansos, dan kontrak pemrosesan data ketika berpindah dari **demo sintetis** ke layanan publik nyata.

| Referensi terverifikasi | Makna terhadap desain | Dokumen/situs |
|---|---|---|
| Permensos No. 3 Tahun 2025 (DTSEN) | Jalur usulan masyarakat dan jalur pemda berbeda; BantuTepat tidak otomatis memasukkan ke DTSEN/SIKS-NG | https://peraturan.bpk.go.id/Details/323075/permensos-no-3-tahun-2025 |
| UU No. 27 Tahun 2022 (PDP) | data biometrik spesifik; pembatasan tujuan, keamanan, dasar pemrosesan, hak subjek, evaluasi risiko | https://jdih.komdigi.go.id/produk_hukum/view/id/832/t/undangundang%2Bnomor%2B27%2Btahun%2B2022 |
| PermenPANRB No. 17 Tahun 2026 (SP4N) | desain pengaduan harus kompatibel dengan tata kelola pengaduan publik; tidak mengklaim terintegrasi SP4N tanpa otorisasi | https://peraturan.bpk.go.id/Details/359268/permen-panrb-no-17-tahun-2026-sistem-pengelolaan-pengaduan-pelayanan-publik-nasi |
| UU No. 14 Tahun 2008 (KIP) | transparansi informasi publik dengan pengecualian/privasi | https://peraturan.bpk.go.id/Details/39047/uu-no-14-tahun-2008 |
| UU No. 25 Tahun 2009 (Pelayanan Publik) | kejelasan prosedur, standar layanan, penyelesaian pengaduan | https://peraturan.bpk.go.id/Details/38748/uu-no-25-tahun-2009 |
| Rulebook Web App Techcomfest 2027 (dokumen unggahan pengguna) | tema, bobot 25/30/25/20, larangan template, video+web+GitHub, monorepo | https://techcomfest.ukmpcc.org/pdf/rulebook/Rulebook%20WEB%20APP%20Techcomfest%202027.pdf |
| Meta WhatsApp Business docs | webhook, pesan interaktif/template, onboarding nomor dan kebijakan pesan | https://developers.facebook.com/docs/whatsapp/cloud-api/ |
| Render FastAPI hosting docs | deployment Python API container/worker; resource allocation dan healthcheck | https://render.com/docs/deploy-fastapi |
| Vercel Functions docs | Next.js serverless runtimes, resource/timeouts harus diperhitungkan | https://vercel.com/docs/functions |
| Supabase RLS docs | perlindungan row-level bila memakai Supabase Data API, jangan expose privileged service keys | https://supabase.com/docs/guides/database/postgres/row-level-security |
| OWASP API Security Top 10 | object-level auth, function auth, rate limits, API trust model | https://owasp.org/API-Security/editions/2023/en/0x11-t10/ |
| InsightFace/ONNX provider/model licenses | kode/library license berbeda dari lisensi bobot wajah | https://github.com/deepinsight/insightface/ |

**Rujukan kontekstual lain yang perlu dipastikan saat produksi:** ketentuan akses data kependudukan Dukcapil; pengaturan PSE/SPBE; dasar pemanfaatan DTSEN lintas instansi; pengelolaan kanal pengaduan; kewajiban pemrosesan data pada layanan pihak ketiga dan lintas batas; tata kelola dokumentasi arsip; aturan distribusi setiap program. Jangan mengasumsikan izin akses hanya dari keberadaan endpoint publik.

**Penjelasan birokrasi utama untuk Codex:** sistem harus membedakan *identitas pengusul*, *subjek/rumah tangga yang diusulkan*, *otoritas pemeriksa*, *otoritas reviewer*, *instansi pemilik program*, *publisher program*, *saluran resmi penerusan*, dan *hasil substantif*. Semua itu **tidak** dapat diturunkan dari satu kolom `is_admin`.

---

## 27. TEAM PLAN, PRIORITY AND DELIVERY MILESTONES

### 27.1 Priority gates

| Gate | Fitur | Rekomendasi |
|---|---|---|
| **P0 CORE** | Auth/RBAC wilayah, program katalog+manager, case submission, RT usulan, operator verify, reviewer independent, rejection reason, revision, appeal, audit, transparency | **Tidak boleh dipotong** |
| **P1 INNOVATION** | Gov Simulator, anomaly model, LLM+RAG on web, WA menu statis/mock | implementasi sesudah P0 lolos e2e |
| **P2 ADVANCED** | Face recognition 1:1 CPU opt-in, OCR, sophisticated dashboards, active gov integrations | opsional, jangan mengancam deployment |

### 27.2 Rencana tim maksimal 3 orang

| Person | Domain utama | Deliverable |
|---|---|---|
| Member A — UI/UX Frontend | publik, katalog, wizard warga, tracking, transparansi, responsive, design assets | polished responsive web + E2E UI |
| Member B — Backend/Data/Security | schema, authz, program, case, transitions, audit, outbox, govt simulator | API + DB + permission tests + deploy backend |
| Member C — AI/Integration/Quality | anomaly, web RAG, WA static, face CPU optional, test + demo video | AI service, adapters, benchmarks, QA |

**Shared duty:** setiap anggota wajib mampu menjelaskan kode yang dibuat dengan bantuan AI; video demo dan presentasi harus menampilkan alur nyata dan perbedaan data simulasi versus resmi.

### 27.3 Milestone (berdasar tenggat rulebook 18 Desember 2026)

| Periode rencana | Hasil yang harus terukur |
|---|---|
| 19–24 Nov 2026 | repository, desain sistem orisinal, auth+RBAC wilayah, DB/schema/seed, katalog |
| 25 Nov–1 Des 2026 | 2 program demo, mandiri+RT submit, verifikasi operator/reviewer, ticket/status |
| 2–7 Des 2026 | reasoned rejection, revision, appeal, feedback, audit, transparansi, e2e |
| 8–12 Des 2026 | simulator, anomaly model, chatbot web API + fallback, WA mock/Meta jika siap |
| 13–15 Des 2026 | cloud deploy, hardening, performance, mobile, smoke, face opt-in hanya jika siap |
| 16–18 Des 2026 | video demo, GitHub monorepo, dokumentasi, final link checks, penyerahan sebelum tenggat |

**Deadline:** 18 Desember 2026 **pukul 23.59 WIB** berdasarkan rulebook yang diberikan. Jadwalkan cut-off internal sebelum itu; tanggal pengumuman final/agenda final di rulebook ada bagian yang perlu dikonfirmasi dengan panitia (tidak memengaruhi scope MVP).

---

## 28. DEMO SCRIPT & NARRATIVE TO MAXIMIZE COMPETITION SCORE

**Jangan hanya presentasi fitur, tunjukkan sebab-akibat tindakan nyata dan perubahan status dalam database.** Demo menggunakan akun fiktif `warga_a`, `rt_desa_a`, `operator_desa_a`, `reviewer_kab_a`, `program_manager_kab_a`, `publisher_kab_a`, `auditor_kab_a`, `super_admin` dengan password disimpan sebagai secret, bukan di dokumen publik.

### Skenario Demo 1 — Program nasional vs lokal & transparansi

1. Pengelola program login, membuat draft program kabupaten (scope KAB_A, mode BOTH) dan program umum tanpa pendaftaran langsung.
2. Akun publisher berbeda melakukan review dan publikasi.
3. Pengunjung melihat katalog, memfilter wilayah, memilih program, membaca sumber kebijakan dan periode.
4. Akun warga luar kabupaten melihat alasan mengapa tidak dapat mendaftar program lokal; tetap dapat membaca katalog dan jalur koreksi data.
5. Tampilan Transparency Center menunjukkan statistik agregat sebelum/sesudah pendaftaran dengan label data simulasi.

### Skenario Demo 2 — Warga mandiri ditandai AI, diminta revisi, lalu selesai

1. Warga di KAB_A mengisi dan mengirim pengajuan; tiket dibuat dan status terkirim.
2. Backend rules memeriksa kelengkapan/duplikat; Government Simulator mengembalikan data sintetis; anomaly service memunculkan flag yang dapat dijelaskan.
3. Operator membaca flag *bukan keputusan*, meminta perbaikan dengan alasan.
4. Warga menerima notifikasi, melakukan revisi pada **tiket sama**; pemeriksaan baru tercatat sebagai revision baru.
5. Reviewer independen meneruskan ke simulator, status `FORWARDED_SIMULATED` dengan ACK, timeline/audit memperlihatkan aktor dan waktu.

### Skenario Demo 3 — Penolakan dan sanggahan

1. Warga membuat pengajuan lain dengan persyaratan administratif yang belum dipenuhi (contoh dokumen wajib kurang setelah kesempatan revisi).
2. Operator memeriksa; reviewer berwenang menyatakan `CLOSED_UNPROCESSABLE` dengan alasan/rule version.
3. Warga melihat alasan, memilih **Ajukan Sanggahan**, mengirim bukti baru.
4. Reviewer sanggahan **berbeda** memeriksa dan memilih `RECHECK_REQUIRED` atau `DECISION_CORRECTED`; hasil baru ditampilkan tanpa menghapus keputusan lama.
5. Warga menyampaikan feedback; pengawas melihat audit dan agregat tanpa menampilkan identitas ke publik.

### Skenario Demo 4 — RT, jurisdiction, security, dan WhatsApp

1. RT_A login, mengusulkan warga hanya dalam RT/wilayah terotorisasi dan mengisi bukti representasi yang sah (akun demo).
2. Upaya mengakses warga RT_B ditolak backend; tampilkan UI aman tanpa bocor data.
3. WA sandbox menu 1 = informasi, menu 2 = tautan pengajuan, menu 3 = cek status lewat auth, tanpa panggilan LLM.
4. Sementara itu chatbot LLM web menjawab syarat program berdasarkan knowledge terverifikasi, memberikan rujukan; bila provider mati fallback FAQ.
5. Opsional: face 1:1 demo relawan; jika `NO_MATCH` masuk jalur manual, **bukan penolakan bantuan**.

### 28.1 Video demo storyboard (indikatif, tidak ada persyaratan durasi yang ditetapkan dokumen ini)

| Segment | Visual/aksi | Sisi nilai lomba |
|---|---|---|
| Pembuka | masalah bansos salah sasaran + batasan demo/simulasi | kesesuaian tema |
| Program | buat + publish + filter wilayah | fungsionalitas & UI |
| Warga | ajukan mandiri, nomor tiket, status | layanan end-to-end |
| Verifikasi | AI flag, operator+reviewer, audit | inovasi & akuntabilitas |
| Keberatan | penolakan beralasan, sanggahan, hasil | fairness & public service |
| Omnichannel | WhatsApp statis + LLM web | efisiensi & aksesibilitas |
| Transparansi | dashboard publik agregat vs dashboard pengawas | SDG 16 |
| Penutup | keterbatasan simulator; URL live, repo, teknologi | kredibilitas demo |

---

## 29. CODEX EXECUTION INSTRUCTIONS — COPY THIS INTO YOUR CODING SESSION

> **Catatan:** bagian ini dibuat agar Codex dapat mulai menulis software dari spesifikasi, tanpa menebak prioritas. Jangan mengklaim selesai sebelum test nyata lulus.

```text
You are the lead full-stack engineer for BantuTepat. Read the ENTIRE attached
BantuTepat_MASTER_SPEC_Codex_FINAL.md before implementing. The document is
normative for business workflow, security controls, roles, policy scope, AI,
frontend, backend, schema, and acceptance tests.

MISSION
Build a deployable, original-design, Indonesian-language monorepo web app
for a public-service competition: multi-role benefits program management,
citizen/self data proposal vs program application, RT officer-assisted proposal,
review/appeal/complaint/feedback, audit, transparency dashboard, synthetic
government API, tabular anomaly model, web-only LLM + approved RAG, and a
menu-driven WhatsApp static adapter. Face verification is optional and
CPU-hosted in a separately deployable FastAPI service, opt-in demo-only with
manual fallback; never fake an official identity check.

NON-NEGOTIABLE
- No government production integration. All datasets/demo identities synthetic.
- Never auto-deny/approve eligibility from AI or LLM.
- Server-side RBAC + agency/territory/object scope per API, maker-checker.
- Separate DATA_PROPOSAL and PROGRAM_APPLICATION state machines.
- Reasoned outcomes, revision and independent appeals, audit, public stats
  with privacy controls, accessibility and mobile responsive UI.
- WhatsApp must be RULE/MENU STATE MACHINE only, no LLM.
- No prebuilt website template prohibited by contest rules.
- API keys, service tokens, and faces must NEVER be exposed to browser/repo/log.

IMPLEMENTATION ORDER
(1) Repo/DB/Auth/RBAC; (2) programs+publishing; (3) cases+RT;
(4) verifier/reviewer+reasoned rejection+appeal+audit+public stats;
(5) synthetic gov API+anomaly AI; (6) RAG web chatbot;
(7) WA static bot; (8) optional face 1:1; (9) test/deploy/docs/demo.

WORKING RULES
- Build actual endpoints and DB actions, no inert buttons or hardcoded fake AI
  outputs without an explicit MOCK badge.
- On each milestone run lint, typecheck, tests, migrations and manual smoke.
- Document architecture decisions and tradeoffs; use feature flags, fallbacks,
  versioned models/rules, and synthetic seed fixtures.
- Report files changed, commands tested, test results, URL only if it exists,
  and any remaining blockers. Do not represent incomplete work as complete.

SUCCESS CRITERIA
Hosted Next.js website with working separate role dashboards, schema+migrations,
real API transaction flows, 4 end-to-end demos including failure paths,
functional audit and privacy controls, documented CPU AI deployment, and a
GitHub monorepo suitable for submission. If integration unavailable, provide
honest degraded mode and explicit operator instructions.
```

---

## 30. FINAL DEFINITION OF DONE — CHECKLIST UNTUK CODEX DAN TIM

### 30.1 Feature completeness

- [ ] Katalog program dan detail per wilayah; approved/active version + dasar aturan.
- [ ] Program draft → review publisher independen → publish; versions immutable.
- [ ] Warga submit `DATA_PROPOSAL` dan `PROGRAM_APPLICATION` yang tidak tercampur.
- [ ] RT/RW terotorisasi mengusulkan warga hanya dalam scope, dengan representasi sah.
- [ ] Operator periksa data, minta perbaikan, kirim rekomendasi.
- [ ] Verifikator independen review dan hasil beralasan; forwarding simulator dengan ACK/fail.
- [ ] Pengajuan gagal/revisi/tidak dapat diproses/ditolak menurut aturan memunculkan next step.
- [ ] Sanggahan disimpan terpisah, reviewer independen, audit keputusan lama/baru.
- [ ] Pengaduan dan feedback terpisah, tidak memengaruhi hasil program.
- [ ] Transparency Center publik hanya agregat aman; Accountability Center internal terautentikasi.
- [ ] FAQ/LLM+RAG web-only; WhatsApp static no LLM; fallback jika kedua layanan down.
- [ ] Anomaly model benar-benar melakukan inference dari data sintetis dan mencatat versi/model; bukan hardcode dummy.
- [ ] Face demo jika aktif: CPU ONNX, consent/notice relevan, model license verified, PAD status jujur, no raw face retention, manual fallback.

### 30.2 Technical and security completeness

- [ ] Satu website, empat kelompok dashboard, tujuh role login dan akses Guest.
- [ ] Satu source of truth DB; API kontrak; migrations; seed sintetis.
- [ ] RBAC/agency/territory/object checks 100% endpoint; session security/MFA petugas.
- [ ] No real NIK/biometric/dokumen warga; secrets server-only; private file bucket.
- [ ] Validasi file/upload/CSRF/rate limit; idempotency, audit append-only, outbox worker.
- [ ] Service degradation tests: Face/AI/LLM/WA/Gov Simulator down.
- [ ] Next.js URL live, internal FastAPI endpoint secured, managed DB backup, deployment docs.
- [ ] CI lint+typecheck+unit+integration+E2E+security tests berhasil; responsif mobile.
- [ ] README, `.env.example`, `docker-compose.yml`, seed, test users, demo runbook tersedia.
- [ ] GitHub monorepo, link demo video dan hosted URL siap diserahkan.

### 30.3 Release gates

- **Gate 1 — Core Flow:** P0 seluruh happy+unhappy paths lulus.
- **Gate 2 — Security:** cross-user/cross-region/privilege bypass tidak ada pada test.
- **Gate 3 — Integrations:** simulator, AI anomaly, web RAG, WA mock di-host/tested; face optional.
- **Gate 4 — Judge Experience:** 4 skenario E2E, responsive design, URL stabil dan video runtut.
- **Gate 5 — Honesty:** semua data simulasi jelas; tidak ada klaim pemerintah/AI/efektivitas tanpa evidence.

---

## 31. INDEX SEMUA DIAGRAM MERMAID DI DOKUMEN INI

> Seluruh diagram berada **langsung di file Markdown ini**, bisa dirender oleh GitHub/VS Code (Mermaid Markdown Preview), dan dapat disalin ke draw.io melalui Insert > Advanced > Mermaid jika versi diagrams.net mendukungnya. Beberapa diagram sequence/state dapat perlu penyesuaian kecil menurut renderer.

| Nomor | Judul diagram | Bagian |
|---|---|---|
| 01 | Role hierarchy dan hubungan tugas | 3 |
| 02 | Sitemap frontend | 4 |
| 03 | Arsitektur high-level/security boundary | 5 |
| 04 | Backend transaction & event outbox | 5 |
| 05 | ERD konseptual | 6 |
| 06 | Master end-to-end | 7 |
| 07 | Program management lifecycle | 8 |
| 08 | Usulan/koreksi data | 9 |
| 09 | Pendaftaran program dengan geographic/mode rules | 10 |
| 10 | Swimlane multi-aktor | 11 |
| 11 | State machine case & appeal | 12 |
| 12 | Penolakan, sanggahan, feedback | 13 |
| 13 | Transparansi publik dan audit | 13 |
| 14 | WA static bot & web handoff | 14 |
| 15 | AI anomaly + fallback | 15 |
| 16 | LLM + RAG web-only | 15 |
| 17 | Face 1:1 + PAD + manual fallback | 15 |
| 18 | Sequence submit warga → AI → petugas | 17 |
| 19 | Sequence keputusan dan sanggahan | 17 |
| 20 | Secure login dan authorization | 17 |
| 21 | Security trust boundaries PII/face | 18 |
| 22 | Frontend navigation, UI paths | 19 |
| 23 | Form loading, errors, retries, success | 19 |
| 24 | Backend policy evaluation | 20 |
| 25 | Async outbox event / WA / AI failure | 20 |
| 26 | AI isolation + graceful fallback | 21 |
| 27 | Hosting architecture | 22 |
| 28 | CI/CD pipeline | 23 |
| 29 | Transparency vs auditor separation | 25 |

### Diagram 30 — Ringkasan keputusan akhir BantuTepat

```mermaid
flowchart TD
  POLICY["Authorized Agency: create approved program policy"] --> CATALOG["Public Program Catalog"]
  CATALOG --> SELF["Citizen direct application when allowed"]
  CATALOG --> OFFICER["RT/Desa proposal within mandate"]
  SELF --> CASE["Central Case API + versioned program rules"]
  OFFICER --> CASE
  CIT["Citizen data update proposal without program"] --> CASE
  CASE --> RISK["Rule engine + optional AI anomaly flags"]
  RISK --> VERIFY["Assigned operator verifies evidence"]
  VERIFY --> REVIEW["Independent authorized reviewer"]
  REVIEW --> OUTCOME{"Reasoned process outcome"}
  OUTCOME -->|Forwarded simulation| FORWARD["Synthetic Gov API ACK"]
  OUTCOME -->|Unprocessable / Not selected| REASON["Reason, legal reference, next steps"]
  FORWARD --> TRACK["Citizen tracking, web inbox, optional WA notification"]
  REASON --> TRACK
  TRACK --> APPEAL["Appeal, complaint or feedback as distinct flows"]
  APPEAL --> AUDIT["Restricted audit + safe public aggregates"]
  REVIEW --> AUDIT
  WEBCHAT["Web-only LLM+RAG FAQ"] -.-> CATALOG
  WAMENU["WA STATIC menus; no LLM"] -.-> TRACK
  FACE["Opt-in Demo Face 1:1 + manual fallback"] -.-> CASE
```

---

## 32. OPEN DECISIONS / RISKS (WAJIB DIDOKUMENTASIKAN)

| Keputusan/risiko | Default implementasi yang aman | Trigger untuk mengubahnya |
|---|---|---|
| Apakah BantuTepat adalah sistem pemerintah? | **Tidak**, prototipe simulasi lomba | izin kerja sama yang nyata |
| Apakah semua wilayah Indonesia aktif? | Data model nasional, demo 1 kabupaten & beberapa desa sintetis | data dan mandat daerah tersedia |
| Apakah warga bebas daftar semua program? | **Tidak**; setiap program mempunyai mode, wilayah, periode & rules | aturan program tertentu berubah |
| Apakah RT otomatis dapat akses? | **Tidak**; undangan+scope+masa berlaku | verifikasi mandat instansi |
| Apakah AI bisa menolak bantuan? | **Tidak** | jangan ubah; keputusan manusia berwenang |
| Apakah foto KTP user cukup identitas? | **Tidak**; verified reference demo dan manual path | sumber identitas tepercaya berizin |
| Apakah WA punya LLM? | **Tidak**; menu statis saja | user telah memilih demikian; jangan ubah tanpa permintaan |
| Apakah setiap program mempunyai appeal identik? | **Tidak**; program-specific rules dan kanal pengaduan | aturan instansi diverifikasi |
| Apakah hasil diteruskan = penerima resmi? | **Tidak**; `FORWARDED_SIMULATED` dibedakan | penerusan resmi + keputusan tersedia |
| Apakah provider gratis cukup? | Tidak diasumsikan | pengukuran RAM/biaya dan SLA nyata |
| Apakah deployed face model otomatis legal? | **Tidak**; risk assessment, lisensi, consent/basis, PAD, manual fallback | persyaratan lengkap dipenuhi |
| Apakah hasil model sintetik valid di populasi nyata? | **Tidak** | validasi eksternal sah dan berkualitas |

### 32.1 Audit isi spesifikasi terhadap permintaan pengguna

- [x] Konsep website dan 7 role + guest, tabel tugas dan pembatasan.
- [x] Perbedaan frontend dan backend, sitemap/route, modul API, DTO, database & worker.
- [x] Program nasional/lokal, buka/tutup pendaftaran, jalur mandiri vs RT, pembatasan wilayah.
- [x] End-to-end success, revision, rejection, appeal, complaint, feedback, tracking.
- [x] Transparency & Accountability Center.
- [x] AI anomaly, LLM via API hanya web, WA static, face 1:1 opsional lengkap fallback.
- [x] Security by design, hosting realistis, komponen deployment + secrets + failure modes.
- [x] Rulebook Techcomfest bobot penilaian & video demo/monorepo requirements.
- [x] Mermaid diagram lengkap dalam satu file `.md`.
- [x] Testing acceptance scenarios, demo scripts, Codex instructions, checklist.

**Dokumen ini adalah blueprint untuk membangun software. Semua checklist `[x]` pada 32.1 hanya berarti spesifikasi tertulisnya lengkap; **bukan** berarti aplikasi atau fitur sudah diimplementasikan.**

