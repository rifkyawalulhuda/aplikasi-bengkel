<script lang="ts">
    import { Form } from '@inertiajs/svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { updateFooterLocation } from '@/actions/App/Http/Controllers/Admin/BookingSettingController';

    let {
        footerLocation,
    }: {
        footerLocation: {
            address: string;
            latitude: string;
            longitude: string;
        };
    } = $props();

    const formDefinition = $derived(updateFooterLocation.form());

    let addressValue = $state('');
    let latitudeValue = $state('');
    let longitudeValue = $state('');

    $effect(() => {
        addressValue = footerLocation.address;
        latitudeValue = footerLocation.latitude;
        longitudeValue = footerLocation.longitude;
    });
</script>

<Card class="border-border/70 bg-card shadow-sm">
    <CardHeader class="gap-2">
        <CardTitle>Lokasi footer bengkel</CardTitle>
        <p class="text-sm leading-6 text-muted-foreground">
            Alamat dan koordinat di bawah ini dipakai untuk map di footer publik.
            Ubah dari dashboard ini supaya tampilannya ikut berubah di seluruh website.
        </p>
    </CardHeader>

    <CardContent>
        <Form {...formDefinition} class="space-y-4">
            {#snippet children({ errors, processing })}
                <div class="space-y-2">
                    <Label for="footer_address">Alamat footer</Label>
                    <Input
                        id="footer_address"
                        name="footer_address"
                        bind:value={addressValue}
                        placeholder="Jl. Badami Ciherang, Telukjambe Barat, Kab. Karawang"
                    />
                    <InputError message={errors.footer_address} />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="footer_latitude">Latitude</Label>
                        <Input
                            id="footer_latitude"
                            name="footer_latitude"
                            type="number"
                            step="0.000001"
                            bind:value={latitudeValue}
                            placeholder="-6.3025000"
                        />
                        <InputError message={errors.footer_latitude} />
                    </div>

                    <div class="space-y-2">
                        <Label for="footer_longitude">Longitude</Label>
                        <Input
                            id="footer_longitude"
                            name="footer_longitude"
                            type="number"
                            step="0.000001"
                            bind:value={longitudeValue}
                            placeholder="107.3035000"
                        />
                        <InputError message={errors.footer_longitude} />
                    </div>
                </div>

                <p class="text-sm leading-6 text-muted-foreground">
                    Koordinat ini bisa kamu ambil dari titik lokasi yang sudah
                    kamu cek di OpenStreetMap atau dari pin di peta booking.
                </p>

                <div class="flex justify-end">
                    <Button type="submit" disabled={processing}>
                        Simpan lokasi footer
                    </Button>
                </div>
            {/snippet}
        </Form>
    </CardContent>
</Card>
