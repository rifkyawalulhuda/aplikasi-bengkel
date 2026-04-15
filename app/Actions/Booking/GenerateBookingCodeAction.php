<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use Carbon\CarbonInterface;

class GenerateBookingCodeAction
{
    public function handle(CarbonInterface $serviceDate): string
    {
        $dateSegment = $serviceDate->format('Ymd');
        $sequence = Booking::query()
            ->where('booking_code', 'like', sprintf('%s-%s-%%', config('booking.code_prefix'), $dateSegment))
            ->lockForUpdate()
            ->count() + 1;

        return sprintf('%s-%s-%04d', config('booking.code_prefix'), $dateSegment, $sequence);
    }
}
