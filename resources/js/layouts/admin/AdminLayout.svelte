<script lang="ts">
    import type { Snippet } from 'svelte';
    import AdminHeader from '@/components/admin/AdminHeader.svelte';
    import AdminSidebar from '@/components/admin/AdminSidebar.svelte';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();
</script>

<div
    class="grid min-h-screen bg-[radial-gradient(circle_at_top_right,rgba(var(--brand-accent-rgb),0.22),transparent_28%),radial-gradient(circle_at_top_left,rgba(var(--brand-primary-rgb),0.14),transparent_32%),linear-gradient(180deg,#fffef6_0%,#f8fbfc_18%,#f3f5f7_100%)] lg:grid-cols-[280px_1fr]"
    style="--background: #f8fbfc; --foreground: #1f2937; --surface-soft: #eef4f6; --card: #ffffff; --card-foreground: #1f2937; --popover: #ffffff; --popover-foreground: #1f2937; --muted: #f3f7f9; --muted-foreground: #64748b; --border: #cfe2e8; --input: #bfd4dc; --ring: #03aed2;"
>
    <div class="hidden lg:block">
        <AdminSidebar />
    </div>

    <div class="flex min-h-screen flex-col">
        <AdminHeader />
        <main class="flex-1 px-4 py-6 md:px-6">
            {@render children?.()}
        </main>
    </div>
</div>
