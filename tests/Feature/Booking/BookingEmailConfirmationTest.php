<?php

use App\Jobs\SendBookingConfirmationEmailJob;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\BookingCustomItem;
use App\Support\Enums\BookingStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

function createEmailBooking(array $overrides = []): Booking
{
    return Booking::query()->create(array_merge([
        'booking_code' => 'ASM-20260414-0099',
        'customer_name' => 'Sinta Pratiwi',
        'customer_email' => 'sinta@example.com',
        'customer_phone' => '081298765432',
        'motorcycle_type' => 'matic',
        'motorcycle_brand' => 'Yamaha',
        'motorcycle_model' => 'NMAX',
        'motorcycle_year' => '2024',
        'plate_number' => 'B2222XYZ',
        'package_type' => 'custom_package',
        'service_package_id' => null,
        'package_name_snapshot' => 'Paket Custom',
        'package_price_snapshot' => 0,
        'notes' => 'Mohon datang setelah jam makan siang.',
        'service_date' => '2026-04-21',
        'service_time' => '13:00',
        'status' => BookingStatus::Pending,
        'subtotal_price' => 155000,
        'service_fee' => 10000,
        'total_price' => 165000,
        'address_text' => 'Jl Melati No 20, Depok',
        'house_landmark' => 'Dekat masjid besar',
        'latitude' => '-6.3900000',
        'longitude' => '106.8300000',
        'admin_notes' => null,
        'requires_manual_review' => false,
    ], $overrides));
}

test('booking confirmation mail renders booking snapshot and admin contact information', function () {
    config()->set('workshop.contact_phone', '0812-0000-0000');
    config()->set('workshop.contact_whatsapp', '6281200000000');

    $booking = createEmailBooking();

    BookingCustomItem::query()->create([
        'booking_id' => $booking->id,
        'custom_service_item_id' => null,
        'item_name_snapshot' => 'Ganti Oli',
        'item_price_snapshot' => 45000,
        'qty' => 2,
        'subtotal' => 90000,
    ]);

    $mail = new BookingConfirmationMail($booking->fresh('customItems'));
    $html = $mail->render();

    expect($html)->toContain('Sinta Pratiwi')
        ->toContain('ASM-20260414-0099')
        ->toContain('Paket Custom')
        ->toContain('Ganti Oli')
        ->toContain('21 Apr 2026')
        ->toContain('13:00')
        ->toContain('Jl Melati No 20, Depok')
        ->toContain('Dekat masjid besar')
        ->toContain('Pending')
        ->toContain('0812-0000-0000')
        ->toContain('6281200000000')
        ->toContain('165.000');
});

test('send booking confirmation email job sends the mailable asynchronously', function () {
    Mail::fake();

    $booking = createEmailBooking();

    SendBookingConfirmationEmailJob::dispatchSync($booking->id);

    Mail::assertSent(BookingConfirmationMail::class, function (BookingConfirmationMail $mail) use ($booking): bool {
        return $mail->booking->is($booking);
    });
});

test('send booking confirmation email job logs warning when booking is missing', function () {
    Log::spy();

    SendBookingConfirmationEmailJob::dispatchSync(999999);

    Log::shouldHaveReceived('warning')
        ->once()
        ->withArgs(function (string $message, array $context): bool {
            return $message === 'Booking confirmation email skipped because booking was not found.'
                && $context['booking_id'] === 999999;
        });
});

test('send booking confirmation email job logs failure details when queue execution fails', function () {
    Log::spy();

    $job = new SendBookingConfirmationEmailJob(123);
    $job->failed(new RuntimeException('SMTP gateway timeout'));

    Log::shouldHaveReceived('error')
        ->once()
        ->withArgs(function (string $message, array $context): bool {
            return $message === 'Booking confirmation email job failed.'
                && $context['booking_id'] === 123
                && $context['error'] === 'SMTP gateway timeout';
        });
});

test('send booking confirmation email job logs the failed send attempt and does not remove the booking', function () {
    Log::spy();

    $booking = createEmailBooking([
        'booking_code' => 'ASM-20260414-0100',
        'customer_email' => 'retry@example.com',
    ]);

    Mail::shouldReceive('to')
        ->once()
        ->with('retry@example.com')
        ->andReturnSelf();

    Mail::shouldReceive('send')
        ->once()
        ->andThrow(new RuntimeException('SMTP gateway timeout'));

    expect(fn () => (new SendBookingConfirmationEmailJob($booking->id))->handle())
        ->toThrow(RuntimeException::class, 'SMTP gateway timeout');

    expect($booking->fresh())->not->toBeNull();

    Log::shouldHaveReceived('warning')
        ->once()
        ->withArgs(function (string $message, array $context) use ($booking): bool {
            return $message === 'Booking confirmation email send attempt failed.'
                && $context['booking_id'] === $booking->id
                && $context['booking_code'] === $booking->booking_code
                && $context['error'] === 'SMTP gateway timeout';
        });
});
