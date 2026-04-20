<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class BookingSetting extends Model
{
    protected $fillable = [
        'service_fee',
    ];

    protected function casts(): array
    {
        return [
            'service_fee' => 'integer',
        ];
    }

    public static function current(): self
    {
        if (! Schema::hasTable('booking_settings')) {
            return new self([
                'service_fee' => (int) config('booking.default_service_fee', 0),
            ]);
        }

        return static::query()->firstOrNew(
            ['id' => 1],
            ['service_fee' => (int) config('booking.default_service_fee', 0)],
        );
    }

    public static function currentServiceFee(): int
    {
        return (int) static::current()->service_fee;
    }
}
