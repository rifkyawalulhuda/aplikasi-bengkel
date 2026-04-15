# PRD V2 + Technical Implementation Blueprint
## Website Bengkel Motor Home Service
**Stack:** Laravel + Svelte + MySQL + Eloquent ORM

---

# 1. Ringkasan Produk

## Nama Produk
**Website Bengkel Motor Home Service**

## Tujuan Produk
Menyediakan website bengkel motor berbasis **Laravel + Svelte + MySQL + Eloquent ORM** untuk:
- menampilkan layanan home service servis ringan,
- menerima booking pelanggan,
- menghitung harga secara transparan,
- mengelola operasional booking dari dashboard admin,
- membantu admin memonitor traffic visitor harian.

## Ruang Lingkup MVP
Produk MVP terdiri dari 2 area utama:

### Public Area
- 1 landing page SEO-friendly
- section paket servis
- form booking home service
- dynamic pricing untuk paket custom
- input lokasi pelanggan via map pin point
- halaman sukses booking
- email konfirmasi ke pelanggan

### Admin Area
- login admin
- dashboard ringkas
- manajemen booking
- manajemen paket servis
- manajemen item servis custom
- visitor analytics harian

---

# 2. Prinsip Produk

## 2.1 Transparansi Harga
User harus tahu estimasi biaya sebelum menekan konfirmasi.

## 2.2 Booking Harus Lengkap
Admin tidak boleh menerima booking yang datanya setengah-setengah.

## 2.3 Fokus Mobile
Mayoritas user kemungkinan buka dari HP. Semua flow booking harus nyaman di mobile.

## 2.4 Operasional Lebih Penting daripada “UI Keren”
Untuk bisnis home service, dashboard operasional yang rapi lebih penting daripada animasi berlebihan.

## 2.5 MVP Harus Sempit tapi Solid
Jangan menambah payment gateway, live tracking, atau sistem mekanik kompleks di fase awal.

---

# 3. Aktor Sistem

## 3.1 Pelanggan
User publik yang datang dari Google, media sosial, atau referensi, lalu melakukan booking.

## 3.2 Admin
Pengelola operasional yang:
- memeriksa booking,
- mengubah status,
- mengatur paket,
- mengatur harga item custom,
- melihat visitor.

---

# 4. Scope Detail

## 4.1 Public Website
Public website hanya memiliki **1 landing page utama** yang berisi:
- hero
- informasi layanan
- daftar paket servis
- cara kerja
- area layanan
- FAQ
- testimoni
- CTA booking
- form booking

Booking bisa dibuat sebagai:
- section dalam landing page, atau
- modal / panel / dedicated section dynamic

Saran: **buat booking form sebagai section terpisah dalam landing page dengan URL anchor atau smooth scroll**, bukan halaman baru dulu. Lebih sederhana dan conversion-friendly.

## 4.2 Dashboard Admin
Dashboard admin adalah panel internal untuk:
- melihat semua booking
- melihat detail booking
- mengganti status booking
- membuat paket servis
- membuat item custom
- melihat visitor harian

---

# 5. Use Cases Utama

## 5.1 Pelanggan Melakukan Booking Paket Tetap
1. Pelanggan membuka landing page.
2. Pelanggan membaca layanan dan paket.
3. Pelanggan memilih Paket A atau Paket B.
4. Sistem menampilkan detail isi paket dan harga.
5. Pelanggan mengisi data lokasi, jadwal, dan kontak.
6. Pelanggan melihat ringkasan booking.
7. Pelanggan menekan konfirmasi.
8. Sistem menyimpan booking.
9. Sistem mengirim email konfirmasi.
10. Booking muncul di dashboard admin dengan status `pending`.

## 5.2 Pelanggan Melakukan Booking Paket Custom
1. Pelanggan memilih Paket Custom.
2. Sistem menampilkan daftar item custom.
3. Pelanggan memilih item-item servis.
4. Total harga berubah otomatis setiap kali pilihan berubah.
5. Pelanggan mengisi detail lain.
6. Sistem menyimpan snapshot item dan harga.
7. Email konfirmasi dikirim.

## 5.3 Admin Memproses Booking
1. Admin login ke dashboard.
2. Admin melihat booking baru.
3. Admin membuka detail booking.
4. Admin memverifikasi jadwal, lokasi, dan kebutuhan servis.
5. Admin mengubah status ke `confirmed`.
6. Saat hari H, status bisa diubah ke `on_the_way`.
7. Setelah selesai, status diubah ke `completed`.

---

# 6. Functional Requirements

## 6.1 Landing Page

### Requirement
Landing page harus:
- SEO-friendly
- mobile-first
- menampilkan value proposition yang jelas
- menampilkan paket servis dan harga
- menampilkan cakupan layanan
- memiliki CTA booking yang jelas
- memuat form booking

### Konten Wajib
- headline utama
- subheadline
- daftar layanan servis ringan
- daftar paket
- cara kerja
- area coverage
- FAQ
- testimoni
- kontak/WhatsApp
- footer

### Acceptance Criteria
- User dapat memahami layanan utama dalam 5 detik pertama.
- CTA booking terlihat tanpa harus scroll terlalu jauh di mobile.
- Paket servis tampil dengan nama, isi, dan harga.
- Terdapat penjelasan bahwa layanan hanya untuk servis ringan.
- Metadata SEO dapat diatur dari backend atau config.

## 6.2 Booking Form

### Field Wajib
- jenis paket
- detail paket / item custom
- nama pelanggan
- email pelanggan
- nomor telepon / WhatsApp
- alamat lengkap
- patokan rumah
- latitude
- longitude
- tanggal servis
- jam servis
- informasi motor
- keterangan tambahan

### Field Informasi Motor
- jenis motor: matic / bebek / sport / lainnya
- merek motor
- model motor
- tahun motor opsional
- plat nomor opsional

### Perilaku Form
- Jika user memilih Paket A/B, tampilkan harga final paket.
- Jika user memilih Paket Custom, tampilkan daftar item custom dengan harga masing-masing.
- Saat item custom dipilih, total harga diperbarui real-time.
- Tanggal servis tidak boleh di masa lalu.
- Jam servis hanya menampilkan slot yang valid.
- Lokasi harus dipilih via map dan tetap harus ada alamat teks.
- Sebelum submit, tampilkan halaman/section review.

### Acceptance Criteria
- User tidak bisa submit tanpa memilih paket.
- User tidak bisa submit tanpa mengisi kontak dan lokasi.
- User tidak bisa submit tanpa tanggal dan jam valid.
- Untuk paket custom, minimal 1 item harus dipilih.
- Total harga selalu sesuai item yang dipilih.
- Setelah submit berhasil, booking tersimpan dan user melihat halaman sukses.

## 6.3 Email Konfirmasi Pelanggan

### Isi Email
- nama pelanggan
- kode booking
- jenis paket
- detail item servis
- total harga estimasi
- alamat servis
- jadwal servis
- status awal booking
- kontak admin / WhatsApp

### Acceptance Criteria
- Email terkirim setelah booking berhasil dibuat.
- Isi email sesuai snapshot data booking.
- Jika email gagal, booking tetap tersimpan dan log error dicatat.

## 6.4 Admin Booking Management

### Fitur
- daftar booking
- filter status
- filter tanggal
- pencarian berdasarkan nama/telepon/kode booking
- detail booking
- update status
- catatan internal admin

### Kolom List Booking
- booking code
- nama pelanggan
- nomor telepon
- paket
- tanggal servis
- jam servis
- total harga
- status
- created at

### Detail Booking Harus Menampilkan
- semua data pelanggan
- detail motor
- detail paket / custom item
- snapshot harga
- alamat + patokan
- titik koordinat
- link buka map
- riwayat status
- catatan pelanggan
- catatan admin

### Acceptance Criteria
- Admin dapat melihat booking terbaru.
- Admin dapat memfilter booking per status.
- Admin dapat mengubah status booking.
- Riwayat perubahan status tersimpan.
- Detail booking mudah dibaca di desktop.

## 6.5 Paket Servis Management

### Fitur
- tambah paket
- edit paket
- hapus/nonaktifkan paket
- atur harga paket
- atur isi paket
- atur estimasi durasi
- atur urutan tampil di landing page

### Acceptance Criteria
- Admin bisa membuat paket baru.
- Paket nonaktif tidak tampil di landing page.
- Paket aktif otomatis tersedia di booking form.
- Harga paket tampil sesuai data backend.

## 6.6 Custom Service Item Management

### Fitur
- tambah item custom
- edit item custom
- nonaktifkan item custom
- kategori item
- harga item
- satuan opsional
- urutan tampil

### Acceptance Criteria
- Admin dapat mengelola item custom tanpa deploy ulang.
- Item nonaktif tidak muncul di booking form.
- Harga item custom langsung memengaruhi perhitungan total booking baru.

## 6.7 Visitor Analytics

### Tujuan
Memberi visibilitas sederhana terhadap traffic website harian.

### Data Minimum
- total visitor per hari
- unique visitor per hari
- total page views
- halaman yang dikunjungi
- referrer opsional

### Acceptance Criteria
- Admin dapat melihat jumlah visitor harian.
- Admin dapat melihat grafik 7 hari / 30 hari.
- Sistem hanya mencatat public traffic, bukan aktivitas admin internal.

---

# 7. Non-Functional Requirements

## 7.1 Performance
- landing page cepat dibuka di jaringan mobile
- gambar dioptimasi
- JS tidak terlalu berat
- interaksi booking terasa responsif

## 7.2 SEO
- title dan meta description tersedia
- heading hierarchy rapi
- struktur konten dapat di-crawl
- schema markup local business/service
- sitemap dan robots tersedia

## 7.3 Security
- semua input tervalidasi di backend
- admin route dilindungi auth
- CSRF protection aktif
- rate limit untuk submit booking
- data email dan telepon disimpan aman

## 7.4 Reliability
- booking tidak boleh hilang walaupun email gagal dikirim
- status penting memiliki logging
- harga booking disimpan sebagai snapshot

---

# 8. Information Architecture

## 8.1 Public Routes
```txt
GET  /                      -> landing page
POST /bookings              -> submit booking
GET  /booking/success       -> success page
GET  /booking/{code}        -> optional public booking summary
```

## 8.2 Admin Routes
```txt
GET  /admin/login
POST /admin/login
POST /admin/logout

GET  /admin/dashboard

GET  /admin/bookings
GET  /admin/bookings/{id}
PATCH /admin/bookings/{id}/status
PATCH /admin/bookings/{id}/notes

GET  /admin/service-packages
POST /admin/service-packages
GET  /admin/service-packages/{id}
PUT  /admin/service-packages/{id}
DELETE /admin/service-packages/{id}

GET  /admin/custom-service-items
POST /admin/custom-service-items
GET  /admin/custom-service-items/{id}
PUT  /admin/custom-service-items/{id}
DELETE /admin/custom-service-items/{id}

GET  /admin/visitors
```

---

# 9. Struktur Halaman Frontend

## 9.1 Public Pages

### Landing Page
Section:
1. Hero
2. Keunggulan layanan
3. Daftar servis ringan
4. Paket servis
5. Cara kerja
6. Coverage area
7. FAQ
8. Testimoni
9. Booking form
10. Footer

### Success Page
Menampilkan:
- pesan booking berhasil
- kode booking
- ringkasan jadwal
- CTA ke WhatsApp admin
- info email konfirmasi

### Optional Booking Summary Page
Menampilkan:
- kode booking
- status booking
- jadwal
- ringkasan layanan

## 9.2 Admin Pages
- Admin Login Page
- Admin Dashboard Page
- Booking List Page
- Booking Detail Page
- Package Management Page
- Custom Service Items Page
- Visitors Page

---

# 10. Komponen Frontend Svelte

## 10.1 Public Components
- `HeroSection.svelte`
- `ServiceHighlights.svelte`
- `PackageCard.svelte`
- `PackageComparison.svelte`
- `HowItWorks.svelte`
- `CoverageAreaSection.svelte`
- `FaqSection.svelte`
- `TestimonialsSection.svelte`
- `BookingForm.svelte`
- `BookingPackageSelector.svelte`
- `BookingCustomItemsSelector.svelte`
- `BookingMotorInfoFields.svelte`
- `BookingLocationPicker.svelte`
- `BookingSchedulePicker.svelte`
- `BookingPriceSummary.svelte`
- `BookingReviewPanel.svelte`
- `SuccessSummaryCard.svelte`

## 10.2 Admin Components
- `AdminSidebar.svelte`
- `DashboardStatCard.svelte`
- `BookingsTable.svelte`
- `BookingFilters.svelte`
- `BookingStatusBadge.svelte`
- `BookingDetailCard.svelte`
- `StatusHistoryTimeline.svelte`
- `ServicePackageForm.svelte`
- `ServicePackageItemsEditor.svelte`
- `CustomServiceItemForm.svelte`
- `VisitorsChart.svelte`

---

# 11. Model Data dan Relasi

## 11.1 User
Untuk admin dashboard.

## 11.2 ServicePackage
Fields:
- id
- name
- slug
- short_description
- description
- price
- duration_estimate_minutes
- is_active
- display_order
- timestamps

Relasi:
- hasMany `ServicePackageItem`

## 11.3 ServicePackageItem
Fields:
- id
- service_package_id
- name
- description
- display_order
- timestamps

## 11.4 CustomServiceItem
Fields:
- id
- name
- slug
- category
- description
- price
- unit_label nullable
- is_active
- display_order
- timestamps

## 11.5 Booking
Fields:
- id
- booking_code
- customer_name
- customer_email
- customer_phone
- motorcycle_type
- motorcycle_brand
- motorcycle_model
- motorcycle_year nullable
- plate_number nullable
- package_type
- service_package_id nullable
- package_name_snapshot
- package_price_snapshot
- notes nullable
- service_date
- service_time
- status
- subtotal_price
- service_fee
- total_price
- address_text
- house_landmark
- latitude
- longitude
- admin_notes nullable
- confirmed_at nullable
- completed_at nullable
- timestamps

Relasi:
- belongsTo `ServicePackage`
- hasMany `BookingCustomItem`
- hasMany `BookingStatusLog`

## 11.6 BookingCustomItem
Fields:
- id
- booking_id
- custom_service_item_id nullable
- item_name_snapshot
- item_price_snapshot
- qty
- subtotal
- timestamps

## 11.7 BookingStatusLog
Fields:
- id
- booking_id
- old_status nullable
- new_status
- changed_by nullable
- note nullable
- timestamps

## 11.8 VisitorLog
Fields:
- id
- visit_date
- ip_hash
- session_key
- path
- referrer nullable
- user_agent nullable
- is_unique_daily
- timestamps

---

# 12. Enum dan Konvensi Data

## 12.1 Booking Status
```txt
pending
confirmed
on_the_way
completed
cancelled
rescheduled
```

## 12.2 Package Type
```txt
fixed_package
custom_package
```

## 12.3 Motorcycle Type
```txt
matic
bebek
sport
lainnya
```

---

# 13. Validasi Backend

## 13.1 Booking Validation Rules

### Wajib
- `customer_name`: required, string, max 100
- `customer_email`: required, email
- `customer_phone`: required, string, max 30
- `motorcycle_type`: required
- `motorcycle_brand`: required
- `motorcycle_model`: required
- `package_type`: required
- `address_text`: required
- `house_landmark`: required
- `latitude`: required, numeric
- `longitude`: required, numeric
- `service_date`: required, date, not past
- `service_time`: required
- `notes`: nullable, max reasonable length

### Conditional
- jika `package_type = fixed_package`, maka `service_package_id` wajib
- jika `package_type = custom_package`, maka minimal ada 1 `custom_item`

### Slot Validation
- slot harus tersedia
- slot tidak boleh melewati batas kapasitas harian/jam

### Coverage Validation
- jika lokasi di luar jangkauan, booking bisa tetap disimpan dengan flag `manual_review`

---

# 14. Business Rules

## 14.1 Harga
- Harga paket tetap diambil dari master package saat booking dibuat.
- Harga item custom diambil dari master custom item saat booking dibuat.
- Setelah booking dibuat, harga tersimpan sebagai snapshot dan tidak berubah walaupun master price diubah.

## 14.2 Service Fee
- `service_fee` default 0
- nanti bisa dikembangkan berdasarkan area atau radius

## 14.3 Booking Code
Format saran:
```txt
ASM-YYYYMMDD-XXXX
```

Contoh:
```txt
ASM-20260413-0042
```

## 14.4 Status Update
- booking baru selalu `pending`
- hanya admin yang bisa ubah status
- setiap perubahan status harus dicatat ke `booking_status_logs`

---

# 15. API / Endpoint Spec Ringkas

## 15.1 Public

### `GET /`
Mengambil data landing page:
- paket aktif
- item custom aktif
- FAQ statis/dinamis
- coverage area
- metadata SEO

### `POST /bookings`
Membuat booking baru.

#### Request Payload
```json
{
  "package_type": "fixed_package",
  "service_package_id": 1,
  "custom_items": [],
  "customer_name": "Budi",
  "customer_email": "budi@email.com",
  "customer_phone": "08123456789",
  "motorcycle_type": "matic",
  "motorcycle_brand": "Honda",
  "motorcycle_model": "Beat",
  "motorcycle_year": "2022",
  "plate_number": "B1234XYZ",
  "address_text": "Jl. Mawar No. 10, Jakarta",
  "house_landmark": "Rumah pagar hitam dekat warung",
  "latitude": -6.200000,
  "longitude": 106.816666,
  "service_date": "2026-04-20",
  "service_time": "10:00",
  "notes": "Tolong datang sebelum jam 11"
}
```

#### Response Success
```json
{
  "success": true,
  "booking_code": "BMS-20260420-0001",
  "redirect_url": "/booking/success?code=BMS-20260420-0001"
}
```

## 15.2 Admin
- `GET /admin/bookings`
- `GET /admin/bookings/{id}`
- `PATCH /admin/bookings/{id}/status`
- `POST /admin/service-packages`
- `PUT /admin/service-packages/{id}`
- `POST /admin/custom-service-items`
- `PUT /admin/custom-service-items/{id}`
- `GET /admin/visitors`

---

# 16. Acceptance Criteria per Epic

## Epic 1 — Setup Project
- Project Laravel berjalan dengan Svelte frontend.
- MySQL terkoneksi.
- Auth admin dapat digunakan.
- Struktur folder dan base layout siap dipakai.

## Epic 2 — Landing Page
- Landing page tampil responsive.
- Paket servis tampil dari database.
- CTA booking dapat diakses dengan jelas.
- Metadata SEO tersedia.
- Mobile UX nyaman dipakai.

## Epic 3 — Booking Engine
- User bisa memilih paket tetap atau custom.
- Dynamic pricing berjalan benar.
- User bisa memilih lokasi via map.
- Form validasi berjalan di frontend dan backend.
- Booking berhasil tersimpan.
- Email konfirmasi terkirim atau tercatat gagal.
- Halaman sukses tampil dengan booking code.

## Epic 4 — Admin Booking Management
- Admin bisa login.
- Admin bisa melihat list booking.
- Admin bisa membuka detail booking.
- Admin bisa mengubah status booking.
- Riwayat status tersimpan.
- Admin notes dapat disimpan.

## Epic 5 — Package & Custom Item Management
- Admin bisa CRUD paket.
- Admin bisa CRUD item custom.
- Data aktif tampil di public booking form.
- Paket dan item nonaktif tidak tampil di public.

## Epic 6 — Visitor Analytics
- Visitor public tercatat per hari.
- Admin bisa melihat total visitor dan unique visitor.
- Tersedia grafik tren harian.
- Page admin tidak dihitung sebagai public visitor.

---

# 17. Edge Cases
- Harga custom item harus dihitung ulang di backend saat submit.
- Dua user booking slot yang sama harus divalidasi di backend saat penyimpanan final.
- Jika email gagal terkirim, booking tetap sukses.
- User wajib isi alamat teks dan patokan walau sudah pilih pin map.
- Jika paket dinonaktifkan saat user masih buka halaman, backend harus menolak saat submit.
- Booking custom tidak boleh tanpa minimal satu item.

---

# 18. UX Recommendations
- Sticky CTA di mobile.
- Price summary selalu terlihat.
- Slot picker harus simpel.
- Peta jangan mendominasi halaman.
- Review sebelum submit.

---

# 19. Technical Implementation Blueprint

## 19.1 Tujuan
Dokumen ini menjabarkan implementasi teknis untuk MVP website bengkel motor home service, meliputi:
- struktur modul backend,
- skema database,
- model dan relasi,
- route,
- controller,
- service/action classes,
- validasi,
- job/email,
- frontend pages dan komponen,
- urutan task implementasi,
- acceptance criteria teknis.

## 19.2 Arsitektur Aplikasi
### Public Layer
- Landing page
- Booking form
- Booking success page

### Application Layer
- Booking creation logic
- Slot validation
- Coverage validation
- Pricing calculation
- Visitor logging
- Email dispatch

### Admin Layer
- Admin auth
- Booking management
- Package management
- Custom service item management
- Visitor analytics

### Data Layer
- MySQL
- Eloquent ORM

## 19.3 Struktur Folder yang Disarankan

### Backend
```txt
app/
  Actions/
    Booking/
      CreateBookingAction.php
      CalculateBookingPriceAction.php
      GenerateBookingCodeAction.php
      ValidateBookingSlotAction.php
      ValidateCoverageAreaAction.php
    Visitor/
      TrackVisitorAction.php

  Http/
    Controllers/
      Public/
        LandingPageController.php
        BookingController.php
      Admin/
        DashboardController.php
        BookingManagementController.php
        ServicePackageController.php
        CustomServiceItemController.php
        VisitorController.php

    Requests/
      Public/
        StoreBookingRequest.php
      Admin/
        UpdateBookingStatusRequest.php
        StoreServicePackageRequest.php
        UpdateServicePackageRequest.php
        StoreCustomServiceItemRequest.php
        UpdateCustomServiceItemRequest.php

  Jobs/
    SendBookingConfirmationEmailJob.php

  Mail/
    BookingConfirmationMail.php

  Models/
    User.php
    Booking.php
    BookingCustomItem.php
    BookingStatusLog.php
    ServicePackage.php
    ServicePackageItem.php
    CustomServiceItem.php
    VisitorLog.php

  Support/
    Enums/
      BookingStatus.php
      PackageType.php
      MotorcycleType.php
```

### Frontend
```txt
resources/
  js/
    pages/
      public/
        LandingPage.svelte
        BookingSuccessPage.svelte
      admin/
        LoginPage.svelte
        DashboardPage.svelte
        BookingsIndexPage.svelte
        BookingDetailPage.svelte
        ServicePackagesPage.svelte
        CustomServiceItemsPage.svelte
        VisitorsPage.svelte

    components/
      public/
        HeroSection.svelte
        PackageCard.svelte
        BookingForm.svelte
        BookingPackageSelector.svelte
        BookingCustomItemsSelector.svelte
        BookingLocationPicker.svelte
        BookingMotorInfoFields.svelte
        BookingSchedulePicker.svelte
        BookingPriceSummary.svelte
        BookingReviewPanel.svelte
        FaqSection.svelte
        CoverageAreaSection.svelte
        TestimonialsSection.svelte

      admin/
        AdminSidebar.svelte
        DashboardStatCard.svelte
        BookingsTable.svelte
        BookingFilters.svelte
        BookingStatusBadge.svelte
        BookingDetailCard.svelte
        StatusHistoryTimeline.svelte
        ServicePackageForm.svelte
        ServicePackageItemsEditor.svelte
        CustomServiceItemForm.svelte
        VisitorsChart.svelte
```

## 19.4 Eloquent Model Methods

### User
```php
public function bookingStatusLogs()
{
    return $this->hasMany(BookingStatusLog::class, 'changed_by');
}
```

### ServicePackage
```php
public function items()
{
    return $this->hasMany(ServicePackageItem::class)->orderBy('display_order');
}

public function bookings()
{
    return $this->hasMany(Booking::class);
}
```

### ServicePackageItem
```php
public function servicePackage()
{
    return $this->belongsTo(ServicePackage::class);
}
```

### CustomServiceItem
```php
public function bookingCustomItems()
{
    return $this->hasMany(BookingCustomItem::class);
}
```

### Booking
```php
public function servicePackage()
{
    return $this->belongsTo(ServicePackage::class);
}

public function customItems()
{
    return $this->hasMany(BookingCustomItem::class);
}

public function statusLogs()
{
    return $this->hasMany(BookingStatusLog::class)->latest();
}
```

### Suggested casts
```php
protected $casts = [
    'service_date' => 'date',
    'service_time' => 'datetime:H:i',
    'latitude' => 'decimal:7',
    'longitude' => 'decimal:7',
    'subtotal_price' => 'integer',
    'service_fee' => 'integer',
    'total_price' => 'integer',
    'requires_manual_review' => 'boolean',
    'confirmed_at' => 'datetime',
    'completed_at' => 'datetime',
];
```

### BookingCustomItem
```php
public function booking()
{
    return $this->belongsTo(Booking::class);
}

public function customServiceItem()
{
    return $this->belongsTo(CustomServiceItem::class);
}
```

### BookingStatusLog
```php
public function booking()
{
    return $this->belongsTo(Booking::class);
}

public function changedByUser()
{
    return $this->belongsTo(User::class, 'changed_by');
}
```

## 19.5 Enums

### BookingStatus
```php
enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case OnTheWay = 'on_the_way';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Rescheduled = 'rescheduled';
}
```

### PackageType
```php
enum PackageType: string
{
    case FixedPackage = 'fixed_package';
    case CustomPackage = 'custom_package';
}
```

### MotorcycleType
```php
enum MotorcycleType: string
{
    case Matic = 'matic';
    case Bebek = 'bebek';
    case Sport = 'sport';
    case Lainnya = 'lainnya';
}
```

## 19.6 Route Definitions

### Public Routes
```php
Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/booking/success', [BookingController::class, 'success'])->name('bookings.success');
```

### Optional
```php
Route::get('/booking/{bookingCode}', [BookingController::class, 'showPublic'])->name('bookings.public.show');
```

### Admin Routes
```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', ...)->name('login');
        Route::post('/login', ...)->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', ...)->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/bookings', [BookingManagementController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [BookingManagementController::class, 'show'])->name('bookings.show');
        Route::patch('/bookings/{booking}/status', [BookingManagementController::class, 'updateStatus'])->name('bookings.update-status');
        Route::patch('/bookings/{booking}/notes', [BookingManagementController::class, 'updateNotes'])->name('bookings.update-notes');

        Route::resource('service-packages', ServicePackageController::class);
        Route::resource('custom-service-items', CustomServiceItemController::class);

        Route::get('/visitors', [VisitorController::class, 'index'])->name('visitors.index');
    });
});
```

## 19.7 Controllers dan Tanggung Jawab
- `LandingPageController@index`
- `BookingController@store`, `success`, `showPublic`
- `DashboardController@index`
- `BookingManagementController@index`, `show`, `updateStatus`, `updateNotes`
- `ServicePackageController` untuk CRUD paket
- `CustomServiceItemController` untuk CRUD item custom
- `VisitorController@index`

## 19.8 Form Requests

### `StoreBookingRequest`
```php
[
  'package_type' => ['required', Rule::in([...])],
  'service_package_id' => ['nullable', 'integer', 'exists:service_packages,id'],
  'custom_items' => ['nullable', 'array'],
  'custom_items.*.id' => ['required_with:custom_items', 'integer', 'exists:custom_service_items,id'],
  'custom_items.*.qty' => ['required_with:custom_items', 'integer', 'min:1'],
  'customer_name' => ['required', 'string', 'max:100'],
  'customer_email' => ['required', 'email', 'max:100'],
  'customer_phone' => ['required', 'string', 'max:30'],
  'motorcycle_type' => ['required', Rule::in([...])],
  'motorcycle_brand' => ['required', 'string', 'max:100'],
  'motorcycle_model' => ['required', 'string', 'max:100'],
  'motorcycle_year' => ['nullable', 'digits:4'],
  'plate_number' => ['nullable', 'string', 'max:20'],
  'address_text' => ['required', 'string', 'max:255'],
  'house_landmark' => ['required', 'string', 'max:255'],
  'latitude' => ['required', 'numeric'],
  'longitude' => ['required', 'numeric'],
  'service_date' => ['required', 'date', 'after_or_equal:today'],
  'service_time' => ['required', 'date_format:H:i'],
  'notes' => ['nullable', 'string', 'max:1000'],
]
```

### `UpdateBookingStatusRequest`
```php
[
  'status' => ['required', Rule::in([...])],
  'note' => ['nullable', 'string', 'max:500'],
]
```

## 19.9 Action / Service Classes

### `CalculateBookingPriceAction`
Menghitung subtotal, service fee, total price.

Output contoh:
```php
[
  'package_name_snapshot' => 'Paket A',
  'package_price_snapshot' => 85000,
  'subtotal_price' => 85000,
  'service_fee' => 0,
  'total_price' => 85000,
  'custom_items_snapshot' => [...],
]
```

### `GenerateBookingCodeAction`
Format:
```txt
BMS-YYYYMMDD-XXXX
```

### `ValidateBookingSlotAction`
- tentukan maksimal booking per slot
- hitung booking existing pada tanggal + jam yang sama
- status yang dihitung: pending, confirmed, on_the_way, rescheduled

Config saran:
```php
config('booking.max_per_slot', 3)
```

### `ValidateCoverageAreaAction`
- whitelist area manual atau radius-based
- untuk MVP, lokasi luar area dapat ditandai `requires_manual_review = true`

### `CreateBookingAction`
Pseudocode:
```php
DB::transaction(function () use ($data) {
    $slotResult = $this->validateBookingSlot->handle(...);
    $coverageResult = $this->validateCoverageArea->handle(...);
    $priceResult = $this->calculateBookingPrice->handle(...);
    $bookingCode = $this->generateBookingCode->handle(...);

    $booking = Booking::create([...]);

    foreach ($priceResult['custom_items_snapshot'] as $item) {
        BookingCustomItem::create([...]);
    }

    BookingStatusLog::create([
        'booking_id' => $booking->id,
        'old_status' => null,
        'new_status' => 'pending',
        'changed_by' => null,
        'note' => 'Booking created by customer',
    ]);

    SendBookingConfirmationEmailJob::dispatch($booking->id);
});
```

### `TrackVisitorAction`
- hanya untuk public routes
- skip admin routes
- skip asset requests

## 19.10 Jobs dan Email
- `SendBookingConfirmationEmailJob`
- `BookingConfirmationMail`

Isi email:
- nama pelanggan
- booking code
- paket / custom item
- total harga estimasi
- jadwal servis
- alamat
- status booking
- kontak admin

## 19.11 Middleware
- Visitor Tracking Middleware
- Rate Limiting submit booking
- Auth Middleware admin

## 19.12 Seeder Strategy
- `AdminSeeder`
- `ServicePackageSeeder`
- `CustomServiceItemSeeder`

## 19.13 Error Handling Strategy
### User-facing errors
- slot sudah penuh
- paket tidak lagi tersedia
- item custom tidak valid
- lokasi perlu review admin
- gagal submit, coba lagi

### Internal logging
- booking creation failure
- email sending failure
- unexpected exceptions in pricing logic
- visitor tracking failure ringan

## 19.14 Urutan Implementasi
### Phase 1 — Foundation
- setup Laravel + Svelte
- setup auth admin
- setup DB connection
- create migrations
- create models
- create enums
- create seeders

### Phase 2 — Public Read Layer
- landing page
- service package listing
- custom item data exposure
- SEO metadata basic

### Phase 3 — Booking Core
- booking form UI
- booking validation
- pricing action
- slot validation
- coverage validation
- booking create action
- success page

### Phase 4 — Email & Reliability
- booking confirmation mail
- queue setup
- email job
- logging

### Phase 5 — Admin Operations
- dashboard metrics
- booking list
- booking detail
- update status
- update notes

### Phase 6 — CMS-like Management
- service package CRUD
- package items editor
- custom service item CRUD

### Phase 7 — Visitor Analytics
- visitor middleware
- visitor log persistence
- daily aggregation query
- admin chart

## 19.15 Suggested Codex Task Breakdown
- setup Laravel project with Svelte starter
- configure MySQL
- configure auth for admin
- create base layouts for public and admin
- create migrations for packages, bookings, custom items, visitor logs
- create Eloquent models and relationships
- create enums for booking statuses and package types
- seed initial admin and sample packages
- create landing page controller
- build landing page sections
- render packages from DB
- add booking CTA and FAQ
- create booking request validation
- create price calculation action
- create slot validation action
- create booking creation action
- create booking controller store endpoint
- create booking success page
- create booking confirmation mail
- create email sending job
- dispatch job after booking creation
- handle failures with logging
- create bookings index page
- create booking detail page
- implement status update action
- implement admin notes update
- CRUD service packages
- manage service package items
- CRUD custom service items
- track public visits
- create admin visitors page
- show 7-day and 30-day summary

---

# 20. Definition of Done untuk MVP
Produk dianggap siap live ketika:
- landing page tampil baik di mobile
- paket servis bisa dikelola dari admin
- pelanggan bisa booking fixed/custom package
- harga muncul jelas dan benar
- lokasi, alamat, dan patokan tersimpan
- booking masuk ke dashboard admin
- admin bisa ubah status booking
- pelanggan menerima email konfirmasi
- visitor harian dapat dilihat admin
- sistem stabil untuk traffic awal

---

# 21. Prompt Ringkas untuk Codex

```md
Create a Laravel + Svelte application for a motorcycle home service workshop business.

Requirements:
- Public SEO-friendly landing page
- Booking form for light motorcycle home service
- Fixed service packages and custom service item selection
- Dynamic price preview
- Customer info, motorcycle info, address, house landmark, map pin coordinates, service date and time
- Booking success page
- Confirmation email sent asynchronously
- Admin dashboard with booking management
- Admin CRUD for service packages
- Admin CRUD for custom service items
- Daily visitor analytics page

Technical requirements:
- MySQL database
- Eloquent ORM
- Use action/service classes for booking domain logic
- Store immutable price snapshots in bookings
- Validate slot availability in backend
- Log booking status changes
- Track only public visitors
- Mobile-first public UI
```
