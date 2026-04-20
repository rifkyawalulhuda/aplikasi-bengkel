<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class DeleteBookingAction
{
    public function handle(Booking $booking): void
    {
        DB::transaction(function () use ($booking): void {
            $booking->delete();
        });
    }
}
