# README Implementasi
## Website Bengkel Motor Home Service

Dokumen ini menjadi panduan utama untuk mulai implementasi project **website bengkel motor home service** menggunakan **Laravel + Svelte + MySQL + Eloquent ORM**.

Gunakan file ini sebagai titik masuk sebelum membaca dokumen lain.

---

# 1. Tujuan Project

Project ini bertujuan membangun website bengkel motor dengan fokus pada:
- landing page SEO-friendly,
- booking home service untuk servis ringan,
- dashboard admin untuk operasional booking,
- manajemen paket servis,
- manajemen item servis custom,
- visitor analytics harian.

Scope MVP difokuskan agar cepat selesai, mudah diuji, dan langsung bisa dipakai untuk validasi operasional bisnis.

---

# 2. Stack Teknologi

## Backend
- Laravel
- PHP
- MySQL
- Eloquent ORM

## Frontend
- Svelte
- Vite

## Supporting
- Queue untuk email async
- Mail driver / SMTP
- Maps provider untuk input latitude dan longitude

---

# 3. Dokumen Acuan

Project ini menggunakan 3 dokumen utama:

## `prd-bengkel.md`
Dokumen kebutuhan produk dan blueprint teknis.

Isi utama:
- ruang lingkup produk,
- use case,
- functional requirements,
- model data,
- route,
- controller,
- action/service classes,
- acceptance criteria.

## `tasks.md`
Checklist implementasi per modul.

Isi utama:
- daftar epic,
- task teknis detail,
- urutan pekerjaan,
- definition of done.

## `codex-prompt.md`
Prompt bertahap untuk Codex.

Isi utama:
- global instruction,
- prompt implementasi per epic,
- scope guard,
- review prompt.

---

# 4. Cara Mulai Implementasi

Urutan kerja yang disarankan:

1. Baca `prd-bengkel.md`
2. Baca `tasks.md`
3. Gunakan `codex-prompt.md` secara bertahap
4. Implementasikan per epic, jangan sekaligus
5. Review hasil tiap epic sebelum lanjut ke tahap berikutnya

---

# 5. Urutan Eksekusi Disarankan

## Tahap 1 — Foundation
Kerjakan:
- setup Laravel project,
- setup Svelte,
- setup MySQL,
- setup auth admin,
- struktur folder backend dan frontend.

## Tahap 2 — Database Layer
Kerjakan:
- migration,
- model,
- relasi,
- enums,
- seeders.

## Tahap 3 — Public Landing Page
Kerjakan:
- landing page,
- package rendering dari database,
- section layanan,
- section FAQ,
- CTA booking.

## Tahap 4 — Booking Engine
Kerjakan:
- booking form,
- fixed package,
- custom package,
- dynamic price preview,
- backend booking validation,
- booking creation,
- success page.

## Tahap 5 — Email
Kerjakan:
- mail class,
- job email,
- queue,
- error logging email.

## Tahap 6 — Admin Dashboard
Kerjakan:
- login,
- dashboard summary,
- booking list,
- booking detail,
- update status,
- admin notes.

## Tahap 7 — CRUD Admin
Kerjakan:
- service package CRUD,
- service package items editor,
- custom service item CRUD.

## Tahap 8 — Visitor Analytics
Kerjakan:
- tracking middleware,
- visitor log,
- analytics page admin.

## Tahap 9 — Reliability dan Testing
Kerjakan:
- rate limiting,
- graceful error handling,
- feature tests,
- final QA.

---

# 6. Aturan Implementasi

Ikuti aturan ini agar hasil project tetap rapi:

## Backend
- controller harus tipis,
- validasi menggunakan FormRequest,
- logic bisnis pakai Action / Service classes,
- email dikirim async via Job,
- booking dibuat dalam DB transaction,
- harga disimpan sebagai snapshot,
- perubahan status dicatat di `booking_status_logs`.

## Frontend
- mobile-first,
- fokus ke conversion,
- booking form jangan terlalu rumit,
- price summary harus jelas,
- admin UI harus sederhana dan operasional.

## Scope
Jangan tambahkan dulu:
- payment gateway,
- live tracking mekanik,
- multi-role kompleks,
- chat system,
- coupon system,
- multi-branch management,
- marketplace features.

---

# 7. Struktur Modul Utama

## Public
- landing page
- booking form
- booking success page
- optional public booking summary

## Admin
- login
- dashboard
- booking management
- service package management
- custom service item management
- visitor analytics

## Domain Logic
- calculate booking price
- generate booking code
- validate booking slot
- validate coverage area
- create booking
- track visitor
- send booking confirmation email

---

# 8. Data Penting yang Harus Ada

## Booking
Setiap booking minimal harus menyimpan:
- booking code,
- nama pelanggan,
- email,
- nomor telepon / WhatsApp,
- jenis motor,
- merek motor,
- model motor,
- paket atau item custom,
- snapshot harga,
- tanggal servis,
- jam servis,
- alamat,
- patokan rumah,
- latitude,
- longitude,
- status,
- admin notes opsional.

## Paket
- nama paket,
- deskripsi,
- harga,
- estimasi durasi,
- item paket,
- status aktif.

## Item Custom
- nama item,
- kategori,
- harga,
- unit,
- status aktif.

---

# 9. Prinsip Booking

Beberapa prinsip ini wajib dijaga:

- frontend total harga hanya preview,
- backend adalah sumber kebenaran,
- slot booking harus divalidasi di backend,
- package aktif harus dicek ulang saat submit,
- custom item aktif harus dicek ulang saat submit,
- booking tetap tersimpan walau email gagal,
- lokasi di luar area bisa ditandai `requires_manual_review`.

---

# 10. Saran Workflow dengan Codex

Gunakan `codex-prompt.md` per tahap.

Flow kerja yang disarankan:

1. kirim satu prompt epic ke Codex,
2. review hasil file yang dibuat,
3. cek apakah sesuai PRD,
4. cek apakah ada logic yang terlalu ditaruh di controller,
5. cek validasi dan error handling,
6. baru lanjut ke prompt berikutnya.

Jangan minta Codex membangun semua fitur sekaligus.

---

# 11. Checklist Review Tiap Epic

Setelah satu epic selesai, cek:
- apakah file yang dibuat sesuai scope,
- apakah naming konsisten,
- apakah controller tetap tipis,
- apakah logic bisnis masuk ke action/service,
- apakah relasi model benar,
- apakah UI mobile masih rapi,
- apakah tidak ada fitur liar di luar PRD.

---

# 12. Definition of Done MVP

MVP dianggap siap ketika:
- landing page tampil baik di mobile,
- paket tampil dari database,
- pelanggan bisa booking fixed package,
- pelanggan bisa booking custom package,
- total harga tampil benar,
- booking tersimpan dengan snapshot harga,
- email konfirmasi terkirim async,
- admin bisa login,
- admin bisa melihat booking,
- admin bisa mengubah status booking,
- admin bisa mengelola paket,
- admin bisa mengelola item custom,
- admin bisa melihat visitor harian,
- core flow lolos testing utama.

---

# 13. Catatan Akhir

Project ini akan jauh lebih sehat kalau dibangun kecil tapi benar.

Fokus utama:
- booking flow yang solid,
- dashboard admin yang enak dipakai,
- data yang lengkap untuk operasional,
- codebase yang modular dan gampang dirawat.

Jangan kejar fitur banyak dulu. Kejar alur inti yang stabil.

