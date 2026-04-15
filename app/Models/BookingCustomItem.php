<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingCustomItem extends Model
{
    protected $fillable = [
        'booking_id',
        'custom_service_item_id',
        'item_name_snapshot',
        'item_price_snapshot',
        'qty',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'item_price_snapshot' => 'integer',
            'qty' => 'integer',
            'subtotal' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function customServiceItem(): BelongsTo
    {
        return $this->belongsTo(CustomServiceItem::class);
    }
}
