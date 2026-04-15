<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Models\User;
use App\Support\Enums\BookingStatus;
use Illuminate\Support\Facades\DB;

class UpdateBookingStatusAction
{
    public function handle(Booking $booking, BookingStatus $newStatus, ?string $note, ?User $actor): Booking
    {
        return DB::transaction(function () use ($booking, $newStatus, $note, $actor): Booking {
            $oldStatus = $booking->status;
            $now = now();

            $booking->status = $newStatus;

            if ($newStatus === BookingStatus::Confirmed && $booking->confirmed_at === null) {
                $booking->confirmed_at = $now;
            }

            if ($newStatus === BookingStatus::Completed) {
                $booking->confirmed_at ??= $now;
                $booking->completed_at = $now;
            } elseif ($oldStatus === BookingStatus::Completed) {
                $booking->completed_at = null;
            }

            $booking->save();

            BookingStatusLog::create([
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => $actor?->id,
                'note' => blank($note) ? null : trim($note),
            ]);

            return $booking->refresh();
        });
    }
}
