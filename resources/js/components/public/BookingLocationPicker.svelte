<script lang="ts">
    import { onMount } from 'svelte';
    import 'leaflet/dist/leaflet.css';
    import markerIcon2xUrl from 'leaflet/dist/images/marker-icon-2x.png?url';
    import markerIconUrl from 'leaflet/dist/images/marker-icon.png?url';
    import markerShadowUrl from 'leaflet/dist/images/marker-shadow.png?url';
    import type { Map as LeafletMap, Marker as LeafletMarker } from 'leaflet';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import type { BookingLocationForm } from '@/types';

    type LocationFeedbackTone = 'info' | 'success' | 'error';

    let {
        location = $bindable<BookingLocationForm>({
            addressText: '',
            houseLandmark: '',
            latitude: '',
            longitude: '',
        }),
        errors = {},
    }: {
        location?: BookingLocationForm;
        errors?: Record<string, string>;
    } = $props();

    const defaultCoordinates = {
        latitude: -6.2,
        longitude: 106.816666,
    };

    let mapContainer: HTMLDivElement | null = null;
    let map = $state<LeafletMap | null>(null);
    let marker = $state<LeafletMarker | null>(null);
    let leaflet = $state<typeof import('leaflet') | null>(null);
    let isRequestingLocation = $state(false);
    let locationFeedback = $state('');
    let locationFeedbackTone = $state<LocationFeedbackTone>('info');

    const parsedCoordinates = $derived.by(() => {
        const latitude = Number(location.latitude);
        const longitude = Number(location.longitude);

        if (
            Number.isNaN(latitude) ||
            Number.isNaN(longitude) ||
            latitude < -90 ||
            latitude > 90 ||
            longitude < -180 ||
            longitude > 180
        ) {
            return null;
        }

        return {
            latitude,
            longitude,
        };
    });

    const mapsUrl = $derived(
        parsedCoordinates
            ? `https://www.openstreetmap.org/?mlat=${parsedCoordinates.latitude}&mlon=${parsedCoordinates.longitude}#map=16/${parsedCoordinates.latitude}/${parsedCoordinates.longitude}`
            : '',
    );

    function formatCoordinate(value: number): string {
        return value.toFixed(6);
    }

    function setLocationCoordinates(
        latitude: number,
        longitude: number,
        feedback: string,
        tone: LocationFeedbackTone = 'success',
    ): void {
        location.latitude = formatCoordinate(latitude);
        location.longitude = formatCoordinate(longitude);
        locationFeedback = feedback;
        locationFeedbackTone = tone;
    }

    function syncMarkerPosition(latitude: number, longitude: number): void {
        if (!map || !marker) {
            return;
        }

        marker.setLatLng([latitude, longitude]);
        map.setView([latitude, longitude], Math.max(map.getZoom(), 16), {
            animate: true,
        });
    }

    function handleMapClick(latitude: number, longitude: number): void {
        setLocationCoordinates(
            latitude,
            longitude,
            'Titik lokasi berhasil dipilih dari peta.',
        );
        syncMarkerPosition(latitude, longitude);
    }

    function useDeviceLocation(): void {
        if (!navigator.geolocation) {
            locationFeedback =
                'Perangkat ini tidak mendukung pengambilan lokasi otomatis.';
            locationFeedbackTone = 'error';
            return;
        }

        isRequestingLocation = true;
        locationFeedback = 'Mencari lokasi perangkat...';
        locationFeedbackTone = 'info';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                isRequestingLocation = false;
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                setLocationCoordinates(
                    latitude,
                    longitude,
                    'Lokasi perangkat berhasil digunakan.',
                );
                syncMarkerPosition(latitude, longitude);
            },
            (error) => {
                isRequestingLocation = false;

                if (error.code === 1) {
                    locationFeedback =
                        'Akses lokasi ditolak. Silakan pilih titik secara manual di peta.';
                } else if (error.code === 3) {
                    locationFeedback =
                        'Permintaan lokasi perangkat terlalu lama. Coba lagi atau pilih titik manual.';
                } else {
                    locationFeedback =
                        'Gagal membaca lokasi perangkat. Silakan pilih titik secara manual di peta.';
                }

                locationFeedbackTone = 'error';
            },
            {
                enableHighAccuracy: true,
                maximumAge: 60_000,
                timeout: 10_000,
            },
        );
    }

    onMount(() => {
        let cancelled = false;

        void (async () => {
            const importedLeaflet = await import('leaflet');

            if (cancelled) {
                return;
            }

            leaflet = importedLeaflet;

            leaflet.Icon.Default.mergeOptions({
                iconRetinaUrl: markerIcon2xUrl,
                iconUrl: markerIconUrl,
                shadowUrl: markerShadowUrl,
            });

            if (!mapContainer) {
                return;
            }

            const initialCoordinates = parsedCoordinates ?? defaultCoordinates;

            map = leaflet.map(mapContainer, {
                scrollWheelZoom: false,
            }).setView(
                [initialCoordinates.latitude, initialCoordinates.longitude],
                15,
            );

            leaflet
                .tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution:
                        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19,
                })
                .addTo(map);

            marker = leaflet
                .marker([
                    initialCoordinates.latitude,
                    initialCoordinates.longitude,
                ], {
                    draggable: true,
                })
                .addTo(map);

            map.on('click', (event) => {
                handleMapClick(event.latlng.lat, event.latlng.lng);
            });

            marker.on('dragend', () => {
                const latLng = marker?.getLatLng();

                if (!latLng) {
                    return;
                }

                setLocationCoordinates(
                    latLng.lat,
                    latLng.lng,
                    'Titik lokasi disesuaikan dari marker peta.',
                );
            });

            map.whenReady(() => {
                locationFeedback =
                    'Klik peta, drag marker, atau gunakan lokasi perangkat.';
                locationFeedbackTone = 'info';
            });
        })();

        return () => {
            cancelled = true;
            map?.remove();
            map = null;
            marker = null;
        };
    });

    $effect(() => {
        if (!map || !marker || !parsedCoordinates) {
            return;
        }

        const currentLatLng = marker.getLatLng();
        const latitudeDifference = Math.abs(
            currentLatLng.lat - parsedCoordinates.latitude,
        );
        const longitudeDifference = Math.abs(
            currentLatLng.lng - parsedCoordinates.longitude,
        );

        if (latitudeDifference < 0.000001 && longitudeDifference < 0.000001) {
            return;
        }

        marker.setLatLng([
            parsedCoordinates.latitude,
            parsedCoordinates.longitude,
        ]);
        map.setView(
            [parsedCoordinates.latitude, parsedCoordinates.longitude],
            Math.max(map.getZoom(), 16),
            {
                animate: true,
            },
        );
    });
</script>

<div class="space-y-6">
    <div class="space-y-2">
        <h3 class="text-lg font-semibold text-foreground">3. Lokasi servis</h3>
        <p class="text-sm leading-6 text-muted-foreground">
            Pilih titik lokasi langsung di peta, atau gunakan lokasi perangkat
            untuk mengisi koordinat dengan lebih cepat.
        </p>
    </div>

    <div class="rounded-[1.5rem] border border-border/70 bg-card/95 p-4 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-1">
                <p class="text-sm font-semibold text-foreground">
                    Pilih titik lokasi di peta
                </p>
                <p class="text-sm leading-6 text-muted-foreground">
                    Klik peta, geser marker, atau pakai lokasi perangkat untuk
                    mengisi latitude dan longitude otomatis.
                </p>
            </div>

            <Button
                type="button"
                variant="outline"
                class="w-full lg:w-auto"
                onclick={useDeviceLocation}
                disabled={isRequestingLocation}
            >
                {#if isRequestingLocation}
                    Mencari lokasi...
                {:else}
                    Gunakan lokasi perangkat saya
                {/if}
            </Button>
        </div>

        <div class="mt-4 overflow-hidden rounded-[1.25rem] border border-border/70">
            <div
                bind:this={mapContainer}
                role="img"
                aria-label="Peta lokasi servis"
                class="h-[320px] w-full bg-muted sm:h-[380px]"
            ></div>
        </div>

        <div class="mt-4 flex flex-col gap-3 rounded-[1.25rem] bg-muted p-4 text-sm leading-6">
            {#if locationFeedback}
                <p
                    class={
                        locationFeedbackTone === 'error'
                            ? 'text-destructive'
                            : locationFeedbackTone === 'success'
                              ? 'text-emerald-600'
                              : 'text-muted-foreground'
                    }
                >
                    {locationFeedback}
                </p>
            {:else}
                <p class="text-muted-foreground">
                    Belum ada titik terpilih. Pakai lokasi perangkat atau klik
                    peta untuk mengisi koordinat.
                </p>
            {/if}

            {#if mapsUrl}
                <a
                    href={mapsUrl}
                    target="_blank"
                    rel="noreferrer"
                    class="inline-flex w-fit text-sm font-medium text-primary underline-offset-4 hover:underline"
                >
                    Buka titik ini di OpenStreetMap
                </a>
            {/if}
        </div>
    </div>

    <div class="grid gap-4">
        <div class="space-y-2">
            <Label for="address-text">Alamat lengkap</Label>
            <textarea
                id="address-text"
                bind:value={location.addressText}
                aria-invalid={Boolean(errors.address_text)}
                rows="4"
                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                placeholder="Contoh: Jl. Mawar No. 10, RT 03 / RW 05, Kel. Sukamaju, Kec. Bogor Tengah"
            ></textarea>
            {#if errors.address_text}
                <p class="text-sm font-medium text-destructive">
                    {errors.address_text}
                </p>
            {/if}
        </div>

        <div class="space-y-2">
            <Label for="house-landmark">Patokan rumah</Label>
            <Input
                id="house-landmark"
                bind:value={location.houseLandmark}
                aria-invalid={Boolean(errors.house_landmark)}
                placeholder="Contoh: pagar hitam dekat warung kelontong"
            />
            {#if errors.house_landmark}
                <p class="text-sm font-medium text-destructive">
                    {errors.house_landmark}
                </p>
            {/if}
        </div>

        <details class="rounded-[1.25rem] border border-dashed border-border/70 bg-muted/50 p-4">
            <summary class="cursor-pointer text-sm font-semibold text-foreground">
                Tampilkan koordinat manual
            </summary>
            <p class="mt-2 text-sm leading-6 text-muted-foreground">
                Jika peta tidak dapat dipakai, koordinat tetap bisa diisi manual
                sebagai cadangan.
            </p>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="latitude">Latitude</Label>
                    <Input
                        id="latitude"
                        bind:value={location.latitude}
                        aria-invalid={Boolean(errors.latitude)}
                        placeholder="-6.200000"
                    />
                    {#if errors.latitude}
                        <p class="text-sm font-medium text-destructive">
                            {errors.latitude}
                        </p>
                    {/if}
                </div>

                <div class="space-y-2">
                    <Label for="longitude">Longitude</Label>
                    <Input
                        id="longitude"
                        bind:value={location.longitude}
                        aria-invalid={Boolean(errors.longitude)}
                        placeholder="106.816666"
                    />
                    {#if errors.longitude}
                        <p class="text-sm font-medium text-destructive">
                            {errors.longitude}
                        </p>
                    {/if}
                </div>
            </div>
        </details>
    </div>
</div>
