<?php

namespace App\Actions\Booking;

class CalculateTransportChargeAction
{
    /**
     * @return array{
     *     distanceKm: float,
     *     extraDistanceKm: float,
     *     freeRadiusKm: float,
     *     feePerKm: int,
     *     charge: int,
     *     isChargeable: bool
     * }
     */
    public function handle(
        float $originLatitude,
        float $originLongitude,
        float $destinationLatitude,
        float $destinationLongitude,
        float $freeRadiusKm,
        int $feePerKm,
    ): array {
        $distanceKm = $this->distanceKm(
            $originLatitude,
            $originLongitude,
            $destinationLatitude,
            $destinationLongitude,
        );
        $freeRadiusKm = max(0, $freeRadiusKm);
        $feePerKm = max(0, $feePerKm);
        $extraDistanceKm = max($distanceKm - $freeRadiusKm, 0);
        $charge = (int) round($extraDistanceKm * $feePerKm);

        return [
            'distanceKm' => round($distanceKm, 2),
            'extraDistanceKm' => round($extraDistanceKm, 2),
            'freeRadiusKm' => round($freeRadiusKm, 2),
            'feePerKm' => $feePerKm,
            'charge' => $charge,
            'isChargeable' => $charge > 0,
        ];
    }

    private function distanceKm(
        float $originLatitude,
        float $originLongitude,
        float $destinationLatitude,
        float $destinationLongitude,
    ): float {
        $earthRadiusKm = 6371.0;

        $latitudeDelta = deg2rad($destinationLatitude - $originLatitude);
        $longitudeDelta = deg2rad($destinationLongitude - $originLongitude);

        $a = sin($latitudeDelta / 2) ** 2
            + cos(deg2rad($originLatitude))
            * cos(deg2rad($destinationLatitude))
            * sin($longitudeDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
