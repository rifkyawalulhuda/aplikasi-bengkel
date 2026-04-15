<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import DashboardStatCard from '@/components/admin/DashboardStatCard.svelte';
    import VisitorsChart from '@/components/admin/VisitorsChart.svelte';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import type {
        VisitorAnalyticsSummary,
        VisitorDailyAnalyticsPoint,
        VisitorTopPath,
    } from '@/types';

    let {
        summary,
        dailyAnalytics,
        topPaths,
    }: {
        summary: VisitorAnalyticsSummary;
        dailyAnalytics: VisitorDailyAnalyticsPoint[];
        topPaths: VisitorTopPath[];
    } = $props();
</script>

<AppHead title="Visitor Analytics" />

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <h2 class="text-2xl font-semibold tracking-tight text-[#D12052]">
            Analytics visitor sederhana
        </h2>
        <p class="text-sm leading-6 text-[#D12052]">
            Ringkasan traffic publik untuk MVP. Data ini fokus ke volume harian,
            unique visitor, page view, dan path yang paling sering dibuka.
        </p>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <DashboardStatCard
            label="Total kunjungan hari ini"
            value={summary.todayTotalVisits}
            helper="GET publik yang berhasil dilacak"
        />
        <DashboardStatCard
            label="Unique visitor hari ini"
            value={summary.todayUniqueVisitors}
            helper="Berdasarkan sesi harian sederhana"
        />
        <DashboardStatCard
            label="Total page views"
            value={summary.pageViews}
            helper="Akumulasi semua log visitor"
        />
        <DashboardStatCard
            label="Tracked paths"
            value={summary.trackedPaths}
            helper="Jumlah path publik yang pernah dikunjungi"
        />
    </div>

    <div class="grid gap-4 xl:grid-cols-[1.15fr_0.85fr]">
        <Card class="border-primary/16 bg-white/88 shadow-[0_24px_54px_-40px_rgb(var(--brand-primary-rgb)/0.36)] backdrop-blur-sm">
            <CardHeader>
                <CardTitle>Trend harian 14 hari</CardTitle>
            </CardHeader>
            <CardContent>
                <VisitorsChart points={dailyAnalytics} />
            </CardContent>
        </Card>

        <Card class="border-primary/16 bg-white/88 shadow-[0_24px_54px_-40px_rgb(var(--brand-primary-rgb)/0.36)] backdrop-blur-sm">
            <CardHeader>
                <CardTitle>Path paling sering dibuka</CardTitle>
            </CardHeader>
            <CardContent>
                {#if topPaths.length === 0}
                    <p class="text-sm leading-6 text-muted-foreground">
                        Belum ada data path yang tercatat.
                    </p>
                {:else}
                    <div class="space-y-3">
                        {#each topPaths as path, index}
                            <div
                                class="rounded-xl border border-border/70 px-4 py-3"
                            >
                                <div
                                    class="flex items-center justify-between gap-4"
                                >
                                    <div class="space-y-1">
                                        <p
                                            class="text-xs uppercase tracking-[0.16em] text-muted-foreground"
                                        >
                                            Rank {index + 1}
                                        </p>
                                        <p
                                            class="break-all text-sm font-medium text-foreground"
                                        >
                                            {path.path}
                                        </p>
                                    </div>
                                    <div
                                        class="text-right text-sm text-muted-foreground"
                                    >
                                        <p>{path.totalViews} views</p>
                                        <p>{path.uniqueVisitors} unique</p>
                                    </div>
                                </div>
                            </div>
                        {/each}
                    </div>
                {/if}
            </CardContent>
        </Card>
    </div>
</div>
