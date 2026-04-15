<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { create as bookingCreate } from '@/routes/bookings';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';

    let {
        tagline,
        serviceAreas,
        packageCount,
        customItemsCount,
    }: {
        tagline: string;
        serviceAreas: string[];
        packageCount: number;
        customItemsCount: number;
    } = $props();

    const heroImage = '/images/hero-main.png';
</script>

<section class="relative isolate min-h-[calc(100svh-5rem)] overflow-hidden bg-[#0d1c24]">
    <img
        src={heroImage}
        alt="Mekanik bengkel sedang menyiapkan servis motor di workshop"
        class="absolute inset-0 h-full w-full object-cover"
    />
    <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(8,19,26,0.9)_0%,rgba(8,19,26,0.8)_38%,rgba(8,19,26,0.46)_100%)]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(3,174,210,0.24),transparent_28%),radial-gradient(circle_at_bottom_right,rgba(244,91,38,0.18),transparent_26%)]"></div>

    <div class="relative mx-auto grid min-h-[calc(100svh-5rem)] max-w-7xl gap-10 px-4 py-12 md:px-6 md:py-16 lg:grid-cols-[minmax(0,1fr)_21rem] lg:items-end lg:py-20">
        <div class="flex max-w-3xl flex-col justify-end gap-6 lg:pb-8">
            <Badge
                variant="secondary"
                class="w-fit rounded-full bg-accent px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[0.26em] text-accent-foreground"
            >
                Layanan Servis Motor Karawang
            </Badge>

            <div class="space-y-4">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-white/70">
                    Solusi home service untuk motor harian
                </p>
                <h1 class="max-w-4xl text-balance text-4xl font-black uppercase leading-[0.92] tracking-[-0.04em] text-white sm:text-5xl lg:text-7xl">
                    Servis Motor
                    <span class="text-white/82">Jadi Mudah,</span>
                    <span class="block text-primary">Cukup Dari</span>
                    <span class="block text-primary">Rumah</span>
                </h1>
                <p class="max-w-2xl text-pretty text-base leading-7 text-white/78 md:text-lg">
                    Bengkel profesional siap datang ke lokasi Anda. Hemat waktu, tenaga, dan tetap aman di rumah dengan standar pengerjaan presisi tinggi.
                </p>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <Button
                    asChild
                    class="h-12 rounded-full bg-secondary px-6 text-sm font-semibold text-secondary-foreground shadow-[0_22px_40px_-20px_rgb(var(--brand-secondary-rgb)/0.8)] hover:bg-secondary/92"
                >
                    {#snippet children(props)}
                        <Link href={bookingCreate()} {...props}>Pesan Home Service Sekarang</Link>
                    {/snippet}
                </Button>
                <Button
                    asChild
                    variant="outline"
                    class="h-12 rounded-full border-white/20 bg-white/6 px-6 text-sm font-semibold text-white backdrop-blur-md hover:bg-white/14 hover:text-white"
                >
                    {#snippet children(props)}
                        <a href="#packages" {...props}>Lihat Paket Servis</a>
                    {/snippet}
                </Button>
            </div>

            <div class="flex flex-wrap gap-2 pt-1">
                {#each serviceAreas.slice(0, 3) as area}
                    <span class="rounded-full border border-white/16 bg-white/8 px-3 py-1.5 text-xs font-medium text-white/78 backdrop-blur-sm">
                        {area}
                    </span>
                {/each}
            </div>
        </div>

        <div class="hidden lg:flex lg:justify-end">
            <div class="w-full max-w-sm rounded-[2rem] border border-white/16 bg-white/10 p-5 text-white shadow-[0_28px_70px_-28px_rgba(0,0,0,0.75)] backdrop-blur-xl">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-2">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-white/62">
                            Ringkasan Layanan
                        </p>
                        <p class="text-xl font-semibold leading-tight">
                            Servis motor ke rumah dengan alur booking yang cepat dan jelas.
                        </p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-accent text-sm font-black text-accent-foreground">
                        GO
                    </span>
                </div>

                <div class="mt-6 grid grid-cols-3 gap-3">
                    <div class="rounded-[1.25rem] border border-white/14 bg-black/18 p-4">
                        <p class="text-[11px] uppercase tracking-[0.2em] text-white/58">Paket</p>
                        <p class="mt-2 text-2xl font-bold">{packageCount}</p>
                    </div>
                    <div class="rounded-[1.25rem] border border-white/14 bg-black/18 p-4">
                        <p class="text-[11px] uppercase tracking-[0.2em] text-white/58">Custom</p>
                        <p class="mt-2 text-2xl font-bold">{customItemsCount}</p>
                    </div>
                    <div class="rounded-[1.25rem] border border-white/14 bg-black/18 p-4">
                        <p class="text-[11px] uppercase tracking-[0.2em] text-white/58">Area</p>
                        <p class="mt-2 text-2xl font-bold">{serviceAreas.length}</p>
                    </div>
                </div>

                <p class="mt-5 text-sm leading-7 text-white/76">
                    Harga awal terlihat sejak awal, lalu admin memverifikasi slot dan area sebelum booking dikonfirmasi.
                </p>
            </div>
        </div>
    </div>
</section>
