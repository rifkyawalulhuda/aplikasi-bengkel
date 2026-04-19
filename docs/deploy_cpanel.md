# Panduan Deploy ke cPanel

Panduan ini khusus untuk proyek `aplikasi-bengkel` dan mengikuti alur deploy yang paling aman untuk shared hosting cPanel dengan akses SSH.

## 1. Persiapan Sebelum Upload

Pastikan hal berikut sudah siap:

- domain sudah dibuat di cPanel, misalnya `bengkel.ngopidulur.my.id`
- akses SSH aktif
- database MySQL sudah dibuat
- user MySQL sudah dibuat dan diberi akses ke database
- PHP versi hosting kompatibel dengan Laravel 13
- folder project Laravel sudah disiapkan di server

Struktur yang disarankan:

- folder aplikasi: `/home/USERNAME/bengkel`
- document root domain: `/home/USERNAME/bengkel/public`

Jika cPanel tidak mengizinkan document root ke folder `public`, gunakan struktur fallback `public_html` sesuai konfigurasi hosting.

## 2. File `.env` Produksi

Buka file `.env` di server, lalu sesuaikan nilai berikut:

```env
APP_NAME="Bengkel Home Service"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://bengkel.ngopidulur.my.id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database_cpanel
DB_USERNAME=nama_user_cpanel
DB_PASSWORD=password_database_cpanel

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=file
MAIL_MAILER=log

WORKSHOP_CONTACT_PHONE=0895xxxxxxxx
WORKSHOP_CONTACT_WHATSAPP=0895xxxxxxxx
```

Catatan:

- nomor WhatsApp website diambil dari `.env`
- jika nomor WhatsApp berubah, jalankan ulang cache config setelah update
- `MAIL_MAILER=log` berarti email booking tidak aktif sebagai jalur utama

## 3. Upload Project ke Server

Upload seluruh source code Laravel ke folder aplikasi, misalnya:

```text
/home/USERNAME/bengkel
```

Jangan upload `node_modules` dan jangan mengandalkan file `public/hot` di production.

Jika source code berasal dari Git, jalankan di server:

```bash
cd ~/bengkel
git pull
```

Jika source code di-upload manual, pastikan folder berikut ikut terbawa:

- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `public/`
- `resources/`
- `routes/`
- `storage/`
- `vendor/` jika tidak mau install composer di server

## 4. Install Dependensi di Server

Jika server memiliki Composer:

```bash
cd ~/bengkel
composer install --no-dev --optimize-autoloader
```

Jika `vendor/` sudah di-upload dari lokal, langkah ini bisa dilewati, tetapi Composer tetap lebih disarankan.

## 5. Build Frontend Asset

Karena shared hosting biasanya tidak memakai Node.js di server, build asset dilakukan di lokal:

```bash
npm run build
```

Lalu upload folder:

- `public/build/`

Pastikan isi `public/build` lengkap, termasuk `manifest.json` dan file asset hasil build.

Jika sebelumnya ada file `public/hot`, hapus file itu di server. File tersebut hanya untuk mode development.

## 6. Jalankan Migrasi Database

Setelah `.env` benar, jalankan:

```bash
php artisan migrate --force
```

Jika database kosong, perintah ini akan membuat seluruh tabel yang dibutuhkan aplikasi.

## 7. Buat Storage Link

Jalankan:

```bash
php artisan storage:link
```

Jika link sudah ada, itu normal. Tidak perlu dibuat ulang.

## 8. Bersihkan dan Cache Konfigurasi

Setelah deploy dan setelah `.env` selesai diisi, jalankan:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Langkah ini penting supaya:

- perubahan `.env` terbaca
- route terbaru aktif
- file view dan config lebih cepat

## 9. Konfigurasi Queue untuk Booking

Aplikasi ini memakai queue database untuk beberapa proses operasional.

Tambahkan cron job di cPanel:

```bash
* * * * * cd /home/USERNAME/bengkel && /usr/local/bin/php artisan queue:work --stop-when-empty --tries=1 >> /dev/null 2>&1
```

Sesuaikan path `USERNAME` dan lokasi PHP di hosting jika berbeda.

Kalau hosting tidak memakai email booking, queue tetap berguna untuk job operasional lain.

## 10. Checklist Setelah Deploy

Setelah website dibuka di domain production, cek hal berikut:

- halaman `/` bisa dibuka
- halaman `/booking` bisa dibuka
- tombol `Booking Sekarang` berfungsi
- tombol `Hubungi Admin` menuju WhatsApp
- gambar/logo tampil normal
- CSS dan JavaScript tidak 404
- form booking bisa disubmit
- admin login bisa dibuka
- dashboard admin bisa dimuat

Jika muncul layar putih atau asset tidak tampil:

- cek apakah `public/hot` masih ada
- cek apakah `public/build` lengkap
- cek `storage/logs/laravel.log`
- cek koneksi database di `.env`

## 11. Perintah Maintenance Saat Update

Kalau ada perubahan aplikasi setelah website live, alur amannya:

```bash
cd ~/bengkel
git pull
composer install --no-dev --optimize-autoloader
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika ada perubahan frontend:

```bash
npm run build
```

Lalu upload ulang `public/build/`.

Jika ada perubahan database:

```bash
php artisan migrate --force
```

## 12. Troubleshooting Cepat

### Tampilan putih

Biasanya karena salah satu dari ini:

- asset `public/build` belum lengkap
- `public/hot` masih ada
- document root belum mengarah ke `public`
- ada error di `storage/logs/laravel.log`

### Error database

Periksa:

- nama database
- username database
- password database
- `DB_HOST=localhost`

### Link WhatsApp tidak berubah

Periksa `.env`:

- `WORKSHOP_CONTACT_PHONE`
- `WORKSHOP_CONTACT_WHATSAPP`

Lalu jalankan:

```bash
php artisan config:clear
php artisan config:cache
```

### Booking gagal walau form sudah terisi

Periksa:

- tanggal servis
- lokasi
- paket yang dipilih
- apakah package aktif
- apakah slot jadwal tersedia

## 13. Rekomendasi Singkat

Untuk deploy paling aman:

1. upload source code Laravel ke folder project
2. arahkan domain ke folder `public`
3. isi `.env` produksi
4. jalankan `composer install`
5. jalankan `php artisan migrate --force`
6. build frontend di lokal lalu upload `public/build`
7. set cron queue
8. clear cache Laravel

Kalau semua langkah di atas beres, website production biasanya sudah siap dipakai.
