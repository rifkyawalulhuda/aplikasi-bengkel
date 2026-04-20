<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Booking\DeleteBookingAction;
use App\Actions\Booking\UpdateBookingNotesAction;
use App\Actions\Booking\UpdateBookingStatusAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBookingNotesRequest;
use App\Http\Requests\Admin\UpdateBookingStatusRequest;
use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Support\Enums\BookingStatus;
use App\Support\Enums\PackageType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class BookingManagementController extends Controller
{
    public function index(Request $request): Response
    {
        $status = BookingStatus::tryFrom((string) $request->string('status'));
        $sort = $this->resolveSortOption((string) $request->string('sort'));
        $filters = [
            'search' => trim((string) $request->string('search')),
            'status' => $status?->value ?? '',
            'date' => trim((string) $request->string('date')),
            'sort' => $sort,
        ];

        $bookingsQuery = Booking::query()
            ->select([
                'id',
                'booking_code',
                'customer_name',
                'customer_phone',
                'package_name_snapshot',
                'service_date',
                'service_time',
                'status',
                'requires_manual_review',
                'total_price',
            ])
            ->when($filters['search'] !== '', function ($query) use ($filters): void {
                $query->where(function ($nestedQuery) use ($filters): void {
                    $nestedQuery
                        ->where('booking_code', 'like', '%'.$filters['search'].'%')
                        ->orWhere('customer_name', 'like', '%'.$filters['search'].'%')
                        ->orWhere('customer_phone', 'like', '%'.$filters['search'].'%');
                });
            })
            ->when($filters['status'] !== '', function ($query) use ($filters): void {
                $query->where('status', $filters['status']);
            })
            ->when($filters['date'] !== '', function ($query) use ($filters): void {
                $query->whereDate('service_date', $filters['date']);
            });

        $this->applySort($bookingsQuery, $sort);

        $bookings = $bookingsQuery
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Booking $booking): array => [
                'id' => $booking->id,
                'bookingCode' => $booking->booking_code,
                'customerName' => $booking->customer_name,
                'customerPhone' => $booking->customer_phone,
                'packageName' => $booking->package_name_snapshot,
                'serviceDate' => optional($booking->service_date)->format('Y-m-d'),
                'serviceDateLabel' => optional($booking->service_date)->format('d M Y'),
                'serviceTime' => $booking->service_time,
                'status' => $booking->status->value,
                'statusLabel' => $this->statusLabel($booking->status),
                'requiresManualReview' => $booking->requires_manual_review,
                'totalPrice' => $booking->total_price,
            ]);

        return Inertia::render('admin/BookingsIndexPage', [
            'bookings' => $bookings,
            'filters' => $filters,
            'statusOptions' => $this->statusOptions()->all(),
            'sortOptions' => $this->sortOptions(),
        ]);
    }

    public function show(Booking $booking): Response
    {
        $booking->load([
            'customItems:id,booking_id,custom_service_item_id,item_name_snapshot,item_price_snapshot,qty,subtotal',
            'statusLogs' => fn ($query) => $query
                ->select([
                    'id',
                    'booking_id',
                    'old_status',
                    'new_status',
                    'changed_by',
                    'note',
                    'created_at',
                ])
                ->with('changedByUser:id,name')
                ->orderByDesc('id'),
        ]);

        return Inertia::render('admin/BookingDetailPage', [
            'booking' => [
                'bookingCode' => $booking->booking_code,
                'status' => $booking->status->value,
                'statusLabel' => $this->statusLabel($booking->status),
                'confirmedAt' => $booking->confirmed_at?->format('Y-m-d H:i'),
                'completedAt' => $booking->completed_at?->format('Y-m-d H:i'),
                'requiresManualReview' => $booking->requires_manual_review,
                'customer' => [
                    'name' => $booking->customer_name,
                    'email' => $booking->customer_email,
                    'phone' => $booking->customer_phone,
                ],
                'motorcycle' => [
                    'type' => $booking->motorcycle_type->value,
                    'typeLabel' => ucfirst($booking->motorcycle_type->value),
                    'brand' => $booking->motorcycle_brand,
                    'model' => $booking->motorcycle_model,
                    'year' => $booking->motorcycle_year,
                    'plateNumber' => $booking->plate_number,
                ],
                'service' => [
                    'packageType' => $booking->package_type->value,
                    'packageTypeLabel' => $this->packageTypeLabel($booking->package_type),
                    'packageName' => $booking->package_name_snapshot,
                    'serviceDate' => optional($booking->service_date)->format('Y-m-d'),
                    'serviceDateLabel' => optional($booking->service_date)->format('d M Y'),
                    'serviceTime' => $booking->service_time,
                    'notes' => $booking->notes,
                    'customItems' => $booking->customItems->map(fn ($item): array => [
                        'id' => $item->id,
                        'name' => $item->item_name_snapshot,
                        'price' => $item->item_price_snapshot,
                        'qty' => $item->qty,
                        'subtotal' => $item->subtotal,
                    ])->all(),
                ],
                'pricing' => [
                    'packagePrice' => $booking->package_price_snapshot,
                    'subtotal' => $booking->subtotal_price,
                    'serviceFee' => $booking->service_fee,
                    'transportDistanceKm' => (float) $booking->transport_distance_km,
                    'transportCharge' => $booking->transport_charge,
                    'total' => $booking->total_price,
                ],
                'location' => [
                    'addressText' => $booking->address_text,
                    'houseLandmark' => $booking->house_landmark,
                    'latitude' => (string) $booking->latitude,
                    'longitude' => (string) $booking->longitude,
                    'mapUrl' => $this->mapUrl($booking),
                ],
                'adminNotes' => $booking->admin_notes,
                'statusHistory' => $booking->statusLogs->map(fn (BookingStatusLog $log): array => [
                    'id' => $log->id,
                    'oldStatus' => $log->old_status?->value,
                    'oldStatusLabel' => $log->old_status ? $this->statusLabel($log->old_status) : null,
                    'newStatus' => $log->new_status->value,
                    'newStatusLabel' => $this->statusLabel($log->new_status),
                    'note' => $log->note,
                    'changedAt' => $log->created_at?->format('Y-m-d H:i'),
                    'changedBy' => $log->changedByUser?->name,
                ])->all(),
            ],
            'statusOptions' => $this->statusOptions()->all(),
        ]);
    }

    public function updateStatus(
        UpdateBookingStatusRequest $request,
        Booking $booking,
        UpdateBookingStatusAction $updateBookingStatus,
    ): RedirectResponse {
        $validated = $request->validated();

        $updateBookingStatus->handle(
            booking: $booking,
            newStatus: BookingStatus::from($validated['status']),
            note: $validated['note'] ?? null,
            actor: $request->user(),
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Status booking berhasil diperbarui.',
        ]);

        return to_route('admin.bookings.show', $booking);
    }

    public function updateNotes(
        UpdateBookingNotesRequest $request,
        Booking $booking,
        UpdateBookingNotesAction $updateBookingNotes,
    ): RedirectResponse {
        $updateBookingNotes->handle(
            booking: $booking,
            adminNotes: $request->validated('admin_notes'),
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Catatan admin berhasil disimpan.',
        ]);

        return to_route('admin.bookings.show', $booking);
    }

    public function destroy(
        Booking $booking,
        DeleteBookingAction $deleteBooking,
    ): RedirectResponse {
        $deleteBooking->handle($booking);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Booking berhasil dihapus permanen.',
        ]);

        return to_route('admin.bookings.index');
    }

    private function mapUrl(Booking $booking): string
    {
        return 'https://www.google.com/maps?q='.$booking->latitude.','.$booking->longitude;
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'booking_code_asc' => $query->orderBy('booking_code')->orderByDesc('service_date')->orderByDesc('id'),
            'booking_code_desc' => $query->orderByDesc('booking_code')->orderByDesc('service_date')->orderByDesc('id'),
            'service_date_asc' => $query->orderBy('service_date')->orderBy('service_time')->orderBy('id'),
            'service_date_desc' => $query->orderByDesc('service_date')->orderByDesc('service_time')->orderByDesc('id'),
            'service_time_asc' => $query->orderBy('service_time')->orderBy('service_date')->orderBy('id'),
            'service_time_desc' => $query->orderByDesc('service_time')->orderByDesc('service_date')->orderByDesc('id'),
            'customer_name_asc' => $query->orderBy('customer_name')->orderByDesc('service_date')->orderByDesc('id'),
            'customer_name_desc' => $query->orderByDesc('customer_name')->orderByDesc('service_date')->orderByDesc('id'),
            'total_price_asc' => $query->orderBy('total_price')->orderByDesc('service_date')->orderByDesc('id'),
            'total_price_desc' => $query->orderByDesc('total_price')->orderByDesc('service_date')->orderByDesc('id'),
            default => $query->orderByDesc('service_date')->orderByDesc('service_time')->orderByDesc('id'),
        };
    }

    private function resolveSortOption(string $sort): string
    {
        $allowedSorts = collect($this->sortOptions())
            ->pluck('value')
            ->all();

        if (in_array($sort, $allowedSorts, true)) {
            return $sort;
        }

        return 'service_date_asc';
    }

    private function packageTypeLabel(PackageType $packageType): string
    {
        return match ($packageType) {
            PackageType::FixedPackage => 'Paket Tetap',
            PackageType::CustomPackage => 'Paket Custom',
        };
    }

    private function statusLabel(BookingStatus $status): string
    {
        return match ($status) {
            BookingStatus::Pending => 'Menunggu konfirmasi',
            BookingStatus::Confirmed => 'Terkonfirmasi',
            BookingStatus::OnTheWay => 'Mekanik menuju lokasi',
            BookingStatus::Completed => 'Selesai',
            BookingStatus::Cancelled => 'Dibatalkan',
            BookingStatus::Rescheduled => 'Dijadwalkan ulang',
        };
    }

    /**
     * @return Collection<int, array{value: string, label: string}>
     */
    private function statusOptions(): Collection
    {
        return collect(BookingStatus::cases())
            ->map(fn (BookingStatus $status): array => [
                'value' => $status->value,
                'label' => $this->statusLabel($status),
            ]);
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function sortOptions(): array
    {
        return [
            [
                'value' => 'service_date_asc',
                'label' => 'Tanggal servis terdekat',
            ],
            [
                'value' => 'service_date_desc',
                'label' => 'Tanggal servis terlama',
            ],
            [
                'value' => 'service_time_asc',
                'label' => 'Jam servis terawal',
            ],
            [
                'value' => 'service_time_desc',
                'label' => 'Jam servis terakhir',
            ],
            [
                'value' => 'booking_code_asc',
                'label' => 'Kode booking A-Z',
            ],
            [
                'value' => 'booking_code_desc',
                'label' => 'Kode booking Z-A',
            ],
            [
                'value' => 'customer_name_asc',
                'label' => 'Nama pelanggan A-Z',
            ],
            [
                'value' => 'customer_name_desc',
                'label' => 'Nama pelanggan Z-A',
            ],
            [
                'value' => 'total_price_desc',
                'label' => 'Total tertinggi',
            ],
            [
                'value' => 'total_price_asc',
                'label' => 'Total terendah',
            ],
        ];
    }
}
