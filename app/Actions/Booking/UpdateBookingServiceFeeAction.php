<?php

namespace App\Actions\Booking;

use App\Models\BookingSetting;

class UpdateBookingServiceFeeAction
{
    public function handle(int $serviceFee): BookingSetting
    {
        return BookingSetting::query()->updateOrCreate(
            ['id' => 1],
            ['service_fee' => $serviceFee],
        );
    }
}
