<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class BookingSetting extends Model
{
    protected $fillable = [
        'service_fee',
        'footer_address',
        'footer_latitude',
        'footer_longitude',
    ];

    protected function casts(): array
    {
        return [
            'service_fee' => 'integer',
            'footer_latitude' => 'decimal:7',
            'footer_longitude' => 'decimal:7',
        ];
    }

    public static function current(): self
    {
        if (! Schema::hasTable('booking_settings')) {
            return new self([
                'service_fee' => (int) config('booking.default_service_fee', 0),
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
