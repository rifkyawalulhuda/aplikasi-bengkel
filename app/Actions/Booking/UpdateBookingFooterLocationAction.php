<?php

namespace App\Actions\Booking;

use App\Models\BookingSetting;

class UpdateBookingFooterLocationAction
{
    /**
     * @param  array{address: string, latitude: string, longitude: string}  $location
     */
    public function handle(array $location): BookingSetting
    {
        return BookingSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'footer_address' => $location['address'],
                'footer_latitude' => $location['latitude'],
                'footer_longitude' => $location['longitude'],
            ],
        );
    }
}
