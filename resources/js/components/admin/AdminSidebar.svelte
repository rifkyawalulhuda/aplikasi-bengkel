<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { SheetClose } from '@/components/ui/sheet';
    import { index as customServiceItemsIndex } from '@/actions/App/Http/Controllers/Admin/CustomServiceItemController';
    import { index as visitorsIndex } from '@/actions/App/Http/Controllers/Admin/VisitorController';
    import { dashboard as dashboardIndex } from '@/routes/admin';
    import { index as bookingsIndex } from '@/routes/admin/bookings';
    import { index as servicePackagesIndex } from '@/routes/admin/service-packages';
    import { cn } from '@/lib/utils';

    const currentUrl = $derived((page.url ?? '') as string);

    let {
        mobile = false,
    }: {
        mobile?: boolean;
    } = $props();

    const links = [
        { label: 'Dashboard', href: dashboardIndex().url },
        { label: 'Bookings', href: bookingsIndex().url },
        { label: 'Service Packages', href: servicePackagesIndex().url },
        { label: 'Custom Items', href: customServiceItemsIndex().url },
        { label: 'Visitors', href: visitorsIndex().url },
    ];
</script>

<aside
    class={cn(
        'flex h-full min-h-screen flex-col bg-background px-4 py-5 text-foreground shadow-sm',
        mobile ? 'border-r-0 shadow-none' : 'border-r border-border/70',
    )}
>
    <div class="flex flex-col gap-2 border-b border-border/70 pb-4">
        <span
            class="text-xs uppercase tracking-[0.2em] text-muted-foreground"
        >
            Admin Area
        </span>
        <strong class="text-lg text-foreground">Bengkel Home Service</strong>
        <Badge
            variant="secondary"
            class="w-fit border border-border/70 bg-background text-foreground"
        >
            MVP Foundation
        </Badge>
    </div>

    <nav class="mt-6 flex flex-col gap-2">
        {#each links as link}
            {#if mobile}
                <SheetClose asChild>
                    {#snippet children(closeProps)}
                        <Button
                            asChild
                            variant="ghost"
                            class={`justify-start border ${
                                currentUrl.startsWith(link.href)
                                    ? 'border-border/70 bg-muted text-foreground hover:bg-muted hover:text-foreground'
                                    : 'border-transparent text-muted-foreground hover:border-border/70 hover:bg-muted hover:text-foreground'
                            }`}
                        >
                            {#snippet children(buttonProps)}
                                <Link href={link.href} {...buttonProps} {...closeProps}>{link.label}</Link>
                            {/snippet}
                        </Button>
                    {/snippet}
                </SheetClose>
            {:else}
                <Button
                    asChild
                    variant="ghost"
                    class={`justify-start border ${
                        currentUrl.startsWith(link.href)
                            ? 'border-border/70 bg-muted text-foreground hover:bg-muted hover:text-foreground'
                            : 'border-transparent text-muted-foreground hover:border-border/70 hover:bg-muted hover:text-foreground'
                    }`}
                >
                    {#snippet children(props)}
                        <Link href={link.href} {...props}>{link.label}</Link>
                    {/snippet}
                </Button>
            {/if}
        {/each}
    </nav>
</aside>
