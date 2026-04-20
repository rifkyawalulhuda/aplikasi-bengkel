<?php

use App\Models\BookingSetting;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('admin can view and update transport charge settings from the dashboard card', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    BookingSetting::query()->updateOrCreate([
        'id' => 1,
    ], [
        'transport_free_radius_km' => 8.5,
        'transport_fee_per_km' => 3500,
    ]);

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/DashboardPage')
            ->where('transportChargeSettings.freeRadiusKm', 8.5)
            ->where('transportChargeSettings.feePerKm', 3500));

    $this->from(route('admin.dashboard'))
        ->patch(route('admin.booking-settings.transport-charge'), [
            'transport_free_radius_km' => 12,
            'transport_fee_per_km' => 5000,
        ])
        ->assertRedirect(route('admin.dashboard'));

    $setting = BookingSetting::query()->find(1);

    expect((string) $setting?->transport_free_radius_km)->toBe('12.00')
        ->and($setting?->transport_fee_per_km)->toBe(5000);
});
