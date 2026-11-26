# Blockers frontend BantuTepat

Tanggal: 26 November 2026. Status berdasarkan FE-00; tidak ada blocker yang diselesaikan dengan asumsi atau endpoint rekaan.

## Blocker sebelum FE-01

| ID | Severity | Blocker | Dampak | Cara membuka | Owner yang dibutuhkan |
|---|---|---|---|---|---|
| BL-01 | Gate | Prompt frontend yang dilampirkan meminta berhenti pada FE-00 dan menunggu persetujuan sebelum FE-01. | Coding feature belum dimulai. | Pengguna/tim meninjau audit, rute, roadmap, blockers dan menyetujui FE-01. | Pengguna / lead |
| BL-02 | Gate | Folder BantuTepat tidak memiliki .git. PRD TASK-01 meminta commit, hash, review, dan artefak Phase 0 di repo. | Tidak ada history diff/branch/commit untuk gate atau review perubahan. | Hubungkan project ke repo tim atau siapkan checkout yang punya Git sebelum task implementasi. | Lead tim |
| BL-03 | Gate | Auth/security ADR v1.2 masih kandidat dan berasumsi Supabase + Next.js BFF; frontend baru ditetapkan Laravel. | Login, MFA, sesi, cookie, CSRF dan proxy tidak dapat dirancang aman tanpa batas layanan yang disepakati. | FE/BE/QA menetapkan pemilik sesi/BFF, deployment same-origin, Origin/CSRF, callback, MFA, header forwarding, logout/revoke. | Backend + FE + QA/PO |
| BL-04 | Gate | Role/policy/OpenAPI v1.2 belum ditandatangani oleh FE/BE/PO; phase0 docs menyatakan TASK-01 belum selesai. | Tidak boleh generate/duplikasi DTO atau memulai integration live. | Approve enum/permission/state/auth/DTO, generate validator/client dari kontrak, catat ADR dan pengecualian. | FE + BE + PO |
| BL-05 | Environment | Backend URL/environment dan auth integration tidak tersedia di project; BACKEND_API_URL/client API belum ada. | Semua screen dinamis belum dapat menggunakan response backend. | Sediakan staging/dev endpoint, auth test accounts sintetis, CORS/Origin dan API credentials server-side sesuai perjanjian. | Backend/OPS |
| BL-06 | Environment | PHP, Composer, dan npm tidak tersedia pada PATH sesi; Node tersedia. | Route:list, Composer scripts, Vite build, PHPUnit dan browser server tidak dapat diverifikasi di sesi ini. | Sediakan toolchain target atau runner CI yang sama; jalankan build dan QA di sana saat fase implementasi. | OPS/lead |

## Blocker API per UI_CONTRACT_GAPS

Sumber rincian: docs/BantuTepat_UIUX_Design_v1.0/UI_CONTRACT_GAPS.md. Gaps ini adalah usulan review, bukan API yang sudah ada.

| Gap | Screen terdampak | Ringkasan kekurangan | Dampak implementasi |
|---|---|---|---|
| G01 | PUB-02 | Filter jenis program tidak tercakup endpoint katalog. | Jangan menampilkan filter jenis yang menghasilkan seolah-olah hasil global. |
| G02 | PUB-03, CIT-04 | Public detail tidak memuat semua metadata program/form yang dibutuhkan. | Program application lengkap belum bisa diikat pada versi/rules publik. |
| G03 | PUB-01/05, ADM-01 | Statistik/agregat tidak mendukung seluruh time range, series, unit, metode, atau parameter scoped. | Jangan buat grafik/tren/filter/median/angka sendiri; server suppression wajib. |
| G04 | CIT-03/04/06/07, REG-04, AGY-03 | Metadata/scan state/read-refresh lampiran tidak lengkap. | Uploader dan petugas tidak dapat menampilkan lifecycle/aksi file dengan benar. |
| G05 | CIT-03/04/06/07, REG-02 | Schema form dinamis, field allowlist, revision/draft resume belum terdefinisi penuh. | Jangan hardcode arbitrary answers atau menyimpan draft dengan asumsi. |
| G06 | CIT-01/06/07, REG-04, AGY-03 | Case detail belum memuat request revisi, alasan keputusan, assignment, evidence/rule link, action capability/vocabulary. | Verifikasi, alasan hasil dan next steps P0 belum lengkap tanpa keputusan kontrak. |
| G07 | CIT-06/08, AGY-08 | Referensi keputusan, jendela/kelayakan sanggahan, linked appeals dan queue belum cukup. | Sanggahan tidak boleh dibuat dengan UUID/eligibility yang ditebak. |
| G08 | CIT-03/11/12 | Profil edit, household/session, dan riwayat identity check tidak tersedia. | Batasi profil read-only; identity history manual result saja jika response tersedia. |
| G09 | REG-01/02/03, AGY-02/08, ADM-03/04 | Lookup mandat/subjek/assignee/handler dan represented case read belum cukup. | Tidak ada directory warga global atau arbitrary UUID input; admin/assignment screens parsial. |
| G10 | AGY-01/04/05/06/07 | Internal program list/detail/version/publication queue dan version workflow belum lengkap. | Program management P0 live belum bisa selesai. |
| G11 | CIT-06/08, AGY-01/08 | Appeal list/queue dan reviewer eligibility lookup tidak ada. | Pengelolaan sanggahan instansi hanya dapat menampilkan data yang sudah memiliki resource ID sah. |
| G12 | ADM-04/05 | User/grant/territory internal read/invite/approval contracts tidak lengkap. | Admin list/action tidak boleh memakai fixture atau data publik sebagai pengganti. |
| G13 | ADM-06, PUB-06, CIT-12 | Knowledge list/read, safe feature-flag projection, dan metadata kesehatan tidak cukup. | Tidak ada toggle/retry admin rekaan; P1 tertunda. |

## Blocker baseline visual dan aset

| ID | Severity | Temuan | Jalan keluar |
|---|---|---|---|
| BL-07 | Tinggi | Gaya lama memakai token palet lain, gradient, shadow/pola/animasi yang tidak cocok dengan Jalur Jelas. | FE-01/02 menyatukan token dan refactor layout tanpa memakai template. |
| BL-08 | Tinggi | Favicon, gambar layanan, dan gambar program tidak memiliki inventory license/provenance yang terisi; font Plus Jakarta Sans lokal belum ada. | Lengkapi asset inventory dan bukti lisensi; gunakan font/icon hanya setelah review. |
| BL-09 | Tinggi | Hanya sembilan route publik tersedia; private/auth canonical belum diimplementasikan. | Bangun FE-03—FE-07 bertahap setelah API/auth blocker di atas selesai. |
| BL-10 | Tinggi | Demo controller menggunakan session/config untuk ticket/state/review. Itu bukan kontrak API dan menaruh proses demo pada presentation app. | Jangan perluas demo flow menjadi produk; petakan ulang view ke API resmi dan dokumentasikan mode demo terisolasi. |
| BL-11 | Sedang | README masih Laravel scaffold dan welcome.blade.php tampak tidak dirujuk route. | Perbarui README/hapus file hanya setelah owner mengonfirmasi kebutuhan dan references. |

## Catatan rulebook untuk tim

Rulebook mengharuskan karya orisinal, melarang website builder/template, dan meminta web publik ter-host, source repository, serta demo video. Tanggal pengumpulan karya tercetak 18 Desember 2026; tanggal final tercetak 20 Januari 2025 walau dokumen bertajuk Techcomfest 2027. Dokumen Phase 0 juga menandai kontradiksi tanggal final untuk konfirmasi tim. Ini blocker administratif terpisah dari implementasi UI.

## Status akhir FE-00

Tidak ada blocker teknis yang membuktikan bahwa proyek harus dibangun ulang. Lanjutkan dengan project Laravel yang ada setelah BL-01—BL-06 dan approval pengguna/tim; tutup G01—G13 sesuai layar sebelum wiring dan acceptance P0. P1/P2 tetap gated.
