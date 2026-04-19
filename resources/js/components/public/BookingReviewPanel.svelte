<script lang="ts">
    import { Badge } from '@/components/ui/badge';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import type {
        BookingCustomerForm,
        BookingLocationForm,
        BookingMotorcycleForm,
        BookingPackageType,
        BookingScheduleForm,
        CustomServiceItemSummary,
        ServicePackageSummary,
    } from '@/types';

    let {
        packageType,
        selectedPackage,
        selectedCustomItems,
        customer,
        motorcycle,
        location,
        schedule,
        subtotal,
        serviceFee,
        total,
    }: {
        packageType: BookingPackageType;
        selectedPackage: ServicePackageSummary | null;
        selectedCustomItems: Array<CustomServiceItemSummary & { qty: number; subtotal: number }>;
        customer: BookingCustomerForm;
        motorcycle: BookingMotorcycleForm;
        location: BookingLocationForm;
        schedule: BookingScheduleForm;
        subtotal: number;
        serviceFee: number;
        total: number;
    } = $props();
</script>

<Card class="border-border/70 bg-card/95 shadow-sm">
    <CardHeader class="gap-3">
        <div class="flex items-center justify-between gap-3">
            <CardTitle class="text-lg">Review booking</CardTitle>
            <Badge variant="secondary" class="rounded-full px-3 py-1 text-xs">
                Siap cek ulang
            </Badge>
        </div>
        <p class="text-sm leading-6 text-muted-foreground">
            Pastikan semua data sudah sesuai.
        </p>
    </CardHeader>

    <CardContent class="space-y-6 text-sm">
        <div class="space-y-2">
            <p class="font-semibold text-foreground">Paket</p>
            {#if packageType === 'fixed_package'}
                <p class="text-muted-foreground">
                    {selectedPackage ? `${selectedPackage.name} - Rp ${selectedPackage.price.toLocaleString('id-ID')}` : 'Belum memilih paket tetap'}
                </p>
            {:else}
                <div class="space-y-2 text-muted-foreground">
                    <p>Paket custom</p>
                    {#if selectedCustomItems.length > 0}
                        <ul class="space-y-1">
                            {#each selectedCustomItems as item (item.id)}
                                <li>{item.name} ({item.qty}x)</li>
                            {/each}
                        </ul>
                    {:else}
                        <p>Belum ada item custom yang dipilih.</p>
                    {/if}
                </div>
            {/if}
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div class="space-y-2">
                <p class="font-semibold text-foreground">Pelanggan</p>
                <p class="text-muted-foreground">{customer.name || '-'}</p>
                <p class="text-muted-foreground">{customer.phone || '-'}</p>
            </div>

            <div class="space-y-2">
                <p class="font-semibold text-foreground">Motor</p>
                <p class="text-muted-foreground">{motorcycle.type || '-'}</p>
                <p class="text-muted-foreground">
                    {motorcycle.brand || '-'} {motorcycle.model || ''}
                </p>
                <p class="text-muted-foreground">
                    Tahun: {motorcycle.year || '-'} | Plat: {motorcycle.plateNumber || '-'}
                </p>
            </div>

            <div class="space-y-2 md:col-span-2">
                <p class="font-semibold text-foreground">Lokasi servis</p>
                <p class="text-muted-foreground">{location.addressText || '-'}</p>
                <p class="text-muted-foreground">Patokan: {location.houseLandmark || '-'}</p>
                <p class="text-muted-foreground">
                    Koordinat: {location.latitude || '-'}, {location.longitude || '-'}
                </p>
            </div>

            <div class="space-y-2">
                <p class="font-semibold text-foreground">Jadwal</p>
                <p class="text-muted-foreground">{schedule.serviceDate || '-'}</p>
                <p class="text-muted-foreground">{schedule.serviceTime || '-'}</p>
            </div>

            <div class="space-y-2">
                <p class="font-semibold text-foreground">Catatan</p>
                <p class="text-muted-foreground">{schedule.notes || '-'}</p>
            </div>
        </div>

        <div class="rounded-[1.5rem] border border-border/70 bg-muted p-4">
            <div class="flex items-center justify-between gap-4">
                <span class="text-muted-foreground">Subtotal</span>
                <strong>Rp {subtotal.toLocaleString('id-ID')}</strong>
            </div>
            <div class="mt-2 flex items-center justify-between gap-4">
                <span class="text-muted-foreground">Service fee</span>
                <strong>Rp {serviceFee.toLocaleString('id-ID')}</strong>
            </div>
            <div class="mt-3 flex items-center justify-between gap-4 border-t border-border/70 pt-3 text-base">
                <span class="font-semibold text-foreground">Total preview</span>
                <strong class="text-primary">Rp {total.toLocaleString('id-ID')}</strong>
            </div>
        </div>
    </CardContent>
</Card>
