<?php

use App\Models\Booking;
use App\Models\User;
use App\Models\VisitorLog;
use App\Support\Enums\BookingStatus;
use Inertia\Testing\AssertableInertia as Assert;

function createDashboardBooking(array $overrides = []): Booking
{
    static $sequence = 1;

    $createdAt = $overrides['created_at'] ?? now();
    $updatedAt = $overrides['updated_at'] ?? $createdAt;

    unset($overrides['created_at'], $overrides['updated_at']);

    $booking = Booking::query()->create(array_merge([
        'booking_code' => sprintf('BMS-DASH-%04d', $sequence),
        'customer_name' => 'Dashboard Tester '.$sequence,
        'customer_email' => 'dashboard'.$sequence.'@example.com',
        'customer_phone' => '0812000000'.$sequence,
        'motorcycle_type' => 'matic',
        'motorcycle_brand' => 'Honda',
        'motorcycle_model' => 'Vario',
        'motorcycle_year' => '2024',
        'plate_number' => 'B'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT).'XYZ',
        'package_type' => 'fixed_package',
        'service_package_id' => null,
        'package_name_snapshot' => 'Paket Dashboard',
        'package_price_snapshot' => 90000,
        'notes' => null,
        'service_date' => now()->format('Y-m-d'),
        'service_time' => '10:00',
        'status' => BookingStatus::Pending,
        'subtotal_price' => 90000,
        'service_fee' => 0,
        'total_price' => 90000,
        'address_text' => 'Jl Dashboard No '.$sequence,
        'house_landmark' => 'Patokan '.$sequence,
        'latitude' => '-6.5900000',
        'longitude' => '106.8000000',
        'admin_notes' => null,
        'requires_manual_review' => false,
    ], $overrides));

    $booking->forceFill([
        'created_at' => $createdAt,
        'updated_at' => $updatedAt,
    ])->save();

    $sequence++;

    return $booking;
}

function createVisitorLog(array $overrides = []): VisitorLog
{
    static $sequence = 1;

    $visitDate = $overrides['visit_date'] ?? today()->toDateString();

    $visitorLog = VisitorLog::query()->create(array_merge([
        'visit_date' => $visitDate,
        'ip_hash' => hash('sha256', '192.168.0.'.$sequence.'|'.$visitDate),
        'session_key' => 'session-'.$sequence,
        'path' => '/',
        'referrer' => null,
        'user_agent' => 'Pest Test Agent',
        'is_unique_daily' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ], $overrides));

    $sequence++;

    return $visitorLog;
}

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('admin.dashboard'));
    $response->assertOk();
});

test('authenticated admin can view operational dashboard stats and seven day visitor trend', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    createDashboardBooking([
        'status' => BookingStatus::Pending,
        'created_at' => now(),
    ]);
    createDashboardBooking([
        'status' => BookingStatus::Pending,
        'created_at' => now()->subDay(),
    ]);
    createDashboardBooking([
        'status' => BookingStatus::Confirmed,
        'created_at' => now(),
    ]);
    createDashboardBooking([
        'status' => BookingStatus::Completed,
        'created_at' => now()->subDays(2),
    ]);

    createVisitorLog([
        'visit_date' => today()->subDays(6)->toDateString(),
        'is_unique_daily' => true,
    ]);
    createVisitorLog([
        'visit_date' => today()->subDays(6)->toDateString(),
        'is_unique_daily' => false,
    ]);
    createVisitorLog([
        'visit_date' => today()->toDateString(),
        'is_unique_daily' => true,
    ]);
    createVisitorLog([
        'visit_date' => today()->toDateString(),
        'is_unique_daily' => true,
    ]);

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/DashboardPage')
            ->where('stats.bookingsToday', 2)
            ->where('stats.pendingBookings', 2)
            ->where('stats.confirmedBookings', 1)
            ->where('stats.completedBookings', 1)
            ->where('stats.visitorsToday', 2)
            ->has('visitorTrend', 7)
            ->where('visitorTrend.0.date', today()->subDays(6)->format('Y-m-d'))
            ->where('visitorTrend.0.totalVisits', 2)
            ->where('visitorTrend.0.uniqueVisits', 1)
            ->where('visitorTrend.6.date', today()->format('Y-m-d'))
            ->where('visitorTrend.6.totalVisits', 2)
            ->where('visitorTrend.6.uniqueVisits', 2)
            ->where('visitorTrend.3.totalVisits', 0)
            ->has('foundationChecklist', 3));
});
