# DESIGN_VALIDATION.md — static verification report
Validasi dilakukan terhadap salinan lokal PRD v1.2 dan dokumen desain ini. Ini bukan audit implementasi aplikasi.

| Pemeriksaan aktual | Hasil |
|---|---|
| Enam deliverable utama ada dan non-empty | PASS (6/6) |
| Inventory route dibandingkan langsung dengan PRD §8 (4.1–4.3) | PASS (42/42, tidak ada route hilang/ekstra) |
| ID screen unik dan delapan state per screen | PASS (42 screen; 336 state entries, termasuk N/A terjelaskan) |
| Referensi method/path eksplisit pada Data/API tiap screen | PASS (99 referensi, 61 operasi unik, cocok OpenAPI; bukan tes payload/guard runtime) |
| Komponen yang diminta brief | PASS (19/19 nama ditemukan) |
| Case, appeal, complaint state labels | PASS (27 entries diperiksa terhadap enum bersama; bukan tes transisi) |
| Kontras pasangan warna utama | PASS (25/25 pasangan dihitung dengan relative luminance sRGB) |

Operasi pendukung yang tidak direferensikan secara eksplisit pada baris Data/API halaman: GET /health, POST /notifications/{id}/read. Operasi tersebut tetap milik kontrak; health bukan rute UI baru, mark-as-read di NotificationPanel. Gap G01–G13 bukan endpoint yang telah tersedia. Pemeriksaan statis ini tidak mengesahkan kontrak kandidat v1.2.

## Kontras token
Rasio = (L terang + 0.05)/(L gelap + 0.05). Minimum 4.5 untuk teks normal dan 3 untuk batas kontrol/fokus. Pasangan dekoratif tidak diuji sebagai teks.

| Foreground | Background | Rasio | Minimum | Hasil |
|---|---|---:|---:|---|
| ink `#192C3A` | panel `#FFFFFF` | 14.36:1 | 4.5:1 | PASS |
| ink `#192C3A` | canvas `#F6F7F5` | 13.36:1 | 4.5:1 | PASS |
| muted `#485B69` | panel `#FFFFFF` | 7.06:1 | 4.5:1 | PASS |
| muted `#485B69` | canvas `#F6F7F5` | 6.57:1 | 4.5:1 | PASS |
| muted `#485B69` | panel-muted `#F0F3F3` | 6.32:1 | 4.5:1 | PASS |
| brand-700 `#204B6C` | panel `#FFFFFF` | 9.19:1 | 4.5:1 | PASS |
| panel `#FFFFFF` | brand-800 `#173C58` | 11.52:1 | 4.5:1 | PASS |
| panel `#FFFFFF` | brand-900 `#112E45` | 13.99:1 | 4.5:1 | PASS |
| panel `#FFFFFF` | teal-700 `#14675D` | 6.71:1 | 4.5:1 | PASS |
| panel `#FFFFFF` | teal-800 `#0D524A` | 9.04:1 | 4.5:1 | PASS |
| panel `#FFFFFF` | danger `#A12832` | 7.33:1 | 4.5:1 | PASS |
| panel `#FFFFFF` | danger-pressed `#7D1B24` | 10.22:1 | 4.5:1 | PASS |
| success `#17603D` | success-bg `#E8F5EC` | 6.74:1 | 4.5:1 | PASS |
| warning `#805100` | warning-bg `#FFF3D9` | 6.16:1 | 4.5:1 | PASS |
| danger `#A12832` | danger-bg `#FDECEF` | 6.43:1 | 4.5:1 | PASS |
| info `#245A85` | info-bg `#EAF2FA` | 6.46:1 | 4.5:1 | PASS |
| neutral `#485B69` | neutral-bg `#EDF1F3` | 6.21:1 | 4.5:1 | PASS |
| disabled-text `#596B77` | disabled-bg `#E9EEF0` | 4.73:1 | 4.5:1 | PASS |
| brand-700 `#204B6C` | brand-100 `#DDE9F3` | 7.45:1 | 4.5:1 | PASS |
| control-line `#74848E` | panel `#FFFFFF` | 3.86:1 | 3:1 | PASS |
| control-line `#74848E` | canvas `#F6F7F5` | 3.60:1 | 3:1 | PASS |
| focus `#185E9A` | panel `#FFFFFF` | 6.76:1 | 3:1 | PASS |
| focus `#185E9A` | canvas `#F6F7F5` | 6.29:1 | 3:1 | PASS |
| teal-700 `#14675D` | teal-50 `#E8F5F2` | 6.00:1 | 4.5:1 | PASS |
| brand-700 `#204B6C` | brand-50 `#EFF5FA` | 8.37:1 | 4.5:1 | PASS |

## Yang belum diuji
Tidak ada implementasi Next.js/React yang dibangun atau dijalankan; snippets bersifat ilustratif. CSS belum dikompilasi dengan lockfile repo. Tidak ada browser screenshot, screen-reader, axe, responsive, usability, E2E, security, API integration, performance, atau audit lisensi font/icon aktual. Target WCAG 2.2 AA belum dibuktikan. Final visual mockup/pixel-perfect belum dibuat; wireframe dalam spesifikasi adalah low-fidelity. Konfirmasi keputusan desain, API gaps dan versi dependency sebelum live wiring.

## Source fingerprints
Hashes berikut mencatat sumber yang diperiksa, tidak menyertakan ulang master atau PRD dalam paket desain.
- `01_PRD.md`: `09d99b16826104a11cc8bbd6925b8c7f562fe497af52c21659fe6ca9f37b5498`
- `05_Role_Matrix.md`: `6ffacf4c5c6fa076beb65790a0a0eef4fdd59b228107570d7670a2681dfb25ea`
- `phase0/openapi.phase0.json`: `b0e3cae32395e640477558ca30f2be04cb3bc4391777d692b4ad4e44c815963c`
- `phase0/STATE_CONTRACT.md`: `c4c6aba1a74616e6247779b18bae7d73a2c81f115e489ecf3532c23b39ff15c2`
- `phase0/AUTH_SECURITY.md`: `59ed45d381c82dc00acdb72b85794be21bb94c5b7706a38b3af6803f3c03440d`
- `phase0/packages/policy/enums.ts`: `37afb4166bcf7792c4d06f08efa7cb9f186558a45cc121bbdfb92a6a712569b1`
- `sources/BantuTepat_MASTER_SPEC_Codex_FINAL.md`: `d5b17f4b8d455318ef7cc5f7e4bd2ac95db4b3ca2d65bd8e7774b9d043724afa`
