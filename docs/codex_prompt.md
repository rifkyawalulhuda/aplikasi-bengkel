# Codex Prompt
## Laravel + Svelte Website Bengkel Motor Home Service

Gunakan dokumen ini sebagai prompt bertahap untuk Codex agar implementasi berjalan rapi, modular, dan tidak langsung membangun semuanya sekaligus.

---

# Cara Pakai

1. Jalankan prompt per epic, jangan semua sekaligus.
2. Setelah tiap epic selesai, review hasilnya dulu.
3. Pastikan Codex mengikuti struktur file dan domain logic yang sudah ditentukan di `prd-bengkel.md` dan `tasks.md`.
4. Prioritaskan backend correctness, lalu UI.
5. Jangan ubah requirement inti tanpa persetujuan.

---

# Global Instruction untuk Codex

```md
You are implementing a Laravel + Svelte web application for a motorcycle home service workshop business.

Follow these implementation rules strictly:
- Use Laravel with MySQL and Eloquent ORM
- Use Svelte for the frontend UI
- Keep controllers thin
- Put business logic into dedicated action/service classes
- Use FormRequest classes for validation
- Use Jobs for async email sending
- Store immutable pricing snapshots inside bookings and booking_custom_items
- Validate slot availability on the backend
- Track booking status changes in booking_status_logs
- Public UI must be mobile-first and SEO-friendly
- Admin area must be auth-protected
- Only active packages and active custom service items should be visible on public pages
- Booking must support fixed packages and custom packages
- Booking must collect customer info, motorcycle info, address, house landmark, map coordinates, service date, service time, and notes
- Do not add payment gateway, live tracking, or multi-role system in MVP
- Prefer clean, maintainable, modular code over clever abstractions
- Match implementation to prd-bengkel.md and tasks.md

When implementing:
- explain what files you will create or modify
- then generate the code
- keep code production-oriented
- include migrations, models, controllers, requests, actions, jobs, mail, routes, and Svelte components as needed
- do not skip validation or error handling
- do not invent features beyond the PRD
```

---

# Prompt 1 — Project Foundation

```md
Read the PRD and implement the initial project foundation for a Laravel + Svelte application for a motorcycle home service workshop business.

Scope for this step only:
- prepare project structure
- configure Laravel app foundation
- create enums
- create base folders for Actions, Requests, Controllers, Jobs, Mail, Models
- create base public and admin layouts in Svelte
- prepare admin auth scaffolding hooks or structure
- do not implement full booking logic yet
- do not implement full CRUD yet

Please:
1. list the files you will create
2. generate the code
3. keep the structure aligned with the PRD

Important:
- keep controllers thin
- do not put business logic inside routes
- prepare for future booking domain logic
- do not overbuild
```

---

# Prompt 2 — Database Schema and Models

```md
Implement the database layer for the motorcycle home service project.

Scope for this step only:
- create migrations for:
  - service_packages
  - service_package_items
  - custom_service_items
  - bookings
  - booking_custom_items
  - booking_status_logs
  - visitor_logs
- add indexes and foreign keys
- create Eloquent models
- define fillable or guarded fields
- define casts
- define relationships
- create enums for booking status, package type, and motorcycle type if not already created

Important rules:
- booking_code must be unique
- pricing fields must be integer-based
- booking must support immutable snapshot fields
- visitor_logs should store hashed IP, not plain IP
- include requires_manual_review on bookings

Please:
1. explain the schema briefly
2. generate migrations and models
3. keep the code clean and production-ready
```

---

# Prompt 3 — Seeder and Sample Data

```md
Implement seeders for the motorcycle home service project.

Scope:
- create AdminSeeder
- create ServicePackageSeeder
- create CustomServiceItemSeeder
- register seeders in DatabaseSeeder

Seed data should include:
- 1 default admin account
- Paket A
- Paket B
- several custom service items such as ganti oli, ganti filter oli, ganti filter udara, cek busi, maintenance bulanan

Important:
- package and item data should look realistic
- active packages and items should be ready to render on the landing page
- package items should be stored in service_package_items

Please generate the full code.
```

---

# Prompt 4 — Public Landing Page Read Layer

```md
Implement the public landing page data and rendering layer.

Scope for this step only:
- create LandingPageController@index
- fetch active service packages with package items
- fetch active custom service items
- pass SEO metadata to the frontend
- create LandingPage.svelte
- create reusable public components for:
  - hero section
  - service highlights
  - package cards
  - how it works
  - coverage area
  - FAQ
  - testimonials
  - CTA section
  - footer

Important:
- mobile-first design
- SEO-friendly structure
- clear CTA to booking section
- do not implement final booking submit yet
- packages must render from database

Please show file plan first, then code.
```

---

# Prompt 5 — Booking Form UI

```md
Implement the booking form UI for the motorcycle home service landing page.

Scope for this step only:
- create BookingForm.svelte
- create subcomponents:
  - BookingPackageSelector.svelte
  - BookingCustomItemsSelector.svelte
  - BookingMotorInfoFields.svelte
  - BookingLocationPicker.svelte
  - BookingSchedulePicker.svelte
  - BookingPriceSummary.svelte
  - BookingReviewPanel.svelte
- build a step-style UX inside one page
- support fixed package and custom package selection
- support dynamic UI subtotal preview
- support motorcycle info, customer info, address, house landmark, lat/lng, service date, service time, notes
- include loading and validation display states

Important:
- frontend price is preview only
- backend remains source of truth
- keep the form mobile-friendly
- do not implement backend store logic in this step unless minimal wiring is needed

Please generate the Svelte components and explain how the form state works.
```

---

# Prompt 6 — Booking Validation and Domain Actions

```md
Implement the booking backend domain layer.

Scope for this step only:
- create StoreBookingRequest
- create action classes:
  - CalculateBookingPriceAction
  - GenerateBookingCodeAction
  - ValidateBookingSlotAction
  - ValidateCoverageAreaAction
  - CreateBookingAction
- create BookingController@store and success method
- add routes for:
  - GET /
  - POST /bookings
  - GET /booking/success
- implement DB transaction for booking creation
- create initial booking_status_logs record with pending status
- store immutable pricing snapshots
- support fixed_package and custom_package flows

Important rules:
- validate active package and active custom items
- for custom package, require at least one item
- validate service_date is not in the past
- validate slot availability on backend
- set requires_manual_review when coverage validation fails softly
- booking creation must succeed even if email sending happens later

Please:
1. explain the flow briefly
2. show file plan
3. generate code
```

---

# Prompt 7 — Success Page and Public Booking Summary

```md
Implement the booking success page and optional public booking summary.

Scope:
- create BookingSuccessPage.svelte
- show booking code, service date, service time, status, and CTA to WhatsApp admin
- optionally implement public booking summary page by booking code

Important:
- keep the success page simple and reassuring
- design for mobile first
- do not expose sensitive internal data

Generate the necessary controller method, route wiring, and Svelte page.
```

---

# Prompt 8 — Email Confirmation and Queue

```md
Implement booking email confirmation.

Scope:
- create BookingConfirmationMail
- create SendBookingConfirmationEmailJob
- dispatch the job after booking creation
- include booking snapshot data in the email
- log failures without breaking booking creation

Email content must include:
- customer name
- booking code
- selected package or custom items
- pricing summary
- service date and time
- address and house landmark
- booking status
- admin contact information

Important:
- email sending must be asynchronous
- booking should remain saved even if email fails
- format email cleanly for mobile readers

Please generate the Job, Mail class, and dispatch wiring.
```

---

# Prompt 9 — Admin Auth and Dashboard

```md
Implement the admin authentication and dashboard.

Scope:
- create admin login page
- implement login and logout flow
- protect admin routes with auth middleware
- create DashboardController@index
- create DashboardPage.svelte
- show dashboard stats:
  - total booking today
  - pending bookings
  - confirmed bookings
  - completed bookings
  - visitor count today
  - simple 7-day visitor trend

Important:
- keep admin UI simple and operational
- do not overdesign
- do not add multiple roles

Please generate the routes, controllers, and Svelte pages.
```

---

# Prompt 10 — Admin Booking Management

```md
Implement admin booking management.

Scope:
- create BookingManagementController with:
  - index
  - show
  - updateStatus
  - updateNotes
- create UpdateBookingStatusRequest
- implement filters for status and date
- implement search by booking code, customer name, or phone
- create BookingsIndexPage.svelte
- create BookingsTable.svelte
- create BookingDetailPage.svelte
- create BookingDetailCard.svelte
- create BookingStatusBadge.svelte
- create StatusHistoryTimeline.svelte

Booking detail must show:
- customer info
- motorcycle info
- service details
- pricing summary
- address and landmark
- map link
- status history
- admin notes

Important:
- every status update must create a booking_status_logs record
- update confirmed_at and completed_at when relevant
- keep list page efficient and filterable

Please generate backend and frontend code.
```

---

# Prompt 11 — Service Package CRUD

```md
Implement admin CRUD for service packages.

Scope:
- create ServicePackageController
- create StoreServicePackageRequest
- create UpdateServicePackageRequest
- support create, edit, list, update, deactivate/delete
- support package items management
- create ServicePackagesPage.svelte
- create ServicePackageForm.svelte
- create ServicePackageItemsEditor.svelte

Important:
- only active packages should appear publicly
- keep package item editing simple and reliable
- do not overcomplicate the admin UX

Please generate the controller, requests, routes, and Svelte components.
```

---

# Prompt 12 — Custom Service Item CRUD

```md
Implement admin CRUD for custom service items.

Scope:
- create CustomServiceItemController
- create StoreCustomServiceItemRequest
- create UpdateCustomServiceItemRequest
- support create, edit, list, update, deactivate/delete
- create CustomServiceItemsPage.svelte
- create CustomServiceItemForm.svelte
- include category, description, price, unit_label, display_order, is_active

Important:
- only active items should appear in the public booking form
- price changes must affect only future bookings, not existing snapshot records

Please generate the full code.
```

---

# Prompt 13 — Visitor Tracking and Analytics

```md
Implement visitor tracking and analytics.

Scope:
- create TrackVisitorAction
- create visitor tracking middleware
- track only public GET requests
- exclude admin routes and asset requests
- store visit_date, session_key, ip_hash, path, referrer, user_agent, is_unique_daily
- create VisitorController@index
- create VisitorsPage.svelte
- create VisitorsChart.svelte
- show daily totals, unique visitors, page views, and top visited paths

Important:
- keep it simple for MVP
- do not try to build a full analytics platform
- avoid tracking admin traffic

Please generate backend and frontend code.
```

---

# Prompt 14 — Reliability, Rate Limiting, and Error Handling

```md
Implement reliability and security improvements.

Scope:
- add rate limiting to booking submission
- add graceful error handling for booking flow
- add logging for booking creation failures
- add logging for email failures
- ensure booking creation is not lost if email job fails
- add helper formatting where useful

Important:
- preserve a good user experience
- show useful validation messages
- avoid leaking internal exception details to users

Please generate the necessary middleware, config, and controller updates.
```

---

# Prompt 15 — Automated Tests

```md
Implement feature tests for the core flows.

Scope:
- test landing page loads
- test fixed package booking success
- test custom package booking success
- test booking fails if slot unavailable
- test booking fails if package inactive
- test booking fails if custom item inactive
- test booking stores immutable snapshots correctly
- test initial status log is created
- test email job is dispatched
- test admin auth protection
- test admin can update booking status
- test visitor tracking excludes admin pages

Important:
- prioritize meaningful feature tests over shallow tests
- use factories or seed data as appropriate

Please generate the test files and any required factories.
```

---

# Prompt 16 — Final Polish

```md
Polish the motorcycle home service application for MVP readiness.

Scope:
- improve empty states and loading states
- improve validation messages
- add copy buttons for WhatsApp number and address in admin booking detail if appropriate
- add button to open map link from admin booking detail
- review responsive behavior
- clean up repetitive code
- improve accessibility labels

Important:
- do not introduce new product scope
- focus on clarity, operational usefulness, and mobile UX

Please list the final polish changes, then generate the updates.
```

---

# Prompt Penjaga Scope

Gunakan prompt ini jika Codex mulai terlalu jauh.

```md
Stay strictly within the approved MVP scope.
Do not add:
- payment gateway
- real-time mechanic tracking
- multi-role authorization beyond admin
- complex analytics platform
- chat system
- coupon system
- marketplace features
- multi-branch management

Keep the implementation aligned with the PRD and existing tasks.
Prefer simpler maintainable code.
```

---

# Prompt Review Hasil

Gunakan ini setelah tiap epic selesai.

```md
Review the implementation you just produced.

Please:
- identify any mismatch against the PRD
- identify any missing validation or error handling
- identify any places where controllers are too fat
- identify any code that should move into action/service classes
- identify potential database or relationship issues
- identify likely UI/UX issues on mobile
- then propose a concise patch plan
```

---

# Urutan Eksekusi yang Disarankan

1. Prompt 1 — Project Foundation
2. Prompt 2 — Database Schema and Models
3. Prompt 3 — Seeder and Sample Data
4. Prompt 4 — Public Landing Page Read Layer
5. Prompt 5 — Booking Form UI
6. Prompt 6 — Booking Validation and Domain Actions
7. Prompt 7 — Success Page and Public Booking Summary
8. Prompt 8 — Email Confirmation and Queue
9. Prompt 9 — Admin Auth and Dashboard
10. Prompt 10 — Admin Booking Management
11. Prompt 11 — Service Package CRUD
12. Prompt 12 — Custom Service Item CRUD
13. Prompt 13 — Visitor Tracking and Analytics
14. Prompt 14 — Reliability, Rate Limiting, and Error Handling
15. Prompt 15 — Automated Tests
16. Prompt 16 — Final Polish

---

# Catatan Akhir

- Jangan kasih semua prompt sekaligus ke Codex.
- Jalankan satu per satu.
- Setelah tiap tahap selesai, review hasil dan cek apakah masih sesuai `prd-bengkel.md`.
- Untuk bagian maps, boleh gunakan provider yang paling mudah diimplementasikan dulu, selama output latitude, longitude, alamat, dan patokan rumah tetap didukung.
- Kalau Codex mulai terlalu “kreatif”, pakai Prompt Penjaga Scope.
