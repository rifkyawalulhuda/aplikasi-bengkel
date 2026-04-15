<script lang="ts">
    import type { Snippet } from 'svelte';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();
</script>

<div
    class="flex min-h-screen items-center justify-center bg-[radial-gradient(circle_at_top_right,rgba(var(--brand-accent-rgb),0.24),transparent_28%),linear-gradient(180deg,#fffef8_0%,#f8fbfc_100%)] px-4 py-10"
    style="--background: #f8fbfc; --foreground: #1f2937; --surface-soft: #eef4f6; --card: #ffffff; --card-foreground: #1f2937; --popover: #ffffff; --popover-foreground: #1f2937; --muted: #f3f7f9; --muted-foreground: #64748b; --border: #cfe2e8; --input: #bfd4dc; --ring: #03aed2;"
>
    <div class="grid w-full max-w-5xl overflow-hidden rounded-[2rem] border border-primary/14 bg-white/88 shadow-[0_28px_70px_-48px_rgb(var(--brand-primary-rgb)/0.5)] backdrop-blur-sm lg:grid-cols-[0.9fr_1.1fr]">
        <div class="flex flex-col justify-between gap-8 bg-[radial-gradient(circle_at_top,rgba(var(--brand-primary-rgb),0.18),transparent_30%),linear-gradient(180deg,#11232D_0%,#17323F_52%,#1C3948_100%)] px-6 py-8 text-sidebar-foreground md:px-8 md:py-10">
            <div class="flex flex-col gap-4">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#03AED2]">
                    Admin Login
                </p>
                <h1 class="text-3xl font-semibold leading-tight text-[#F8DE22]">
                    Area admin dibuat terpisah agar operasional harian tetap fokus.
                </h1>
            </div>

            <div class="flex flex-col gap-3 text-sm leading-6 text-sidebar-foreground/80">
                <p>Login admin diprefix ke <strong>/admin</strong> agar area publik dan internal tidak bercampur.</p>
                <p>Langkah berikutnya tinggal menyambungkan booking list, package management, dan visitor analytics ke layout ini.</p>
            </div>
        </div>

        <div class="flex items-center bg-white/76 px-6 py-8 md:px-10 md:py-10">
            <div class="w-full">
                {@render children?.()}
            </div>
        </div>
    </div>
</div>
