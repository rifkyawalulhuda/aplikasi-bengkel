<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import { home, logout } from '@/routes';

    const auth = $derived(page.props.auth as { user: { name: string; email: string } | null });
    const isDashboardRoute = $derived((page.url ?? '') === '/dashboard');
</script>

<header class={`flex flex-col gap-4 border-b px-4 py-4 shadow-sm md:flex-row md:items-center md:justify-between md:px-6 ${
    isDashboardRoute
        ? 'border-border/70 bg-background text-foreground'
        : 'border-slate-900/10 bg-[linear-gradient(135deg,#122631_0%,#183543_58%,#214452_100%)] text-white shadow-[0_18px_40px_-26px_rgba(18,38,49,0.72)]'
}`}>
    <div class="flex flex-col gap-1">
        <p class={`text-sm font-semibold uppercase tracking-[0.2em] ${isDashboardRoute ? 'text-muted-foreground' : 'text-[#03AED2]'}`}>
            Dashboard Admin
        </p>
        <h1 class={`text-2xl font-semibold ${isDashboardRoute ? 'text-foreground' : 'text-[#F8DE22]'}`}>Operasional bengkel home service</h1>
    </div>

    <div class="flex items-center gap-3">
        <div class="hidden text-right md:block">
            <p class={`text-sm font-medium ${isDashboardRoute ? 'text-foreground' : 'text-white'}`}>{auth.user?.name}</p>
            <p class={`text-xs ${isDashboardRoute ? 'text-muted-foreground' : 'text-white/68'}`}>{auth.user?.email}</p>
        </div>

        <Button
            asChild
            variant="outline"
            size="sm"
            class={isDashboardRoute
                ? 'border-border/70 bg-background text-foreground hover:bg-muted hover:text-foreground'
                : 'border-white/16 bg-white/6 text-white hover:bg-white/10 hover:text-white'}
        >
            {#snippet children(props)}
                <Link href={home()} {...props}>Lihat public site</Link>
            {/snippet}
        </Button>
        <Button
            asChild
            size="sm"
            class={isDashboardRoute
                ? 'bg-primary text-primary-foreground hover:bg-primary/90'
                : 'bg-[#03AED2] text-[#062B35] hover:bg-[#03AED2]/92'}
        >
            {#snippet children(props)}
                <Link href={logout()} {...props}>Logout</Link>
            {/snippet}
        </Button>
    </div>
</header>
