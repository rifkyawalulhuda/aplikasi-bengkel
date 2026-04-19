<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

    let {
        label,
        value,
        helper = '',
    }: {
        label: string;
        value: number;
        helper?: string;
    } = $props();

    const isDashboardRoute = $derived((page.url ?? '') === '/dashboard');
</script>

<Card class={isDashboardRoute
    ? 'border-border/70 bg-card shadow-sm'
    : 'border-primary/16 bg-white/88 shadow-[0_24px_54px_-38px_rgb(var(--brand-primary-rgb)/0.4)] backdrop-blur-sm'}>
    <CardHeader class={isDashboardRoute ? 'border-b border-border/70 pb-3' : 'border-b border-primary/10 pb-3'}>
        <CardTitle class={`text-sm font-medium ${isDashboardRoute ? 'text-foreground' : 'text-[#D12052]'}`}>{label}</CardTitle>
    </CardHeader>
    <CardContent class="flex flex-col gap-2">
        <strong class={`text-3xl font-semibold ${isDashboardRoute ? 'text-primary' : 'text-[#03AED2]'}`}>{value}</strong>
        {#if helper}
            <p class={`text-sm ${isDashboardRoute ? 'text-muted-foreground' : 'text-slate-500'}`}>{helper}</p>
        {/if}
    </CardContent>
</Card>
