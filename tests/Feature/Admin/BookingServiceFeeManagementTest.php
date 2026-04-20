<?php

use App\Models\Booking;
use App\Models\BookingSetting;
use App\Models\ServicePackage;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;

function createBookingServiceFeePackage(array $overrides = []): ServicePackage
{
    static $sequence = 1;

    $servicePackage = ServicePackage::query()->create(array_merge([
        'name' => 'Paket Fee '.$sequence,
        'slug' => 'paket-fee-'.$sequence,
        'short_description' => 'Paket untuk test service fee.',
        'description' => 'Deskripsi paket service fee.',
        'price' => 85000,
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

function bookingServiceFeePayload(array $overrides = []): array
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
        'latitude' => '-6.5900000',
        'longitude' => '106.8000000',
        'service_date' => now()->addDays(3)->format('Y-m-d'),
        'service_time' => '10:00',
        'notes' => 'Tolong cek rem depan juga.',
    ], $overrides);
}

test('admin pages expose the booking service fee editor and current value', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    BookingSetting::query()->updateOrCreate([
        'id' => 1,
    ], [
        'service_fee' => 25000,
    ]);

    $this->get(route('admin.service-packages.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/ServicePackagesPage')
            ->where('serviceFee', 25000));

    $this->get(route('admin.custom-service-items.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/CustomServiceItemsPage')
            ->where('serviceFee', 25000));

    $this->get(route('bookings.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/BookingPage')
            ->where('serviceFee', 25000));
});

test('admin can update the booking service fee from the shared card', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    BookingSetting::query()->updateOrCreate([
        'id' => 1,
    ], [
        'service_fee' => 10000,
    ]);

    $this->from(route('admin.service-packages.index'))
        ->patch(route('admin.booking-settings.service-fee'), [
            'service_fee' => 35000,
        ])
        ->assertRedirect(route('admin.service-packages.index'));

    expect(BookingSetting::query()->find(1)?->service_fee)->toBe(35000);
});

test('updated booking service fee is applied to new bookings', function () {
    Queue::fake();

    BookingSetting::query()->updateOrCreate([
        'id' => 1,
    ], [
        'service_fee' => 35000,
    ]);

    $servicePackage = createBookingServiceFeePackage();

    $this->post(route('bookings.store'), bookingServiceFeePayload([
        'service_package_id' => $servicePackage->id,
        'custom_items' => [],
    ]))->assertRedirect();

    $booking = Booking::query()->latest('id')->first();

    expect($booking)->not->toBeNull()
        ->and($booking->service_fee)->toBe(35000)
        ->and($booking->total_price)->toBe($booking->package_price_snapshot + 35000);

    Queue::assertNothingPushed();
});
