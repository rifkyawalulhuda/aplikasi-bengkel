<?php

use App\Models\Booking;
use App\Models\BookingSetting;
use App\Models\ServicePackage;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;

function createTransportChargeServicePackage(array $overrides = []): ServicePackage
{
    static $sequence = 1;

    $servicePackage = ServicePackage::query()->create(array_merge([
        'name' => 'Paket Transport '.$sequence,
        'slug' => 'paket-transport-'.$sequence,
        'short_description' => 'Paket untuk pengujian transport charge.',
        'description' => 'Deskripsi paket transport charge.',
        'price' => 120000,
        'duration_estimate_minutes' => 60,
        'is_active' => true,
        'display_order' => $sequence,
    ], $overrides));

    $servicePackage->items()->createMany([
        ['name' => 'Item A '.$sequence, 'description' => 'Desc A', 'display_order' => 1],
    ]);

    $sequence++;

    return $servicePackage->fresh('items');
}

function transportChargeBookingPayload(array $overrides = []): array
{
    return array_merge([
        'package_type' => 'fixed_package',
        'service_package_id' => null,
        'customer_name' => 'Budi Santoso',
        'customer_email' => 'budi@example.com',
        'customer_phone' => '081234567890',
        'motorcycle_type' => 'matic',
        'motorcycle_brand' => 'Honda',
        'motorcycle_model' => 'Beat',
        'motorcycle_year' => '2022',
        'plate_number' => 'B1234XYZ',
        'address_text' => 'Jl Mawar No 10, Bogor',
        'house_landmark' => 'Pagar hitam dekat minimarket',
        'latitude' => '-6.1800000',
        'longitude' => '107.0000000',
        'service_date' => now()->addDays(3)->format('Y-m-d'),
        'service_time' => '10:00',
        'notes' => 'Tolong cek rem depan juga.',
    ], $overrides);
}

test('booking page exposes transport charge settings and footer location', function () {
    BookingSetting::query()->updateOrCreate([
        'id' => 1,
    ], [
        'footer_address' => 'Jl. Badami Ciherang, Telukjambe Barat, Kab. Karawang',
        'footer_latitude' => '-6.3000000',
        'footer_longitude' => '107.0000000',
        'transport_free_radius_km' => 10,
        'transport_fee_per_km' => 5000,
    ]);

    $this->get(route('bookings.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/BookingPage')
            ->where('footerLocation.address', 'Jl. Badami Ciherang, Telukjambe Barat, Kab. Karawang')
            ->where('footerLocation.latitude', '-6.3000000')
            ->where('footerLocation.longitude', '107.0000000')
            ->where('transportChargeSettings.freeRadiusKm', 10)
            ->where('transportChargeSettings.feePerKm', 5000));
});

test('booking outside the free radius stores transport charge in the final snapshot', function () {
    Queue::fake();

    BookingSetting::query()->updateOrCreate([
        'id' => 1,
    ], [
        'service_fee' => 10000,
        'footer_address' => 'Jl. Badami Ciherang, Telukjambe Barat, Kab. Karawang',
        'footer_latitude' => '-6.3000000',
        'footer_longitude' => '107.0000000',
        'transport_free_radius_km' => 10,
        'transport_fee_per_km' => 5000,
    ]);

    $servicePackage = createTransportChargeServicePackage([
        'price' => 120000,
    ]);

    $response = $this->post(route('bookings.store'), transportChargeBookingPayload([
        'service_package_id' => $servicePackage->id,
    ]));

    $booking = Booking::query()->latest('id')->first();

    expect($booking)->not->toBeNull();

    $response->assertRedirect(route('bookings.success', [
        'code' => $booking->booking_code,
    ], false));

    expect((float) $booking->transport_distance_km)->toBeGreaterThan(10)
        ->and($booking->transport_charge)->toBeGreaterThan(0)
        ->and($booking->requires_manual_review)->toBeFalse()
        ->and($booking->service_fee)->toBe(10000)
        ->and($booking->total_price)->toBe(
            $booking->package_price_snapshot + $booking->service_fee + $booking->transport_charge,
        );

    Queue::assertNothingPushed();
});
