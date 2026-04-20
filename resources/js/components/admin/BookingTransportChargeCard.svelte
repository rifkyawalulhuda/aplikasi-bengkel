<script lang="ts">
    import { Form } from '@inertiajs/svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { updateTransportCharge } from '@/actions/App/Http/Controllers/Admin/BookingSettingController';

    let {
        transportChargeSettings,
    }: {
        transportChargeSettings: {
            freeRadiusKm: number;
            feePerKm: number;
        };
    } = $props();

    const formDefinition = $derived(updateTransportCharge.form());

    let freeRadiusValue = $state('');
    let feePerKmValue = $state('');

    $effect(() => {
        freeRadiusValue = String(transportChargeSettings.freeRadiusKm);
        feePerKmValue = String(transportChargeSettings.feePerKm);
    });
</script>

<Card class="border-border/70 bg-card shadow-sm">
    <CardHeader class="gap-2">
        <CardTitle>Transport booking di luar radius</CardTitle>
        <p class="text-sm leading-6 text-muted-foreground">
            Jika customer memilih titik di luar radius gratis, sistem akan menambahkan
            biaya transport per km pada preview harga dan total booking final.
        </p>
    </CardHeader>

    <CardContent>
        <Form {...formDefinition} class="space-y-4">
            {#snippet children({ errors, processing })}
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="transport_free_radius_km">Radius gratis (km)</Label>
                        <Input
                            id="transport_free_radius_km"
                            name="transport_free_radius_km"
                            type="number"
                            min="0"
                            step="0.1"
                            bind:value={freeRadiusValue}
                            placeholder="10"
                        />
                        <InputError message={errors.transport_free_radius_km} />
                    </div>

                    <div class="space-y-2">
                        <Label for="transport_fee_per_km">Biaya transport / km</Label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-muted-foreground">
                                Rp
                            </span>
                            <Input
                                id="transport_fee_per_km"
                                name="transport_fee_per_km"
                                type="number"
                                min="0"
                                step="1000"
                                class="pl-10"
                                bind:value={feePerKmValue}
                                placeholder="5000"
                            />
                        </div>
                        <InputError message={errors.transport_fee_per_km} />
                    </div>
                </div>

                <p class="text-sm leading-6 text-muted-foreground">
                    Contoh: radius gratis 10 km dan biaya transport Rp 5.000 per km
                    untuk jarak di luar radius tersebut.
                </p>

                <div class="flex justify-end">
                    <Button type="submit" disabled={processing}>
                        Simpan transport booking
                    </Button>
                </div>
            {/snippet}
        </Form>
    </CardContent>
</Card>
