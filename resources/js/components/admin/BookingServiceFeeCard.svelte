<script lang="ts">
    import { Form } from '@inertiajs/svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { updateServiceFee } from '@/actions/App/Http/Controllers/Admin/BookingSettingController';

    let { serviceFee = 0 }: { serviceFee: number } = $props();

    const formDefinition = $derived(updateServiceFee.form());

    let serviceFeeValue = $state('');

    $effect(() => {
        serviceFeeValue = String(serviceFee);
    });
</script>

<Card class="border-primary/16 bg-white/88 shadow-[0_24px_54px_-40px_rgb(var(--brand-primary-rgb)/0.36)] backdrop-blur-sm">
    <CardHeader class="gap-2">
        <CardTitle>Biaya layanan booking</CardTitle>
        <p class="text-sm leading-6 text-muted-foreground">
            Angka ini dipakai di preview harga publik dan total booking final.
            Ubah sekali, lalu akan berlaku untuk booking baru.
        </p>
    </CardHeader>

    <CardContent>
        <Form {...formDefinition} class="space-y-4">
            {#snippet children({ errors, processing })}
                <div class="grid gap-2 sm:max-w-sm">
                    <Label for="service_fee">Nominal service fee</Label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-muted-foreground">
                            Rp
                        </span>
                        <Input
                            id="service_fee"
                            name="service_fee"
                            type="number"
                            min="0"
                            step="1000"
                            class="pl-10"
                            value={serviceFeeValue}
                            oninput={(event) =>
                                (serviceFeeValue = event.currentTarget.value)}
                        />
                    </div>
                    <InputError message={errors.service_fee} />
                </div>

                <p class="text-sm leading-6 text-muted-foreground">
                    Gunakan angka tanpa titik. Contoh: 10000.
                </p>

                <div class="flex justify-end">
                    <Button type="submit" disabled={processing}>
                        Simpan service fee
                    </Button>
                </div>
            {/snippet}
        </Form>
    </CardContent>
</Card>
