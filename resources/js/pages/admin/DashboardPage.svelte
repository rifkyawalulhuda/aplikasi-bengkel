<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import DashboardStatCard from '@/components/admin/DashboardStatCard.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import type { DashboardStats, VisitorTrendPoint } from '@/types';

    let {
        stats,
        visitorTrend,
        foundationChecklist,
    }: {
        stats: DashboardStats;
        visitorTrend: VisitorTrendPoint[];
        foundationChecklist: string[];
    } = $props();

    const maxVisits = $derived(Math.max(...visitorTrend.map((point) => point.totalVisits), 1));
    const visitorTrendSummary = $derived(
        visitorTrend.reduce(
            (summary, point) => {
                summary.total += point.totalVisits;
                summary.unique += point.uniqueVisits;

                return summary;
            },
            { total: 0, unique: 0 },
        ),
    );
</script>

<AppHead title="Dashboard Admin" />

<div class="flex flex-col gap-6 text-foreground">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <DashboardStatCard label="Booking hari ini" value={stats.bookingsToday} helper="Siap dihubungkan ke list booking" />
        <DashboardStatCard label="Pending" value={stats.pendingBookings} helper="Monitoring masuk pertama" />
        <DashboardStatCard label="Confirmed" value={stats.confirmedBookings} helper="Booking yang sudah disetujui" />
        <DashboardStatCard label="Completed" value={stats.completedBookings} helper="Siap untuk laporan harian" />
        <DashboardStatCard label="Visitor hari ini" value={stats.visitorsToday} helper="Public traffic only" />
    </div>

    <div class="grid gap-4 xl:grid-cols-[1.2fr_0.8fr]">
        <Card class="border-border/70 bg-card shadow-sm">
            <CardHeader>
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-1">
                        <CardTitle>Trend visitor 7 hari</CardTitle>
                        <p class="text-sm text-muted-foreground">
                            Ringkasan traffic harian untuk kebutuhan monitoring operasional.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <Badge variant="outline">Total {visitorTrendSummary.total}</Badge>
                        <Badge variant="secondary">Unique {visitorTrendSummary.unique}</Badge>
                    </div>
                </div>
            </CardHeader>
            <CardContent class="flex flex-col gap-3">
                {#if visitorTrend.length > 0}
                    <div class="grid gap-3">
                        {#each visitorTrend as point}
                            <div class="rounded-xl border border-border/70 px-4 py-3">
                                <div class="flex items-center justify-between gap-4 text-sm">
                                    <span class="font-medium text-foreground">{point.date}</span>
                                    <div class="flex items-center gap-4 text-muted-foreground">
                                        <span>Total {point.totalVisits}</span>
                                        <span>Unique {point.uniqueVisits}</span>
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
                {:else}
                    <p class="text-sm leading-6 text-muted-foreground">
                        Belum ada data visitor. Middleware tracking bisa disambungkan pada fase visitor analytics.
                    </p>
                {/if}
            </CardContent>
        </Card>

        <Card class="border-border/70 bg-card shadow-sm">
            <CardHeader>
                <CardTitle>Yang sudah siap dipakai</CardTitle>
            </CardHeader>
            <CardContent>
                <ul class="flex flex-col gap-3 text-sm leading-6 text-muted-foreground">
                    {#each foundationChecklist as item}
                        <li>{item}</li>
                    {/each}
                </ul>
            </CardContent>
        </Card>
    </div>
</div>
