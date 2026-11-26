# BantuTepat — Acceptance Criteria & QA

**Versi 1.2 | 26 November 2026 | Draft requirements, bukan bukti implementasi**

Acuan: PRD §5–6, §16, §19; Master Specification §24. Baca `01_PRD.md` untuk scope, prioritas dan Open Decisions. Penomoran bagian yang diambil dari master dipertahankan. Kontrak v1.2 adalah kandidat freeze untuk persetujuan tim; sumber historis tidak mengungguli kontrak ini.

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

## 16. Testing strategy dan traceability

Unit: rules, scope, state, suppression, idempotency. Integration: API+DB, storage, authz, outbox, service adapters. E2E: Playwright multi-role dan mobile, happy/unhappy path. Security: BOLA/IDOR, privilege escalation, CSRF, file upload, prompt injection, replay, secret scan. Performance: p95 dan Lighthouse pada deployment nyata; catat environment, sample count, data seed, dan hasil. Fault injection: AI/LLM/WA/Gov down, retry serta backup/restore. Benchmark AI terpisah dari eligibility. Tambah test untuk setiap acceptance criteria yang belum punya T-ID.

### Acceptance suite baseline (MS §24.2)

### 24.2 Test matrix P0/P1/P2 (test IDs stabil)

| Test ID | Skenario | Ekspektasi |
|---|---|---|
| T-01 | Guest lihat katalog | info publik tampil tanpa login |
| T-02 | Guest akses detail kasus warga | 401/404 aman, tanpa data pribadi |
| T-03 | Warga daftar/login | sesi secure, role warga |
| T-04 | Warga mengajukan usulan data valid | nomor tiket DATA_PROPOSAL, status PRECHECKED, audit+outbox |
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

**Release gate:** semua T-ID yang relevan dengan fitur aktif harus lulus; T-39–48 juga wajib sesuai cakupan security/reliability P0/P1, bukan hanya T-01–30. P2 yang dinonaktifkan dicatat N/A dengan alasan; mock WA tidak mengklaim live integration. Setiap failure kritis (leak, bypass, status salah, audit hilang, core flow tidak berjalan) memblokir release. Bukti: CI report, E2E trace, screenshot, benchmark, smoke log. Belum ada bukti pengujian pada dokumen sumber.

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
| T-04 | Warga mengajukan usulan data valid | nomor tiket DATA_PROPOSAL, status PRECHECKED, audit+outbox |
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
    And warga memiliki pengajuan PRECHECKED
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

## 19. Definition of Done

- [ ] Semua P0 feature FR-01–13 dan FR-19 serta security invariants berjalan dengan API/DB nyata.
- [ ] P1 FR-14–17 teruji dan ter-host; bila provider WA belum siap, mock diberi label; tidak ada ACK/AI hasil hardcode.
- [ ] P2 hanya diaktifkan jika lisensi, keamanan, evaluasi dan manual fallback lolos; jika tidak, feature flag off dan N/A terdokumentasi.
- [ ] Seluruh acceptance criteria dan negative tests relevan pass; tidak ada bug fatal, akses tidak sah, tiket ganda, kehilangan audit atau keputusan tanpa alasan.
- [ ] Mobile/desktop, keyboard, loading/empty/error/ready, retry dan fallback diperiksa.
- [ ] Migrations, synthetic seed, README, lockfile, `.env.example`, Docker Compose, CI, rollback dan backup/restore tersedia.
- [ ] Hosted URL HTTPS, service internal secured, GitHub monorepo, video Google Drive viewer dan akses juri terverifikasi.
- [ ] Semua demo berlabel simulasi, tidak ada klaim resmi atau data riil; tim dapat menjelaskan kode dan desain.

## Test evidence

Semua skenario berstatus belum diverifikasi; tidak ada hasil testing aktual pada sumber. Gunakan ID FR/NFR/T sebagai traceability; setiap implementasi harus menyertakan hasil test, environment, bukti, dan issue jika gagal. Gate rilis mengikuti PRD §16.


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

## Regression checks v1.2 (tambahan pada T-16, T-50 dan gate TASK-01)

| Check | Given / When / Then | Bukti wajib |
|---|---|---|
| R-01 | REVISION_REQUIRED; resubmit valid → nomor tiket dan case ID identik; revision naik; status UNDER_REVIEW; audit dan outbox committed satu transaksi | Integration test kedua jenis case |
| R-02 | Resubmit invalid → 422; case/revision/ticket unchanged; retry idempotent tidak menambah revision | Integration test dan DB assertion |
| R-03 | Seluruh 63 operasi inventory public/auth/legacy/addendum cocok dengan OpenAPI; tidak ada unresolved reference | Static validation + generated client + contract tests |
| R-04 | Artefak Phase 0 lengkap dan berada pada commit yang sama; gate tidak lolos jika file hilang | Manifest check, commit SHA dan sign-off |

Regression checks adalah target pengujian, bukan bukti runtime lulus.
