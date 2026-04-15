<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Booking $booking)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf('Konfirmasi Booking %s', $this->booking->booking_code),
        );
    }

    public function content(): Content
    {
        $booking = $this->booking->loadMissing('customItems');

        return new Content(
            view: 'emails.bookings.confirmation',
            with: [
                'booking' => $booking,
                'statusLabel' => $booking->status->label(),
                'workshopPhone' => config('workshop.contact_phone'),
                'workshopWhatsapp' => config('workshop.contact_whatsapp'),
            ],
        );
    }
}
