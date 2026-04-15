<?php

use App\Models\ServicePackage;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function servicePackagePayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Paket Tune Up Harian',
        'short_description' => 'Servis ringan untuk motor yang dipakai harian.',
        'description' => 'Paket servis ringan dengan fokus cek cepat dan penggantian oli.',
        'price' => 120000,
        'duration_estimate_minutes' => 75,
        'display_order' => 3,
        'is_active' => true,
        'is_featured' => false,
        'items' => [
            ['name' => 'Ganti oli mesin', 'description' => 'Oli mesin sesuai kebutuhan motor.'],
            ['name' => 'Cek rem depan belakang', 'description' => 'Pemeriksaan kampas dan fungsi rem.'],
        ],
    ], $overrides);
}

function createServicePackageRecord(array $overrides = []): ServicePackage
{
    static $sequence = 1;

    $servicePackage = ServicePackage::query()->create(array_merge([
        'name' => 'Paket Seeder '.$sequence,
        'slug' => 'paket-seeder-'.$sequence,
        'short_description' => 'Short description '.$sequence,
        'description' => 'Description '.$sequence,
        'price' => 95000,
        'duration_estimate_minutes' => 60,
        'is_active' => true,
        'is_featured' => false,
        'display_order' => $sequence,
    ], $overrides));

    $servicePackage->items()->createMany([
        ['name' => 'Item A '.$sequence, 'description' => 'Desc A', 'display_order' => 1],
        ['name' => 'Item B '.$sequence, 'description' => 'Desc B', 'display_order' => 2],
    ]);

    $sequence++;

    return $servicePackage->fresh('items');
}

test('guests are redirected from admin service packages page', function () {
    $this->get(route('admin.service-packages.index'))
        ->assertRedirect(route('login'));
});

test('authenticated admin can view service packages page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $package = createServicePackageRecord();

    $this->get(route('admin.service-packages.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/ServicePackagesPage')
            ->has('packages', 1)
            ->where('packages.0.name', $package->name)
            ->where('packages.0.itemsCount', 2)
            ->where('editingPackage', null));
});

test('admin can create service package with items', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('admin.service-packages.store'), servicePackagePayload())
        ->assertRedirect(route('admin.service-packages.index'));

    $package = ServicePackage::query()->where('name', 'Paket Tune Up Harian')->first();

    expect($package)->not->toBeNull()
        ->and($package->slug)->toBe('paket-tune-up-harian')
        ->and($package->is_active)->toBeTrue()
        ->and($package->is_featured)->toBeFalse();

    expect($package->items()->count())->toBe(2);
});

test('admin can update service package and sync item list', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $package = createServicePackageRecord([
        'name' => 'Paket Lama',
        'slug' => 'paket-lama',
    ]);

    $this->patch(route('admin.service-packages.update', $package), servicePackagePayload([
        'name' => 'Paket Baru',
        'price' => 150000,
        'is_active' => false,
        'is_featured' => true,
        'items' => [
            ['name' => 'Ganti filter udara', 'description' => 'Sekaligus bersihkan area intake.'],
        ],
    ]))->assertRedirect(route('admin.service-packages.edit', $package));

    $package->refresh();

    expect($package->name)->toBe('Paket Baru')
        ->and($package->price)->toBe(150000)
        ->and($package->is_active)->toBeFalse()
        ->and($package->is_featured)->toBeTrue();

    expect($package->items()->count())->toBe(1)
        ->and($package->items()->first()->name)->toBe('Ganti filter udara');
});

test('admin can deactivate package and public landing only shows active packages', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $activePackage = createServicePackageRecord([
        'name' => 'Paket Aktif',
        'slug' => 'paket-aktif',
    ]);

    $packageToDeactivate = createServicePackageRecord([
        'name' => 'Paket Nonaktif',
        'slug' => 'paket-nonaktif',
    ]);

    $this->patch(route('admin.service-packages.deactivate', $packageToDeactivate))
        ->assertRedirect(route('admin.service-packages.index'));

    expect($packageToDeactivate->fresh()->is_active)->toBeFalse();

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/LandingPage')
            ->has('packages', 1)
            ->where('packages.0.name', $activePackage->name));
});

test('admin can reactivate inactive package and it appears on public landing again', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $inactivePackage = createServicePackageRecord([
        'name' => 'Paket Aktif Lagi',
        'slug' => 'paket-aktif-lagi',
        'is_active' => false,
    ]);

    $this->patch(route('admin.service-packages.activate', $inactivePackage))
        ->assertRedirect(route('admin.service-packages.index'));

    expect($inactivePackage->fresh()->is_active)->toBeTrue();

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/LandingPage')
            ->where('packages.0.name', $inactivePackage->name));
});

test('admin can mark package as featured and public landing receives the flag', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $featuredPackage = createServicePackageRecord([
        'name' => 'Paket Featured',
        'slug' => 'paket-featured',
        'is_featured' => true,
        'display_order' => 0,
    ]);

    createServicePackageRecord([
        'name' => 'Paket Reguler',
        'slug' => 'paket-reguler',
        'display_order' => 1,
    ]);

    $this->get(route('admin.service-packages.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/ServicePackagesPage')
            ->where('packages.0.isFeatured', true));

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/LandingPage')
            ->where('packages.0.name', $featuredPackage->name)
            ->where('packages.0.isFeatured', true));
});

test('inactive package can be deleted permanently', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $package = createServicePackageRecord([
        'name' => 'Paket Hapus',
        'slug' => 'paket-hapus',
        'is_active' => false,
    ]);

    $this->delete(route('admin.service-packages.destroy', $package))
        ->assertRedirect(route('admin.service-packages.index'));

    expect(ServicePackage::query()->whereKey($package->id)->exists())->toBeFalse();
});
