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

    let expandedItemId = $state<number | null>(null);

    function qtyFor(itemId: number): number {
        return selectedItems.find((entry) => entry.id === itemId)?.qty ?? 0;
    }

    $effect(() => {
        if (!visible || customItems.length === 0) {
            expandedItemId = null;
            return;
        }

        const selectedItem = selectedItems.find((entry) => entry.qty > 0);

        if (selectedItem) {
            expandedItemId = selectedItem.id;
        }
    });

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

    function toggleItemPanel(itemId: number) {
        expandedItemId = expandedItemId === itemId ? null : itemId;
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
            <div class="space-y-3 lg:hidden">
                {#each customItems as item (item.id)}
                    {@const quantity = qtyFor(item.id)}
                    {@const isExpanded = expandedItemId === item.id}

                    <article
                        class={`overflow-hidden rounded-[1.5rem] border transition ${
                            quantity > 0
                                ? 'border-primary/25 bg-primary/8 shadow-sm'
                                : 'border-border/70 bg-card'
                        }`}
                    >
                        <button
                            type="button"
                            aria-expanded={isExpanded}
                            aria-controls={`mobile-custom-item-panel-${item.id}`}
                            aria-label={`Buka detail item ${item.name}`}
                            class="flex w-full items-start justify-between gap-4 px-4 py-4 text-left"
                            onclick={() => toggleItemPanel(item.id)}
                        >
                            <div class="space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-base font-semibold text-foreground">
                                        {item.name}
                                    </p>
                                    <Badge
                                        variant="secondary"
                                        class="rounded-full px-2.5 py-1 text-[11px]"
                                    >
                                        {item.category}
                                    </Badge>
                                    {#if quantity > 0}
                                        <Badge
                                            variant="secondary"
                                            class="rounded-full px-2.5 py-1 text-[11px]"
                                        >
                                            Qty {quantity}
                                        </Badge>
                                    {/if}
                                </div>

                                <p class="text-sm text-primary">
                                    {formatCurrency(item.price)}
                                    {#if item.unitLabel}
                                        / {item.unitLabel}
                                    {/if}
                                </p>

                                {#if item.description}
                                    <p
                                        class="max-h-12 overflow-hidden text-sm leading-6 text-muted-foreground"
                                    >
                                        {item.description}
                                    </p>
                                {/if}
                            </div>

                            <span
                                class={`mt-1 inline-flex size-9 shrink-0 items-center justify-center rounded-full border transition duration-200 ${
                                    isExpanded
                                        ? 'rotate-180 border-primary/25 bg-primary text-primary-foreground'
                                        : 'border-border/70 bg-muted text-muted-foreground'
                                }`}
                                aria-hidden="true"
                            >
                                ▾
                            </span>
                        </button>

                        <div
                            id={`mobile-custom-item-panel-${item.id}`}
                            class={`grid overflow-hidden transition-all duration-300 ease-out ${
                                isExpanded
                                    ? 'grid-rows-[1fr] opacity-100'
                                    : 'grid-rows-[0fr] opacity-0'
                            }`}
                        >
                            <div class="min-h-0 px-4 pb-4">
                                <div class="space-y-4 rounded-[1.25rem] border border-border/60 bg-background/80 p-4">
                                    <div class="flex items-center gap-2 self-start">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            aria-label={`Kurangi jumlah ${item.name}`}
                                            disabled={quantity === 0}
                                            onclick={() =>
                                                updateQty(item.id, quantity - 1)}
                                        >
                                            -
                                        </Button>
                                        <span
                                            aria-live="polite"
                                            class="flex h-9 min-w-10 items-center justify-center rounded-md border border-input bg-background px-3 text-sm font-medium"
                                        >
                                            {quantity}
                                        </span>
                                        <Button
                                            type="button"
                                            size="sm"
                                            aria-label={`Tambah jumlah ${item.name}`}
                                            onclick={() =>
                                                updateQty(item.id, quantity + 1)}
                                        >
                                            +
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                {/each}
            </div>

            <div class="hidden gap-3 lg:grid">
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
