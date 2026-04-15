# Development Task List
## Website Bengkel Motor Home Service

Dokumen ini memecah PRD menjadi task implementasi yang lebih kecil dan langsung bisa dieksekusi.

---

## Epic 1 — Project Setup

### Backend Foundation
- [ ] Setup project Laravel terbaru
- [ ] Setup koneksi MySQL
- [ ] Setup environment `.env`
- [ ] Setup app key, timezone, locale, mail config
- [ ] Setup queue driver
- [ ] Setup filesystem dan logging config

### Frontend Foundation
- [ ] Install dan setup Svelte starter kit untuk Laravel
- [ ] Setup Vite build pipeline
- [ ] Buat base layout public
- [ ] Buat base layout admin
- [ ] Setup global styles dan design tokens sederhana
- [ ] Setup reusable button, input, card, badge components

### Auth Admin
- [ ] Implement login admin
- [ ] Implement logout admin
- [ ] Protect route admin dengan middleware auth
- [ ] Buat halaman login admin

### Seed Data Awal
- [ ] Buat seeder admin default
- [ ] Buat seeder sample service packages
- [ ] Buat seeder sample custom service items

---

## Epic 2 — Database Layer

### Migrations
- [ ] Create migration `service_packages`
- [ ] Create migration `service_package_items`
- [ ] Create migration `custom_service_items`
- [ ] Create migration `bookings`
- [ ] Create migration `booking_custom_items`
- [ ] Create migration `booking_status_logs`
- [ ] Create migration `visitor_logs`

### Database Constraints
- [ ] Tambahkan foreign keys yang sesuai
- [ ] Tambahkan indexes untuk kolom pencarian dan filter
- [ ] Tambahkan unique index pada `booking_code`
- [ ] Tambahkan index pada `service_date`, `service_time`, `status`
- [ ] Tambahkan index pada `visit_date`

### Models
- [ ] Buat model `ServicePackage`
- [ ] Buat model `ServicePackageItem`
- [ ] Buat model `CustomServiceItem`
- [ ] Buat model `Booking`
- [ ] Buat model `BookingCustomItem`
- [ ] Buat model `BookingStatusLog`
- [ ] Buat model `VisitorLog`
- [ ] Definisikan fillable/guarded di tiap model
- [ ] Definisikan casts di model `Booking`
- [ ] Definisikan semua relasi Eloquent

### Enums
- [ ] Buat enum `BookingStatus`
- [ ] Buat enum `PackageType`
- [ ] Buat enum `MotorcycleType`

---

## Epic 3 — Public Landing Page

### Landing Data
- [ ] Buat `LandingPageController@index`
- [ ] Query paket aktif beserta item paket
- [ ] Query custom service items aktif
- [ ] Siapkan metadata SEO dasar

### Public UI Sections
- [ ] Build Hero section
- [ ] Build Service Highlights section
- [ ] Build Package listing section
- [ ] Build How It Works section
- [ ] Build Coverage Area section
- [ ] Build FAQ section
- [ ] Build Testimonials section
- [ ] Build CTA section
- [ ] Build Footer section

### SEO
- [ ] Implement title dan meta description
- [ ] Implement Open Graph tags
- [ ] Implement schema markup local business/service
- [ ] Tambahkan sitemap
- [ ] Tambahkan robots.txt

### Responsive UI
- [ ] Optimasi layout mobile-first
- [ ] Tambahkan sticky CTA mobile
- [ ] Validasi performa awal landing page

---

## Epic 4 — Booking Form UI

### Form Structure
- [ ] Buat `BookingForm.svelte`
- [ ] Bagi form ke step visual: paket, motor, lokasi, jadwal, kontak, review
- [ ] Buat state management form
- [ ] Buat client-side validation dasar

### Package Selection
- [ ] Buat `BookingPackageSelector.svelte`
- [ ] Render fixed packages dari backend
- [ ] Tambahkan opsi custom package
- [ ] Tampilkan detail harga fixed package

### Custom Items
- [ ] Buat `BookingCustomItemsSelector.svelte`
- [ ] Render daftar custom items aktif
- [ ] Tambahkan qty selector
- [ ] Update subtotal secara real-time di UI

### Motor Information
- [ ] Buat `BookingMotorInfoFields.svelte`
- [ ] Tambahkan field type, brand, model, year, plate number

### Location Picker
- [ ] Buat `BookingLocationPicker.svelte`
- [ ] Integrasikan map provider
- [ ] Simpan latitude dan longitude
- [ ] Tambahkan input alamat lengkap
- [ ] Tambahkan input patokan rumah

### Schedule Picker
- [ ] Buat `BookingSchedulePicker.svelte`
- [ ] Pilih tanggal servis
- [ ] Pilih slot jam servis
- [ ] Validasi tanggal tidak boleh masa lalu

### Price Summary & Review
- [ ] Buat `BookingPriceSummary.svelte`
- [ ] Tampilkan subtotal, service fee, total
- [ ] Buat `BookingReviewPanel.svelte`
- [ ] Tampilkan ringkasan sebelum submit

### Submit Flow
- [ ] Submit form ke backend
- [ ] Tampilkan loading state
- [ ] Tampilkan validation errors dari backend
- [ ] Redirect ke halaman sukses setelah berhasil

---

## Epic 5 — Booking Backend Core

### Routes
- [ ] Tambahkan route `GET /`
- [ ] Tambahkan route `POST /bookings`
- [ ] Tambahkan route `GET /booking/success`
- [ ] Tambahkan optional route `GET /booking/{bookingCode}`

### Form Request
- [ ] Buat `StoreBookingRequest`
- [ ] Tambahkan validasi semua field wajib
- [ ] Tambahkan validasi conditional fixed vs custom package
- [ ] Tambahkan validasi custom item aktif
- [ ] Tambahkan validasi package aktif

### Actions / Services
- [ ] Buat `CalculateBookingPriceAction`
- [ ] Buat `GenerateBookingCodeAction`
- [ ] Buat `ValidateBookingSlotAction`
- [ ] Buat `ValidateCoverageAreaAction`
- [ ] Buat `CreateBookingAction`

### Booking Logic
- [ ] Hitung harga di backend untuk fixed package
- [ ] Hitung harga di backend untuk custom package
- [ ] Simpan snapshot nama dan harga paket
- [ ] Simpan snapshot nama dan harga custom items
- [ ] Generate booking code unik
- [ ] Set status default `pending`
- [ ] Simpan log status awal
- [ ] Set flag `requires_manual_review` jika perlu

### Transaction Safety
- [ ] Bungkus pembuatan booking dalam DB transaction
- [ ] Validasi ulang slot saat commit booking
- [ ] Pastikan booking code unik walau request paralel

### Success Page
- [ ] Buat `BookingSuccessPage.svelte`
- [ ] Tampilkan booking code
- [ ] Tampilkan jadwal servis
- [ ] Tampilkan CTA WhatsApp admin

---

## Epic 6 — Email dan Queue

### Mail
- [ ] Buat `BookingConfirmationMail`
- [ ] Isi email dengan snapshot booking
- [ ] Format email mobile-friendly

### Job
- [ ] Buat `SendBookingConfirmationEmailJob`
- [ ] Dispatch job setelah booking berhasil dibuat
- [ ] Tangani failure logging untuk email

### Queue Setup
- [ ] Setup queue worker lokal/deploy
- [ ] Test email dispatch end-to-end

---

## Epic 7 — Admin Dashboard

### Dashboard Backend
- [ ] Buat `DashboardController@index`
- [ ] Hitung total booking hari ini
- [ ] Hitung booking pending
- [ ] Hitung booking confirmed
- [ ] Hitung booking completed
- [ ] Hitung visitor hari ini
- [ ] Siapkan data grafik 7 hari

### Dashboard Frontend
- [ ] Buat `DashboardPage.svelte`
- [ ] Buat `DashboardStatCard.svelte`
- [ ] Render grafik visitor sederhana

---

## Epic 8 — Booking Management Admin

### Listing
- [ ] Buat `BookingManagementController@index`
- [ ] Implement filter status
- [ ] Implement filter tanggal
- [ ] Implement search nama/telepon/kode booking
- [ ] Implement pagination

### Detail
- [ ] Buat `BookingManagementController@show`
- [ ] Tampilkan data pelanggan
- [ ] Tampilkan data motor
- [ ] Tampilkan detail layanan
- [ ] Tampilkan pricing summary
- [ ] Tampilkan alamat + patokan + koordinat
- [ ] Tampilkan link buka map
- [ ] Tampilkan riwayat status
- [ ] Tampilkan admin notes

### Status Update
- [ ] Buat `UpdateBookingStatusRequest`
- [ ] Implement endpoint update status
- [ ] Simpan status log setiap perubahan
- [ ] Set `confirmed_at` saat status confirmed
- [ ] Set `completed_at` saat status completed

### Admin Notes
- [ ] Implement endpoint update notes
- [ ] Tampilkan catatan admin di detail booking

### Admin UI
- [ ] Buat `BookingsIndexPage.svelte`
- [ ] Buat `BookingsTable.svelte`
- [ ] Buat `BookingFilters.svelte`
- [ ] Buat `BookingDetailPage.svelte`
- [ ] Buat `BookingDetailCard.svelte`
- [ ] Buat `StatusHistoryTimeline.svelte`
- [ ] Buat `BookingStatusBadge.svelte`

---

## Epic 9 — Service Package Management

### CRUD Backend
- [ ] Buat `ServicePackageController`
- [ ] Buat `StoreServicePackageRequest`
- [ ] Buat `UpdateServicePackageRequest`
- [ ] Implement create package
- [ ] Implement update package
- [ ] Implement delete/deactivate package

### Package Items
- [ ] Simpan item-item penyusun paket
- [ ] Implement urutan tampil item paket

### Admin UI
- [ ] Buat `ServicePackagesPage.svelte`
- [ ] Buat `ServicePackageForm.svelte`
- [ ] Buat `ServicePackageItemsEditor.svelte`
- [ ] Tambahkan toggle aktif/nonaktif

### Public Sync
- [ ] Pastikan hanya paket aktif tampil di landing page
- [ ] Pastikan hanya paket aktif bisa dipilih saat booking

---

## Epic 10 — Custom Service Items Management

### CRUD Backend
- [ ] Buat `CustomServiceItemController`
- [ ] Buat `StoreCustomServiceItemRequest`
- [ ] Buat `UpdateCustomServiceItemRequest`
- [ ] Implement create item
- [ ] Implement update item
- [ ] Implement delete/deactivate item

### Admin UI
- [ ] Buat `CustomServiceItemsPage.svelte`
- [ ] Buat `CustomServiceItemForm.svelte`
- [ ] Tambahkan filter kategori
- [ ] Tambahkan toggle aktif/nonaktif

### Public Sync
- [ ] Pastikan hanya item aktif tampil di booking form
- [ ] Pastikan harga item terbaru hanya berlaku untuk booking baru

---

## Epic 11 — Visitor Analytics

### Tracking
- [ ] Buat `TrackVisitorAction`
- [ ] Buat Visitor Tracking Middleware
- [ ] Track hanya GET public pages
- [ ] Skip admin routes
- [ ] Skip asset requests
- [ ] Simpan `ip_hash`, `session_key`, `path`, `referrer`, `user_agent`
- [ ] Hitung `is_unique_daily`

### Analytics Backend
- [ ] Buat `VisitorController@index`
- [ ] Query total visitor per hari
- [ ] Query unique visitor per hari
- [ ] Query total page views
- [ ] Query top visited paths

### Admin UI
- [ ] Buat `VisitorsPage.svelte`
- [ ] Buat `VisitorsChart.svelte`
- [ ] Tampilkan summary 7 hari
- [ ] Tampilkan summary 30 hari

---

## Epic 12 — Middleware, Security, dan Reliability

### Security
- [ ] Aktifkan CSRF protection
- [ ] Tambahkan rate limit untuk submit booking
- [ ] Validasi semua input di backend
- [ ] Pastikan admin routes butuh auth

### Reliability
- [ ] Tambahkan application logging untuk booking failure
- [ ] Tambahkan logging untuk email failure
- [ ] Tambahkan graceful error handling
- [ ] Pastikan booking tetap tersimpan walau email gagal

### DX / Maintainability
- [ ] Rapikan config file booking
- [ ] Rapikan constants dan enums
- [ ] Tambahkan helper formatter harga dan status label

---

## Epic 13 — Testing

### Feature Tests
- [ ] Test landing page loads successfully
- [ ] Test booking fixed package success
- [ ] Test booking custom package success
- [ ] Test booking fails if slot unavailable
- [ ] Test booking fails if package inactive
- [ ] Test booking fails if custom item inactive
- [ ] Test booking stores price snapshots correctly
- [ ] Test booking creates initial status log
- [ ] Test email job dispatched after booking creation

### Admin Tests
- [ ] Test admin login
- [ ] Test admin cannot access dashboard without auth
- [ ] Test admin can view bookings list
- [ ] Test admin can update booking status
- [ ] Test admin can update notes
- [ ] Test admin can CRUD service packages
- [ ] Test admin can CRUD custom service items

### Visitor Tests
- [ ] Test public visit tracked
- [ ] Test admin visit not tracked
- [ ] Test unique daily visitor logic

---

## Epic 14 — Polish dan Go-Live Prep

### UX Polish
- [ ] Rapikan loading states
- [ ] Rapikan empty states
- [ ] Rapikan validation messages
- [ ] Tambahkan tombol copy nomor WA dan alamat di admin detail
- [ ] Tambahkan tombol buka Google Maps

### Performance
- [ ] Kompres gambar landing page
- [ ] Audit bundle frontend
- [ ] Optimasi query dashboard dan listing

### Deployment Prep
- [ ] Finalize production `.env`
- [ ] Setup mail provider produksi
- [ ] Setup queue worker produksi
- [ ] Setup cron jika diperlukan
- [ ] Jalankan migration & seed produksi

### Final QA
- [ ] Uji booking end-to-end dari public sampai admin
- [ ] Uji email konfirmasi
- [ ] Uji dashboard admin
- [ ] Uji visitor analytics
- [ ] Uji mobile layout di beberapa ukuran layar

---

## Milestone Rekomendasi

### Milestone 1
- Project setup
- DB layer
- Auth admin

### Milestone 2
- Landing page
- Booking form UI

### Milestone 3
- Booking backend core
- Success page
- Email confirmation

### Milestone 4
- Admin dashboard
- Booking management

### Milestone 5
- Service package CRUD
- Custom service item CRUD

### Milestone 6
- Visitor analytics
- Testing
- Polish dan deploy prep

---

## Definition of Done
- [ ] Landing page responsive dan SEO-friendly
- [ ] Booking fixed/custom package berjalan end-to-end
- [ ] Harga tampil benar dan snapshot tersimpan
- [ ] Lokasi, alamat, dan patokan tersimpan lengkap
- [ ] Email konfirmasi terkirim async
- [ ] Admin dapat kelola booking
- [ ] Admin dapat kelola paket dan item custom
- [ ] Admin dapat melihat visitor harian
- [ ] Core flows lolos QA dan testing utama

