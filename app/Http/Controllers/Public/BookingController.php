<?php

namespace App\Http\Controllers\Public;

use App\Actions\Booking\CreateBookingAction;
use App\Actions\Booking\GetPublicBookingPageDataAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreBookingRequest;
use App\Models\Booking;
use App\Support\Enums\BookingStatus;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class BookingController extends Controller
{
    public function create(Request $request, GetPublicBookingPageDataAction $getPublicBookingPageData): Response
    {
        $bookingPageData = $getPublicBookingPageData->handle();
        $prefill = $this->resolvePrefill(
            (string) $request->query('package', ''),
            collect($bookingPageData['packages']),
        );

        return Inertia::render('public/BookingPage', [
            'seo' => [
                'title' => 'Booking Home Service Motor',
                'description' => 'Isi data booking servis motor di rumah dengan alur 4 langkah, harga awal transparan, dan konfirmasi admin setelah pengajuan masuk.',
                'keywords' => [
                    'booking servis motor',
                    'booking home service motor',
                    'servis motor panggilan',
                ],
                'canonicalUrl' => route('bookings.create'),
            ],
            ...$bookingPageData,
            'prefill' => $prefill,
        ]);
    }

    public function store(StoreBookingRequest $request, CreateBookingAction $createBooking): RedirectResponse
    {
        try {
            $booking = $createBooking->handle($request->validated());
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Public booking creation failed.', $this->bookingFailureContext($request, $exception));

            return redirect()
                ->to($this->previousUrl($request))
                ->withInput($request->except('_token'))
                ->withErrors([
                    'booking' => (string) config('booking.messages.submission_failed'),
                ]);
        }

        return to_route('bookings.success', [
            'code' => $booking->booking_code,
        ]);
    }

    public function success(Request $request): Response
    {
        $booking = null;
        $code = (string) $request->string('code');

        if ($code !== '' && Schema::hasTable('bookings')) {
            $bookingModel = Booking::query()
                ->where('booking_code', $code)
                ->first([
                    'booking_code',
                    'service_date',
                    'service_time',
                    'status',
                ]);

            if ($bookingModel) {
                $booking = [
                    'code' => $bookingModel->booking_code,
                    'serviceDate' => optional($bookingModel->service_date)->format('d M Y'),
                    'serviceTime' => $bookingModel->service_time,
                    'status' => $bookingModel->status->value,
                    'statusLabel' => $this->statusLabel($bookingModel->status),
                ];
            }
        }

        return Inertia::render('public/BookingSuccessPage', [
            'booking' => $booking,
            'bookingCode' => $code,
            'whatsAppUrl' => $this->buildWhatsAppUrl($code),
        ]);
    }

    public function show(Booking $booking): Response
    {
        $booking->load(['customItems', 'statusLogs']);

        return Inertia::render('public/BookingSummaryPage', [
            'booking' => [
                'code' => $booking->booking_code,
                'packageName' => $booking->package_name_snapshot,
                'serviceDate' => optional($booking->service_date)->format('d M Y'),
                'serviceTime' => $booking->service_time,
                'status' => $booking->status->value,
                'statusLabel' => $this->statusLabel($booking->status),
                'totalPrice' => $booking->total_price,
                'customItems' => $booking->customItems->map(fn ($item): array => [
                    'name' => $item->item_name_snapshot,
                    'qty' => $item->qty,
                    'subtotal' => $item->subtotal,
                ])->all(),
            ],
            'whatsAppUrl' => $this->buildWhatsAppUrl($booking->booking_code),
        ]);
    }

    private function statusLabel(BookingStatus $status): string
    {
        return match ($status) {
            BookingStatus::Pending => 'Menunggu konfirmasi',
            BookingStatus::Confirmed => 'Sudah dikonfirmasi',
            BookingStatus::OnTheWay => 'Mekanik sedang menuju lokasi',
            BookingStatus::Completed => 'Servis selesai',
            BookingStatus::Cancelled => 'Booking dibatalkan',
            BookingStatus::Rescheduled => 'Jadwal diubah',
        };
    }

    private function buildWhatsAppUrl(string $bookingCode): string
    {
        $whatsAppNumber = preg_replace('/\D+/', '', (string) config('workshop.contact_whatsapp'));
        $message = $bookingCode !== ''
            ? sprintf('Halo admin, saya ingin konfirmasi booking dengan kode %s.', $bookingCode)
            : 'Halo admin, saya ingin bertanya tentang booking servis motor.';

        return sprintf(
            'https://wa.me/%s?%s',
            $whatsAppNumber,
            http_build_query([
                'text' => $message,
            ]),
        );
    }

    /**
     * @return array<string, int|string|null>
     */
    private function bookingFailureContext(StoreBookingRequest $request, Throwable $exception): array
    {
        return [
            'session_key' => $request->session()->getId(),
            'ip_hash' => hash('sha256', (string) $request->ip()),
            'path' => $request->path(),
            'package_type' => (string) $request->input('package_type'),
            'service_package_id' => $request->integer('service_package_id') ?: null,
            'custom_item_count' => count($request->input('custom_items', [])),
            'service_date' => (string) $request->input('service_date'),
            'service_time' => (string) $request->input('service_time'),
            'customer_email_hash' => $this->hashValue((string) $request->input('customer_email')),
            'exception' => $exception::class,
            'error' => $exception->getMessage(),
        ];
    }

    private function hashValue(string $value): ?string
    {
        $trimmedValue = trim($value);

        if ($trimmedValue === '') {
            return null;
        }

        return hash('sha256', mb_strtolower($trimmedValue));
    }

    private function previousUrl(Request $request): string
    {
        $previousUrl = url()->previous();

        if ($previousUrl === '' || $previousUrl === $request->fullUrl()) {
            return route('bookings.create');
        }

        return $previousUrl;
    }

    /**
     * @param  Collection<int, array{id: int, slug: string}>  $packages
     * @return array{packageSlug: string|null, startsInCustomMode: bool}
     */
    private function resolvePrefill(string $requestedPackage, Collection $packages): array
    {
        $normalizedPackage = mb_strtolower(trim($requestedPackage));

        if ($normalizedPackage === 'custom') {
            return [
                'packageSlug' => null,
                'startsInCustomMode' => true,
            ];
        }

        $selectedPackage = $packages
            ->first(fn (array $package): bool => mb_strtolower((string) Arr::get($package, 'slug')) === $normalizedPackage);

        return [
            'packageSlug' => Arr::get($selectedPackage, 'slug'),
            'startsInCustomMode' => false,
        ];
    }
}
