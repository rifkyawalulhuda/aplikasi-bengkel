<?php

namespace App\Actions\Booking;

use App\Models\Booking;

class UpdateBookingNotesAction
{
    public function handle(Booking $booking, ?string $adminNotes): Booking
    {
        $booking->forceFill([
            'admin_notes' => blank($adminNotes) ? null : trim($adminNotes),
        ])->save();

        return $booking->refresh();
    }
}
