<?php

namespace App\Actions\Booking;

use App\Models\CustomServiceItem;
use App\Models\ServicePackage;
use App\Support\Enums\MotorcycleType;
use App\Support\Enums\PackageType;
use Illuminate\Support\Facades\Schema;

class GetPublicBookingPageDataAction
{
    public function __construct(
        private readonly GetBookingServiceFeeAction $getBookingServiceFee,
    ) {}

    /**
     * @return array{
     *     packageTypes: array<int, array{value: string, label: string}>,
     *     motorcycleTypes: array<int, array{value: string, label: string}>,
     *     packages: array<int, array{
     *         id: int,
     *         name: string,
     *         slug: string,
     *         shortDescription: string|null,
     *         price: int,
     *         durationEstimateMinutes: int,
     *         items: array<int, array{id: int, name: string, description: string|null}>
     *     }>,
     *     customItems: array<int, array{
     *         id: int,
     *         name: string,
     *         category: string,
     *         description: string|null,
     *         price: int,
     *         unitLabel: string|null
     *     }>,
     *     serviceFee: int,
     *     availableSlots: array<int, string>
     * }
     */
    public function handle(): array
    {
        return [
            'packageTypes' => array_map(
                static fn (PackageType $type): array => [
                    'value' => $type->value,
                    'label' => $type->label(),
                ],
                PackageType::cases(),
            ),
            'motorcycleTypes' => array_map(
                static fn (MotorcycleType $type): array => [
                    'value' => $type->value,
                    'label' => $type->label(),
                ],
                MotorcycleType::cases(),
            ),
            'packages' => $this->packages(),
            'customItems' => $this->customItems(),
            'serviceFee' => $this->getBookingServiceFee->handle(),
            'availableSlots' => config('booking.available_hours'),
        ];
    }

    /**
     * @return array<int, array{
     *     id: int,
     *     name: string,
     *     slug: string,
     *     shortDescription: string|null,
     *     price: int,
     *     durationEstimateMinutes: int,
     *     isFeatured: bool,
     *     items: array<int, array{id: int, name: string, description: string|null}>
     * }>
     */
    private function packages(): array
    {
        if (! Schema::hasTable('service_packages')) {
            return [];
        }

        return ServicePackage::query()
            ->active()
            ->with('items')
            ->ordered()
            ->get()
            ->map(fn (ServicePackage $servicePackage): array => [
                'id' => $servicePackage->id,
                'name' => $servicePackage->name,
                'slug' => $servicePackage->slug,
                'shortDescription' => $servicePackage->short_description,
                'price' => $servicePackage->price,
                'durationEstimateMinutes' => $servicePackage->duration_estimate_minutes,
                'isFeatured' => $servicePackage->is_featured,
                'items' => $servicePackage->items->map(fn ($item): array => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                ])->all(),
            ])
            ->all();
    }

    /**
     * @return array<int, array{
     *     id: int,
     *     name: string,
     *     category: string,
     *     description: string|null,
     *     price: int,
     *     unitLabel: string|null
     * }>
     */
    private function customItems(): array
    {
        if (! Schema::hasTable('custom_service_items')) {
            return [];
        }

        return CustomServiceItem::query()
            ->active()
            ->ordered()
            ->get(['id', 'name', 'category', 'description', 'price', 'unit_label'])
            ->map(fn (CustomServiceItem $item): array => [
                'id' => $item->id,
                'name' => $item->name,
                'category' => $item->category,
                'description' => $item->description,
                'price' => $item->price,
                'unitLabel' => $item->unit_label,
            ])
            ->all();
    }
}
