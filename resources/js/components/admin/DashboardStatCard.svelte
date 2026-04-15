<script lang="ts">
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
</script>

<Card class="border-primary/16 bg-white/88 shadow-[0_24px_54px_-38px_rgb(var(--brand-primary-rgb)/0.4)] backdrop-blur-sm">
    <CardHeader class="border-b border-primary/10 pb-3">
        <CardTitle class="text-sm font-medium text-[#D12052]">{label}</CardTitle>
    </CardHeader>
    <CardContent class="flex flex-col gap-2">
        <strong class="text-3xl font-semibold text-[#03AED2]">{value}</strong>
        {#if helper}
            <p class="text-sm text-slate-500">{helper}</p>
        {/if}
    </CardContent>
</Card>
