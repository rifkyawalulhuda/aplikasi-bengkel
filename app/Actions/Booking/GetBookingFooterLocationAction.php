<?php

namespace App\Actions\Booking;

use App\Models\BookingSetting;

class GetBookingFooterLocationAction
{
    /**
     * @return array{address: string, latitude: string, longitude: string}
     */
    public function handle(): array
    {
        return BookingSetting::currentFooterLocation();
    }
}
