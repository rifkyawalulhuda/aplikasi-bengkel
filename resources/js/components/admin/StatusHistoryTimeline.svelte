<script lang="ts">
    import BookingStatusBadge from '@/components/admin/BookingStatusBadge.svelte';
    import type { AdminBookingStatusHistoryItem } from '@/types';

    let {
        history,
    }: {
        history: AdminBookingStatusHistoryItem[];
    } = $props();
</script>

{#if history.length === 0}
    <p class="text-sm leading-6 text-muted-foreground">
        Belum ada histori status untuk booking ini.
    </p>
{:else}
    <div class="space-y-4">
        {#each history as item (item.id)}
            <div class="rounded-2xl border border-primary/12 bg-white/76 px-4 py-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            {#if item.oldStatusLabel}
                                <span class="text-sm text-muted-foreground">{item.oldStatusLabel}</span>
                                <span class="text-sm text-muted-foreground">-&gt;</span>
                            {/if}
                            <BookingStatusBadge status={item.newStatus} label={item.newStatusLabel} />
                        </div>

                        <div class="text-sm leading-6 text-muted-foreground">
                            <p>{item.changedAt ?? '-'}</p>
                            <p>{item.changedBy ?? 'Sistem'}</p>
                        </div>
                    </div>

                    {#if item.note}
                        <p class="max-w-xl text-sm leading-6 text-foreground">{item.note}</p>
                    {/if}
                </div>
            </div>
        {/each}
    </div>
{/if}
