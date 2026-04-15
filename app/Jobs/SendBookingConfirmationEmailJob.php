<?php

namespace App\Jobs;

use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendBookingConfirmationEmailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public readonly int $bookingId)
    {
    }

    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function handle(): void
    {
        $booking = Booking::query()
            ->with(['customItems', 'statusLogs'])
            ->find($this->bookingId);

        if (! $booking) {
            Log::warning('Booking confirmation email skipped because booking was not found.', [
                'booking_id' => $this->bookingId,
            ]);

            return;
        }

        try {
            Mail::to($booking->customer_email)->send(new BookingConfirmationMail($booking));
        } catch (Throwable $exception) {
            Log::warning('Booking confirmation email send attempt failed.', [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'recipient_hash' => hash('sha256', mb_strtolower($booking->customer_email)),
                'mailer' => Config::get('mail.default'),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Booking confirmation email job failed.', [
            'booking_id' => $this->bookingId,
            'mailer' => Config::get('mail.default'),
            'error' => $exception->getMessage(),
        ]);
    }
}
