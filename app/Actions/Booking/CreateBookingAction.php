<?php

namespace App\Actions\Booking;

use App\Jobs\SendBookingConfirmationEmailJob;
use App\Models\Booking;
use App\Models\BookingCustomItem;
use App\Models\BookingStatusLog;
use App\Support\Enums\BookingStatus;
use App\Support\Enums\PackageType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class CreateBookingAction
{
    public function __construct(
        private readonly CalculateBookingPriceAction $calculateBookingPrice,
        private readonly GenerateBookingCodeAction $generateBookingCode,
        private readonly ValidateBookingSlotAction $validateBookingSlot,
        private readonly ValidateCoverageAreaAction $validateCoverageArea,
    ) {
    }

    public function handle(array $attributes): Booking
    {
        return DB::transaction(function () use ($attributes): Booking {
            $packageType = PackageType::from($attributes['package_type']);

            $this->validateBookingSlot->handle(
                serviceDate: $attributes['service_date'],
                serviceTime: $attributes['service_time'],
            );

            $coverage = $this->validateCoverageArea->handle(
                latitude: (float) $attributes['latitude'],
                longitude: (float) $attributes['longitude'],
            );

            $pricing = $this->calculateBookingPrice->handle(
                packageType: $packageType,
                servicePackageId: $packageType === PackageType::FixedPackage
                    ? ($attributes['service_package_id'] ?? null)
                    : null,
                customItems: $attributes['custom_items'] ?? [],
            );

            $statusLogNote = 'Booking created by customer.';

            if ($coverage['requires_manual_review'] && $coverage['reason']) {
                $statusLogNote .= ' '.$coverage['reason'];
            }

            $booking = Booking::create([
                'booking_code' => $this->generateBookingCode->handle(CarbonImmutable::parse($attributes['service_date'])),
                'customer_name' => $attributes['customer_name'],
                'customer_email' => $attributes['customer_email'],
                'customer_phone' => $attributes['customer_phone'],
                'motorcycle_type' => $attributes['motorcycle_type'],
                'motorcycle_brand' => $attributes['motorcycle_brand'],
                'motorcycle_model' => $attributes['motorcycle_model'],
                'motorcycle_year' => $attributes['motorcycle_year'] ?? null,
                'plate_number' => $attributes['plate_number'] ?? null,
                'package_type' => $packageType,
                'service_package_id' => $packageType === PackageType::FixedPackage
                    ? ($attributes['service_package_id'] ?? null)
                    : null,
                'package_name_snapshot' => $pricing['package_name_snapshot'],
                'package_price_snapshot' => $pricing['package_price_snapshot'],
                'notes' => $attributes['notes'] ?? null,
                'service_date' => $attributes['service_date'],
                'service_time' => $attributes['service_time'],
                'status' => BookingStatus::Pending,
                'subtotal_price' => $pricing['subtotal_price'],
                'service_fee' => $pricing['service_fee'],
                'total_price' => $pricing['total_price'],
                'address_text' => $attributes['address_text'],
                'house_landmark' => $attributes['house_landmark'],
                'latitude' => $attributes['latitude'],
                'longitude' => $attributes['longitude'],
                'admin_notes' => $coverage['requires_manual_review'] ? $coverage['reason'] : null,
                'requires_manual_review' => $coverage['requires_manual_review'],
            ]);

            foreach ($pricing['custom_items_snapshot'] as $itemSnapshot) {
                BookingCustomItem::create([
                    'booking_id' => $booking->id,
                    ...$itemSnapshot,
                ]);
            }

            BookingStatusLog::create([
                'booking_id' => $booking->id,
                'old_status' => null,
                'new_status' => BookingStatus::Pending,
                'changed_by' => null,
                'note' => $statusLogNote,
            ]);

            SendBookingConfirmationEmailJob::dispatch($booking->id)->afterCommit();

            return $booking->load(['customItems', 'statusLogs']);
        });
    }
}
