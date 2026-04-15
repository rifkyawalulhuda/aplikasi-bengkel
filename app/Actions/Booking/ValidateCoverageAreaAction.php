<?php

namespace App\Actions\Booking;

class ValidateCoverageAreaAction
{
    public function handle(float $latitude, float $longitude): array
    {
        $boundingBox = config('workshop.coverage.bounding_box');

        if (! is_array($boundingBox)) {
            return [
                'requires_manual_review' => false,
                'reason' => null,
            ];
        }

        $insideCoverage = $latitude >= $boundingBox['min_latitude']
            && $latitude <= $boundingBox['max_latitude']
            && $longitude >= $boundingBox['min_longitude']
            && $longitude <= $boundingBox['max_longitude'];

        return [
            'requires_manual_review' => ! $insideCoverage,
            'reason' => $insideCoverage ? null : 'Lokasi berada di luar coverage otomatis dan perlu review admin.',
        ];
    }
}
