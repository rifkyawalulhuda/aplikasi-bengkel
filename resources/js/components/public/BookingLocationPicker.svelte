<script lang="ts">
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import type { BookingLocationForm } from '@/types';

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

    const mapsUrl = $derived(
        location.latitude && location.longitude
            ? `https://www.google.com/maps?q=${location.latitude},${location.longitude}`
            : '',
    );
</script>

<div class="space-y-6">
    <div class="space-y-2">
        <h3 class="text-lg font-semibold text-foreground">3. Lokasi servis</h3>
        <p class="text-sm leading-6 text-muted-foreground">
            Isi alamat lengkap, patokan rumah, dan koordinat agar admin lebih
            cepat memvalidasi area layanan dan mengarahkan mekanik.
        </p>
    </div>

    <div
        class="rounded-[1.5rem] border border-dashed border-border/70 bg-muted p-4"
    >
        <p class="text-sm font-medium text-foreground">Preview titik lokasi</p>
        <p class="mt-2 text-sm leading-6 text-muted-foreground">
            Setelah latitude dan longitude terisi, tombol preview titik lokasi
            akan muncul untuk pengecekan cepat.
        </p>

        {#if mapsUrl}
            <a
                href={mapsUrl}
                target="_blank"
                rel="noreferrer"
                class="mt-3 inline-flex text-sm font-medium text-primary underline-offset-4 hover:underline"
            >
                Buka preview titik di Google Maps
            </a>
        {/if}
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

        <div class="grid gap-4 sm:grid-cols-2">
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
    </div>
</div>
