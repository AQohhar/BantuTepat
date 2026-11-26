# Matriks route frontend BantuTepat

Sumber route: PAGE_DESIGN_SPECIFICATION.md dan PRD v1.2. Total 42 route kanonik, sesuai hitungan validasi statis paket UI/UX. Route path ditulis dalam bentuk placeholder Laravel.

| ID | Route kanonik | Layar / role | Prioritas | Status di project BantuTepat saat audit | Target fase |
|---|---|---|---|---|---|
| PUB-01 | / | Beranda, Guest | P0 | ADA — Blade, katalog dari config demo; bukan API | FE-03 |
| PUB-02 | /program | Katalog program, Guest | P0 | ADA — Blade, pencarian/filter lokal; belum pagination cursor/API | FE-03 |
| PUB-03 | /program/{slug} | Detail program, Guest | P0 | ADA — Blade, data config demo | FE-03 |
| PUB-04 | /panduan | Panduan layanan, Guest | P0 | ADA — Blade statis | FE-03 |
| PUB-05 | /transparansi | Transparansi publik, Guest | P0 | ADA — Blade dengan snapshot demo/config; belum GET /public/stats | FE-03 |
| PUB-06 | /bantuan | Pusat bantuan, Guest | P0 FAQ / P1 RAG+WA | ADA — Blade/FAQ; belum chatbot atau handoff sesuai API | FE-03; P1 FE-08 |
| AUTH-01 | /login | Masuk, pre-auth | P0 | BELUM — ada /demo/masuk, bukan auth kanonik | FE-03 |
| AUTH-02 | /daftar | Daftar akun warga | P0 | BELUM — tidak ditemukan route kanonik | FE-03 |
| AUTH-03 | /lupa-password | Pemulihan kata sandi | P0 | BELUM — tidak ditemukan route kanonik | FE-03 |
| PUB-07 | /privasi | Kebijakan privasi, Guest | P0 | ADA — Blade statis | FE-03 |
| PUB-08 | /syarat-layanan | Syarat layanan, Guest | P0 | ADA — Blade statis | FE-03 |
| PUB-09 | /aksesibilitas | Aksesibilitas, Guest | P0 | ADA — Blade statis | FE-03 |
| CIT-01 | /dashboard | Dashboard warga, CITIZEN | P0 | BELUM — demo /demo/pengajuan-saya bukan dashboard kanonik | FE-04 |
| CIT-02 | /pengajuan/baru | Pilih jenis pengajuan, CITIZEN | P0 | BELUM — tidak ditemukan route kanonik | FE-04 |
| CIT-03 | /pengajuan/usulan | Usulan data sosial ekonomi, CITIZEN | P0 | BELUM — alur demo di /demo/usulan-data | FE-04 |
| CIT-04 | /program/{slug}/daftar | Pendaftaran program, CITIZEN | P0 | BELUM — alur demo di /demo/program/{slug}/pengajuan | FE-04 |
| CIT-05 | /pengajuan-saya | Pengajuan saya, CITIZEN | P0 | BELUM — daftar demo di /demo/pengajuan-saya | FE-04 |
| CIT-06 | /pengajuan-saya/{caseId} | Detail pengajuan, CITIZEN | P0 | BELUM — detail demo /demo/pengajuan/{ticket} | FE-04 |
| CIT-07 | /pengajuan-saya/{caseId}/perbaiki | Perbaikan pengajuan, CITIZEN | P0 | BELUM — perbaikan demo memakai URL /demo | FE-04 |
| CIT-08 | /pengajuan-saya/{caseId}/sanggah | Sanggahan, CITIZEN | P0 | BELUM — tidak ditemukan route demo/kanonik | FE-04 |
| CIT-09 | /pengaduan | Pengaduan layanan, CITIZEN | P0 | BELUM — tidak ditemukan route | FE-04 |
| CIT-10 | /feedback/{caseId} | Feedback pelayanan, CITIZEN | P0 | BELUM — tidak ditemukan route | FE-04 |
| CIT-11 | /profil | Profil, CITIZEN | P0 | BELUM — tidak ditemukan route | FE-04 |
| CIT-12 | /verifikasi-identitas | Verifikasi identitas, CITIZEN | P0 manual / P2 face | BELUM — tidak ditemukan route | FE-04; P2 setelah approval |
| REG-01 | /wilayah | Dashboard wilayah, REGIONAL_PROPOSER / VILLAGE_OPERATOR | P0 | BELUM — operator demo memiliki halaman /demo/wilayah/kasus | FE-05 |
| REG-02 | /wilayah/usulkan | Pengusulan warga dengan mandat, REGIONAL_PROPOSER / VILLAGE_OPERATOR | P0 | BELUM — tidak ditemukan route representasi kanonik | FE-05 |
| REG-03 | /wilayah/kasus | Daftar kasus wilayah, RT/operator | P0 | BELUM — antrean operator demo ada di /demo/wilayah/kasus | FE-05 |
| REG-04 | /wilayah/verifikasi/{id} | Verifikasi operator assigned | P0 / P1 AI | BELUM — layar demo memakai /demo/wilayah/verifikasi/{ticket} | FE-05; P1 FE-08 |
| AGY-01 | /instansi | Dashboard instansi, verifier/manager | P0 | BELUM — tidak ditemukan route kanonik | FE-06 |
| AGY-02 | /instansi/review | Antrean review kasus, AGENCY_VERIFIER | P0 | BELUM — antrean demo di /demo/instansi/review | FE-06 |
| AGY-03 | /instansi/review/{id} | Review kasus instansi, assigned independent reviewer | P0 / P1 AI+simulator | BELUM — detail demo di /demo/instansi/review/{ticket} | FE-06; P1 FE-08 |
| AGY-04 | /instansi/program | Manajemen program, PROGRAM_MANAGER | P0 | BELUM — tidak ditemukan route | FE-06 |
| AGY-05 | /instansi/program/baru | Buat program, PROGRAM_MANAGER | P0 | BELUM — tidak ditemukan route | FE-06 |
| AGY-06 | /instansi/program/{id}/edit | Edit versi program, PROGRAM_MANAGER | P0 | BELUM — tidak ditemukan route | FE-06 |
| AGY-07 | /instansi/program/review-publikasi | Tinjauan publikasi oleh grant terpisah | P0 | BELUM — tidak ditemukan route | FE-06 |
| AGY-08 | /instansi/sanggahan | Manajemen sanggahan, verifier/coordinator | P0 | BELUM — tidak ditemukan route | FE-06 |
| ADM-01 | /administrasi/pengawasan | Pengawasan/monitoring, AUDITOR | P0 / P1 health details | BELUM — tidak ditemukan route | FE-07 |
| ADM-02 | /administrasi/audit | Audit log, AUDITOR scoped | P0 | BELUM — tidak ditemukan route | FE-07 |
| ADM-03 | /administrasi/pengaduan | Penanganan pengaduan berizin | P0 | BELUM — tidak ditemukan route | FE-07 |
| ADM-04 | /administrasi/pengguna | Manajemen pengguna, SUPER_ADMIN | P0 | BELUM — tidak ditemukan route | FE-07 |
| ADM-05 | /administrasi/wilayah | Manajemen wilayah, SUPER_ADMIN scoped | P0 | BELUM — tidak ditemukan route | FE-07 |
| ADM-06 | /administrasi/integrasi | Integrasi/health, SUPER_ADMIN | P0 health / P1 service features | BELUM — tidak ditemukan route | FE-07; P1 FE-08 |

## Interpretasi coverage

- 9 dari 42 URL kanonik sudah ada: seluruh sembilan halaman publik (PUB-01—06, PUB-07—09). Sebagian data/isi adalah config atau konten statis, jadi keberadaan route tidak menyatakan API integration atau UI spec v1.0 sudah terpenuhi.
- AUTH-01—03 tidak tersedia di URL kanonik; route login demo bukan pengganti auth backend.
- Alur warga/operator/verifikator yang ada memakai URL /demo dan session-based state. Itu dapat menjadi referensi journey tetapi bukan route coverage, permission, atau bukti production readiness.
- 33 URL kanonik auth/privat belum tersedia. Tabel di atas menjadi backlog FE-03—FE-07.
- Semua halaman dinamis mengacu ke FRONTEND_API_HANDOFF.md dan blocker G01—G13. Jangan menampilkan fixture sebagai hasil backend.

## Perilaku navigasi

Gunakan URL kanonik tanpa menambahkan route baru. Navigation items mengikuti permission/scope response. Route guard frontend hanya membuat pengalaman aman; API/backend memeriksa otorisasi setiap objek. Guest tidak menerima role database. Notifikasi adalah panel di topbar, bukan route baru. Halaman P1/P2 tetap tersembunyi bila flag mati.
