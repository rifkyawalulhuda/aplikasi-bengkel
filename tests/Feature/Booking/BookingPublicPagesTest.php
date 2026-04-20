<?php

use App\Models\Booking;
use App\Models\BookingCustomItem;
use App\Models\BookingSetting;
use App\Models\CustomServiceItem;
use App\Models\ServicePackage;
use App\Support\Enums\BookingStatus;
use Inertia\Testing\AssertableInertia as Assert;

function createLandingServicePackage(array $overrides = []): ServicePackage
{
    static $sequence = 1;

    $servicePackage = ServicePackage::query()->create(array_merge([
        'name' => 'Paket Landing '.$sequence,
        'slug' => 'paket-landing-'.$sequence,
        'short_description' => 'Paket untuk kebutuhan test landing page.',
        'description' => 'Deskripsi paket landing page.',
        'price' => 110000,
        'duration_estimate_minutes' => 75,
        'is_active' => true,
        'display_order' => $sequence,
    ], $overrides));

    $servicePackage->items()->createMany([
        ['name' => 'Ganti oli', 'description' => 'Oli mesin sesuai kebutuhan.', 'display_order' => 1],
        ['name' => 'Cek rem', 'description' => 'Pemeriksaan rem depan dan belakang.', 'display_order' => 2],
    ]);

    $sequence++;

    return $servicePackage->fresh('items');
}

function createLandingCustomServiceItem(array $overrides = []): CustomServiceItem
{
    static $sequence = 1;

    $customServiceItem = CustomServiceItem::query()->create(array_merge([
        'name' => 'Item Landing '.$sequence,
        'slug' => 'item-landing-'.$sequence,
        'category' => 'Perawatan',
        'description' => 'Item untuk kebutuhan test landing page.',
        'price' => 45000,
        'unit_label' => 'layanan',
        'is_active' => true,
        'display_order' => $sequence,
    ], $overrides));

    $sequence++;

    return $customServiceItem;
}

function createPublicBooking(array $overrides = []): Booking
{
    return Booking::query()->create(array_merge([
        'booking_code' => 'ASM-20260414-0001',
        'customer_name' => 'Rifky',
        'customer_email' => 'rifky@example.com',
        'customer_phone' => '081234567890',
        'motorcycle_type' => 'matic',
        'motorcycle_brand' => 'Honda',
        'motorcycle_model' => 'Beat',
        'motorcycle_year' => '2023',
        'plate_number' => 'B1111XYZ',
        'package_type' => 'custom_package',
        'service_package_id' => null,
        'package_name_snapshot' => 'Paket Custom',
        'package_price_snapshot' => 0,
        'notes' => 'Catatan internal pelanggan',
        'service_date' => now()->addDays(2)->format('Y-m-d'),
        'service_time' => '10:00',
        'status' => BookingStatus::Pending,
        'subtotal_price' => 125000,
        'service_fee' => 0,
        'total_price' => 125000,
        'address_text' => 'Jl Contoh No 12, Bogor',
        'house_landmark' => 'Pagar hijau',
        'latitude' => '-6.5900000',
        'longitude' => '106.8000000',
        'admin_notes' => 'Catatan admin',
        'requires_manual_review' => false,
    ], $overrides));
}

test('landing page loads with public booking data and only active services', function () {
    BookingSetting::query()->updateOrCreate([
        'id' => 1,
    ], [
        'footer_address' => 'Jl. Badami Ciherang, Telukjambe Barat, Kab. Karawang',
        'footer_latitude' => '-6.3025000',
        'footer_longitude' => '107.3035000',
    ]);

    $activePackage = createLandingServicePackage([
        'name' => 'Paket Aktif Landing',
        'slug' => 'paket-aktif-landing',
        'display_order' => 1,
    ]);

    createLandingServicePackage([
        'name' => 'Paket Nonaktif Landing',
        'slug' => 'paket-nonaktif-landing',
        'is_active' => false,
        'display_order' => 2,
    ]);

    $activeCustomItem = createLandingCustomServiceItem([
        'name' => 'Ganti Oli Aktif',
        'slug' => 'ganti-oli-aktif',
        'display_order' => 1,
    ]);

    createLandingCustomServiceItem([
        'name' => 'Cek CVT Nonaktif',
        'slug' => 'cek-cvt-nonaktif',
        'is_active' => false,
        'display_order' => 2,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/LandingPage')
            ->where('seo.canonicalUrl', route('home'))
            ->where('workshop.footerLocation.address', 'Jl. Badami Ciherang, Telukjambe Barat, Kab. Karawang')
            ->where('workshop.footerLocation.latitude', '-6.3025000')
            ->where('workshop.footerLocation.longitude', '107.3035000')
            ->has('availableSlots')
            ->has('packages', 1)
            ->where('packages.0.name', $activePackage->name)
            ->where('packages.0.price', $activePackage->price)
            ->has('packages.0.items', 2)
            ->has('customItems', 1)
            ->where('customItems.0.name', $activeCustomItem->name)
            ->where('customItems.0.price', $activeCustomItem->price)
            ->missing('packageTypes')
            ->missing('motorcycleTypes'));
});

test('booking page loads full booking flow data and accepts safe prefill query', function () {
    $activePackage = createLandingServicePackage([
        'name' => 'Paket Booking Landing',
        'slug' => 'paket-booking-landing',
        'display_order' => 1,
    ]);

    createLandingCustomServiceItem([
        'name' => 'Maintenance Booking Landing',
        'slug' => 'maintenance-booking-landing',
        'display_order' => 1,
    ]);

    $this->get(route('bookings.create', [
        'package' => $activePackage->slug,
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/BookingPage')
            ->where('seo.canonicalUrl', route('bookings.create'))
            ->has('packageTypes', 2)
            ->has('motorcycleTypes')
            ->has('availableSlots')
            ->has('packages', 1)
            ->where('prefill.packageSlug', $activePackage->slug)
            ->where('prefill.startsInCustomMode', false));
});

test('booking page switches to custom mode when custom prefill is requested', function () {
    createLandingServicePackage([
        'name' => 'Paket Booking Custom',
        'slug' => 'paket-booking-custom',
        'display_order' => 1,
    ]);

    $this->get(route('bookings.create', [
        'package' => 'custom',
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/BookingPage')
            ->where('prefill.packageSlug', null)
            ->where('prefill.startsInCustomMode', true));
});

test('booking page ignores invalid package prefill safely', function () {
    createLandingServicePackage([
        'name' => 'Paket Valid Saja',
        'slug' => 'paket-valid-saja',
        'display_order' => 1,
    ]);

    $this->get(route('bookings.create', [
        'package' => 'paket-tidak-ada',
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/BookingPage')
            ->where('prefill.packageSlug', null)
            ->where('prefill.startsInCustomMode', false));
});

test('booking success page renders reassuring public-safe booking details', function () {
    $booking = createPublicBooking([
        'booking_code' => 'ASM-20260414-0042',
        'status' => BookingStatus::Confirmed,
        'service_date' => '2026-04-20',
        'service_time' => '14:00',
    ]);

    $this->get(route('bookings.success', ['code' => $booking->booking_code]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/BookingSuccessPage')
            ->where('booking.code', $booking->booking_code)
            ->where('booking.serviceDate', '20 Apr 2026')
            ->where('booking.serviceTime', '14:00')
            ->where('booking.status', 'confirmed')
            ->where('booking.statusLabel', 'Sudah dikonfirmasi')
            ->where('bookingCode', $booking->booking_code)
            ->where('whatsAppUrl', fn (string $url): bool => str_contains($url, $booking->booking_code))
            ->missing('booking.customer_name')
            ->missing('booking.address_text')
            ->missing('booking.total_price'));
});

test('public booking summary only exposes safe summary fields', function () {
    $booking = createPublicBooking([
        'booking_code' => 'ASM-20260414-0043',
        'service_date' => '2026-04-21',
        'total_price' => 155000,
    ]);

    BookingCustomItem::query()->create([
        'booking_id' => $booking->id,
        'custom_service_item_id' => null,
        'item_name_snapshot' => 'Ganti Oli',
        'item_price_snapshot' => 45000,
        'qty' => 2,
        'subtotal' => 90000,
    ]);

    $this->get(route('bookings.public.show', ['booking' => $booking->booking_code]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/BookingSummaryPage')
            ->where('booking.code', $booking->booking_code)
            ->where('booking.packageName', 'Paket Custom')
            ->where('booking.serviceDate', '21 Apr 2026')
            ->where('booking.serviceTime', '10:00')
            ->where('booking.status', 'pending')
            ->where('booking.statusLabel', 'Menunggu konfirmasi')
            ->where('booking.totalPrice', 155000)
            ->has('booking.customItems', 1)
            ->where('booking.customItems.0.name', 'Ganti Oli')
            ->where('booking.customItems.0.qty', 2)
            ->where('booking.customItems.0.subtotal', 90000)
            ->where('whatsAppUrl', fn (string $url): bool => str_contains($url, $booking->booking_code))
            ->missing('booking.customerName')
            ->missing('booking.customer_email')
            ->missing('booking.address_text')
            ->missing('booking.notes')
            ->missing('booking.admin_notes'));
});
