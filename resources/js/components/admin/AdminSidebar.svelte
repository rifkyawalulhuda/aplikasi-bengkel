<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { index as customServiceItemsIndex } from '@/actions/App/Http/Controllers/Admin/CustomServiceItemController';
    import { index as visitorsIndex } from '@/actions/App/Http/Controllers/Admin/VisitorController';
    import { dashboard as dashboardIndex } from '@/routes/admin';
    import { index as bookingsIndex } from '@/routes/admin/bookings';
    import { index as servicePackagesIndex } from '@/routes/admin/service-packages';

    const currentUrl = $derived((page.url ?? '') as string);
    const isDashboardRoute = $derived(currentUrl === '/dashboard');

    const links = [
        { label: 'Dashboard', href: dashboardIndex().url },
        { label: 'Bookings', href: bookingsIndex().url },
        { label: 'Service Packages', href: servicePackagesIndex().url },
        { label: 'Custom Items', href: customServiceItemsIndex().url },
        { label: 'Visitors', href: visitorsIndex().url },
    ];
</script>

<aside
    class={`flex h-full flex-col border-r px-4 py-5 shadow-[18px_0_40px_-30px_rgba(18,38,49,0.62)] ${
        isDashboardRoute
            ? 'border-border/70 bg-background text-foreground shadow-sm'
            : 'border-slate-900/10 bg-[radial-gradient(circle_at_top,rgba(var(--brand-primary-rgb),0.16),transparent_32%),linear-gradient(180deg,#11232D_0%,#17323F_52%,#1C3948_100%)] text-sidebar-foreground'
    }`}
>
    <div class={`flex flex-col gap-2 border-b pb-4 ${isDashboardRoute ? 'border-border/70' : 'border-sidebar-border'}`}>
        <span
            class={`text-xs uppercase tracking-[0.2em] ${isDashboardRoute ? 'text-muted-foreground' : 'text-[#03AED2]'}`}
        >
            Admin Area
        </span>
        <strong class={`text-lg ${isDashboardRoute ? 'text-foreground' : 'text-[#F8DE22]'}`}>Bengkel Home Service</strong>
        <Badge
            variant="secondary"
            class={isDashboardRoute
                ? 'w-fit border border-border/70 bg-background text-foreground'
                : 'w-fit border-0 bg-[#D12052] text-white'}
        >
            MVP Foundation
        </Badge>
    </div>

    <nav class="mt-6 flex flex-col gap-2">
        {#each links as link}
            <Button
                asChild
                variant="ghost"
                class={`justify-start border ${
                    currentUrl.startsWith(link.href)
                        ? isDashboardRoute
                            ? 'border-border/70 bg-muted text-foreground hover:bg-muted hover:text-foreground'
                            : 'border-[#F45B26]/35 bg-[#F45B26]/16 text-white hover:bg-[#F45B26]/22 hover:text-white'
                        : isDashboardRoute
                            ? 'border-transparent text-muted-foreground hover:border-border/70 hover:bg-muted hover:text-foreground'
                            : 'border-transparent text-sidebar-foreground/80 hover:border-white/10 hover:bg-white/8 hover:text-white'
                }`}
            >
                {#snippet children(props)}
                    <Link href={link.href} {...props}>{link.label}</Link>
                {/snippet}
            </Button>
        {/each}
    </nav>
</aside>
