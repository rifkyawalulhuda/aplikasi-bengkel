# Codex Context File
## Proyek Website Bengkel Motor Home Service

Dokumen ini dipakai sebagai konteks lanjutan ketika proyek dibuka lagi di Codex, agar pekerjaan bisa diteruskan tanpa harus mengulang orientasi dari nol.

## 1. Ringkasan Singkat

- Nama proyek: `aplikasi-bengkel`
- Domain bisnis: bengkel motor home service
- Fokus MVP:
  - landing page publik yang SEO-friendly
  - booking fixed package dan custom package
  - admin dashboard operasional
  - CRUD paket servis
  - CRUD item servis custom
  - visitor analytics
- Database aktif saat ini: `MySQL`
- Frontend stack: `Svelte 5 + Inertia v3 + Tailwind v4`
- Backend stack: `Laravel 13 + Fortify + Wayfinder`
- Test stack: `Pest 4`

## 2. Dokumen Acuan Utama

Sebelum mengubah fitur besar, baca dokumen ini dulu:

1. `docs/prd_bengkel.md`
2. `docs/tasks.md`
3. `docs/readme_implementasi.md`
4. `docs/codex_prompt.md`
5. `C:/Users/rifky/.codex/RTK.md`

Dokumen ini tidak menggantikan PRD. Fungsinya adalah sebagai handoff praktis tentang kondisi proyek saat ini.

### Rule tambahan

- pastikan gunakan `RTK for Codex` sebagai acuan kerja tambahan di setiap sesi lanjutan

## 3. Kondisi Implementasi Saat Ini

### Sudah tersedia

- public landing page
- booking page terpisah di `/booking`
- booking success page
- optional public booking summary by booking code
- admin login
- admin dashboard
- admin booking management
- admin service package management
- admin custom service item management
- admin visitor analytics
- email booking confirmation via queue job
- visitor tracking
- test suite Pest berjalan

### Keputusan implementasi penting

- Booking form sudah dipindah dari landing page ke halaman khusus `/booking`.
- Landing page sekarang fokus ke marketing dan conversion.
- CTA dari landing page diarahkan ke halaman booking.
- Paket di landing page saat ini menampilkan maksimal `3` paket aktif.
- Badge `Paling Diminati` sekarang dikontrol manual dari admin lewat field `is_featured`, bukan otomatis.
- Paket `inactive` bisa diaktifkan kembali dari admin.
- Public dan admin sudah memakai palet warna brand:
  - primary `#03AED2`
  - accent `#F8DE22`
  - secondary `#F45B26`
  - strong `#D12052`
- Kontak WhatsApp/telepon website sekarang sumber utamanya dari `.env` lewat `WORKSHOP_CONTACT_PHONE` dan `WORKSHOP_CONTACT_WHATSAPP`.
- Tombol `Hubungi Admin` di landing page mengarah langsung ke WhatsApp admin.
- Fitur email booking sudah dinonaktifkan sebagai default dan fokus operasional saat ini ada di WhatsApp.
- Hero pembuka halaman booking sudah dihapus supaya user langsung masuk ke alur form booking.
- Mobile booking flow sudah diringkas agar step overview tidak memakan ruang berlebih.
- Step booking berikutnya otomatis scroll ke atas section supaya alur lebih nyaman di HP.
- Sidebar/admin dashboard sempat dipoles ke tampilan default shadcn dan beberapa area sudah dikembalikan ke style library yang netral.

## 4. Route Penting yang Aktif Sekarang

### Public

- `GET /` -> landing page
- `GET /booking` -> halaman booking
- `POST /bookings` -> submit booking
- `GET /booking/success` -> halaman sukses
- `GET /booking/{booking:booking_code}` -> public booking summary

### Admin

- `GET /admin/dashboard`
- `GET /admin/bookings`
- `GET /admin/bookings/{booking:booking_code}`
- `PATCH /admin/bookings/{booking:booking_code}/status`
- `PATCH /admin/bookings/{booking:booking_code}/notes`
- `GET /admin/service-packages`
- `POST /admin/service-packages`
- `PATCH /admin/service-packages/{servicePackage}`
- `PATCH /admin/service-packages/{servicePackage}/activate`
- `PATCH /admin/service-packages/{servicePackage}/deactivate`
- `DELETE /admin/service-packages/{servicePackage}`
- `GET /admin/custom-service-items`
- `POST /admin/custom-service-items`
- `PATCH /admin/custom-service-items/{customServiceItem}`
- `PATCH /admin/custom-service-items/{customServiceItem}/deactivate`
- `DELETE /admin/custom-service-items/{customServiceItem}`
- `GET /admin/visitors`

Untuk memastikan route terbaru, jalankan:

```powershell
php artisan route:list --except-vendor
```

## 5. Login Admin Default

Seeder admin default:

- email: `admin@bengkel.test`
- password: `password`

Lokasi seeder:

- `database/seeders/AdminSeeder.php`

Jika akun tidak ada, jalankan:

```powershell
php artisan db:seed --class=AdminSeeder
```

## 6. File dan Folder yang Paling Sering Dipakai

### Konfigurasi bisnis

- `config/workshop.php`
- `config/booking.php`

### Public pages

- `resources/js/pages/public/LandingPage.svelte`
- `resources/js/pages/public/BookingPage.svelte`
- `resources/js/pages/public/BookingSuccessPage.svelte`
- `resources/js/pages/public/BookingSummaryPage.svelte`

### Public components

- `resources/js/components/public/PublicHeader.svelte`
- `resources/js/components/public/HeroSection.svelte`
- `resources/js/components/public/ServiceHighlights.svelte`
- `resources/js/components/public/PackageCardsSection.svelte`
- `resources/js/components/public/HowItWorksSection.svelte`
- `resources/js/components/public/FaqSection.svelte`
- `resources/js/components/public/PublicFooter.svelte`
- `resources/js/components/public/BookingForm.svelte`
- `resources/js/components/public/BookingPriceSummary.svelte`
- `resources/js/components/public/BookingCtaSection.svelte`
- `resources/js/components/public/BookingLocationPicker.svelte`

### Public backend

- `app/Http/Controllers/Public/LandingPageController.php`
- `app/Http/Controllers/Public/BookingController.php`
- `app/Actions/Booking/*`
- `app/Http/Requests/Public/StoreBookingRequest.php`

### Admin pages dan components

- `resources/js/pages/admin/DashboardPage.svelte`
- `resources/js/pages/admin/BookingsIndexPage.svelte`
- `resources/js/pages/admin/BookingDetailPage.svelte`
- `resources/js/pages/admin/ServicePackagesPage.svelte`
- `resources/js/pages/admin/CustomServiceItemsPage.svelte`
- `resources/js/pages/admin/VisitorsPage.svelte`
- `resources/js/components/admin/AdminSidebar.svelte`
- `resources/js/components/admin/AdminHeader.svelte`
- `resources/js/components/admin/BookingsTable.svelte`

### Admin backend

- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Http/Controllers/Admin/BookingManagementController.php`
- `app/Http/Controllers/Admin/ServicePackageController.php`
- `app/Http/Controllers/Admin/CustomServiceItemController.php`
- `app/Http/Controllers/Admin/VisitorController.php`

### Types frontend

- `resources/js/types/domain.ts`

## 7. Detail Booking Domain yang Perlu Dijaga

Saat melanjutkan pengembangan, jangan rusak aturan ini:

- controller harus tetap tipis
- validasi pakai `FormRequest`
- logic booking pakai action/service classes
- harga di frontend hanya preview
- backend adalah source of truth
- snapshot harga wajib disimpan saat booking dibuat
- slot availability divalidasi di backend
- coverage area divalidasi di backend
- booking tetap tersimpan walau email gagal
- perubahan status harus masuk `booking_status_logs`
- `visitor_logs` menyimpan hashed IP, bukan plain IP

## 8. Catatan UI/UX yang Sudah Diputuskan

- public site sudah di-refactor agar lebih dekat ke layout referensi visual yang diberikan user
- hero memakai gambar utama yang sudah beberapa kali diganti sesuai file lampiran user
- footer memakai warna brand primary
- nomor telepon di footer tetap tampil sebagai teks nomor, tetapi klik mengarah ke WhatsApp
- tombol `Hubungi Admin` di landing page juga mengarah ke WhatsApp admin
- booking mobile price preview sudah dibuat lebih ringkas/interaktif
- booking mobile step overview dibuat compact supaya ruang form lebih lega
- alamat booking memakai peta OpenStreetMap/Leaflet dengan pin, suggestion alamat, dan geocoding/reverse geocoding
- landing page package cards sekarang maksimal 3
- badge featured package berasal dari admin
- admin dashboard memakai palet brand yang sama, tetapi visualnya tetap lebih restrained daripada landing page
- dashboard admin sempat diubah ke default template shadcn sebelum disesuaikan lagi

## 9. Konfigurasi Konten yang Mudah Diubah

Beberapa text dan data statis publik diambil dari:

- `config/workshop.php`

Isi penting di sana:

- brand name
- tagline
- contact phone
- contact WhatsApp
- service areas
- SEO landing page
- highlights
- how it works
- coverage text
- FAQ
- testimonials
- CTA content

Catatan terbaru:

- field kontak sekarang dibaca dari `.env`
- `WORKSHOP_CONTACT_PHONE` dan `WORKSHOP_CONTACT_WHATSAPP` harus diisi jika ingin semua link WA konsisten
- tidak ada lagi nomor WhatsApp hardcoded sebagai fallback di UI publik

Kalau user bertanya "di mana ubah teks ini?", sering kali jawabannya ada di:

1. `config/workshop.php`
2. komponen Svelte public terkait

## 10. Catatan Khusus Service Package

- paket aktif tampil di public
- paket inactive tidak tampil di public
- paket inactive bisa diaktifkan kembali dari admin
- field `is_featured` menentukan badge `Paling Diminati`
- landing page memprioritaskan paket featured, lalu paket lain
- landing page saat ini mengambil maksimal 3 paket

Kalau nanti ingin mengubah aturan featured, cek:

- `database/migrations/*add_is_featured_to_service_packages_table.php`
- `app/Models/ServicePackage.php`
- `app/Actions/ServicePackage/UpsertServicePackageAction.php`
- `resources/js/components/admin/ServicePackageForm.svelte`
- `resources/js/components/public/PackageCardsSection.svelte`

## 11. Perintah Kerja Harian yang Berguna

### Install dan setup

```powershell
composer install
npm install
php artisan migrate --force
php artisan db:seed
```

### Development yang disarankan

Karena proyek ini sudah memakai Laravel Herd, jangan mengandalkan `php artisan serve` untuk akses web utama. Site lokal biasanya tersedia via Herd.

Yang biasa dibutuhkan:

```powershell
npm run dev
php artisan queue:listen --tries=1
```

Jika butuh build production asset:

```powershell
npm run build
```

### Testing

```powershell
php artisan test --compact
```

Untuk file tertentu:

```powershell
php artisan test --compact tests/Feature/Admin/ServicePackageManagementTest.php
```

### Formatting

```powershell
vendor/bin/pint --dirty --format agent
```

## 12. Known Gotchas

- Jika perubahan frontend tidak terlihat, biasanya perlu `npm run dev` atau `npm run build`.
- `composer run dev` bisa gagal jika `php artisan serve` bentrok dengan port lokal. Untuk proyek ini lebih aman pakai Herd untuk web server, lalu jalankan Vite dan queue secara terpisah.
- Jika muncul error Vite manifest, jalankan `npm run build` atau `npm run dev`.
- Jika phpMyAdmin malah diarahkan ke Herd, cek routing/domain lokal di environment Windows dan Herd, bukan di kode aplikasi.
- Kalau link WhatsApp tidak berubah, cek `.env` lalu jalankan `php artisan config:clear` dan `php artisan config:cache`.
- Booking public saat ini memprioritaskan WhatsApp sebagai jalur konfirmasi, jadi jangan kaget bila email booking tidak aktif secara default.

## 13. Status Testing Terakhir yang Diketahui

Status terakhir yang sempat dijalankan:

- `php artisan test --compact` -> lulus, dengan puluhan test pass dan beberapa skip environment/browser
- `npm run build` -> lulus
- test fitur `ServicePackageManagementTest` -> lulus
- verifikasi browser publik untuk tombol WhatsApp landing page -> lulus
- `npm run types:check` -> lulus setelah perubahan UI/booking terakhir

Tetap jalankan ulang test yang relevan setelah perubahan baru.

## 14. Saran Prioritas Jika Lanjut Pengembangan

Kalau melanjutkan proyek ini, urutan aman yang disarankan:

1. baca PRD dan dokumen ini
2. cek route aktif
3. cek `config/workshop.php` untuk konten publik
4. cek `resources/js/types/domain.ts` untuk kontrak data frontend
5. cek controller + action terkait fitur yang mau disentuh
6. ubah test dulu atau tambahkan test relevan
7. implementasi
8. jalankan `pint`, test terkait, lalu build frontend jika ada perubahan UI

## 15. Prompt Singkat untuk Melanjutkan di Codex

Jika ingin lanjut cepat di sesi baru, gunakan konteks seperti ini:

```md
Lanjutkan proyek Laravel + Svelte website bengkel motor home service ini.

Gunakan `docs/prd_bengkel.md`, `docs/tasks.md`, dan `docs/codex_context.md` sebagai acuan utama.

Kondisi proyek saat ini:
- public landing page, booking page, admin dashboard, booking management, package CRUD, custom item CRUD, visitor analytics, dan email booking confirmation sudah tersedia
- booking page ada di `/booking`
- service package featured badge dikontrol manual oleh admin lewat `is_featured`
- landing page menampilkan maksimal 3 paket aktif
- stack: Laravel 13, Inertia v3, Svelte 5, Tailwind v4, MySQL, Pest 4

Sebelum implementasi:
- baca file terkait
- pertahankan controller tetap tipis
- simpan logic bisnis di action/service
- jangan ubah aturan booking domain tanpa alasan jelas
- jalankan test yang relevan setelah perubahan
```

## 16. Catatan Penutup

Prinsip utama proyek ini:

- conversion-first di public
- operasional-first di admin
- modular di backend
- mobile-first di frontend
- jangan overbuild di luar PRD

Kalau ragu, selalu kembali ke PRD lalu cocokkan dengan implementasi yang sudah ada saat ini.
