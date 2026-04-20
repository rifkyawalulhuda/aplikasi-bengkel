<?php

namespace App\Actions\Booking;

use App\Models\BookingSetting;

class GetBookingServiceFeeAction
{
    public function handle(): int
    {
        return BookingSetting::currentServiceFee();
    }
}
