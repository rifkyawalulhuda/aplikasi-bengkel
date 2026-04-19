<script lang="ts">
    import ChevronDown from 'lucide-svelte/icons/chevron-down';
    import ChevronUp from 'lucide-svelte/icons/chevron-up';
    import ReceiptText from 'lucide-svelte/icons/receipt-text';
    import { Badge } from '@/components/ui/badge';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import type {
        BookingPackageType,
        CustomServiceItemSummary,
        ServicePackageSummary,
    } from '@/types';

    let {
        packageType,
        selectedPackage,
        selectedCustomItems,
        subtotal,
        serviceFee,
        total,
    }: {
        packageType: BookingPackageType;
        selectedPackage: ServicePackageSummary | null;
        selectedCustomItems: Array<CustomServiceItemSummary & { qty: number; subtotal: number }>;
        subtotal: number;
        serviceFee: number;
        total: number;
    } = $props();

    let isMobileSummaryOpen = $state(false);

    const mobileSummaryLabel = $derived.by(() => {
        if (packageType === 'fixed_package') {
            return selectedPackage?.name ?? 'Pilih paket untuk melihat harga';
        }

        if (selectedCustomItems.length === 0) {
            return 'Belum ada item custom dipilih';
        }

        return `${selectedCustomItems.length} item custom dipilih`;
    });

    const mobileSummaryItems = $derived(selectedCustomItems.slice(0, 3));
    const remainingItemsCount = $derived(
        Math.max(selectedCustomItems.length - mobileSummaryItems.length, 0),
    );
</script>

<div class="xl:hidden">
    {#if isMobileSummaryOpen}
        <button
            type="button"
            class="fixed inset-0 z-30 bg-slate-950/24 backdrop-blur-[2px]"
            aria-label="Tutup detail preview harga"
            onclick={() => {
                isMobileSummaryOpen = false;
            }}
        ></button>
    {/if}

    <div class="pointer-events-none fixed inset-x-0 bottom-0 z-40 px-4 pb-4">
        <div
            class="pointer-events-auto mx-auto max-w-xl overflow-hidden rounded-[1.6rem] border border-slate-900/10 bg-slate-950/94 text-white shadow-[0_-12px_40px_-18px_rgba(15,23,42,0.65)]"
        >
            <button
                type="button"
                class="flex w-full items-center justify-between gap-4 px-4 py-4 text-left"
                aria-expanded={isMobileSummaryOpen}
                aria-controls="mobile-price-summary-panel"
                onclick={() => {
                    isMobileSummaryOpen = !isMobileSummaryOpen;
                }}
            >
                <div class="min-w-0 space-y-1">
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-accent"
                        >
                            <ReceiptText class="size-4" />
                        </span>
                        <p class="text-sm font-semibold">Preview harga</p>
                    </div>
                    <p class="truncate text-xs text-white/68">
                        {mobileSummaryLabel}
                    </p>
                </div>

                <div class="shrink-0 text-right">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-white/52">
                        Total live
                    </p>
                    <div class="mt-1 flex items-center justify-end gap-2">
                        <strong class="text-base text-accent">
                            Rp {total.toLocaleString('id-ID')}
                        </strong>
                        {#if isMobileSummaryOpen}
                            <ChevronUp class="size-4 text-white/66" />
                        {:else}
                            <ChevronDown class="size-4 text-white/66" />
                        {/if}
                    </div>
                </div>
            </button>

            {#if isMobileSummaryOpen}
                <div
                    id="mobile-price-summary-panel"
                    class="max-h-[60vh] overflow-y-auto border-t border-white/10 px-4 pb-4 pt-3"
                >
                    <div
                        class="rounded-[1.1rem] bg-white/6 px-3 py-3 text-xs leading-5 text-white/72"
                    >
                        Harga berubah langsung saat paket atau item Anda diedit. Backend tetap menjadi sumber kebenaran final.
                    </div>

                    {#if packageType === 'fixed_package'}
                        <div class="mt-3 rounded-[1.2rem] border border-white/10 bg-white/7 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.16em] text-white/52">
                                        Paket tetap
                                    </p>
                                    <p class="mt-2 font-semibold">
                                        {selectedPackage?.name ?? 'Belum ada paket dipilih'}
                                    </p>
                                </div>
                                <strong class="text-accent">
                                    Rp {(selectedPackage?.price ?? 0).toLocaleString('id-ID')}
                                </strong>
                            </div>
                        </div>
                    {:else}
                        <div class="mt-3 space-y-2">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-xs uppercase tracking-[0.16em] text-white/52">
                                    Item custom
                                </p>
                                <p class="text-xs text-white/64">
                                    {selectedCustomItems.length} item
                                </p>
                            </div>

                            {#if selectedCustomItems.length > 0}
                                {#each mobileSummaryItems as item (item.id)}
                                    <div
                                        class="flex items-start justify-between gap-3 rounded-[1.15rem] border border-white/10 bg-white/[0.07] px-3 py-3"
                                    >
                                        <div class="min-w-0">
                                            <p class="truncate font-medium">
                                                {item.name}
                                            </p>
                                            <p class="text-xs text-white/62">
                                                {item.qty}x pilihan
                                            </p>
                                        </div>
                                        <strong class="shrink-0 text-sm">
                                            Rp {item.subtotal.toLocaleString('id-ID')}
                                        </strong>
                                    </div>
                                {/each}

                                {#if remainingItemsCount > 0}
                                    <div class="px-1 text-xs text-white/62">
                                        +{remainingItemsCount} item lain masih terpilih
                                    </div>
                                {/if}
                            {:else}
                                <div
                                    class="rounded-[1.15rem] border border-dashed border-white/12 bg-white/[0.04] px-3 py-4 text-sm text-white/62"
                                >
                                    Belum ada item custom yang dipilih.
                                </div>
                            {/if}
                        </div>
                    {/if}

                    <div class="mt-3 space-y-3 rounded-[1.3rem] bg-white px-4 py-4 text-sm text-slate-900">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-slate-500">Subtotal</span>
                            <strong>Rp {subtotal.toLocaleString('id-ID')}</strong>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-slate-500">Service fee</span>
                            <strong>Rp {serviceFee.toLocaleString('id-ID')}</strong>
                        </div>
                        <div
                            class="flex items-center justify-between gap-4 border-t border-slate-200 pt-3 text-base"
                        >
                            <span class="font-semibold">Total preview</span>
                            <strong class="text-primary">
                                Rp {total.toLocaleString('id-ID')}
                            </strong>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>
</div>

<Card class="hidden border-border/70 bg-card/95 shadow-sm xl:block">
    <CardHeader class="gap-3">
        <div class="flex items-center justify-between gap-3">
            <CardTitle class="text-lg">Preview harga</CardTitle>
            <Badge variant="secondary" class="rounded-full px-3 py-1 text-xs">
                Frontend preview
            </Badge>
        </div>
    </CardHeader>

    <CardContent class="space-y-4 text-sm">
        {#if packageType === 'fixed_package'}
            <div class="rounded-2xl border border-primary/12 bg-primary/7 p-4">
                <p class="font-medium text-foreground">Paket tetap</p>
                {#if selectedPackage}
                    <p class="mt-1 text-muted-foreground">{selectedPackage.name}</p>
                    <p class="mt-2 font-semibold text-primary">
                        Rp {selectedPackage.price.toLocaleString('id-ID')}
                    </p>
                {:else}
                    <p class="mt-2 text-muted-foreground">Belum ada paket yang dipilih.</p>
                {/if}
            </div>
        {:else}
            <div class="space-y-2">
                <p class="font-medium text-foreground">Item custom terpilih</p>
                {#if selectedCustomItems.length > 0}
                    {#each selectedCustomItems as item (item.id)}
                        <div class="flex items-start justify-between gap-3 rounded-2xl border border-border/60 bg-muted px-3 py-3">
                            <div>
                                <p class="font-medium text-foreground">{item.name}</p>
                                <p class="text-muted-foreground">{item.qty}x pilihan</p>
                            </div>
                            <p class="font-semibold text-foreground">
                                Rp {item.subtotal.toLocaleString('id-ID')}
                            </p>
                        </div>
                    {/each}
                {:else}
                    <div class="rounded-2xl border border-border/60 bg-muted p-4 text-muted-foreground">
                        Belum ada item custom yang dipilih.
                    </div>
                {/if}
            </div>
        {/if}

        <div class="space-y-3 rounded-[1.5rem] border border-border/70 bg-background/90 p-4">
            <div class="flex items-center justify-between gap-4">
                <span class="text-muted-foreground">Subtotal</span>
                <strong>Rp {subtotal.toLocaleString('id-ID')}</strong>
            </div>
            <div class="flex items-center justify-between gap-4">
                <span class="text-muted-foreground">Service fee</span>
                <strong>Rp {serviceFee.toLocaleString('id-ID')}</strong>
            </div>
            <div class="flex items-center justify-between gap-4 border-t border-border/70 pt-3 text-base">
                <span class="font-semibold text-foreground">Total preview</span>
                <strong class="text-primary">Rp {total.toLocaleString('id-ID')}</strong>
            </div>
        </div>
    </CardContent>
</Card>
