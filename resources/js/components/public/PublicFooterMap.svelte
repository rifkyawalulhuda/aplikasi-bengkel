<script lang="ts">
    let {
        address,
        latitude,
        longitude,
        label = 'Lokasi bengkel',
    }: {
        address: string;
        latitude: string;
        longitude: string;
        label?: string;
    } = $props();

    const parsedCoordinates = $derived.by(() => {
        const parsedLatitude = Number(latitude);
        const parsedLongitude = Number(longitude);

        if (
            Number.isNaN(parsedLatitude) ||
            Number.isNaN(parsedLongitude) ||
            parsedLatitude < -90 ||
            parsedLatitude > 90 ||
            parsedLongitude < -180 ||
            parsedLongitude > 180
        ) {
            return null;
        }

        return {
            latitude: parsedLatitude,
            longitude: parsedLongitude,
        };
    });

    const mapsUrl = $derived.by(() => {
        if (!parsedCoordinates) {
            return '';
        }

        return `https://www.openstreetmap.org/?mlat=${parsedCoordinates.latitude}&mlon=${parsedCoordinates.longitude}#map=17/${parsedCoordinates.latitude}/${parsedCoordinates.longitude}`;
    });

    const embedUrl = $derived.by(() => {
        if (!parsedCoordinates) {
            return '';
        }

        const latitudeDelta = 0.0045;
        const longitudeDelta = 0.0045;

        const minLatitude = parsedCoordinates.latitude - latitudeDelta;
        const maxLatitude = parsedCoordinates.latitude + latitudeDelta;
        const minLongitude = parsedCoordinates.longitude - longitudeDelta;
        const maxLongitude = parsedCoordinates.longitude + longitudeDelta;

        return `https://www.openstreetmap.org/export/embed.html?bbox=${minLongitude.toFixed(6)}%2C${minLatitude.toFixed(6)}%2C${maxLongitude.toFixed(6)}%2C${maxLatitude.toFixed(6)}&layer=mapnik&marker=${parsedCoordinates.latitude.toFixed(6)}%2C${parsedCoordinates.longitude.toFixed(6)}`;
    });
</script>

<div class="space-y-3">
    <div class="overflow-hidden rounded-[1.25rem] border border-white/20 bg-white/8 shadow-sm">
        {#if parsedCoordinates}
            <iframe
                title={`${label} ${address}`.trim()}
                src={embedUrl}
                class="h-52 w-full border-0"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        {:else}
            <div class="flex h-52 items-center justify-center px-4 text-center text-sm leading-6 text-white/84">
                Lokasi bengkel belum diatur dari dashboard admin.
            </div>
        {/if}
    </div>

    {#if mapsUrl}
        <a
            href={mapsUrl}
            target="_blank"
            rel="noreferrer"
            class="inline-flex text-sm font-medium text-white transition-colors hover:text-accent"
        >
            Buka di OpenStreetMap
        </a>
    {/if}
</div>
