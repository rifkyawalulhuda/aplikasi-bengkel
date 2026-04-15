<?php

namespace App\Http\Requests\Public;

use App\Models\CustomServiceItem;
use App\Models\ServicePackage;
use App\Support\Enums\MotorcycleType;
use App\Support\Enums\PackageType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'package_type' => ['required', Rule::enum(PackageType::class)],
            'service_package_id' => [
                Rule::requiredIf($this->input('package_type') === PackageType::FixedPackage->value),
                'nullable',
                'integer',
                'exists:service_packages,id',
            ],
            'custom_items' => [
                Rule::requiredIf($this->input('package_type') === PackageType::CustomPackage->value),
                'nullable',
                'array',
                'min:1',
            ],
            'custom_items.*.id' => ['required_with:custom_items', 'integer', 'exists:custom_service_items,id'],
            'custom_items.*.qty' => ['required_with:custom_items', 'integer', 'min:1'],
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_email' => ['required', 'email', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'motorcycle_type' => ['required', Rule::enum(MotorcycleType::class)],
            'motorcycle_brand' => ['required', 'string', 'max:100'],
            'motorcycle_model' => ['required', 'string', 'max:100'],
            'motorcycle_year' => ['nullable', 'digits:4'],
            'plate_number' => ['nullable', 'string', 'max:20'],
            'address_text' => ['required', 'string', 'max:255'],
            'house_landmark' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'service_date' => ['required', 'date', 'after_or_equal:today'],
            'service_time' => ['required', 'date_format:H:i', Rule::in(config('booking.available_hours', []))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $packageType = PackageType::tryFrom((string) $this->input('package_type'));

                if ($packageType === PackageType::FixedPackage) {
                    $servicePackageId = $this->integer('service_package_id');

                    $packageIsActive = ServicePackage::query()
                        ->active()
                        ->whereKey($servicePackageId)
                        ->exists();

                    if (! $packageIsActive) {
                        $validator->errors()->add(
                            'service_package_id',
                            'Paket servis tidak tersedia atau sudah dinonaktifkan.',
                        );
                    }

                    return;
                }

                if ($packageType !== PackageType::CustomPackage) {
                    return;
                }

                $customItems = collect($this->input('custom_items', []));
                $customItemIds = $customItems
                    ->pluck('id')
                    ->filter()
                    ->unique()
                    ->values();

                if ($customItemIds->isEmpty()) {
                    $validator->errors()->add(
                        'custom_items',
                        'Minimal satu item custom harus dipilih.',
                    );

                    return;
                }

                $activeCustomItemsCount = CustomServiceItem::query()
                    ->active()
                    ->whereIn('id', $customItemIds->all())
                    ->count();

                if ($activeCustomItemsCount !== $customItemIds->count()) {
                    $validator->errors()->add(
                        'custom_items',
                        'Ada item custom yang tidak valid atau sudah tidak aktif.',
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'customer_email.email' => 'Alamat email pelanggan harus valid.',
            'custom_items.min' => 'Minimal satu item custom harus dipilih.',
            'service_date.after_or_equal' => 'Tanggal servis tidak boleh di masa lalu.',
            'service_time.in' => 'Slot jadwal yang dipilih tidak tersedia. Silakan pilih jam lain.',
        ];
    }

    public function attributes(): array
    {
        return [
            'package_type' => 'jenis paket',
            'service_package_id' => 'paket servis',
            'custom_items' => 'item custom',
            'customer_name' => 'nama pelanggan',
            'customer_email' => 'email pelanggan',
            'customer_phone' => 'nomor telepon pelanggan',
            'motorcycle_type' => 'jenis motor',
            'motorcycle_brand' => 'merek motor',
            'motorcycle_model' => 'model motor',
            'motorcycle_year' => 'tahun motor',
            'plate_number' => 'plat nomor',
            'address_text' => 'alamat lengkap',
            'house_landmark' => 'patokan rumah',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'service_date' => 'tanggal servis',
            'service_time' => 'jam servis',
            'notes' => 'catatan tambahan',
        ];
    }
}
