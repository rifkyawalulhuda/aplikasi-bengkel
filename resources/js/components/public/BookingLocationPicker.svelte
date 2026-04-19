<script lang="ts">
    import { onMount } from 'svelte';
    import 'leaflet/dist/leaflet.css';
    import type {
        DivIcon as LeafletDivIcon,
        Map as LeafletMap,
        Marker as LeafletMarker,
    } from 'leaflet';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import type { BookingLocationForm } from '@/types';

    type AddressSuggestion = {
        displayName: string;
        shortLabel: string;
        latitude: number;
        longitude: number;
        category: string | null;
    };

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
    let isSearchingAddress = $state(false);
    let addressSearchMessage = $state('');
    let addressSuggestions = $state<AddressSuggestion[]>([]);
    let locationFeedback = $state('');
    let locationFeedbackTone = $state<LocationFeedbackTone>('info');
    let selectedAddressLabel = $state('');
    let addressSearchAbortController: AbortController | null = null;
    let reverseGeocodeAbortController: AbortController | null = null;
    let addressSearchTimeout: ReturnType<typeof setTimeout> | null = null;

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

    function buildCompactAddressLabel(parts: Array<string | null | undefined>): string {
        return parts
            .map((part) => part?.trim())
            .filter((part): part is string => Boolean(part))
            .filter((part, index, collection) => collection.indexOf(part) === index)
            .join(', ');
    }

    function shortenAddressLabel(value: string): string {
        const trimmedValue = value.trim();

        if (trimmedValue.length <= 255) {
            return trimmedValue;
        }

        return `${trimmedValue.slice(0, 252).trimEnd()}...`;
    }

    function buildAddressLabel(record: Record<string, string | undefined> | undefined, fallback: string): string {
        const compactLabel = buildCompactAddressLabel([
            record?.house_number,
            record?.road,
            record?.village,
            record?.suburb,
            record?.town,
            record?.city,
            record?.county,
            record?.state,
            record?.postcode,
        ]);

        if (compactLabel !== '') {
            return shortenAddressLabel(compactLabel);
        }

        return shortenAddressLabel(fallback);
    }

    function createMarkerIcon(): LeafletDivIcon | null {
        if (!leaflet) {
            return null;
        }

        return leaflet.divIcon({
            className: 'booking-location-marker',
            html: `
                <div class="booking-location-marker__pin" aria-hidden="true">
                    <span class="booking-location-marker__ring"></span>
                    <span class="booking-location-marker__body"></span>
                    <span class="booking-location-marker__dot"></span>
                </div>
            `,
            iconSize: [30, 44],
            iconAnchor: [15, 42],
            popupAnchor: [0, -38],
        });
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

    function syncMarkerPosition(
        latitude: number,
        longitude: number,
        animate = true,
    ): void {
        if (!map || !marker) {
            return;
        }

        marker.setLatLng([latitude, longitude]);

        const nextZoom = Math.max(map.getZoom(), 16);

        if (animate) {
            map.flyTo([latitude, longitude], nextZoom, {
                animate: true,
                duration: 0.75,
            });

            return;
        }

        map.setView([latitude, longitude], nextZoom, {
            animate: false,
        });
    }

    function moveToCoordinates(
        latitude: number,
        longitude: number,
        feedback: string,
        tone: LocationFeedbackTone = 'success',
    ): void {
        setLocationCoordinates(latitude, longitude, feedback, tone);
        syncMarkerPosition(latitude, longitude);
    }

    async function reverseGeocodeCoordinates(
        latitude: number,
        longitude: number,
    ): Promise<void> {
        reverseGeocodeAbortController?.abort();

        const controller = new AbortController();
        reverseGeocodeAbortController = controller;

        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=jsonv2&addressdetails=1&lat=${latitude}&lon=${longitude}&zoom=18&accept-language=id`,
                {
                    headers: {
                        Accept: 'application/json',
                    },
                    signal: controller.signal,
                },
            );

            if (!response.ok) {
                throw new Error(
                    `Reverse geocoding request failed with status ${response.status}`,
                );
            }

            const result = (await response.json()) as {
                display_name?: string;
                name?: string;
                address?: Record<string, string | undefined>;
            };

            const resolvedAddress = buildAddressLabel(
                result.address,
                result.display_name?.trim() || result.name?.trim() || '',
            );

            if (!resolvedAddress) {
                locationFeedback =
                    'Titik lokasi dipindahkan, tetapi alamat belum ditemukan otomatis.';
                locationFeedbackTone = 'info';
                return;
            }

            location.addressText = resolvedAddress;
            selectedAddressLabel = resolvedAddress;
            addressSuggestions = [];
            addressSearchMessage =
                'Alamat otomatis diisi dari titik lokasi yang dipilih.';
            locationFeedback = 'Alamat berhasil diisi otomatis dari peta.';
            locationFeedbackTone = 'success';
        } catch (error) {
            if (error instanceof DOMException && error.name === 'AbortError') {
                return;
            }

            locationFeedback =
                'Titik lokasi tersimpan, tetapi alamat otomatis belum berhasil diambil.';
            locationFeedbackTone = 'error';
        }
    }

    function handleMapClick(latitude: number, longitude: number): void {
        selectedAddressLabel = '';
        addressSuggestions = [];
        addressSearchMessage = '';
        moveToCoordinates(
            latitude,
            longitude,
            'Titik lokasi berhasil dipilih dari peta.',
        );
        void reverseGeocodeCoordinates(latitude, longitude);
    }

    function selectAddressSuggestion(suggestion: AddressSuggestion): void {
        selectedAddressLabel = suggestion.shortLabel;
        addressSuggestions = [];
        addressSearchMessage = '';
        location.addressText = suggestion.shortLabel;
        moveToCoordinates(
            suggestion.latitude,
            suggestion.longitude,
            'Alamat dipilih dari saran alamat dan pin sudah dipindahkan.',
        );
    }

    async function autofillAddressFromInput(): Promise<void> {
        const normalizedQuery = location.addressText.trim();

        if (normalizedQuery.length < 3) {
            return;
        }

        if (normalizedQuery === selectedAddressLabel) {
            return;
        }

        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/search?format=jsonv2&addressdetails=1&limit=1&countrycodes=id&q=${encodeURIComponent(normalizedQuery)}`,
                {
                    headers: {
                        Accept: 'application/json',
                    },
                },
            );

            if (!response.ok) {
                return;
            }

            const results = (await response.json()) as Array<{
                display_name?: string;
                lat?: string;
                lon?: string;
                category?: string;
                address?: Record<string, string | undefined>;
            }>;

            const firstResult = results.at(0);

            if (
                !firstResult?.display_name ||
                !firstResult.lat ||
                !firstResult.lon
            ) {
                return;
            }

            const latitude = Number(firstResult.lat);
            const longitude = Number(firstResult.lon);

            if (
                Number.isNaN(latitude) ||
                Number.isNaN(longitude) ||
                latitude < -90 ||
                latitude > 90 ||
                longitude < -180 ||
                longitude > 180
            ) {
                return;
            }

            const resolvedSuggestion: AddressSuggestion = {
                displayName: firstResult.display_name,
                shortLabel: buildAddressLabel(
                    firstResult.address,
                    firstResult.display_name,
                ),
                latitude,
                longitude,
                category: firstResult.category ?? null,
            };

            selectAddressSuggestion(resolvedSuggestion);
            addressSearchMessage =
                'Alamat dilengkapi otomatis dari teks yang kamu masukkan.';
        } catch {
            // Keep manual input available if geocoding fails.
        }
    }

    async function searchAddressSuggestions(query: string): Promise<void> {
        const normalizedQuery = query.trim();

        if (normalizedQuery.length < 3) {
            addressSuggestions = [];
            addressSearchMessage =
                normalizedQuery.length === 0
                    ? ''
                    : 'Ketik minimal 3 karakter untuk mencari alamat.';
            isSearchingAddress = false;

            return;
        }

        if (normalizedQuery === selectedAddressLabel) {
            addressSearchMessage = 'Alamat sudah dipilih dari saran.';
            addressSuggestions = [];
            isSearchingAddress = false;

            return;
        }

        addressSearchAbortController?.abort();

        const controller = new AbortController();
        addressSearchAbortController = controller;
        isSearchingAddress = true;
        addressSearchMessage = 'Mencari saran alamat...';

        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/search?format=jsonv2&addressdetails=1&limit=5&countrycodes=id&q=${encodeURIComponent(normalizedQuery)}`,
                {
                    headers: {
                        Accept: 'application/json',
                    },
                    signal: controller.signal,
                },
            );

            if (!response.ok) {
                throw new Error(`Geocoding request failed with status ${response.status}`);
            }

            const results = (await response.json()) as Array<{
                display_name?: string;
                lat?: string;
                lon?: string;
                category?: string;
                address?: Record<string, string | undefined>;
            }>;

            addressSuggestions = results
                .map((result) => ({
                    displayName: result.display_name ?? '',
                    shortLabel: buildAddressLabel(result.address, result.display_name ?? ''),
                    latitude: Number(result.lat),
                    longitude: Number(result.lon),
                    category: result.category ?? null,
                }))
                .filter(
                    (result): result is AddressSuggestion =>
                        result.displayName !== '' &&
                        result.shortLabel !== '' &&
                        Number.isFinite(result.latitude) &&
                        Number.isFinite(result.longitude),
                );

            addressSearchMessage = addressSuggestions.length
                ? 'Pilih salah satu saran alamat untuk memindahkan pin.'
                : 'Tidak ada saran alamat yang cocok.';
        } catch (error) {
            if (error instanceof DOMException && error.name === 'AbortError') {
                return;
            }

            addressSuggestions = [];
            addressSearchMessage =
                'Pencarian alamat gagal. Silakan pilih titik di peta atau coba lagi.';
            locationFeedbackTone = 'error';
            locationFeedback = addressSearchMessage;
        } finally {
            if (addressSearchAbortController === controller) {
                isSearchingAddress = false;
            }
        }
    }

    function scheduleAddressSuggestionSearch(query: string): void {
        if (addressSearchTimeout) {
            clearTimeout(addressSearchTimeout);
        }

        addressSearchTimeout = setTimeout(() => {
            void searchAddressSuggestions(query);
        }, 350);
    }

    $effect(() => {
        const query = location.addressText.trim();

        if (addressSearchTimeout) {
            clearTimeout(addressSearchTimeout);
            addressSearchTimeout = null;
        }

        if (query.length === 0) {
            addressSuggestions = [];
            addressSearchMessage = '';
            isSearchingAddress = false;
            return;
        }

        if (query === selectedAddressLabel) {
            addressSuggestions = [];
            addressSearchMessage = 'Alamat sudah dipilih dari saran.';
            isSearchingAddress = false;
            return;
        }

        if (query.length < 3) {
            addressSuggestions = [];
            addressSearchMessage = 'Ketik minimal 3 karakter untuk mencari alamat.';
            isSearchingAddress = false;
            return;
        }

        scheduleAddressSuggestionSearch(query);
    });

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
                void reverseGeocodeCoordinates(latitude, longitude);
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

            if (!mapContainer) {
                return;
            }

            const initialCoordinates = parsedCoordinates ?? defaultCoordinates;

            map = leaflet.map(mapContainer, {
                scrollWheelZoom: false,
                zoomAnimation: true,
                fadeAnimation: true,
                markerZoomAnimation: true,
            }).setView(
                [initialCoordinates.latitude, initialCoordinates.longitude],
                15,
            );

            leaflet
                .tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution:
                        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19,
                    crossOrigin: true,
                })
                .addTo(map);

            marker = leaflet
                .marker([
                    initialCoordinates.latitude,
                    initialCoordinates.longitude,
                ], {
                    draggable: true,
                    icon: createMarkerIcon() ?? undefined,
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

                selectedAddressLabel = '';
                addressSuggestions = [];
                addressSearchMessage = '';
                moveToCoordinates(
                    latLng.lat,
                    latLng.lng,
                    'Titik lokasi disesuaikan dari marker peta.',
                );
                void reverseGeocodeCoordinates(latLng.lat, latLng.lng);
            });

            map.whenReady(() => {
                locationFeedback =
                    'Klik peta, drag marker, atau gunakan lokasi perangkat.';
                locationFeedbackTone = 'info';
            });
        })();

        return () => {
            cancelled = true;
            if (addressSearchTimeout) {
                clearTimeout(addressSearchTimeout);
                addressSearchTimeout = null;
            }
            addressSearchAbortController?.abort();
            reverseGeocodeAbortController?.abort();
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
        map.flyTo(
            [parsedCoordinates.latitude, parsedCoordinates.longitude],
            Math.max(map.getZoom(), 16),
            {
                animate: true,
                duration: 0.75,
            },
        );
    });
</script>

<div class="space-y-6">
    <div class="space-y-2">
        <h3 class="text-lg font-semibold text-foreground">3. Lokasi servis</h3>
        <p class="text-sm leading-6 text-muted-foreground">
            Ketik alamat untuk melihat saran lokasi, pilih titik di peta, atau
            gunakan lokasi perangkat untuk mengisi koordinat dengan lebih cepat.
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
            <div class="space-y-2">
                <Input
                    id="address-text"
                    bind:value={location.addressText}
                    aria-invalid={Boolean(errors.address_text)}
                    autocomplete="off"
                    inputmode="text"
                    placeholder="Ketik alamat lengkap atau nama jalan"
                    onblur={() => void autofillAddressFromInput()}
                    onkeydown={(event) => {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            void autofillAddressFromInput();
                        }
                    }}
                />

                {#if isSearchingAddress || addressSearchMessage}
                    <p class="text-sm leading-6 text-muted-foreground">
                        {addressSearchMessage ||
                            'Ketik minimal 3 karakter untuk mulai mencari alamat.'}
                    </p>
                {/if}

                {#if addressSuggestions.length > 0}
                    <div
                        role="listbox"
                        aria-label="Saran alamat"
                        class="max-h-64 overflow-auto rounded-[1.25rem] border border-border/70 bg-card shadow-sm"
                    >
                        {#each addressSuggestions as suggestion, index (suggestion.displayName)}
                            <button
                                type="button"
                                role="option"
                                aria-selected={selectedAddressLabel === suggestion.shortLabel}
                                class={`flex w-full flex-col gap-1 border-b border-border/60 px-4 py-3 text-left transition last:border-b-0 hover:bg-muted/80 ${
                                    selectedAddressLabel === suggestion.shortLabel
                                        ? 'bg-primary/8'
                                        : ''
                                }`}
                                onclick={() => selectAddressSuggestion(suggestion)}
                            >
                                <span class="text-sm font-medium text-foreground">
                                    {suggestion.displayName}
                                </span>
                                <span class="text-xs uppercase tracking-[0.16em] text-muted-foreground">
                                    {suggestion.category ?? 'Alamat'}
                                    {' '}
                                    •
                                    {' '}
                                    Pilih untuk pindahkan pin
                                </span>
                            </button>
                        {/each}
                    </div>
                {/if}

                <p class="text-xs leading-5 text-muted-foreground">
                    Saran alamat memakai data OpenStreetMap. Pilih salah satu hasil
                    untuk memindahkan pin secara otomatis.
                </p>
            </div>
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

<style>
    :global(.booking-location-marker) {
        background: transparent;
        border: none;
    }

    :global(.booking-location-marker__pin) {
        position: relative;
        display: flex;
        width: 30px;
        height: 42px;
        align-items: center;
        justify-content: center;
        filter: drop-shadow(0 10px 16px rgb(15 23 42 / 0.24));
    }

    :global(.booking-location-marker__ring) {
        position: absolute;
        top: 0;
        width: 30px;
        height: 30px;
        border-radius: 9999px;
        background: rgb(var(--brand-primary-rgb) / 0.16);
        box-shadow: 0 0 0 8px rgb(var(--brand-primary-rgb) / 0.08);
    }

    :global(.booking-location-marker__body) {
        position: absolute;
        top: 0;
        width: 22px;
        height: 22px;
        border-radius: 9999px 9999px 9999px 0;
        background: linear-gradient(
            180deg,
            rgb(var(--brand-primary-rgb)) 0%,
            rgb(var(--brand-accent-rgb)) 100%
        );
        transform: rotate(-45deg);
    }

    :global(.booking-location-marker__dot) {
        position: absolute;
        top: 6px;
        width: 8px;
        height: 8px;
        border-radius: 9999px;
        background: rgb(255 255 255);
        box-shadow: 0 0 0 2px rgb(15 23 42 / 0.08);
    }
</style>
