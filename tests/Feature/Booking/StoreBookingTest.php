<?php

use App\Actions\Booking\CreateBookingAction;
use App\Models\Booking;
use App\Models\BookingCustomItem;
use App\Models\BookingStatusLog;
use App\Models\CustomServiceItem;
use App\Models\ServicePackage;
use App\Support\Enums\BookingStatus;
use App\Support\Enums\PackageType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

function createServicePackage(array $overrides = []): ServicePackage
{
    return ServicePackage::query()->create(array_merge([
        'name' => 'Paket Tune Up Ringan',
        'slug' => 'paket-tune-up-ringan',
        'short_description' => 'Servis ringan untuk motor harian.',
        'description' => 'Cek dan servis ringan motor harian.',
        'price' => 85000,
        'duration_estimate_minutes' => 60,
        'is_active' => true,
        'display_order' => 1,
    ], $overrides));
}

function createCustomServiceItem(array $overrides = []): CustomServiceItem
{
    static $displayOrder = 1;

    return CustomServiceItem::query()->create(array_merge([
        'name' => 'Ganti Oli',
        'slug' => 'ganti-oli-'.$displayOrder,
        'category' => 'Perawatan',
        'description' => 'Penggantian oli mesin motor.',
        'price' => 45000,
        'unit_label' => 'layanan',
        'is_active' => true,
        'display_order' => $displayOrder++,
    ], $overrides));
}

function bookingPayload(array $overrides = []): array
{
    return array_merge([
        'package_type' => 'fixed_package',
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

test('customer can create a fixed package booking with immutable snapshots and initial status log', function () {
    Queue::fake();

    $servicePackage = createServicePackage();

    $response = $this->post(route('bookings.store'), bookingPayload([
        'service_package_id' => $servicePackage->id,
        'custom_items' => [],
    ]));

    $booking = Booking::query()->first();

    expect($booking)->not->toBeNull();

    $response->assertRedirect(route('bookings.success', [
        'code' => $booking->booking_code,
    ], false));

    expect($booking->package_name_snapshot)->toBe($servicePackage->name)
        ->and($booking->package_price_snapshot)->toBe($servicePackage->price)
        ->and($booking->subtotal_price)->toBe($servicePackage->price)
        ->and($booking->total_price)->toBe($servicePackage->price)
        ->and($booking->status)->toBe(BookingStatus::Pending)
        ->and($booking->requires_manual_review)->toBeFalse()
        ->and($booking->service_package_id)->toBe($servicePackage->id);

    expect(BookingCustomItem::query()->count())->toBe(0);

    $statusLog = BookingStatusLog::query()->first();

    expect($statusLog)->not->toBeNull()
        ->and($statusLog->new_status)->toBe(BookingStatus::Pending)
        ->and($statusLog->note)->toContain('Booking created by customer.');

    Queue::assertNothingPushed();
});

test('customer can create a fixed package booking even when an empty custom items array is submitted', function () {
    Queue::fake();

    $servicePackage = createServicePackage();

    $response = $this->post(route('bookings.store'), bookingPayload([
        'service_package_id' => $servicePackage->id,
        'custom_items' => [],
    ]));

    $booking = Booking::query()->first();

    expect($booking)->not->toBeNull();

    $response->assertRedirect(route('bookings.success', [
        'code' => $booking->booking_code,
    ], false));

    expect($booking->package_type)->toBe(PackageType::FixedPackage)
        ->and($booking->service_package_id)->toBe($servicePackage->id);

    Queue::assertNothingPushed();
});

test('customer can create a booking without providing an email address', function () {
    Queue::fake();

    $servicePackage = createServicePackage();
    $payload = bookingPayload([
        'service_package_id' => $servicePackage->id,
    ]);

    unset($payload['customer_email']);

    $response = $this->post(route('bookings.store'), $payload);

    $booking = Booking::query()->first();

    expect($booking)->not->toBeNull();

    $response->assertRedirect(route('bookings.success', [
        'code' => $booking->booking_code,
    ], false));

    expect($booking->customer_email)->toBe('');

    Queue::assertNothingPushed();
});

test('customer can create a custom package booking and duplicate items are merged into snapshots', function () {
    Queue::fake();

    $oilChange = createCustomServiceItem([
        'name' => 'Ganti Oli',
        'slug' => 'ganti-oli',
        'price' => 45000,
    ]);

    $sparkPlugCheck = createCustomServiceItem([
        'name' => 'Cek Busi',
        'slug' => 'cek-busi',
        'price' => 20000,
    ]);

    $response = $this->post(route('bookings.store'), bookingPayload([
        'package_type' => 'custom_package',
        'service_package_id' => null,
        'custom_items' => [
            ['id' => $oilChange->id, 'qty' => 1],
            ['id' => $oilChange->id, 'qty' => 2],
            ['id' => $sparkPlugCheck->id, 'qty' => 1],
        ],
    ]));

    $booking = Booking::query()->with('customItems')->first();

    expect($booking)->not->toBeNull();

    $response->assertRedirect(route('bookings.success', [
        'code' => $booking->booking_code,
    ], false));

    expect($booking->service_package_id)->toBeNull()
        ->and($booking->package_name_snapshot)->toBe('Paket Custom')
        ->and($booking->package_price_snapshot)->toBe(0)
        ->and($booking->subtotal_price)->toBe(155000)
        ->and($booking->total_price)->toBe(155000);

    expect($booking->customItems)->toHaveCount(2);

    $oilSnapshot = $booking->customItems
        ->firstWhere('custom_service_item_id', $oilChange->id);

    $sparkPlugSnapshot = $booking->customItems
        ->firstWhere('custom_service_item_id', $sparkPlugCheck->id);

    expect($oilSnapshot)->not->toBeNull()
        ->and($oilSnapshot->item_name_snapshot)->toBe('Ganti Oli')
        ->and($oilSnapshot->item_price_snapshot)->toBe(45000)
        ->and($oilSnapshot->qty)->toBe(3)
        ->and($oilSnapshot->subtotal)->toBe(135000);

    expect($sparkPlugSnapshot)->not->toBeNull()
        ->and($sparkPlugSnapshot->qty)->toBe(1)
        ->and($sparkPlugSnapshot->subtotal)->toBe(20000);

    Queue::assertNothingPushed();
});

test('booking outside automatic coverage is still created but marked for manual review', function () {
    Queue::fake();

    $customItem = createCustomServiceItem();

    $this->post(route('bookings.store'), bookingPayload([
        'package_type' => 'custom_package',
        'service_package_id' => null,
        'latitude' => '-7.1000000',
        'longitude' => '107.4000000',
        'custom_items' => [
            ['id' => $customItem->id, 'qty' => 1],
        ],
    ]))->assertRedirect();

    $booking = Booking::query()->first();
    $statusLog = BookingStatusLog::query()->first();

    expect($booking)->not->toBeNull()
        ->and($booking->requires_manual_review)->toBeTrue()
        ->and($booking->admin_notes)->toContain('perlu review admin');

    expect($statusLog)->not->toBeNull()
        ->and($statusLog->note)->toContain('Booking created by customer.')
        ->and($statusLog->note)->toContain('perlu review admin');

    Queue::assertNothingPushed();
});

test('custom package requires at least one active item', function () {
    $response = $this->from(route('bookings.create'))
        ->post(route('bookings.store'), bookingPayload([
            'package_type' => 'custom_package',
            'service_package_id' => null,
            'custom_items' => [],
        ]));

    $response->assertRedirect(route('bookings.create'))
        ->assertSessionHasErrors(['custom_items']);

    $this->assertDatabaseCount('bookings', 0);
});

test('inactive fixed package can not be booked', function () {
    $inactivePackage = createServicePackage([
        'name' => 'Paket Lama',
        'slug' => 'paket-lama',
        'is_active' => false,
    ]);

    $response = $this->from(route('bookings.create'))
        ->post(route('bookings.store'), bookingPayload([
            'service_package_id' => $inactivePackage->id,
        ]));

    $response->assertRedirect(route('bookings.create'))
        ->assertSessionHasErrors(['service_package_id']);
    $this->assertDatabaseCount('bookings', 0);
});

test('inactive custom item can not be booked', function () {
    $inactiveItem = createCustomServiceItem([
        'name' => 'Cek CVT Lama',
        'slug' => 'cek-cvt-lama',
        'is_active' => false,
    ]);

    $response = $this->from(route('bookings.create'))
        ->post(route('bookings.store'), bookingPayload([
            'package_type' => 'custom_package',
            'service_package_id' => null,
            'custom_items' => [
                ['id' => $inactiveItem->id, 'qty' => 1],
            ],
        ]));

    $response->assertRedirect(route('bookings.create'))
        ->assertSessionHasErrors(['custom_items']);

    $this->assertDatabaseCount('bookings', 0);
});

test('slot availability is validated on the backend', function () {
    Queue::fake();

    $servicePackage = createServicePackage();
    $serviceDate = now()->addDays(5)->format('Y-m-d');
    $serviceTime = '09:00';

    foreach (range(1, (int) config('booking.max_per_slot')) as $index) {
        Booking::query()->create([
            'booking_code' => 'ASM-TEST-000'.$index,
            'customer_name' => 'Customer '.$index,
            'customer_email' => 'customer'.$index.'@example.com',
            'customer_phone' => '08123456789'.$index,
            'motorcycle_type' => 'matic',
            'motorcycle_brand' => 'Honda',
            'motorcycle_model' => 'Beat',
            'motorcycle_year' => '2022',
            'plate_number' => 'B123'.$index.'XYZ',
            'package_type' => 'fixed_package',
            'service_package_id' => $servicePackage->id,
            'package_name_snapshot' => $servicePackage->name,
            'package_price_snapshot' => $servicePackage->price,
            'notes' => null,
            'service_date' => $serviceDate,
            'service_time' => $serviceTime,
            'status' => BookingStatus::Pending,
            'subtotal_price' => $servicePackage->price,
            'service_fee' => 0,
            'total_price' => $servicePackage->price,
            'address_text' => 'Jl Contoh No '.$index,
            'house_landmark' => 'Patokan '.$index,
            'latitude' => '-6.5900000',
            'longitude' => '106.8000000',
            'admin_notes' => null,
            'requires_manual_review' => false,
        ]);
    }

    $response = $this->from(route('bookings.create'))
        ->post(route('bookings.store'), bookingPayload([
            'service_package_id' => $servicePackage->id,
            'service_date' => $serviceDate,
            'service_time' => $serviceTime,
        ]));

    $response->assertRedirect(route('bookings.create'))
        ->assertSessionHasErrors(['service_time']);
    $this->assertDatabaseCount('bookings', (int) config('booking.max_per_slot'));

    Queue::assertNothingPushed();
});

test('booking submission is rate limited with a helpful message', function () {
    Queue::fake();

    config()->set('booking.rate_limit.max_attempts', 1);
    config()->set('booking.rate_limit.decay_seconds', 120);

    $servicePackage = createServicePackage([
        'slug' => 'paket-throttle-test',
    ]);

    $headers = ['User-Agent' => 'Pest booking throttle test'];

    $this->withHeaders($headers)
        ->from(route('bookings.create'))
        ->post(route('bookings.store'), bookingPayload([
            'service_package_id' => $servicePackage->id,
        ]))
        ->assertRedirect();

    $response = $this->withHeaders($headers)
        ->from(route('bookings.create'))
        ->post(route('bookings.store'), bookingPayload([
            'service_package_id' => $servicePackage->id,
            'customer_email' => 'throttle@example.com',
        ]));

    $response->assertStatus(429)
        ->assertHeader('Location', route('bookings.create'))
        ->assertSessionHasErrors(['booking']);

    expect(session('errors')->getBag('default')->first('booking'))
        ->toContain('Terlalu banyak percobaan booking')
        ->toContain('menit');
});

test('booking flow shows a generic message and logs the failure when creation crashes', function () {
    Log::spy();

    $servicePackage = createServicePackage([
        'slug' => 'paket-error-test',
    ]);

    $mock = Mockery::mock(CreateBookingAction::class);
    $mock->shouldReceive('handle')
        ->once()
        ->andThrow(new RuntimeException('Database offline'));

    $this->app->instance(CreateBookingAction::class, $mock);

    $response = $this->from(route('bookings.create'))
        ->post(route('bookings.store'), bookingPayload([
            'service_package_id' => $servicePackage->id,
            'customer_email' => 'graceful@example.com',
        ]));

    $response->assertRedirect(route('bookings.create'))
        ->assertSessionHasErrors(['booking']);

    expect(session('errors')->getBag('default')->first('booking'))
        ->toBe(config('booking.messages.submission_failed'))
        ->not->toContain('Database offline');

    expect(Booking::query()->count())->toBe(0);

    Log::shouldHaveReceived('error')
        ->once()
        ->withArgs(function (string $message, array $context): bool {
            return $message === 'Public booking creation failed.'
                && $context['exception'] === RuntimeException::class
                && $context['error'] === 'Database offline'
                && $context['package_type'] === 'fixed_package';
        });
});
