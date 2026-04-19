<script lang="ts">
    import type { Snippet } from 'svelte';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();
</script>

<div
    class="flex min-h-screen items-center justify-center bg-background px-4 py-10"
    style="--background: #f8fafc; --foreground: #0f172a; --surface-soft: #f1f5f9; --card: #ffffff; --card-foreground: #0f172a; --popover: #ffffff; --popover-foreground: #0f172a; --muted: #f8fafc; --muted-foreground: #64748b; --border: #dbe4ee; --input: #cbd5e1; --ring: #0f172a;"
>
    <div class="grid w-full max-w-5xl overflow-hidden rounded-[2rem] border border-border/70 bg-card shadow-[0_28px_70px_-48px_rgb(15_23_42/0.22)] lg:grid-cols-[0.9fr_1.1fr]">
        <div class="flex flex-col justify-between gap-8 bg-muted px-6 py-8 md:px-8 md:py-10">
            <div class="flex flex-col gap-4">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-muted-foreground">
                    Admin Login
                </p>
                <h1 class="text-3xl font-semibold leading-tight text-foreground">
                    Area admin dibuat terpisah agar operasional harian tetap fokus.
                </h1>
            </div>

            <div class="flex flex-col gap-3 text-sm leading-6 text-muted-foreground">
                <p>Login admin diprefix ke <strong>/admin</strong> agar area publik dan internal tidak bercampur.</p>
                <p>Langkah berikutnya tinggal menyambungkan booking list, package management, dan visitor analytics ke layout ini.</p>
            </div>
        </div>

        <div class="flex items-center bg-card px-6 py-8 md:px-10 md:py-10">
            <div class="w-full">
                {@render children?.()}
            </div>
        </div>
    </div>
</div>
