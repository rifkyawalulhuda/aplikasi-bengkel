<script lang="ts">
    import type { Snippet } from 'svelte';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

    let {
        title,
        description = '',
        children,
    }: {
        title: string;
        description?: string;
        children?: Snippet;
    } = $props();
</script>

<Card class="border-primary/16 bg-white/88 shadow-[0_24px_54px_-40px_rgb(var(--brand-primary-rgb)/0.36)] backdrop-blur-sm">
    <CardHeader class="gap-2">
        <CardTitle class="text-[#F45B26]">{title}</CardTitle>
        {#if description}
            <p class="text-sm leading-6 text-muted-foreground">{description}</p>
        {/if}
    </CardHeader>
    <CardContent>
        {@render children?.()}
    </CardContent>
</Card>
