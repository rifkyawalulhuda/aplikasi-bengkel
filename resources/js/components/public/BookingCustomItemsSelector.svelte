<script lang="ts">
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { formatCurrency } from '@/lib/utils';
    import type {
        BookingCustomItemSelection,
        CustomServiceItemSummary,
    } from '@/types';

    let {
        selectedItems = $bindable<BookingCustomItemSelection[]>([]),
        customItems,
        visible = true,
        onSelectionChange = () => {},
        errors = {},
    }: {
        selectedItems?: BookingCustomItemSelection[];
        customItems: CustomServiceItemSummary[];
        visible?: boolean;
        onSelectionChange?: () => void;
        errors?: Record<string, string>;
    } = $props();

    function qtyFor(itemId: number): number {
        return selectedItems.find((entry) => entry.id === itemId)?.qty ?? 0;
    }

    function updateQty(itemId: number, nextQty: number) {
        if (nextQty <= 0) {
            selectedItems = selectedItems.filter(
                (entry) => entry.id !== itemId,
            );
            onSelectionChange();

            return;
        }

        const existing = selectedItems.find((entry) => entry.id === itemId);

        if (existing) {
            selectedItems = selectedItems.map((entry) =>
                entry.id === itemId ? { ...entry, qty: nextQty } : entry,
            );
            onSelectionChange();

            return;
        }

        selectedItems = [...selectedItems, { id: itemId, qty: nextQty }];
        onSelectionChange();
    }
</script>

{#if visible}
    <div class="space-y-5">
        <div class="space-y-2">
            <h3 class="text-lg font-semibold text-foreground">
                Tambahkan item custom
            </h3>
            <p class="text-sm leading-6 text-muted-foreground">
                Pilih item servis yang dibutuhkan. Subtotal akan berubah
                otomatis di ringkasan harga.
            </p>
        </div>

        {#if customItems.length === 0}
            <div
                class="rounded-[1.5rem] border border-dashed border-border/70 bg-background/80 px-4 py-5 text-sm leading-6 text-muted-foreground"
            >
                Belum ada item custom aktif yang tersedia saat ini. Jika perlu,
                pilih paket tetap atau hubungi admin untuk bantuan.
            </div>
        {:else}
            <div class="grid gap-3">
                {#each customItems as item (item.id)}
                    <div
                        class="rounded-[1.5rem] border border-border/70 bg-card p-4 shadow-sm"
                    >
                        <div
                            class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
                        >
                            <div class="space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p
                                        class="text-sm font-semibold text-foreground"
                                    >
                                        {item.name}
                                    </p>
                                    <Badge
                                        variant="secondary"
                                        class="rounded-full px-2.5 py-1 text-[11px]"
                                    >
                                        {item.category}
                                    </Badge>
                                </div>
                                <p class="text-sm text-muted-foreground">
                                    {formatCurrency(item.price)}
                                    {#if item.unitLabel}
                                        / {item.unitLabel}
                                    {/if}
                                </p>
                                {#if item.description}
                                    <p
                                        class="max-w-xl text-sm leading-6 text-muted-foreground"
                                    >
                                        {item.description}
                                    </p>
                                {/if}
                            </div>

                            <div class="flex items-center gap-2 self-start">
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    aria-label={`Kurangi jumlah ${item.name}`}
                                    disabled={qtyFor(item.id) === 0}
                                    onclick={() =>
                                        updateQty(item.id, qtyFor(item.id) - 1)}
                                >
                                    -
                                </Button>
                                <span
                                    aria-live="polite"
                                    class="flex h-9 min-w-10 items-center justify-center rounded-md border border-input bg-background px-3 text-sm font-medium"
                                >
                                    {qtyFor(item.id)}
                                </span>
                                <Button
                                    type="button"
                                    size="sm"
                                    aria-label={`Tambah jumlah ${item.name}`}
                                    onclick={() =>
                                        updateQty(item.id, qtyFor(item.id) + 1)}
                                >
                                    +
                                </Button>
                            </div>
                        </div>
                    </div>
                {/each}
            </div>
        {/if}

        {#if errors.custom_items}
            <p class="text-sm font-medium text-destructive">
                {errors.custom_items}
            </p>
        {/if}
    </div>
{/if}
