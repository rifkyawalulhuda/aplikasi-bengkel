<script lang="ts">
    import type { Snippet } from 'svelte';
    import { cn } from '@/lib/utils';

    type Variant =
        | 'default'
        | 'secondary'
        | 'ghost'
        | 'destructive'
        | 'outline'
        | 'link';
    type Size = 'default' | 'sm' | 'lg' | 'icon';
    type AsChildProps = {
        class?: string;
        onClick?: (event: MouseEvent) => void;
        [key: string]: any;
    };

    const base =
        'inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50';

    const variants: Record<Variant, string> = {
        default:
            'bg-primary text-primary-foreground shadow-sm shadow-[0_18px_34px_-20px_rgb(var(--brand-primary-rgb)/0.72)] hover:bg-primary/92',
        secondary:
            'bg-secondary text-secondary-foreground shadow-sm shadow-[0_18px_34px_-22px_rgb(var(--brand-secondary-rgb)/0.65)] hover:bg-secondary/92',
        ghost: 'text-foreground hover:bg-primary/10 hover:text-primary',
        destructive:
            'bg-destructive text-destructive-foreground shadow-sm shadow-[0_18px_34px_-22px_rgb(var(--brand-strong-rgb)/0.62)] hover:bg-destructive/92',
        outline:
            'border border-input bg-background/90 hover:border-primary/25 hover:bg-primary/6 hover:text-foreground',
        link: 'text-primary underline-offset-4 hover:underline',
    };

    const sizes: Record<Size, string> = {
        default: 'h-9 px-4 py-2',
        sm: 'h-8 rounded-md px-3 text-xs',
        lg: 'h-10 rounded-md px-8',
        icon: 'h-9 w-9',
    };

    let {
        children,
        asChild = false,
        variant = 'default',
        size = 'default',
        class: className = '',
        type = 'button',
        ...rest
    }: {
        children?: Snippet<[AsChildProps]>;
        asChild?: boolean;
        variant?: Variant;
        size?: Size;
        class?: string;
        type?: 'button' | 'submit' | 'reset';
        [key: string]: unknown;
    } = $props();

    const classes = () => cn(base, variants[variant], sizes[size], className);
</script>

{#if asChild}
    {@render children?.({ class: classes(), ...rest })}
{:else}
    <button class={classes()} type={type} {...rest}>
        {@render children?.({})}
    </button>
{/if}
