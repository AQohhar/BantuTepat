# RESPONSIVE_GUIDELINES.md — BantuTepat v1.0
Mobile-first; ukuran viewport CSS, bukan device pixel. Breakpoint tablet 768, desktop 1280, wide 1440. 431–767 tetap mobile; 769–1279 tablet, tidak memaksa sidebar permanen pada 1024. Uji 320 reflow juga.

| Aspek | Mobile 360–430 | Tablet 768–1279 | Desktop 1280–1439 | Large ≥1440 |
|---|---|---|---|---|
| Grid | 4 kolom, gutter 12 | 8, 16 | 12, 24 | 12, 24 |
| Page padding | 16 | 24 | 32 | 32 |
| Public header | mark+nama + menu; auth di drawer | mark + nav bila muat, jika tidak drawer | full nav, auth kanan | sama, max 1200 |
| Private sidebar | drawer max 320; topbar 64 | drawer 320; topbar 64 | fixed/sticky width 248; topbar 64 | sama, jangan membesar hanya karena ruang |
| Private width | 100%, min-width 0 | 100% | viewport−248 (1032 di 1280), inner ≈968 | viewport−248 (1192 di 1440), max 1440 |
| Cards | 1 kolom; stats 2 bila label muat | 2; public 2–3 bila min 250 | public 3; private 2–3 | public 3; private 3–4 stats bila min 200 |
| PageHeader | title/action stack, primary full width | wrap title/action | flex row align end | sama |
| Table | citizen & queue cards berlabel; audit scroll region | native table bila kolom penting muat, fallback card | native; horizontal scroll untuk audit | native, kolom tambahan hanya data sah |
| SearchFilter | search full + filter button sheet | 2–3 controls, wrap | row wrap; filter panel jika banyak | row wrap |
| FormWizard | 1 col, progress teks, footer stack / sticky safe | 1 col max680; field pair opsional | max680; aside guide bila muat | sama, jangan stretch field |
| ReviewPanel | stack: ringkas → bukti → keputusan | stack atau 2 panel jika masing-masing ≥320 | split 7/5 jika review min 360 | split 7/5 + sticky ringkasan jika ruang |
| Dialog | full-width minus 32; complex sheet/full-screen | max480/640 | center max480/640 | sama |
| Timeline | vertikal satu jalur | vertikal | vertikal, aside | sama |
| Bottom nav | hanya warga list/dashboard, 56–64 + safe area | tidak | tidak | tidak |

## 1. Layout recipes
Public container `mx-auto w-full max-w-[1200px] px-4 tablet:px-6 desktop:px-8`. Hero `grid gap-8 tablet:grid-cols-2` hanya jika teks min 300; mobile diagram di bawah CTA, tidak lebih tinggi dari 180px. Katalog `grid grid-cols-1 gap-4 tablet:grid-cols-2 desktop:grid-cols-3`; card min-width 0. Private shell `desktop:grid desktop:grid-cols-[248px_minmax(0,1fr)]`; main `min-w-0`; header sticky dengan tinggi tetap, hindari fixed desktop sidebar yang memutus zoom/reflow. Sidebar scroll independen bila tinggi viewport pendek. Content `mx-auto w-full max-w-[1440px] px-4 tablet:px-6 desktop:px-8`.

Detail `grid gap-6 desktop:grid-cols-[minmax(0,7fr)_minmax(0,5fr)]`; jika decision form kurang 360 px, stack lewat container query/ukuran nyata, bukan breakpoint buta. Form `max-w-[680px]`; pair hanya field logis (misal periode mulai/akhir), mobile selalu stack. Sticky footer hanya bila tombol sering dibutuhkan; sediakan padding bawah setara tinggi footer + safe-area, focus tidak tertutup, saat keyboard mobile terbuka gunakan flow normal. Tidak ada dua sticky action bar bersamaan.

## 2. Transformasi tabel
Jangan sembunyikan data penting dengan `display:none` tanpa alternatif. Case list mobile memakai CaseCard dari data yang sama: jenis/tiket, status, tanggal, link detail; task card operator menampilkan action hanya bila assigned. DataTable untuk audit, data kompleks: `<div role="region" aria-label="Tabel audit, geser untuk melihat kolom" tabindex="0">` dengan overflow-x-auto; hanya region ini yang scroll, bukan body. Native headers dan caption dipertahankan. Kolom prioritas: ID/tiket, status, waktu, aksi; metadata tambahan di detail scoped. Tidak pakai table layout div tanpa semantics. Pagination di luar scroll.

## 3. Navigasi dan overlay
Drawer dari kiri, backdrop, focus trap, close button 44, Escape, restore focus, body scroll lock. Bottom navigation warga pada dashboard/pengajuan-saya/pengaduan/profil bila relevan: Ringkasan, Pengajuan, Pengaduan, Profil; akses program via topbar/drawer. Jangan duplikasi tab order bila komponen hidden. Drawer filter mobile bisa bottom sheet max 85dvh, scroll isi; primary "Terapkan filter" dan "Reset". Modal sederhana max height calc(100dvh - 32px), overflow-y auto; form panjang sebagai halaman, bukan modal bertingkat. Tooltip tidak penting di touch; tampilkan helper inline. Untuk 403/401 jangan menampilkan menu privat sebelum session diketahui.

## 4. Touch, reflow, keyboard
44×44 min target, jarak minimal 8 antar aksi, text input 16px. Tap bukan hover; no hidden actions. Gunakan `min-w-0`, `break-words`, `overflow-wrap:anywhere` untuk ID/URL; heading wrap. Jangan lock orientation. `env(safe-area-inset-bottom)` bottom bar. Uji desktop 200% browser zoom dan 320 CSS px reflow (scroll tabel dikecualikan secara lokal), font-size user, landscape, keyboard virtual. Screen reader membaca satu versi konten; mobile card/table branch tidak keduanya terpapar.

## 5. Layout acceptance matrix
| Viewport | Wajib diuji |
|---|---|
| 360×800 | landing CTA terlihat, catalog search, login, wizard error summary, revision, upload pending, mobile nav safe-area, no horizontal body overflow |
| 390×844, 430×932 | form review panjang, case reason wrap, filter sheet, drawer keyboard, staff queue cards |
| 768×1024 | 2-col katalog, tablet drawer, filter wrap, audit scroll, evidence panel stack |
| 1280×800 | sidebar 248, review split atau fallback stack, data table actions, sticky topbar tidak menutup focus |
| 1440×900 | max widths, asymmetric dashboard, section alignment, publication/review forms, no whitespace stretch |
| 320px / 200% zoom | reflow, focus not obscured, dialog scrolling, no loss of content/function |

Tangkapan QA harus menggunakan synthetic fixtures. Layout tidak dinyatakan lulus sebelum browser test nyata.
