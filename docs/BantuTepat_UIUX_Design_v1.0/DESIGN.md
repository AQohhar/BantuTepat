# BantuTepat — DESIGN.md
**Design system v1.0 | 26 November 2026 | Kandidat persetujuan desain | Target PRD v1.2**

## 1. Mandat dan sumber
Dokumen ini adalah pedoman UI/UX untuk Codex, bukan perubahan persyaratan bisnis. BantuTepat adalah prototipe pelayanan publik untuk Techcomfest 2027, Smart Governance & Public Service. Nilai utama: warga tahu jalur layanan, langkah berikutnya, dan alasan keputusan; petugas bekerja sesuai mandat; auditor memperoleh jejak yang dapat dipertanggungjawabkan. Tidak ada penetapan penerima atau penyaluran resmi.

Baca berurutan: `DESIGN.md` → `DESIGN_TOKENS.md` → `COMPONENT_SPECIFICATION.md` → `PAGE_DESIGN_SPECIFICATION.md` → `RESPONSIVE_GUIDELINES.md` → `UI_IMPLEMENTATION_GUIDE.md`. `tokens.css` adalah implementasi referensi token (Tailwind v4 candidate); `UI_CONTRACT_GAPS.md` adalah daftar blocker kontrak; `DESIGN_VALIDATION.md` memuat hasil pemeriksaan yang benar-benar dijalankan. Semua artefak dalam paket ini bersifat rancangan, bukan website selesai.

Sumber yang digunakan: `BantuTepat_MASTER_SPEC_Codex_FINAL.md` §3–4, §8–15, §18–19, §23–25; PRD Package v1.2 `01_PRD.md` §3–8, §11–13, §17–20; `02_Requirements.md`, `03_ERD.md`, `04_API_Contract.md`, `05_Role_Matrix.md`, `06_Workflow.md`, `07_Acceptance_Criteria.md`, `phase0/STATE_CONTRACT.md`, `phase0/AUTH_SECURITY.md`, `phase0/openapi.phase0.json` dan `phase0/packages/policy/enums.ts`. Sumber v1.2 mengungguli detail historis Master Spec yang bertentangan (termasuk URL, enum, dan deployment). Kontrak v1.2 sendiri masih kandidat freeze. Apabila desain tidak dapat diikat pada kontrak, catat gap, jangan menebak endpoint atau menyimpan data dummy di production.

## 2. Konsep desain: **Jalur Jelas**
Visual language menyerupai lembar layanan yang teratur: judul seperti kepala surat, informasi penting dikelompokkan, jalur proses ditampilkan sebagai garis vertikal dengan titik dan waktu. Identitas visual khusus BantuTepat: motif dua garis tipis yang bertemu di satu titik tujuan (jalur usulan data dan pendaftaran program). Gunakan sebagai mark kecil pada header dan ilustrasi alur buatan tim; bukan lambang negara/instansi, bukan logo resmi pemerintah. Konsep dan palet ini merupakan keputusan desain yang direkomendasikan, belum diuji dengan pengguna.

- **Jelas:** kalimat pendek, label nyata, satu aksi utama per konteks, urutan baca dari kebutuhan ke tindakan.
- **Terpercaya:** sumber aturan, versi, status aktual, waktu pembaruan, label simulasi; hindari angka keberhasilan fiktif, logo institusi tanpa izin, testimoni rekaan.
- **Manusiawi:** jelaskan alasan hasil tanpa menyalahkan; beda revisi, precheck, sanggahan, pengaduan, kegagalan teknis. Jangan memakai kata "Ditolak AI", "Lolos bansos" atau "Bantuan cair".
- **Tenang:** dominasi netral terang, biru tinta untuk navigasi/aksi, teal untuk penekanan informasi; hindari gradient, glassmorphism, kartu bertumpuk, ilustrasi AI dekoratif, animasi berlebihan.
- **Terarah:** layar warga berorientasi tindakan; operator berorientasi antrean; verifikator berorientasi bukti dan keputusan; pengelola program berorientasi versi; auditor berorientasi bukti audit; admin teknis berorientasi mandat dan kesehatan sistem.

## 3. Komposisi utama
Public: header putih, konten max 1200 px, hero dua kolom editorial (teks kiri, diagram jalur buatan sendiri kanan), dua jalur layanan, program, alur layanan, statistik aman dan footer. Jangan menampilkan dashboard administrasi di landing. Satu CTA utama "Lihat program", sekunder "Pelajari cara pengajuan"; guest diarahkan ke login untuk aksi privat dengan return path lokal yang aman.

Private: sidebar 248 px pada desktop, topbar dengan breadcrumb/identitas scope, konten max 1440 px. Header halaman memuat judul, deskripsi singkat, aksi utama; filter sebelum daftar. Di mobile gunakan drawer menu dan topbar, bukan sidebar sempit yang mengambil ruang. Bottom navigation hanya warga pada halaman daftar/dashboard yang relevan, bukan pada wizard atau layar keputusan. Detail case memakai panel status/timeline, bukti dan aksi yang diizinkan. Tidak semua dashboard harus berisi grafik atau KPI besar.

### 3.1 Persona dan fokus kerja
| Konteks | Landing privat | Hierarki atas → bawah | Tidak boleh |
|---|---|---|---|
| CITIZEN (Warga) | `/dashboard` | Tindakan diperlukan → pengajuan terakhir → notifikasi → jalur layanan/program | Melihat kasus orang lain tanpa hak |
| REGIONAL_PROPOSER (RT/RW) | `/wilayah` | Usulkan warga → mandat sah → kasus yang didampingi | Setujui/menetapkan penerima |
| VILLAGE_OPERATOR | `/wilayah` | Antrean assigned → cek bukti → revisi/rekomendasi | Review final kasus yang sama |
| AGENCY_VERIFIER | `/instansi` | Antrean review → alasan/bukti → keputusan administratif; appeal terpisah | Self-review, memutus penerima resmi |
| PROGRAM_MANAGER | `/instansi` | Draft/versi → periode → publikasi berizin | Publish draft sendiri; mengubah hasil kasus |
| AUDITOR | `/administrasi/pengawasan` | Backlog/KPI aman → audit → pengaduan jika mandat | Mengubah audit/hasil kasus |
| SUPER_ADMIN | `/administrasi/pengguna` atau `/administrasi/integrasi` sesuai scope | Mandat pengguna → kesehatan → wilayah | Menentukan penerima, akses privat tanpa tugas |
| Guest | `/` | Informasi layanan → program → panduan | Tidak ada akun/enum GUEST; tidak submit |

Pemilihan scope (bila akun punya beberapa mandat) hanya memakai scope valid dari `/me`; nama wilayah/instansi memakai lookup resmi bila tersedia. Server mengevaluasi ulang setiap objek dan aksi. Jika scope tak tersedia, tampilkan layar tanpa akses; jangan fallback ke scope global. Jangan menampilkan tombol yang tidak punya permission; apabila user tahu aksi tetapi guard status menghalangi, tampilkan alasan di dekat aksi yang dinonaktifkan.

## 4. Information architecture
URL kanonik dalam PAGE_DESIGN_SPECIFICATION.md mengikuti PRD §8, bukan route lama dari MS §19.2. Next.js route groups `(public)`, `(citizen)`, `(territory)`, `(agency)`, `(admin)`, `(auth)` tidak mengubah URL. Navigasi publik: Beranda, Program, Panduan, Transparansi, Bantuan; Masuk/Daftar. Warga: Ringkasan, Pengajuan saya, Program, Pengaduan, Profil. Wilayah: Ringkasan, Usulkan warga (mandat), Daftar kasus. Instansi: Ringkasan, Review kasus, Sanggahan, Program, Review publikasi sesuai grant. Administrasi: Pengawasan, Audit, Pengaduan, Pengguna, Wilayah, Integrasi sesuai grant. Notifikasi adalah panel di topbar dan bagian dashboard, bukan rute baru. Tidak menambah ekspor, pembayaran, peta GIS, live chat petugas, ranking penerima, atau fitur lain tanpa kontrak.

## 5. Pola konten dan state
Bahasa Indonesia di seluruh UI; kode enum/permission hanya internal. Gunakan istilah konsisten: "Usulan data", "Pendaftaran program", "Perbaikan", "Sanggahan", "Pengaduan layanan", "Tiket", "Riwayat proses", "Tindak lanjut internal". Sapaan "Anda"; label formal tetapi sederhana. Tanggal lokal gunakan `Intl.DateTimeFormat('id-ID', ...)` zona yang ditetapkan server/program, tampilkan WIB jika berlaku, bukan hardcode semua wilayah. Nomor tiket sebagai teks salin jika sudah ada; draft tanpa nomor tiket. Tampilkan data kosong sebagai "Belum tersedia", bukan angka 0. Angka 0 hanya hasil aktual. Status tidak diturunkan dari warna atau jam browser.

| Situasi | Teks utama | Aksi |
|---|---|---|
| Precheck 422 sebelum tiket | "Pengajuan belum dapat dikirim. Periksa bagian berikut." | Perbaiki field atau lihat prosedur alternatif |
| Revisi | "Pengajuan perlu diperbaiki" | Perbaiki pengajuan jika diizinkan; alasan & tenggat dari server |
| Hasil tidak dapat diproses | "Pengajuan tidak dapat diproses" | Baca alasan; sanggah bila diizinkan |
| Tidak terpilih (program) | "Tidak terpilih dalam proses program" | Baca dasar aturan; sanggah bila diizinkan |
| Rekomendasi | "Direkomendasikan untuk tindak lanjut" | Lihat riwayat; belum dikirim atau ditetapkan |
| ACK simulator | "Diteruskan (simulasi)" | Riwayat; tidak mengklaim penerimaan bantuan |
| Gangguan jaringan | "Layanan sementara tidak dapat diakses" | Coba lagi; pertahankan draft di memori |
| AI tidak tersedia | "Pemeriksaan otomatis belum tersedia. Pemeriksaan manual tetap berjalan." | Lanjut proses manual |
| 403/404 objek | "Halaman tidak tersedia atau Anda tidak memiliki akses." | Kembali ke daftar aman |

Terapkan 8 state per halaman sesuai PAGE…: initial, loading, success/ready, empty, error, disabled, validation, permission denied. State UI berbeda dari state bisnis. Halaman baca tanpa mutasi: validation = filter/parameter bila ada atau N/A yang dijelaskan, disabled = kontrol yang tidak didukung/menunggu respons; success berarti data siap, bukan hasil administratif sukses. Skeleton hanya saat loading; empty hanya sesudah respons sah. Mutation error mempertahankan input tanpa menyimpan PII browser. Action sukses setelah server confirm; jangan optimistis pada keputusan.

## 6. Accessibility, privacy, performa
Target WCAG 2.2 AA, bukan klaim kepatuhan. Kontras teks normal ≥4.5:1, teks besar ≥3:1; UI/focus bermakna ≥3:1. Touch target internal ≥44×44 px, focus ring jelas dan tidak tertutup, skip link, heading semantic, keyboard, dialog trap/restore focus, aria-live untuk feedback, label eksplisit dan pesan error terkait field; dukung zoom 200%, reflow 320 CSS px (uji utama 360–430). Jangan blok paste password atau OTP, jangan meminta puzzle kognitif tanpa alternatif. Gunakan reduced-motion. Jangan mengandalkan ikon saja.

Data sintetis; mask/redact sesuai DTO; tidak ada PII publik, localStorage/sessionStorage untuk payload privat, CDN cache privat, tracking analytics berisi nama/NIK/tiket/dokumen, signed URL di log, token di JS. Screen capture QA hanya data sintetis. Berkas private, scan dahulu. Statistik publik server-suppressed; jangan menghitung total sel tersembunyi atau mengubah suppression menjadi nol. Label simulasi selalu terlihat pada data dan status simulasi; jangan menyebut "terintegrasi pemerintah" seolah nyata. Public SSR, font lokal berlisensi, lazy-load chat/grafik P1, gunakan pagination. Target performa PRD: p95 UI publik <2.5 s (lingkungan demo), perlu pengukuran aktual.

## 7. Prioritas dan keputusan desain
| ID | Rekomendasi | Alasan | Persetujuan |
|---|---|---|---|
| UI-D01 | Palet biru tinta + teal; light-only v1 | identitas layanan dan status lebih terbaca; dark mode bukan scope | PO/designer |
| UI-D02 | Plus Jakarta Sans lokal (OFL bila lisensi artefak diverifikasi), fallback system sans | bentuk ramah, bahasa Indonesia, satu keluarga font | verifikasi lisensi/font/performansi |
| UI-D03 | Tailwind v4 @theme, CSS vars, React primitives custom; jika repo v3 gunakan config mapping | token tunggal dan implementasi cepat tanpa template | FE lead, versi lockfile |
| UI-D04 | Desktop sidebar + mobile drawer; bottom nav warga saja | peran petugas butuh konteks/ruang bukti | uji navigasi di 360 px |
| UI-D05 | KPI hanya sesuai DTO; tidak grafik bila agregasi seri belum ada | mencegah data palsu | BE + PO |
| UI-D06 | Diagram alur SVG handmade dan icon Lucide (ISC bila versi diverifikasi), tanpa stock imagery di MVP | orisinal, ringan, lisensi dapat ditelusuri | tim aset |
| UI-D07 | Gap kontrak dibekukan sebelum wiring; P0 FAQ tetap tanpa LLM; P2 default mati | tenggat dan fallback | PO/FE/BE |

P0 mencakup tampilan manual identitas, pusat bantuan statis, semua proses inti sesuai hak; P1 simulator/AI/RAG/WA hanya jika flag aktif dan kontrak siap; P2 face/OCR hanya bila disetujui dan aman. Tidak ada menu P2 mati kecuali jalur manual dan penjelasan yang relevan. Jangan mengubah machine state, auth provider, DTO, SLA, rules, scope atau feature flags hanya untuk membuat desain berjalan.

## 8. Definition of Done desain → implementasi
Semua rute kanonik punya spesifikasi dan state; reusable components dan token tersedia; gate API gap ditutup per screen; FE/BE/PO sign-off; UI diuji di 360/390/430/768/1280/1440 px dan keyboard; axe tanpa critical/serious (manual tetap wajib); screenshot visual review synthetic; semua aksi nyata dan sesuai izin; tidak ada private leaks; lisensi aset dicatat; regression PRD T-ID terkait lulus. Dokumentasi ini adalah baseline desain, tidak menggantikan pengujian aplikasi.
