<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { create as bookingCreate } from '@/routes/bookings';
    import type { LandingHighlight } from '@/types';

    let {
        highlights,
    }: {
        highlights: LandingHighlight[];
    } = $props();

    const serviceImages = [
        'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?auto=format&fit=crop&w=1200&q=80',
        '/images/service-emergency.png',
    ];
</script>

<section id="services" class="bg-background">
    <div class="mx-auto flex max-w-7xl flex-col gap-10 px-4 py-16 md:px-6 md:py-24">
        <div class="max-w-3xl space-y-4">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-primary">
                Layanan Unggulan Kami
            </p>
            <h2 class="text-balance text-3xl font-black tracking-[-0.04em] text-foreground md:text-5xl">
                Solusi servis yang <span class="text-primary">praktis</span> untuk motor harian tanpa perlu antre ke bengkel.
            </h2>
            <p class="max-w-2xl text-base leading-7 text-muted-foreground md:text-lg">
                Solusi perawatan motor terlengkap langsung di garasi Anda tanpa perlu antri di bengkel.
            </p>
        </div>

        <div class="grid gap-5 lg:grid-cols-3">
            {#each highlights.slice(0, 3) as item, index (item.title)}
                <article class="group overflow-hidden rounded-[2rem] border border-border/70 bg-card shadow-[0_24px_60px_-40px_rgba(15,23,42,0.2)] transition-transform duration-300 hover:-translate-y-1">
                    <div class="relative aspect-[1.22] overflow-hidden">
                        <img
                            src={serviceImages[index] ?? serviceImages[0]}
                            alt={item.title}
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-[linear-gradient(180deg,transparent_0%,rgba(15,23,42,0.28)_100%)]"></div>
                    </div>

                    <div class="flex flex-col gap-4 p-5 md:p-6">
                        <div class="space-y-3">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-muted-foreground">
                                0{index + 1}
                            </p>
                            <h3 class="text-xl font-bold tracking-[-0.02em] text-foreground">
                                {item.title}
                            </h3>
                            <p class="text-sm leading-7 text-muted-foreground">
                                {item.description}
                            </p>
                        </div>

                        <Link
                            href={bookingCreate()}
                            class="inline-flex w-fit items-center gap-2 text-sm font-semibold uppercase tracking-[0.14em] text-primary transition-colors hover:text-secondary"
                        >
                            Lihat Detail
                        </Link>
                    </div>
                </article>
            {/each}
        </div>
    </div>
</section>
