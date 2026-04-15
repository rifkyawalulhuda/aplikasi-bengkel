<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import { home, logout } from '@/routes';

    const auth = $derived(page.props.auth as { user: { name: string; email: string } | null });
</script>

<header class="flex flex-col gap-4 border-b border-slate-900/10 bg-[linear-gradient(135deg,#122631_0%,#183543_58%,#214452_100%)] px-4 py-4 text-white shadow-[0_18px_40px_-26px_rgba(18,38,49,0.72)] md:flex-row md:items-center md:justify-between md:px-6">
    <div class="flex flex-col gap-1">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#03AED2]">
            Dashboard Admin
        </p>
        <h1 class="text-2xl font-semibold text-[#F8DE22]">Operasional bengkel home service</h1>
    </div>

    <div class="flex items-center gap-3">
        <div class="hidden text-right md:block">
            <p class="text-sm font-medium text-white">{auth.user?.name}</p>
            <p class="text-xs text-white/68">{auth.user?.email}</p>
        </div>

        <Button
            asChild
            variant="outline"
            size="sm"
            class="border-white/16 bg-white/6 text-white hover:bg-white/10 hover:text-white"
        >
            {#snippet children(props)}
                <Link href={home()} {...props}>Lihat public site</Link>
            {/snippet}
        </Button>
        <Button
            asChild
            size="sm"
            class="bg-[#03AED2] text-[#062B35] hover:bg-[#03AED2]/92"
        >
            {#snippet children(props)}
                <Link href={logout()} {...props}>Logout</Link>
            {/snippet}
        </Button>
    </div>
</header>
