# DESIGN_TOKENS.md — BantuTepat v1.0
Sumber implementasi `tokens.css`. Nilai di bawah adalah rekomendasi desain, tidak mengubah kontrak domain. Semua warna menggunakan sRGB, light mode default. Tidak ada gradient. Jangan hardcode HEX di komponen; gunakan semantic token.

## 1. Palette
| Token | HEX | Fungsi |
|---|---|---|
| brand-50 | #EFF5FA | latar informasi identitas |
| brand-100 | #DDE9F3 | active nav background |
| brand-300 | #91B1CC | aksen non-teks |
| brand-500 | #386990 | elemen brand sekunder |
| brand-700 / primary | #204B6C | tombol utama, link, identitas |
| brand-800 / primary-hover | #173C58 | hover |
| brand-900 / primary-pressed | #112E45 | pressed, judul hero |
| teal-50 | #E8F5F2 | surface accent |
| teal-100 | #D0EAE4 | border dekoratif teal |
| teal-700 / secondary | #14675D | aksi sekunder tertentu, aksen |
| teal-800 | #0D524A | hover sekunder |
| accent | #14675D | motif jalur, label informasi (bukan status) |
| background | #F6F7F5 | kanvas aplikasi |
| surface | #FFFFFF | card, header, form |
| surface-muted | #F0F3F3 | panel sekunder, table header |
| text | #192C3A | teks utama |
| text-muted | #485B69 | deskripsi, metadata |
| border | #D7DFE3 | pemisah dekoratif |
| border-control | #74848E | batas input yang harus terlihat |
| focus | #185E9A | fokus keyboard |
| disabled-bg | #E9EEF0 | disabled control |
| disabled-text | #596B77 | label disabled |
| success | #17603D | selesai proses teknis/administratif sesuai konteks |
| success-bg | #E8F5EC | status sukses |
| warning | #805100 | perhatian, revisi, pending yang perlu tindakan |
| warning-bg | #FFF3D9 | surface warning |
| danger | #A12832 | error, outcome tidak dapat diproses |
| danger-bg | #FDECEF | surface danger |
| danger-pressed | #7D1B24 | hover/pressed destructive button |
| info | #245A85 | proses informasi |
| info-bg | #EAF2FA | surface info |
| neutral | #485B69 | draft, arsip, belum tersedia |
| neutral-bg | #EDF1F3 | surface neutral |
| overlay | rgba(17,46,69,.48) | backdrop modal/drawer |

Pasangan yang disetujui: teks utama/muted pada surface/background; putih pada primary/hover/pressed, secondary/hover, danger; semantic foreground hanya dengan semantic background atau putih. Border tipis dekoratif tidak cukup untuk batas input. Jangan menggunakan warna status sebagai satu-satunya indikator; gunakan ikon + teks, dan reason terpisah. Focus ring 2 px offset 2 px pada latar terang, putih inner offset pada tombol gelap. Link di paragraf bergaris bawah; hover bukan satu-satunya petunjuk. `surface-muted` tidak digunakan sebagai disabled palsu. Kontras numerik hasil skrip di `DESIGN_VALIDATION.md`.

## 2. Tipografi
Satu keluarga: Plus Jakarta Sans self-host WOFF2 (jika lisensi OFL artefak versi terpilih telah diverifikasi), fallback `system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif`. Jangan remote font runtime. Weights 400 regular (body), 500 medium (label), 600 semibold (section/CTA), 700 bold (headline/stat). Jangan uppercase kalimat. Gunakan tabular-nums untuk tiket, KPI dan angka; bukan font monospace untuk body.

| Token | Size / line-height | Weight | Penggunaan |
|---|---|---|---|
| display | 44/52 desktop; 32/40 mobile | 700 | hero tunggal |
| h1 | 32/40; 26/34 mobile | 700 | judul page |
| h2 | 24/32; 22/30 mobile | 600 | section |
| h3 | 20/28 | 600 | judul card/panel |
| lead | 18/28 | 400 | ringkasan publik |
| body | 16/24 | 400 | isi & form mobile, hindari zoom iOS |
| ui | 14/22 | 400/500 | table, nav, badge, tombol desktop |
| caption | 12/18 | 500 | timestamp/metadata non-esensial |
| metric | 32/40 | 700 | angka KPI terbatas |

Panjang paragraf 55–75 karakter (max 68ch). Label form min 14 px; pesan error min 14 px. Caption bukan tempat menaruh syarat penting. Jangan truncate alasan/aturan; wrap panjang (`overflow-wrap:anywhere` untuk ID). Judul mobile jangan dipaksa satu baris.

## 3. Ruang, layout dan elevation
Scale 4 px: `0=0, 1=4, 2=8, 3=12, 4=16, 5=20, 6=24, 8=32, 10=40, 12=48, 16=64, 20=80, 24=96`. Gap ikon-label 8; field label/control 8; form group 16–24; padding card 16 mobile/24 desktop; section 32/48; hero 48/80; sidebar item 44 min-height. Grid: mobile 4 kolom/gutter 12; tablet 8/gutter 16; desktop 12/gutter 24; large 12/gutter 24. Max public content 1200, private main 1440 termasuk padding internal, reading/form 680, review form 480–560. Page padding 16/24/32 px. Breakpoints min-width 768/1280/1440; 360–430 mobile; 1024–1279 tetap drawer. Detail RESPONSIVE_GUIDELINES.

Radius: xs 4 (badge rectangular), sm 8 (field/button), md 12 (card/panel), lg 16 (dialog), full 9999 (avatar/pill hanya bila tepat). Border 1 px; selected 2 px atau inset agar tidak bergeser. Shadow `none`, `sm: 0 1px 2px rgba(17,46,69,.08)`, `md: 0 4px 16px rgba(17,46,69,.10)`, `lg: 0 16px 48px rgba(17,46,69,.16)`; card biasa border tanpa shadow, md untuk dropdown, lg untuk modal. Layer z: content 0, sticky 10, header 20, popover 30, overlay 40, dialog/drawer 50, toast 60. Overlay dalam portal; jangan menumpuk dua modal. Motion 120 ms hover, 180 ms disclosure, 220 ms drawer, ease-out; reduced-motion 0 atau fade singkat.

## 4. Icon system
Lucide-style stroke 1.75–2 px, 16 px inline, 20 px control, 24 px nav/section, 32 px empty. Satu library (lucide-react candidate; verifikasi lisensi). Ikon dekoratif aria-hidden; icon-only wajib accessible name + tooltip pada desktop dan target 44 px. Gunakan `FileText`, `ClipboardList`, `Clock`, `CheckCircle2`, `AlertTriangle`, `Info`, `CircleX`, `ShieldCheck`, `History`, `Search`, `Menu`, `ChevronDown`, `Bell`, `Upload`, `ExternalLink`, `LockKeyhole`, `RefreshCw`. Tidak perlu icon di setiap heading. Hindari ikon menyerupai segel pemerintah. 

## 5. Controls dan interaction tokens
| Control | Ukuran & style | Hover/focus/disabled |
|---|---|---|
| Primary button | bg primary, white, 44 min-height, 16px x padding, radius 8, 600 | primary-hover/pressed; focus; disabled muted, loading teks + spinner |
| Secondary | white, text primary, border-control | brand-50 hover; focus |
| Tertiary | transparent, primary text, underline pada link teks | brand-50 hover |
| Destructive | danger solid, white, confirmation untuk perubahan irreversible | darken via danger-pressed; tidak default focused |
| IconButton | 44 square, surface/transparent, accessible name | focus/hover; jangan mengandalkan tooltip untuk instruksi |
| Field / select | min 44 (48 mobile), 12 padding, 16 font, border-control, white | focus ring, invalid danger border + teks, readonly surface-muted |
| Textarea | min-height 112, vertical resize, counter bila batas DTO ada | validation; tidak mengganti label dengan placeholder |
| Checkbox/radio | 20 visual, 44 target row | checked primary + glyph; label clickable |
| Table | header surface-muted, row min 56; separator; cells 12/16 padding | sort hanya bila kontrak mendukung; focus link/action; no hover-only |
| Badge | min 24, 6×10 padding, radius 4, text 12/18 600 | label + icon, wrap; not interactive |
| Dialog | width max 480 confirm/640 complex; 16 radius, backdrop | fokus trap, escape jika aman; no double dialog |
| Drawer | 320 max (mobile ≤ viewport−32), full height; filter bottom sheet atau full page | keyboard/focus/scroll lock |
| Dropdown | radius 8, shadow md, max height 320 scroll | arrow/Escape/typeahead native/library |
| Tooltip | 12–14, dark on light text, max 240 | hover + focus, dismissible; tidak menaruh satu-satunya info penting |
| Skeleton | neutral blocks, static/reduced motion, no fake values | aria-busy pada region, satu status loading |
| Error/empty | ikon 24–32, heading h3, kalimat/tindakan | inline dekat konteks; empty berbeda dari error |

## 6. Tailwind mapping
`tokens.css` memakai `:root` + `@theme inline`; import setelah `@import "tailwindcss"` di global stylesheet (untuk v4). Utility contoh: `bg-canvas text-ink`, `bg-panel border-line`, `bg-primary text-on-primary hover:bg-primary-hover`, `text-muted`, `rounded-control`, `shadow-float`, `tablet:grid-cols-8 desktop:grid-cols-12`, `focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2`. Jika repo Tailwind v3, mapping yang sama di `theme.extend.colors/fontFamily/borderRadius/boxShadow/screens` menggunakan `var(--bt-...)`; jangan migrasi versi semata-mata karena dokumen. Kelas status statis dalam map TypeScript, jangan membangun nama kelas Tailwind dinamis. Font fallback berfungsi tanpa aset font. Tidak menyertakan font binary.
