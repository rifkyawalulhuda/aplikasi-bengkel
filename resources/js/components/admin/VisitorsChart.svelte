<script lang="ts">
    import type { VisitorDailyAnalyticsPoint } from '@/types';

    let {
        points,
    }: {
        points: VisitorDailyAnalyticsPoint[];
    } = $props();

    const maxVisits = $derived(
        Math.max(...points.map((point) => point.totalVisits), 1),
    );
</script>

<div class="space-y-4">
    {#if points.length === 0}
        <p class="text-sm leading-6 text-muted-foreground">
            Belum ada data visitor yang bisa ditampilkan.
        </p>
    {:else}
        <div class="grid gap-3">
            {#each points as point}
                <div class="rounded-xl border border-border/70 px-4 py-3">
                    <div
                        class="flex items-center justify-between gap-4 text-sm"
                    >
                        <span class="font-medium text-foreground"
                            >{point.date}</span
                        >
                        <div
                            class="flex items-center gap-4 text-muted-foreground"
                        >
                            <span>Total {point.totalVisits}</span>
                            <span>Unique {point.uniqueVisitors}</span>
                            <span>Views {point.pageViews}</span>
                        </div>
                    </div>

                    <div class="mt-3 h-2 rounded-full bg-muted">
                        <div
                            class="h-2 rounded-full bg-primary transition-[width]"
                            style={`width: ${Math.max((point.totalVisits / maxVisits) * 100, point.totalVisits > 0 ? 8 : 0)}%`}
                        ></div>
                    </div>
                </div>
            {/each}
        </div>
    {/if}
</div>
