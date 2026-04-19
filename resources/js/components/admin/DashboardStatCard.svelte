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

<Card class="border-border/70 bg-card shadow-sm">
    <CardHeader class="border-b border-border/70 pb-3">
        <CardTitle class="text-sm font-medium text-foreground">{label}</CardTitle>
    </CardHeader>
    <CardContent class="flex flex-col gap-2">
        <strong class="text-3xl font-semibold text-foreground">{value}</strong>
        {#if helper}
            <p class="text-sm text-muted-foreground">{helper}</p>
        {/if}
    </CardContent>
</Card>
