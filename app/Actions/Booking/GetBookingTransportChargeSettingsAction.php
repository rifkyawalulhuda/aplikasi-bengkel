<?php

namespace App\Actions\Booking;

use App\Models\BookingSetting;

class GetBookingTransportChargeSettingsAction
{
    /**
     * @return array{freeRadiusKm: float, feePerKm: int}
     */
    public function handle(): array
    {
        return BookingSetting::currentTransportChargeSettings();
    }
}
