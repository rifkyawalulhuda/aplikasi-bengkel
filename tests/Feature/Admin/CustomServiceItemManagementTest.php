<?php

use App\Models\Booking;
use App\Models\CustomServiceItem;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;

function customServiceItemPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Tune Up CVT Ringan',
        'category' => 'Perawatan rutin',
        'description' => 'Pembersihan ringan area CVT dan pengecekan komponen dasar.',
        'price' => 95000,
        'unit_label' => 'layanan',
        'display_order' => 2,
        'is_active' => true,
    ], $overrides);
}

function createCustomServiceItemRecord(array $overrides = []): CustomServiceItem
{
    static $sequence = 1;

    $customServiceItem = CustomServiceItem::query()->create(array_merge([
        'name' => 'Item Seeder '.$sequence,
        'slug' => 'item-seeder-'.$sequence,
        'category' => 'Kategori '.$sequence,
        'description' => 'Deskripsi '.$sequence,
        'price' => 30000 * $sequence,
        'unit_label' => 'layanan',
        'is_active' => true,
        'display_order' => $sequence,
    ], $overrides));

    $sequence++;

    return $customServiceItem->refresh();
}

function bookingPayloadForCustomItem(array $overrides = []): array
{
    return array_merge([
        'package_type' => 'custom_package',
        'service_package_id' => null,
        'customer_name' => 'Sinta Larasati',
        'customer_email' => 'sinta@example.com',
        'customer_phone' => '081234567891',
        'motorcycle_type' => 'matic',
        'motorcycle_brand' => 'Yamaha',
        'motorcycle_model' => 'NMAX',
        'motorcycle_year' => '2023',
        'plate_number' => 'B4321XYZ',
        'address_text' => 'Jl Melati No 21, Bogor',
        'house_landmark' => 'Dekat masjid hijau',
        'latitude' => '-6.5900000',
        'longitude' => '106.8000000',
        'service_date' => now()->addDays(5)->format('Y-m-d'),
        'service_time' => '11:00',
        'notes' => 'Mohon cek bunyi di area CVT.',
        'custom_items' => [],
    ], $overrides);
}

test('guests are redirected from admin custom service items page', function () {
    $this->get(route('admin.custom-service-items.index'))
        ->assertRedirect(route('login'));
});

test('authenticated admin can view custom service items page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $item = createCustomServiceItemRecord();

    $this->get(route('admin.custom-service-items.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/CustomServiceItemsPage')
            ->has('items', 1)
            ->where('items.0.name', $item->name)
            ->where('items.0.category', $item->category)
            ->where('editingItem', null));
});

test('admin can create custom service item', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('admin.custom-service-items.store'), customServiceItemPayload())
        ->assertRedirect(route('admin.custom-service-items.index'));

    $item = CustomServiceItem::query()->where('name', 'Tune Up CVT Ringan')->first();

    expect($item)->not->toBeNull()
        ->and($item->slug)->toBe('tune-up-cvt-ringan')
        ->and($item->is_active)->toBeTrue()
        ->and($item->price)->toBe(95000);
});

test('admin can update custom service item', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $item = createCustomServiceItemRecord([
        'name' => 'Item Lama',
        'slug' => 'item-lama',
    ]);

    $this->patch(route('admin.custom-service-items.update', $item), customServiceItemPayload([
        'name' => 'Item Baru',
        'category' => 'Pemeriksaan',
        'price' => 125000,
        'unit_label' => 'kunjungan',
        'is_active' => false,
    ]))->assertRedirect(route('admin.custom-service-items.edit', $item));

    $item->refresh();

    expect($item->name)->toBe('Item Baru')
        ->and($item->category)->toBe('Pemeriksaan')
        ->and($item->price)->toBe(125000)
        ->and($item->unit_label)->toBe('kunjungan')
        ->and($item->is_active)->toBeFalse();
});

test('admin can deactivate custom item and public landing only shows active custom items', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $activeItem = createCustomServiceItemRecord([
        'name' => 'Cek Rem Aktif',
        'slug' => 'cek-rem-aktif',
    ]);

    $itemToDeactivate = createCustomServiceItemRecord([
        'name' => 'Cek CVT Nonaktif',
        'slug' => 'cek-cvt-nonaktif',
    ]);

    $this->patch(route('admin.custom-service-items.deactivate', $itemToDeactivate))
        ->assertRedirect(route('admin.custom-service-items.index'));

    expect($itemToDeactivate->fresh()->is_active)->toBeFalse();

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/LandingPage')
            ->has('customItems', 1)
            ->where('customItems.0.name', $activeItem->name));
});

test('inactive custom item can be deleted permanently', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $item = createCustomServiceItemRecord([
        'name' => 'Item Hapus',
        'slug' => 'item-hapus',
        'is_active' => false,
    ]);

    $this->delete(route('admin.custom-service-items.destroy', $item))
        ->assertRedirect(route('admin.custom-service-items.index'));

    expect(CustomServiceItem::query()->whereKey($item->id)->exists())->toBeFalse();
});

test('updating custom item price only affects future bookings and keeps existing snapshots intact', function () {
    Queue::fake();

    $user = User::factory()->create();
    $this->actingAs($user);

    $item = createCustomServiceItemRecord([
        'name' => 'Ganti Oli Premium',
        'slug' => 'ganti-oli-premium',
        'price' => 45000,
    ]);

    $this->post(route('bookings.store'), bookingPayloadForCustomItem([
        'custom_items' => [
            ['id' => $item->id, 'qty' => 2],
        ],
    ]))->assertRedirect();

    $firstBooking = Booking::query()->with('customItems')->latest('id')->first();

    expect($firstBooking)->not->toBeNull()
        ->and($firstBooking->customItems)->toHaveCount(1)
        ->and($firstBooking->customItems->first()->item_price_snapshot)->toBe(45000)
        ->and($firstBooking->customItems->first()->subtotal)->toBe(90000);

    $this->patch(route('admin.custom-service-items.update', $item), customServiceItemPayload([
        'name' => $item->name,
        'category' => $item->category,
        'description' => $item->description,
        'price' => 65000,
        'unit_label' => $item->unit_label,
        'display_order' => $item->display_order,
        'is_active' => true,
    ]))->assertRedirect(route('admin.custom-service-items.edit', $item));

    $firstBooking->refresh();
    $firstBooking->load('customItems');

    expect($firstBooking->customItems->first()->item_price_snapshot)->toBe(45000)
        ->and($firstBooking->customItems->first()->subtotal)->toBe(90000);

    $this->post(route('bookings.store'), bookingPayloadForCustomItem([
        'customer_email' => 'future@example.com',
        'customer_phone' => '081234567892',
        'service_date' => now()->addDays(6)->format('Y-m-d'),
        'custom_items' => [
            ['id' => $item->id, 'qty' => 2],
        ],
    ]))->assertRedirect();

    $latestBooking = Booking::query()->with('customItems')->latest('id')->first();

    expect($latestBooking)->not->toBeNull()
        ->and($latestBooking->id)->not->toBe($firstBooking->id)
        ->and($latestBooking->customItems->first()->item_price_snapshot)->toBe(65000)
        ->and($latestBooking->customItems->first()->subtotal)->toBe(130000);
});
