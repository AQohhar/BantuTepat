# Inventaris komponen frontend BantuTepat

Status baseline: source tree diperiksa statis pada FE-00. Keberadaan komponen lama tidak membuktikan kesesuaian desain v1.0.

## Komponen yang sudah ada

| Komponen saat ini | Lokasi | Cakupan saat ini | Adaptasi yang direncanakan |
|---|---|---|---|
| Public layout | resources/views/layouts/public.blade.php | Shell publik dengan Vite. | Selaraskan token, skip link, metadata, header/footer dan batas lebar dengan Public layout spec. |
| Navbar / nav item | resources/views/components/public/navbar.blade.php, nav-item.blade.php | Navigasi publik dan menu layar kecil. | Pertahankan sebagai PublicHeader setelah direvisi; jangan samakan dengan private nav. |
| Footer | resources/views/components/public/footer.blade.php | Link layanan/kebijakan. | Jadikan PublicFooter dengan konten yang disetujui. |
| InformationPage / section links | resources/views/components/public/information-page.blade.php, section-links.blade.php | Layout halaman informasi publik. | Reuse untuk kebijakan setelah copy, heading, dan state diselaraskan. |
| Program status/photo | resources/views/components/public/program-status.blade.php, program-photo.blade.php | Informasi visual program. | Ganti label sesuai enum v1.2; cek kebutuhan/provenance aset. |
| FilterSelect | resources/views/components/public/filter-select.blade.php | Select filter publik. | Jadikan bagian SearchFilter; dukung hanya filter API yang sudah dibekukan. |
| ActionLink | resources/views/components/ui/action-link.blade.php | Link aksi. | Pertahankan sebagai primitive link; tetapkan variants dan focus token. |
| Demo navigation/status/timeline/revision/review | resources/views/components/public/demo-*.blade.php | Komponen alur simulasi lama. | Tetap isolated; jika direuse, pisahkan presentasi dari data/controller demo dan map hanya status kanonik. |

## Inventory shared components target

| Komponen wajib | Baseline | Sumber data dan batas perilaku |
|---|---|---|
| PublicHeader | Parsial: navbar. | Status sesi dari kontrak; nav publik saja. |
| PublicFooter | Parsial: footer. | Link legal/bantuan resmi yang telah diverifikasi. |
| PrivateSidebar | Belum ada. | Menu per permission dan scope, desktop 248 px. |
| MobileNavigation | Parsial hanya menu publik. | Drawer untuk semua role; bottom bar warga di halaman sesuai spec. |
| PageHeader | Belum ada reusable component. | Breadcrumb, satu h1, deskripsi, aksi yang diizinkan. |
| StatCard | Belum ada reusable component. | Nilai actual, null “Belum tersedia”, suppressed “Disembunyikan untuk privasi”; tidak membuat tren. |
| ProgramCard | Belum ada reusable component. | Data versi publik saja; CTA mengikuti mode/status/preview server. |
| CaseCard | Belum ada reusable component. | Kasus owner/mandate/assigned dari server; draf tidak memiliki tiket. |
| StatusBadge | Parsial: status program dan tiket demo. | Label/tone dipisahkan untuk case, appeal, complaint, program, upload, integration; exhaustive terhadap enum bersama. |
| CaseTimeline | Parsial: demo timeline. | Event server aktual; tanpa estimasi progres, actor/PII yang tidak berhak, atau event rekaan. |
| DataTable | Belum ada. | Table accessible, cursor pagination, tidak mengarang total/sort/filter. |
| SearchFilter | Parsial: select filter katalog. | Query sesuai API, reset cursor, hasil filter lokal tidak diklaim sebagai hasil global. |
| FormWizard | Parsial: wizard demo. | Definisi field dari schema API versi; error summary; save/submit hanya dari response backend. |
| DocumentUploader | Belum ada. | Upload privat dan scan status server; gambar bukan bukti CLEAN; no public storage. |
| ReviewPanel | Belum ada. | Assignment/capability dan evidence server; no default decision; AI hanya advisory. |
| AuditTimeline | Belum ada. | Read-only, scoped, redacted; bukan timeline warga. |
| ConfirmationDialog | Belum ada reusable dialog. | Focus trap/restore, aksi spesifik, guard/expected version, cegah double submit. |
| EmptyState | Belum ditemukan reusable state. | Hanya saat GET berhasil dan data kosong; bedakan tidak ada hasil filter. |
| ErrorState | Belum ditemukan reusable state. | Pesan aman, retry sesuai error contract, tanpa stack/PII. |
| LoadingState / Skeleton | Belum ditemukan reusable state. | aria-busy, skeleton tanpa angka palsu, reduced motion. |

## UI primitives target

Belum ditemukan katalog Blade primitives generik untuk Button, Input, Textarea, Select, Checkbox, Radio, FormField, Badge, Card, Table, Pagination, Dialog, Drawer, Dropdown, Tooltip, Alert, Toast, Skeleton, Tabs, dan Breadcrumb. Sebagian native controls dan CSS mungkin digunakan di view satuan; FE-02 membangun variants reusable, bukan menyalin pattern satu per satu.

Ketentuan primitives:

- Gunakan Blade props/slots dan class semantic token; Bahasa Indonesia untuk semua label.
- Control minimal 44 px (48 px untuk field mobile bila sesuai design tokens), label eksplisit, helper/error terhubung, visible focus.
- Gunakan native HTML untuk kontrol sederhana; dialog/drawer harus menangani keyboard dan focus.
- Tombol mutation hanya diberikan bila capability tersedia; link untuk navigasi; disabled reason tetap dapat dibaca.
- Status menggunakan teks + ikon/tone, tidak warna saja; tone tidak menambah state bisnis.
- Empty, loading, error, permission denied, dan validation adalah state terpisah.

## Tokens dan asset

resources/css/app.css saat ini mendefinisikan token berbeda dari docs/BantuTepat_UIUX_Design_v1.0/tokens.css dan berisi banyak gradient/shadow. FE-01 perlu menjadikan tokens.css sumber kanonik atau hasil build tunggal, tanpa drift. Font project saat ini Segoe/system; Plus Jakarta Sans belum ada sebagai berkas lokal. Lucide belum ditemukan sebagai dependency.

Sebelum memakai font, icon, logo, atau foto, lengkapi docs/BantuTepat_PRD_Package_v1.2/phase0/ASSET_LICENSE_INVENTORY.csv dengan asal, lisensi, versi, kebutuhan atribusi, dan bukti. Tidak ada klaim provenance pada aset yang ada.

## Urutan pembangunan komponen

1. FE-01: tokens, typography, Vite/Tailwind integration, Alpine/ES module entry points.
2. FE-02: controls, feedback, states, public/private layout, navigation, page header, cards, table/filter/timeline.
3. FE-03: komponen publik dan auth yang bergantung pada kontrak.
4. FE-04—FE-07: form, review, admin, dan role components hanya pada screen yang memerlukannya.
5. FE-08: P1 enhancements setelah gate P0; P2 tetap mati tanpa approval.
