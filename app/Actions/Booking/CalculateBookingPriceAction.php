<?php

namespace App\Actions\Booking;

use App\Models\CustomServiceItem;
use App\Models\ServicePackage;
use App\Support\Enums\PackageType;
use Illuminate\Validation\ValidationException;

class CalculateBookingPriceAction
{
    public function handle(PackageType $packageType, ?int $servicePackageId, array $customItems = []): array
    {
        $serviceFee = (int) config('booking.default_service_fee', 0);

        if ($packageType === PackageType::FixedPackage) {
            $servicePackage = ServicePackage::query()
                ->active()
                ->find($servicePackageId);

            if (! $servicePackage) {
                throw ValidationException::withMessages([
                    'service_package_id' => 'Paket servis tidak tersedia atau sudah dinonaktifkan.',
                ]);
            }

            return [
                'package_name_snapshot' => $servicePackage->name,
                'package_price_snapshot' => $servicePackage->price,
                'subtotal_price' => $servicePackage->price,
                'service_fee' => $serviceFee,
                'total_price' => $servicePackage->price + $serviceFee,
                'custom_items_snapshot' => [],
            ];
        }

        if ($customItems === []) {
            throw ValidationException::withMessages([
                'custom_items' => 'Minimal satu item custom harus dipilih.',
            ]);
        }

        $requestedItems = collect($customItems)
            ->groupBy('id')
            ->map(function ($items, $itemId): array {
                return [
                    'id' => (int) $itemId,
                    'qty' => max(1, $items->sum(fn (array $item): int => (int) data_get($item, 'qty', 1))),
                ];
            })
            ->keyBy('id');

        $activeItems = CustomServiceItem::query()
            ->active()
            ->whereIn('id', $requestedItems->keys()->all())
            ->get()
            ->keyBy('id');

        if ($activeItems->count() !== $requestedItems->count()) {
            throw ValidationException::withMessages([
                'custom_items' => 'Ada item custom yang tidak valid atau sudah tidak aktif.',
            ]);
        }

        $snapshots = $requestedItems
            ->map(function (array $requestedItem) use ($activeItems): array {
                /** @var \App\Models\CustomServiceItem $item */
                $item = $activeItems->get($requestedItem['id']);
                $qty = max(1, (int) $requestedItem['qty']);
                $subtotal = $item->price * $qty;

                return [
                    'custom_service_item_id' => $item->id,
                    'item_name_snapshot' => $item->name,
                    'item_price_snapshot' => $item->price,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ];
            })
            ->values();

        $subtotal = $snapshots->sum('subtotal');

        return [
            'package_name_snapshot' => PackageType::CustomPackage->label(),
            'package_price_snapshot' => 0,
            'subtotal_price' => $subtotal,
            'service_fee' => $serviceFee,
            'total_price' => $subtotal + $serviceFee,
            'custom_items_snapshot' => $snapshots->all(),
        ];
    }
}
