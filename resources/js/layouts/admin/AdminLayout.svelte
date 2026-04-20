<script lang="ts">
    import type { Snippet } from 'svelte';
    import { page } from '@inertiajs/svelte';
    import AdminHeader from '@/components/admin/AdminHeader.svelte';
    import AdminSidebar from '@/components/admin/AdminSidebar.svelte';
    import { Sheet, SheetContent } from '@/components/ui/sheet';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();

    let isMobileSidebarOpen = $state(false);
    const currentUrl = $derived((page.url ?? '') as string);

    $effect(() => {
        currentUrl;
        isMobileSidebarOpen = false;
    });
</script>

<Sheet bind:open={isMobileSidebarOpen}>
    <div
        class="grid min-h-screen bg-background lg:grid-cols-[280px_1fr]"
        style="--background: #f8fafc; --foreground: #0f172a; --surface-soft: #f1f5f9; --card: #ffffff; --card-foreground: #0f172a; --popover: #ffffff; --popover-foreground: #0f172a; --muted: #f8fafc; --muted-foreground: #64748b; --border: #dbe4ee; --input: #cbd5e1; --ring: #0f172a;"
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

    <SheetContent side="left" class="w-[280px] p-0">
        <AdminSidebar mobile />
    </SheetContent>
</Sheet>
