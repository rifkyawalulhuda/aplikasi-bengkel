<?php

namespace App\Actions\Booking;

use App\Models\BookingSetting;

class UpdateBookingTransportChargeAction
{
    public function handle(float $freeRadiusKm, int $feePerKm): BookingSetting
    {
        return BookingSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'transport_free_radius_km' => $freeRadiusKm,
                'transport_fee_per_km' => $feePerKm,
            ],
        );
    }
}
