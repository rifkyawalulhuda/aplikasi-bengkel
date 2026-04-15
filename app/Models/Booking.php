<?php

namespace App\Models;

use App\Support\Enums\BookingStatus;
use App\Support\Enums\MotorcycleType;
use App\Support\Enums\PackageType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    public function getRouteKeyName(): string
    {
        return 'booking_code';
    }

    protected $fillable = [
        'booking_code',
        'customer_name',
        'customer_email',
        'customer_phone',
        'motorcycle_type',
        'motorcycle_brand',
        'motorcycle_model',
        'motorcycle_year',
        'plate_number',
        'package_type',
        'service_package_id',
        'package_name_snapshot',
        'package_price_snapshot',
        'notes',
        'service_date',
        'service_time',
        'status',
        'subtotal_price',
        'service_fee',
        'total_price',
        'address_text',
        'house_landmark',
        'latitude',
        'longitude',
        'admin_notes',
        'requires_manual_review',
        'confirmed_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'motorcycle_type' => MotorcycleType::class,
            'package_type' => PackageType::class,
            'status' => BookingStatus::class,
            'service_date' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'package_price_snapshot' => 'integer',
            'subtotal_price' => 'integer',
            'service_fee' => 'integer',
            'total_price' => 'integer',
            'requires_manual_review' => 'boolean',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->latest();
    }

    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    public function customItems(): HasMany
    {
        return $this->hasMany(BookingCustomItem::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(BookingStatusLog::class)->latest();
    }
}
