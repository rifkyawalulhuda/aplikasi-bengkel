<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class BookingSetting extends Model
{
    protected $fillable = [
        'service_fee',
        'transport_free_radius_km',
        'transport_fee_per_km',
        'footer_address',
        'footer_latitude',
        'footer_longitude',
    ];

    protected function casts(): array
    {
        return [
            'service_fee' => 'integer',
            'transport_free_radius_km' => 'decimal:2',
            'transport_fee_per_km' => 'integer',
            'footer_latitude' => 'decimal:7',
            'footer_longitude' => 'decimal:7',
        ];
    }

    public static function current(): self
    {
        if (! Schema::hasTable('booking_settings')) {
            return new self([
                'service_fee' => (int) config('booking.default_service_fee', 0),
                'transport_free_radius_km' => (float) data_get(
                    config('workshop.transport_charge'),
                    'free_radius_km',
                    0,
                ),
                'transport_fee_per_km' => (int) data_get(
                    config('workshop.transport_charge'),
                    'fee_per_km',
                    0,
                ),
                'footer_address' => (string) data_get(
                    config('workshop.footer_location'),
                    'address',
                    '',
                ),
                'footer_latitude' => data_get(
                    config('workshop.footer_location'),
                    'latitude',
                ),
                'footer_longitude' => data_get(
                    config('workshop.footer_location'),
                    'longitude',
                ),
            ]);
        }

        return static::query()->firstOrNew(
            ['id' => 1],
            [
                'service_fee' => (int) config('booking.default_service_fee', 0),
                'transport_free_radius_km' => (float) data_get(
                    config('workshop.transport_charge'),
                    'free_radius_km',
                    0,
                ),
                'transport_fee_per_km' => (int) data_get(
                    config('workshop.transport_charge'),
                    'fee_per_km',
                    0,
                ),
                'footer_address' => (string) data_get(
                    config('workshop.footer_location'),
                    'address',
                    '',
                ),
                'footer_latitude' => data_get(
                    config('workshop.footer_location'),
                    'latitude',
                ),
                'footer_longitude' => data_get(
                    config('workshop.footer_location'),
                    'longitude',
                ),
            ],
        );
    }

    public static function currentServiceFee(): int
    {
        return (int) static::current()->service_fee;
    }

    /**
     * @return array{freeRadiusKm: float, feePerKm: int}
     */
    public static function currentTransportChargeSettings(): array
    {
        $current = static::current();

        return [
            'freeRadiusKm' => (float) ($current->transport_free_radius_km ?? data_get(
                config('workshop.transport_charge'),
                'free_radius_km',
                0,
            )),
            'feePerKm' => (int) ($current->transport_fee_per_km ?? data_get(
                config('workshop.transport_charge'),
                'fee_per_km',
                0,
            )),
        ];
    }

    /**
     * @return array{address: string, latitude: string, longitude: string}
     */
    public static function currentFooterLocation(): array
    {
        $current = static::current();

        return [
            'address' => (string) ($current->footer_address ?? data_get(
                config('workshop.footer_location'),
                'address',
                '',
            )),
            'latitude' => (string) ($current->footer_latitude ?? data_get(
                config('workshop.footer_location'),
                'latitude',
                '',
            )),
            'longitude' => (string) ($current->footer_longitude ?? data_get(
                config('workshop.footer_location'),
                'longitude',
                '',
            )),
        ];
    }
}
