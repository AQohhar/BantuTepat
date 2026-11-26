# BantuTepat — Requirements (FR, NFR, User Stories & Backlog)

**Versi 1.2 | 26 November 2026 | Draft requirements, bukan bukti implementasi**

Acuan: PRD §2–6, §14–15, §18–20; Master Specification §2, §27; rulebook. Baca `01_PRD.md` untuk scope, prioritas dan Open Decisions. Penomoran bagian yang diambil dari master dipertahankan. Kontrak v1.2 adalah kandidat freeze untuk persetujuan tim; sumber historis tidak mengungguli kontrak ini.

## 2. Problem statement dan sasaran

| Masalah | Kebutuhan produk | Bukti keberhasilan demo |
|---|---|---|
| Informasi program tersebar dan syarat tidak jelas | Katalog, detail, cakupan, periode dan mode | Warga menemukan program dan jalur yang benar |
| Data sosial ekonomi perlu diperbaiki | Usulan data mandiri/representasi | Tiket usulan tidak bergantung pada program |
| Progres administratif sulit dipantau | Timeline kasus dan aksi berikutnya | Warga dapat melihat status, revisi dan alasan |
| Hasil bermasalah sulit dikoreksi | Sanggahan independen dan pengaduan | Keputusan lama/baru tercatat; keluhan punya tiket sendiri |
| Akses dan keputusan petugas sulit diawasi | Mandat, maker-checker, audit internal | Percobaan akses lintas wilayah dan self-approval diblokir |
| Pemeriksaan data memakan waktu | Rules deterministik dan AI triage | Flag dapat ditinjau manusia tanpa penolakan otomatis |

Hipotesis dampak: kejelasan prosedur dan akses tracking dapat menurunkan ketidakpastian pengguna, audit dapat meningkatkan keterlacakan, dan triage dapat memfokuskan pemeriksaan. Ukur pada demo; manfaat lapangan belum tervalidasi.

## 3. Scope, non-goals dan prioritas

| Prioritas | Scope | Gate |
|---|---|---|
| P0 — Core MVP | Auth, RBAC/scope, katalog dan publikasi, dua jenis pengajuan, representasi, verifikasi, revisi, hasil beralasan, appeal, complaint, feedback, FAQ statis, tracking, audit, transparansi | Semua alur dan negative tests lulus sebelum P1 |
| P1 — Innovation | Government Data Simulator, inference anomali asli, web LLM + RAG, WA menu statis/mock | Terintegrasi, fallback dan label simulasi lulus |
| P2 — Optional | Face verification 1:1 CPU opt-in, OCR, perluasan fitur AI | Hanya setelah P0/P1 stabil, dinonaktifkan default bila belum aman |

**Di luar scope:** koneksi resmi DTSEN/Dukcapil/SIKS-NG/SP4N, penetapan penerima atau pencairan bantuan, database warga nyata, face identification massal, AI yang memberi keputusan kelayakan, LLM di WhatsApp, akses publik ke data kasus individual. Model data dapat mendukung hierarki nasional; demo cukup satu kabupaten dan beberapa desa fiktif.

**Batas P0/P1:** P0 menguji rekomendasi/tindak lanjut internal tanpa mengklaim penerusan berhasil; adapter simulator nyata untuk demo baru aktif di P1. `FORWARDED_SIMULATED` tidak boleh muncul sebelum ACK simulator. Untuk kedua jenis pengajuan yang lolos review saat adapter belum aktif, gunakan `RECOMMENDED_FOR_FORWARD` sesuai kontrak v1.2. Jangan membuat ACK palsu.

## 4. Persona, jobs to be done dan user stories

Persona berikut adalah persona desain, bukan hasil wawancara.

| ID | Persona | User story | Outcome / FR |
|---|---|---|---|
| US-01 | Guest, pencari informasi | Sebagai pengunjung, saya ingin memahami syarat dan jalur program tanpa login | Informasi publik yang jelas; FR-01, FR-12 |
| US-02 | Warga, akses mobile | Saya ingin mengusulkan koreksi data tanpa dipaksa memilih program | Tiket usulan mandiri; FR-04 |
| US-03 | Warga, calon pendaftar | Saya ingin mendaftar program yang sesuai wilayah dan periode saya | Precheck dan tiket program; FR-05 |
| US-04 | Warga, pemohon | Saya ingin mengetahui progres, melengkapi data dan memahami hasil | Timeline dan revisi; FR-08, FR-09 |
| US-05 | Warga, keberatan | Saya ingin menyampaikan sanggahan atau keluhan sesuai masalah | Tiket terpisah dan hasil terjelaskan; FR-10, FR-11 |
| US-06 | RT/RW | Saya ingin membantu warga yang memberi mandat | Usulan representatif dengan scope; FR-06 |
| US-07 | Operator desa | Saya ingin memeriksa antrean, meminta revisi dan memberi rekomendasi | Pemeriksaan tercatat; FR-07 |
| US-08 | Verifikator instansi | Saya ingin review independen dan memberi alasan tindak lanjut | Keputusan terlacak; FR-08, FR-10 |
| US-09 | Pengelola program | Saya ingin mengatur program berdasarkan mandat dan aturan | Versi program dan review publikasi; FR-03 |
| US-10 | Auditor | Saya ingin memantau backlog, audit dan keluhan secara sah | Akuntabilitas internal; FR-11, FR-13 |
| US-11 | Super Admin | Saya ingin mengelola akun, izin, wilayah dan kesehatan sistem | Akses terkontrol tanpa hak menetapkan hasil; FR-02 |
| US-12 | Pengunjung/warga | Saya ingin memperoleh bantuan prosedural lewat web atau WhatsApp | Jawaban bersumber atau menu statis; FR-16, FR-17 |

## 5. Functional requirements dan acceptance criteria

Semua aksi mutasi harus memenuhi otorisasi server, validasi, state transition, audit sesuai objek, dan respons kegagalan yang jelas. AC di bawah merupakan syarat penerimaan, bukan hasil pengujian. T-ID merujuk suite MS §24.2 yang dilampirkan pada bagian QA.

| ID / Prioritas | Fitur dan requirement | Acceptance criteria (observable) | Tes referensi |
|---|---|---|---|
| FR-01 P0 | Portal publik, katalog, pencarian/filter, detail dan panduan | Guest melihat hanya versi publik; filter wilayah/mode/status bekerja; program tutup/khusus petugas menampilkan alasan dan jalur tepat; tidak membocorkan draft | T-01, T-06–10 |
| FR-02 P0 | Auth, 7 role terautentikasi + 1 akses publik (Guest), scope wilayah/instansi, assignment, mandat dan admin teknis | Sesi aman; scope expired/revoked ditolak; akun warga tidak dapat mengubah role; akses lintas user/instansi/area ditolak di API dan berkas; admin tidak dapat memutus kasus | T-02,03,12,13,29,30,42 |
| FR-03 P0 | Draft, edit, konfigurasi, review dan publikasi program | Field dan periode tervalidasi; reviewer berbeda dari pembuat; draft tidak tampil publik; published version immutable; perubahan membuat versi baru; kasus lama tetap memakai snapshot | T-27,28 |
| FR-04 P0 | Data proposal mandiri | `DATA_PROPOSAL` tanpa `program_version_id`; draft dapat disimpan; submit valid menghasilkan satu tiket, audit dan outbox; input gagal punya field error | T-04,40,48 |
| FR-05 P0 | Program application mandiri | `PROGRAM_APPLICATION` mengacu versi program; server memvalidasi mode, wilayah, waktu, syarat dan duplikat; blocked precheck bukan keputusan bansos; submit menghasilkan tiket tersendiri | T-05–10,40 |
| FR-06 P0 | Pengusulan RT/operator sesuai mandat | Aktor, subjek, dasar representasi dan scope berbeda tercatat; mandat dicabut/expired atau luar wilayah ditolak; RT tidak dapat approve | T-11–13 |
| FR-07 P0 | Pemeriksaan awal dan assignment | Operator melihat antrean assigned, cek lampiran sah, minta revisi dengan alasan/field/tenggat atau rekomendasi; tidak menjadi reviewer akhir kasus yang sama | T-14,15,17 |
| FR-08 P0 | Review instansi independen dan reasoned outcome | Reviewer yang berwenang berbeda dari pengaju/pemeriksa awal; keputusan menyertakan alasan, dasar rule versi, evidence dan next step; recheck tersedia; hasil tidak mengklaim penetapan resmi | T-17,20,21,30 |
| FR-09 P0 | Tracking dan revisi | Warga hanya melihat kasus miliknya/mandat; status, riwayat, alasan dan CTA konsisten; resubmit memakai tiket yang sama dan snapshot revisi baru; stale version/duplicate aman | T-02,15,16,40,48 |
| FR-10 P0 | Appeal | Hanya hasil dan periode yang memenuhi policy dapat disanggah; reviewer independen; tiket terpisah, bukti, outcome dan riwayat lama tidak ditimpa | T-22–24 |
| FR-11 P0 | Complaint dan feedback | Keluhan dibuat, ditriase, diassign, diselesaikan/dirujuk secara scoped; laporan rahasia dibatasi; feedback tidak mengubah hasil kasus | T-25,26 |
| FR-12 P0 | Transparency Center dan FAQ statis | Filter agregat/periode/wilayah, metadata waktu pembaruan; small cell suppression k≥10 configurable, tanpa PII; FAQ tetap tersedia ketika LLM down | T-39,34 |
| FR-13 P0 | Accountability Center, audit dan notifikasi | Audit append-only merekam aktor, objek, aksi, waktu, reason/request ID; auditor scoped read-only; status perubahan tampil pada timeline; transaksi dan outbox konsisten | T-04,14,24,29,40 |
| FR-14 P1 | Government Data Simulator | Data sintetis terisolasi; request terautentikasi/idempotent; ACK baru menghasilkan `FORWARDED_SIMULATED`; gagal menjadi pending/failed + retry; semua label simulasi jelas | T-18,19 |
| FR-15 P1 | AI anomaly detection | Model benar-benar inferensi; fitur minim dan versioned; flags RULE/MODEL dibedakan; assessment failure `AI_UNAVAILABLE` tidak memblokir submit/review; tidak ada auto rejection | T-31,32 |
| FR-16 P1 | Web LLM + RAG | Sumber approved/versioned dan sitasi; pertanyaan umum saja; tanpa PII/akses kasus/mutasi; jika tanpa sumber atau provider gagal, tampil FAQ dan batasan | T-33,34,44,47 |
| FR-17 P1 | WA static menu / simulator | Menu 1–5/0, state TTL, idempotency; tracking pribadi melalui autentikasi web; jika live webhook, signature wajib; tidak memanggil LLM | T-35–37,43 |
| FR-18 P2 | Face 1:1 opsional dan manual alternatif | Opt-in, referensi berizin, model berlisensi, PAD status jujur; nonmatch/uncertain menuju manual; tidak menyimpan biometrik mentah default; tidak memengaruhi kelayakan | T-38 |
| FR-19 P0 | Lampiran dan perlindungan dokumen | Private storage, pemeriksaan tipe/ukuran, URL sementara setelah authz objek, file invalid ditolak; lintas role/scope tidak dapat mengunduh | T-29,41 |
| FR-20 P2 | OCR opsional | Ekstraksi hanya berkas relevan; warga memeriksa dan mengoreksi hasil; manual input selalu tersedia; tidak mengubah data tanpa konfirmasi | Tes manual/QA tambahan |

## 6. Non-functional requirements dan KPI

| ID | Requirement | Acceptance / pengukuran |
|---|---|---|
| NFR-01 | Performa publik | Target internal p95 respons UI publik <2,5 detik, diukur pada hosting nyata dengan skenario demo normal |
| NFR-02 | Performa API | Target internal p95 <1 detik untuk core API pada seed dan beban ringan, tidak memasukkan LLM/AI eksternal; catat beban, sampel dan hasil |
| NFR-03 | Security | Tidak ada temuan bypass pada suite RBAC/IDOR/maker-checker; input allowlist, TLS, CSRF, rate limiting, secure session, secret scanning |
| NFR-04 | Privacy | Data sintetis saja, PII tidak publik; berkas private, biometrik tidak disimpan default, agregasi aman; redaction logs dan retensi terdokumentasi |
| NFR-05 | Reliability | Submit/review tidak terhambat AI/LLM/WA down; retry idempotent, tidak ada tiket duplikat; simulator gagal tidak mengaku sukses |
| NFR-06 | UX/accessibility | Mobile 360 px dan desktop, Bahasa Indonesia, keyboard/focus/label/error, target WCAG 2.2 AA; evaluasi axe dan manual, bukan sertifikasi |
| NFR-07 | Maintainability | Monorepo, TypeScript strict, Zod/Pydantic, Prisma migration, lockfile, unit/integration/E2E, shared contract, dokumentasi ADR |
| NFR-08 | Auditability | 100% mutasi bisnis penting memiliki audit; DB privilege mencegah UPDATE/DELETE audit dari role aplikasi normal |
| NFR-09 | Recoverability | Backup/restore staging teruji, rollback terdokumentasi, health endpoint tanpa rahasia, outbox retry/DLQ |
| NFR-10 | Deployment | Web publik HTTPS stabil, service internal dilindungi, README/local Docker Compose dapat direproduksi tanpa secret nyata |
| NFR-11 | AI evaluation | Bandingkan model anomali dengan rules, precision/recall/F1/FPR pada label sintetis bila ada, review workload per 100, latency, versi dan stabilitas multi-seed; tanpa klaim validitas populasi nyata |
| NFR-12 | Originality | UI orisinal, tidak memakai template/website builder, aset berlisensi, kode dipahami tim; empat skenario demo tanpa edit DB manual |

Metrik produk: hitung completion rate skenario tugas, waktu median penanganan simulasi, jumlah tiket revisi/appeal yang ditangani, persentase keputusan dengan alasan, jumlah audit per aksi wajib, dan backlog/aging. Baseline, metode pengumpulan dan target dampak lapangan belum tersedia; tidak boleh direkayasa.

## 14. Backlog implementasi yang dapat digunakan Codex

Setiap baris adalah satu task kecil dengan deliverable terbatas. FE = frontend; BE = backend; DATA = schema/seed; AI = service; QA = pengujian; OPS = deployment. Semua berstatus **TODO / belum diverifikasi**, bukan progress aktual. Setiap task harus punya PR, tes, bukti hasil, dan referensi FR. FE boleh menggunakan mock contract pada development/test setelah kontrak disetujui, tetapi release harus terhubung backend nyata. Dependency ditulis ID task; phase 0 wajib selesai dahulu.

| ID | Layer / prioritas | Tujuan dan scope | Dependency | Acceptance dan cara uji |
|---|---|---|---|---|
| TASK-01 | BE/P0 | Freeze enum, DTO, permission, transition dan ADR (FR-02–20) | — | Commit seluruh paket Phase 0 + dokumen utama; verifikasi manifest; review 63 operasi, schema, route, policy; contract tests dan sign-off tim sebelum TASK-02 |
| TASK-02 | OPS/P0 | Bootstrap monorepo, lockfile, lint, CI, env example | 01 | Install/build/lint/typecheck reproducible; no secret |
| TASK-03 | DATA/P0 | Schema role, scope, agency, territory dan seed akun fiktif | 01,02 | Migration reset; scope expiry dan hierarchy tests |
| TASK-04 | BE/P0 | Session auth, register/login/revoke dan CSRF (FR-02) | 03 | T-03,42; cookie dan rate-limit integration tests |
| TASK-05 | BE/P0 | Policy engine role+scope+owner+assignment (FR-02) | 03,04 | T-02,12,13,29,30; deny default unit/API |
| TASK-06 | FE/P0 | Design tokens, responsive shell dan komponen dasar (NFR-06) | 01,02 | 360px/desktop, keyboard, axe, visual review |
| TASK-07 | FE/P0 | Login/recovery dan role dashboard shell (FR-02) | 04,05,06 | Playwright login dan forbidden route |
| TASK-08 | DATA/P0 | Schema program, versions, territories, criteria dan seeds | 03 | Migration, published version constraint; seed 2 program nasional/lokal fiktif |
| TASK-09 | BE/P0 | Draft program CRUD dan rules validation (FR-03) | 05,08 | Invalid dates/mode/scope rejected; draft hidden API tests |
| TASK-10 | BE/P0 | Publication review/publish/return dan archive (FR-03) | 09,11 | T-27,28; version immutable; independent publisher |
| TASK-11 | BE/P0 | Audit append-only, transaction/outbox dan idempotency foundation (FR-13) | 03,05 | DB privilege tests; rollback/replay tests T-40 |
| TASK-12 | BE/P0 | Public catalog/detail/territories API (FR-01) | 08,10 | T-01,09,10; public projection hides drafts |
| TASK-13 | FE/P0 | Landing, catalog, filter, detail dan panduan (FR-01) | 06,12 | E2E public and closed/officer-only CTA |
| TASK-14 | FE/P0 | Program configuration wizard dan publication queue (FR-03) | 06,09,10 | Create draft/review/publish via distinct accounts |
| TASK-15 | DATA/P0 | Case/subtype/revision/household/representation schema | 03,08 | Type conditional constraint; unique ticket/version, seed |
| TASK-16 | BE/P0 | Representation grant checks (FR-06) | 05,15 | T-11,12,13; revoked/expired grant denied |
| TASK-17 | BE/P0 | Data proposal draft/submit (FR-04) | 11,15,16 | T-04,40; no program_id; same retry ticket |
| TASK-18 | BE/P0 | Program precheck rules (FR-05) | 08,12,15,16 | T-06–10; server time, address basis, mode, duplicate |
| TASK-19 | BE/P0 | Program application draft/submit (FR-05) | 11,15,18 | T-05,40; pin immutable version, blocked precheck no submitted ticket |
| TASK-20 | BE/P0 | Private attachment API (FR-19) | 05,15 | T-29,41; signed URL and validation |
| TASK-21 | FE/P0 | Data proposal wizard (FR-04) | 06,17,20 | E2E draft, invalid, submit, network retry |
| TASK-22 | FE/P0 | Program application wizard (FR-05) | 06,18,19,20 | E2E precheck and eligibility messaging; ticket |
| TASK-23 | FE/P0 | RT/operator represented submission UI (FR-06) | 07,16,17,19,20 | E2E scope and actor/subjek visible |
| TASK-24 | BE/P0 | Assignment and initial verification service (FR-07) | 05,11,15,17,19 | T-14,15; assigned scope and reason |
| TASK-25 | BE/P0 | Resubmit/revision snapshots (FR-09) | 20,24 | T-16,40,48; same ticket and increment revision |
| TASK-26 | BE/P0 | Independent review and recheck/outcome (FR-08) | 11,24,25 | T-17,20,21,30; reason required; internal recommendation only |
| TASK-27 | FE/P0 | Operator queue and verification screen (FR-07) | 07,20,24 | E2E assigned review and request revision |
| TASK-28 | FE/P0 | Agency review queue and reasoned decision (FR-08) | 07,26 | E2E reviewer different; failed self-review |
| TASK-29 | BE/P0 | Scoped case read, timeline and notification API (FR-09,13) | 11,15,24,26 | T-02,29; no unauthorized detail; audit timeline |
| TASK-30 | FE/P0 | Warga dashboard, tracking and revision UI (FR-09) | 07,25,29 | E2E status, reason, revision, retry |
| TASK-31 | BE/P0 | Appeal create/read/review (FR-10) | 11,26,29 | T-22–24; independent reviewer; old history preserved |
| TASK-32 | FE/P0 | Appeal form, status and reviewer UI (FR-10) | 07,31 | E2E eligible/expired/corrected; next step |
| TASK-33 | BE/P0 | Complaint and triage lifecycle (FR-11) | 05,11,15 | T-26; confidential handler and scoped read |
| TASK-34 | BE/P0 | Feedback endpoint (FR-11) | 11,26 | T-25; no case status mutation |
| TASK-35 | FE/P0 | Complaint, handler and feedback screens (FR-11) | 06,07,33,34 | E2E complaint ticket, triage, negative feedback |
| TASK-36 | BE/P0 | Public aggregates, FAQ API and suppression (FR-12) | 11,12,29,31 | T-39; no PII; small counts suppressed |
| TASK-37 | BE/P0 | Scoped audit/metrics and technical role management APIs (FR-02,13) | 05,11,29,33 | T-29,30,45; append-only, revoked scope and redaction |
| TASK-38 | FE/P0 | Transparency and FAQ static (FR-12) | 06,36 | Mobile filters, suppression, fallback UI |
| TASK-39 | FE/P0 | Auditor/technical admin dashboard (FR-02,13) | 07,37 | Scoped audit read, revoke, role proof; no decisions |
| TASK-40 | QA/P0 | Core E2E, abuse and outage tests; gate review | 10,13,14,21,22,23,27,28,30,32,35,38,39 | All applicable P0 tests and security suite pass; evidence recorded |
| TASK-41 | OPS/P0 | Deploy core staging and backup/restore | 40 | HTTPS, T-45,46; smoke, env, no real PII |
| TASK-42 | BE/P1 | Simulator API and forwarding worker (FR-14) | 40,26,11 | T-18,19; ACK/pending/failure/retry and labels |
| TASK-43 | AI/P1 | Train/version synthetic Isolation Forest and benchmark (FR-15) | 40,15 | Model artifact and evaluation reproducible; no hardcoded output |
| TASK-44 | AI/P1 | FastAPI anomaly serving and auth (FR-15) | 43 | Schema/M2M tests; model version and latency logged |
| TASK-45 | BE/P1 | Async anomaly adapter and assessment persist (FR-15) | 44,11,24,25 | T-31,32; failure not blocking; distinct RULE/MODEL |
| TASK-46 | FE/P1 | AI assessment UI and simulated forwarding states (FR-14,15) | 42,45,27,28 | Flags advisory; pending/ACK error distinct; fallback visible |
| TASK-47 | BE/P1 | Approved public knowledge and retrieval index (FR-16) | 40,05 | Unapproved docs excluded; citations/version tests |
| TASK-48 | BE/P1 | LLM API guardrails and FAQ fallback (FR-16) | 47,36 | T-33,34,44,47; no PII, timeout/spend cap |
| TASK-49 | FE/P1 | Web help/chat widget (FR-16) | 38,48 | Citations, no source response, down→FAQ |
| TASK-50 | BE/P1 | WA static state engine and mock adapter (FR-17) | 40,12,29,11 | T-35–37; TTL, idempotency, no private status |
| TASK-51 | FE/P1 | WA simulator/demo and secure handoff (FR-17) | 50,06 | Menu and web auth; no documents in WA |
| TASK-52 | BE/P1 conditional | Live WA webhook only if provider ready (FR-17) | 50 | T-43 signature/replay tests; otherwise mock retained |
| TASK-53 | OPS/P1 | Deploy AI/worker/simulator, integration config and health | 41,42,44,45,48,50 | Cloud smoke + service auth + degradation tests |
| TASK-54 | QA/P1 | P1 integration, performance and security gate | 46,49,51,53 | All applicable T-18,19,31–37,43,44,47 pass; benchmark and screenshots |
| TASK-55 | AI/P2 optional | Face 1:1 model service/licensing/quality/PAD evaluation (FR-18) | 54 | License and consent design reviewed; FMR/FNMR/PAD/CPU benchmark |
| TASK-56 | BE/P2 optional | Face opt-in API and manual fallback (FR-18) | 55,05 | T-38; no raw retention; no eligibility mutation |
| TASK-57 | FE/P2 optional | Face and manual verification UI (FR-18) | 56,07 | Opt-in decline/fail routes manual; flag default off |
| TASK-58 | AI/P2 optional | OCR extraction service (FR-20) | 54,20 | Synthetic doc extraction; editable result, manual fallback |
| TASK-59 | FE/P2 optional | OCR preview/confirmation UI (FR-20) | 58,21,22 | Correction/decline retains manual input; no auto commit |
| TASK-60 | QA/P0+P1 | Final regression and judge journey | 41,54 | 4 scenarios pass on public hosting; optional modules only if tested |
| TASK-61 | OPS/P0+P1 | README, demo credentials, runbook, repo and video links | 60 | Clean install and judge access verified; links and licenses checked |

## 15. Delivery phases dan pembagian tim

Rencana berikut adalah usulan rebaseline per 26 November, karena status aktual implementasi belum diketahui. Jangan mengasumsikan fase awal sudah selesai. P0 tidak dipotong untuk mengejar fitur AI tambahan.

| Fase | Target internal | Gate |
|---|---|---|
| 0 — Contract freeze | 26–27 Nov | TASK-01–02: PRD, schema, shared DTO, API/state/permission, ADR disetujui |
| 1 — Foundation & first vertical slice | 27 Nov–3 Des | Auth, program publish, katalog, dua tipe submit dan tracking dasar |
| 2 — Core completion | 4–8 Des | Verification, revision, independent review/appeal, complaint, feedback, audit, transparency, P0 tests |
| 3 — Innovation | 9–12 Des | Simulator, inference asli, RAG web, WA mock dengan fallback |
| 4 — Deployment & hardening | 13–15 Des | Cloud, security, performance, mobile, QA; P2 hanya jika aman |
| 5 — Submission readiness | 16–17 Des | Freeze, demo video, README, repo, link check; 18 Des buffer sebelum 23.59 WIB |

Maksimal 3 anggota: A — FE/UI/UX dan E2E UI; B — BE/data/security dan deployment; C — AI/integration/QA dan demo video. Semua anggota wajib memahami kode yang digunakan. Perubahan kontrak diputuskan bersama; tidak ada edit manual DB untuk menyelamatkan demo.

## 18. Competition score traceability

| Bobot RB | Aspek | Bukti yang harus ditunjukkan |
|---:|---|---|
| 25% | Kesesuaian tema & solusi | Narasi masalah, SDG 16, tracking, alasan, sanggahan, audit, transparansi aman |
| 30% | Fungsionalitas & kinerja | Hosted URL, seluruh core E2E, no fatal bugs, hasil ukur performa dan graceful degradation |
| 25% | Desain UI/UX & estetika | UI orisinal, navigasi, mobile, aksesibilitas, design system dan state yang konsisten |
| 20% | Kelengkapan & kualitas demo video | Empat skenario runtut, konsep jelas, demonstrasi nyata dan label simulasi |

RB halaman 5–8: website builder/template dilarang, AI boleh digunakan dengan tanggung jawab keamanan dan pemahaman kode; tiga tautan wajib: Google Drive video (viewer access), GitHub monorepo, dan hosted web publik. Deadline 18 Desember 2026 23.59 WIB. Tanggal final tercetak 20 Januari 2025 tidak konsisten dengan Techcomfest 2027; konfirmasi panitia, jangan mengasumsikan tanggal koreksi.

### Judge demo flow

1. Program nasional vs lokal: filter, periode, mode, ketentuan, publikasi independen dan agregat transparansi.
2. Warga usulan mandiri: submit → tiket → AI flag P1 → operator revisi → resubmit → independent review → simulator ACK dengan label simulasi.
3. Hasil tidak dapat diproses: alasan → sanggahan terpisah → reviewer independen → keputusan lama/baru → audit.
4. RT representasi sah vs luar scope, percobaan admin/RT approve ditolak, WA menu statis dan secure web handoff, fallback saat service gagal.

## 19. Definition of Done

- [ ] Semua P0 feature FR-01–13 dan FR-19 serta security invariants berjalan dengan API/DB nyata.
- [ ] P1 FR-14–17 teruji dan ter-host; bila provider WA belum siap, mock diberi label; tidak ada ACK/AI hasil hardcode.
- [ ] P2 hanya diaktifkan jika lisensi, keamanan, evaluasi dan manual fallback lolos; jika tidak, feature flag off dan N/A terdokumentasi.
- [ ] Seluruh acceptance criteria dan negative tests relevan pass; tidak ada bug fatal, akses tidak sah, tiket ganda, kehilangan audit atau keputusan tanpa alasan.
- [ ] Mobile/desktop, keyboard, loading/empty/error/ready, retry dan fallback diperiksa.
- [ ] Migrations, synthetic seed, README, lockfile, `.env.example`, Docker Compose, CI, rollback dan backup/restore tersedia.
- [ ] Hosted URL HTTPS, service internal secured, GitHub monorepo, video Google Drive viewer dan akses juri terverifikasi.
- [ ] Semua demo berlabel simulasi, tidak ada klaim resmi atau data riil; tim dapat menjelaskan kode dan desain.

## 20. Open decisions, risks dan assumptions

| ID | Keputusan/risiko | Default usulan / aksi | Owner |
|---|---|---|---|
| OD-01 | Routing | Diputuskan v1.2: URL Indonesia §4; route groups bukan URL; sign-off | FE+BE |
| OD-02 | Simulator P1 vs outcome P0 | Diputuskan v1.2: P0 RECOMMENDED_FOR_FORWARD; P1 queue/ACK/retry | BE |
| OD-03 | State | Kandidat freeze v1.2: STATE_CONTRACT.md + enums/transitions.ts | BE |
| OD-04 | Endpoint/permission/auth | Kandidat freeze v1.2: OpenAPI gabungan §16.1–16.3, §16.7 dan auth (63 operasi); Supabase Auth + BFF; sign-off, generated client dan kontrak internal §16.4 masih diperlukan | BE |
| OD-05 | Complaint dan representation | Authenticated only; scoped handler, explicit grants; tim review aturan mandat dan kerahasiaan | Product+BE |
| OD-06 | Provider, budget, region hosting | Satu web/API container + satu worker ringan opsional, managed PG/private storage; ukur biaya/runtime/cold start | OPS |
| OD-07 | Face model/license/PAD | Default disabled; no biometrik nyata, manual fallback; uji hanya dalam kondisi yang diizinkan | AI+QA |
| OD-08 | Validitas AI sintetis | Hanya triage demo; tidak mengukur ketepatan sasaran bantuan nyata; report metrics actual | AI |
| OD-09 | Data policy dan aturan program | Semua fiktif, policy versioned, SLA/masa sanggah configurable; review hukum sebelum penggunaan nyata | Product |
| OD-10 | Brand/design visual | Palet awal hanya usulan, buat UI original dan validasi usability | FE |
| OD-11 | Jadwal dan status pekerjaan | Rebaseline 26 Nov; aktual progress belum diverifikasi; cut-off internal 17 Des | Tim |
| OD-12 | WA provider dan LLM budget | WA simulator dan FAQ baseline tersedia; live hanya setelah readiness; tidak mengklaim koneksi aktif | AI+OPS |
| OD-13 | Tanggal final RB tidak konsisten | Hubungi panitia untuk konfirmasi final; deadline penyisihan jelas | Tim |


## Phase 0 — supplemental acceptance criteria (v1.2)

| Test | Requirement | Expected result |
|---|---|---|
| T-49 | FR-02 | GUEST tidak pernah disimpan sebagai role; 7 authenticated enums, grant scoped, permission string canonical |
| T-50 | FR-04/05/09 | kedua tipe mengikuti PRECHECKED; invalid submit 422 tanpa tiket; revisi valid tiket sama; NOT_SELECTED ditolak untuk data proposal |
| T-51 | FR-08/14 | P0 RECOMMENDED_FOR_FORWARD; P1 pending/failure/retry; ACK tervalidasi satu kali; no fake success |
| T-52 | FR-10 | appeal states canonical; reviewer independen; prior decision immutable; recheck creates task atomically |
| T-53 | FR-19 | upload invalid/oversize ditolak; quarantine tidak downloadable; scoped CLEAN signed URL max 60s; expired URL denied |
| T-54 | FR-07/11 | assign/triage scope and expected_version enforced; no self assignment; confidentiality; cross-owner read denied |
| T-55 | FR-13 | same idempotency key+payload replay; mismatch 409; concurrent submit one ticket; webhook duplicate one event |
| T-56 | FR-13 | runtime DB role UPDATE/DELETE/TRUNCATE audit_events rejected; INSERT permitted; audit in transaction |
| T-57 | FR-02 | missing/wrong CSRF or Origin denied, rotated/revoked session denied, staff AAL1 blocked, MFA step-up enforced |
| T-58 | FR-03/16 | archive preserves version; knowledge approval maker-checker; unapproved doc excluded; notification recipient-only |
| T-59 | NFR-12 | all used assets recorded, license evidence checked, no template prohibited by rulebook |
| T-60 | FR-06 | grant pending until independent approval, expired/revoked/own grant denied |

All are expected behavior, NOT executed tests. See `phase0/PHASE0_GATE.md`.
