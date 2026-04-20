<?php

use App\Models\BookingSetting;
use App\Models\User;

test('admin can update the footer location from the dashboard card', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    BookingSetting::query()->updateOrCreate([
        'id' => 1,
    ], [
        'footer_address' => 'Alamat lama',
        'footer_latitude' => '-6.1000000',
        'footer_longitude' => '107.1000000',
    ]);

    $this->from(route('admin.dashboard'))
        ->patch(route('admin.booking-settings.footer-location'), [
            'footer_address' => 'Jl. Badami Ciherang, Telukjambe Barat, Kab. Karawang',
            'footer_latitude' => '-6.3025000',
            'footer_longitude' => '107.3035000',
        ])
        ->assertRedirect(route('admin.dashboard'));

    $setting = BookingSetting::query()->find(1);

    expect($setting?->footer_address)->toBe('Jl. Badami Ciherang, Telukjambe Barat, Kab. Karawang')
        ->and((string) $setting?->footer_latitude)->toBe('-6.3025000')
        ->and((string) $setting?->footer_longitude)->toBe('107.3035000');
});
