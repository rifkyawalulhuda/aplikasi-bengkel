<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use Illuminate\Validation\ValidationException;

class ValidateBookingSlotAction
{
    public function handle(string $serviceDate, string $serviceTime): void
    {
        $existingCount = Booking::query()
            ->whereDate('service_date', $serviceDate)
            ->where('service_time', $serviceTime)
            ->whereIn('status', config('booking.capacity_statuses', []))
            ->lockForUpdate()
            ->count();

        if ($existingCount >= (int) config('booking.max_per_slot', 3)) {
            throw ValidationException::withMessages([
                'service_time' => 'Slot jadwal tersebut sudah penuh. Silakan pilih jam lain.',
            ]);
        }
    }
}
