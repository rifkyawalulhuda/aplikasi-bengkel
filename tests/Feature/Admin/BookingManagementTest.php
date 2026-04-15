<?php

use App\Models\Booking;
use App\Models\BookingCustomItem;
use App\Models\BookingStatusLog;
use App\Models\User;
use App\Support\Enums\BookingStatus;
use Inertia\Testing\AssertableInertia as Assert;

function createAdminBooking(array $overrides = []): Booking
{
    static $sequence = 1;

    $booking = Booking::query()->create(array_merge([
        'booking_code' => sprintf('BMS-ADMIN-%04d', $sequence),
        'customer_name' => 'Customer Admin '.$sequence,
        'customer_email' => 'customer-admin-'.$sequence.'@example.com',
        'customer_phone' => '081230000'.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT),
        'motorcycle_type' => 'matic',
        'motorcycle_brand' => 'Honda',
        'motorcycle_model' => 'Beat',
        'motorcycle_year' => '2023',
        'plate_number' => 'B'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT).'XYZ',
        'package_type' => 'fixed_package',
        'service_package_id' => null,
        'package_name_snapshot' => 'Paket Servis Reguler',
        'package_price_snapshot' => 95000,
        'notes' => 'Mohon dicek rem depan.',
        'service_date' => today()->addDays(2)->toDateString(),
        'service_time' => '10:00',
        'status' => BookingStatus::Pending,
        'subtotal_price' => 95000,
        'service_fee' => 15000,
        'total_price' => 110000,
        'address_text' => 'Jl Admin No '.$sequence.', Bogor',
        'house_landmark' => 'Rumah cat putih',
        'latitude' => '-6.5900000',
        'longitude' => '106.8000000',
        'admin_notes' => null,
        'requires_manual_review' => false,
    ], $overrides));

    $sequence++;

    return $booking;
}

test('guests are redirected when visiting admin bookings page', function () {
    $response = $this->get(route('admin.bookings.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated admin can filter and search booking list', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $targetBooking = createAdminBooking([
        'booking_code' => 'BMS-ADMIN-TARGET',
        'customer_name' => 'Rina Lestari',
        'customer_phone' => '081234567890',
        'status' => BookingStatus::Confirmed,
        'service_date' => today()->addDays(4)->toDateString(),
        'service_time' => '14:00',
    ]);

    createAdminBooking([
        'booking_code' => 'BMS-ADMIN-OTHER',
        'customer_name' => 'Budi Santoso',
        'status' => BookingStatus::Pending,
        'service_date' => today()->addDays(4)->toDateString(),
    ]);

    createAdminBooking([
        'booking_code' => 'BMS-ADMIN-OLD',
        'customer_name' => 'Rina Lama',
        'status' => BookingStatus::Confirmed,
        'service_date' => today()->addDays(1)->toDateString(),
    ]);

    $this->get(route('admin.bookings.index', [
        'search' => 'Rina',
        'status' => BookingStatus::Confirmed->value,
        'date' => today()->addDays(4)->toDateString(),
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/BookingsIndexPage')
            ->where('filters.search', 'Rina')
            ->where('filters.status', BookingStatus::Confirmed->value)
            ->where('filters.date', today()->addDays(4)->toDateString())
            ->has('statusOptions', count(BookingStatus::cases()))
            ->has('bookings.data', 1)
            ->where('bookings.data.0.bookingCode', $targetBooking->booking_code)
            ->where('bookings.data.0.customerName', 'Rina Lestari')
            ->where('bookings.data.0.status', BookingStatus::Confirmed->value));
});

test('authenticated admin can view booking detail with status history', function () {
    $user = User::factory()->create(['name' => 'Admin Operasional']);
    $this->actingAs($user);

    $booking = createAdminBooking([
        'booking_code' => 'BMS-DETAIL-0001',
        'package_type' => 'custom_package',
        'package_name_snapshot' => 'Paket Custom',
        'package_price_snapshot' => 0,
        'subtotal_price' => 135000,
        'service_fee' => 10000,
        'total_price' => 145000,
        'admin_notes' => 'Prioritaskan cek aki.',
    ]);

    BookingCustomItem::query()->create([
        'booking_id' => $booking->id,
        'custom_service_item_id' => null,
        'item_name_snapshot' => 'Ganti Oli',
        'item_price_snapshot' => 45000,
        'qty' => 2,
        'subtotal' => 90000,
    ]);

    BookingStatusLog::query()->create([
        'booking_id' => $booking->id,
        'old_status' => null,
        'new_status' => BookingStatus::Pending,
        'changed_by' => null,
        'note' => 'Booking created by customer.',
    ]);

    BookingStatusLog::query()->create([
        'booking_id' => $booking->id,
        'old_status' => BookingStatus::Pending,
        'new_status' => BookingStatus::Confirmed,
        'changed_by' => $user->id,
        'note' => 'Jadwal sudah diverifikasi.',
    ]);

    $this->get(route('admin.bookings.show', $booking))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/BookingDetailPage')
            ->where('booking.bookingCode', $booking->booking_code)
            ->where('booking.customer.name', $booking->customer_name)
            ->where('booking.service.packageName', 'Paket Custom')
            ->where('booking.pricing.total', 145000)
            ->where('booking.adminNotes', 'Prioritaskan cek aki.')
            ->has('booking.service.customItems', 1)
            ->has('booking.statusHistory', 2)
            ->where('booking.statusHistory.0.changedBy', 'Admin Operasional'));
});

test('status update creates booking status log and updates timestamps', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $booking = createAdminBooking([
        'status' => BookingStatus::Pending,
        'confirmed_at' => null,
        'completed_at' => null,
    ]);

    $this->patch(route('admin.bookings.update-status', $booking), [
        'status' => BookingStatus::Completed->value,
        'note' => 'Mekanik sudah menyelesaikan servis.',
    ])->assertRedirect(route('admin.bookings.show', $booking));

    $booking->refresh();

    expect($booking->status)->toBe(BookingStatus::Completed)
        ->and($booking->confirmed_at)->not->toBeNull()
        ->and($booking->completed_at)->not->toBeNull();

    $statusLog = BookingStatusLog::query()
        ->where('booking_id', $booking->id)
        ->latest('id')
        ->first();

    expect($statusLog)->not->toBeNull()
        ->and($statusLog->old_status)->toBe(BookingStatus::Pending)
        ->and($statusLog->new_status)->toBe(BookingStatus::Completed)
        ->and($statusLog->changed_by)->toBe($user->id)
        ->and($statusLog->note)->toBe('Mekanik sudah menyelesaikan servis.');
});

test('admin notes can be updated from booking detail page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $booking = createAdminBooking([
        'admin_notes' => null,
    ]);

    $this->patch(route('admin.bookings.update-notes', $booking), [
        'admin_notes' => 'Customer minta datang setelah jam makan siang.',
    ])->assertRedirect(route('admin.bookings.show', $booking));

    expect($booking->fresh()->admin_notes)->toBe('Customer minta datang setelah jam makan siang.');
});
