export type BookingTransportChargeSettings = {
    freeRadiusKm: number;
    feePerKm: number;
};

export type BookingTransportChargePreview = {
    distanceKm: number;
    extraDistanceKm: number;
    freeRadiusKm: number;
    feePerKm: number;
    charge: number;
    isChargeable: boolean;
};

function toRadians(value: number): number {
    return (value * Math.PI) / 180;
}

function haversineDistanceKm(
    originLatitude: number,
    originLongitude: number,
    destinationLatitude: number,
    destinationLongitude: number,
): number {
    const earthRadiusKm = 6371;
    const latitudeDelta = toRadians(destinationLatitude - originLatitude);
    const longitudeDelta = toRadians(destinationLongitude - originLongitude);

    const a =
        Math.sin(latitudeDelta / 2) ** 2 +
        Math.cos(toRadians(originLatitude)) *
            Math.cos(toRadians(destinationLatitude)) *
            Math.sin(longitudeDelta / 2) ** 2;
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return earthRadiusKm * c;
}

export function calculateTransportChargePreview(
    origin: { latitude: number; longitude: number } | null,
    destination: { latitude: number; longitude: number } | null,
    settings: BookingTransportChargeSettings,
): BookingTransportChargePreview | null {
    if (!origin || !destination) {
        return null;
    }

    if (
        !Number.isFinite(origin.latitude) ||
        !Number.isFinite(origin.longitude) ||
        !Number.isFinite(destination.latitude) ||
        !Number.isFinite(destination.longitude)
    ) {
        return null;
    }

    const distanceKm = haversineDistanceKm(
        origin.latitude,
        origin.longitude,
        destination.latitude,
        destination.longitude,
    );
    const freeRadiusKm = Math.max(0, settings.freeRadiusKm);
    const feePerKm = Math.max(0, settings.feePerKm);
    const extraDistanceKm = Math.max(distanceKm - freeRadiusKm, 0);
    const charge = Math.round(extraDistanceKm * feePerKm);

    return {
        distanceKm: Math.round(distanceKm * 100) / 100,
        extraDistanceKm: Math.round(extraDistanceKm * 100) / 100,
        freeRadiusKm: Math.round(freeRadiusKm * 100) / 100,
        feePerKm,
        charge,
        isChargeable: charge > 0,
    };
}
