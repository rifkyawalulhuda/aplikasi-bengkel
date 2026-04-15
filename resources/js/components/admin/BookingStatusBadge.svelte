<script lang="ts">
    import { Badge } from '@/components/ui/badge';

    let {
        status,
        label,
        class: className = '',
    }: {
        status: string;
        label: string;
        class?: string;
    } = $props();

    const variant = $derived(status === 'cancelled' ? 'destructive' : 'outline');

    const accentClass = $derived(
        status === 'completed'
            ? 'border-accent/55 bg-accent/28 text-accent-foreground'
            : status === 'confirmed'
              ? 'border-primary/25 bg-primary/10 text-primary'
              : status === 'on_the_way'
                ? 'border-secondary/25 bg-secondary/10 text-secondary'
                : status === 'rescheduled'
                  ? 'border-primary/18 bg-muted text-foreground'
                  : 'border-border bg-background/85 text-muted-foreground',
    );
</script>

<Badge variant={variant} class={status === 'cancelled' ? className : `${accentClass} ${className}`}>
    {label}
</Badge>
