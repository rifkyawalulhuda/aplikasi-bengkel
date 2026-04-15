<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import { create as bookingCreate } from '@/routes/bookings';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import type { LandingCta } from '@/types';

    let {
        cta,
        availableSlots,
        customItemsCount,
        packageCount,
    }: {
        cta: LandingCta;
        availableSlots: string[];
        customItemsCount: number;
        packageCount: number;
    } = $props();

    const workshop = $derived(page.props.workshop as { contactPhone: string });
</script>

<section id="booking-cta" class="bg-background">
    <div class="mx-auto max-w-7xl px-4 py-16 md:px-6 md:py-24">
        <div class="overflow-hidden rounded-[2.4rem] border border-border/70 bg-[linear-gradient(135deg,#0c2430_0%,#123441_54%,#184a59_100%)] text-white shadow-[0_36px_90px_-46px_rgba(12,36,48,0.7)]">
            <div class="grid gap-8 px-6 py-8 md:px-8 md:py-10 lg:grid-cols-[1.15fr_0.85fr] lg:px-12 lg:py-12">
                <div class="flex flex-col gap-5">
                    <Badge class="w-fit rounded-full border-0 bg-accent px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[0.24em] text-accent-foreground">
                        Mulai Booking
                    </Badge>

                    <div class="space-y-3">
                        <h2 class="text-balance text-3xl font-black tracking-[-0.04em] md:text-5xl">
                            {cta.title}
                        </h2>
                        <p class="max-w-2xl text-base leading-7 text-white/74 md:text-lg">
                            {cta.description}
                        </p>
                    </div>

                    <ul class="grid gap-3 text-sm text-white/78 md:grid-cols-2">
                        {#each cta.points as point}
                            <li class="rounded-[1.3rem] border border-white/12 bg-white/[0.08] px-4 py-3 backdrop-blur-sm">
                                {point}
                            </li>
                        {/each}
                    </ul>

                    <div class="flex flex-wrap gap-3 pt-1">
                        <Button
                            asChild
                            class="h-12 rounded-full bg-secondary px-6 text-sm font-semibold text-secondary-foreground shadow-[0_24px_44px_-22px_rgb(var(--brand-secondary-rgb)/0.75)] hover:bg-secondary/92"
                        >
                            {#snippet children(props)}
                                <Link href={bookingCreate()} {...props}>Booking Sekarang</Link>
                            {/snippet}
                        </Button>

                        <Button
                            asChild
                            variant="outline"
                            class="h-12 rounded-full border-white/16 bg-white/[0.08] px-6 text-sm font-semibold text-white hover:bg-white/[0.14] hover:text-white"
                        >
                            {#snippet children(props)}
                                <Link
                                    href={bookingCreate({
                                        query: {
                                            package: 'custom',
                                        },
                                    })}
                                    {...props}
                                >
                                    Pilih Paket Custom
                                </Link>
                            {/snippet}
                        </Button>

                        <Button
                            asChild
                            variant="ghost"
                            class="h-12 rounded-full px-5 text-sm font-semibold text-white hover:bg-white/10 hover:text-white"
                        >
                            {#snippet children(props)}
                                <a href={`tel:${workshop.contactPhone}`} {...props}>Hubungi Admin</a>
                            {/snippet}
                        </Button>
                    </div>
                </div>

                <div class="grid gap-3 rounded-[1.8rem] border border-white/12 bg-white/10 p-5 text-sm backdrop-blur-md">
                    <div class="rounded-[1.25rem] border border-white/12 bg-black/[0.16] px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-white/56">
                            Slot contoh
                        </p>
                        <p class="mt-2 text-2xl font-black tracking-[-0.03em]">
                            {availableSlots.slice(0, 3).join(' / ')}
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                        <div class="flex items-center justify-between gap-4 rounded-[1.25rem] border border-white/10 bg-black/[0.12] px-4 py-3">
                            <span class="text-white/62">Paket aktif</span>
                            <strong class="text-lg">{packageCount}</strong>
                        </div>
                        <div class="flex items-center justify-between gap-4 rounded-[1.25rem] border border-white/10 bg-black/[0.12] px-4 py-3">
                            <span class="text-white/62">Item custom</span>
                            <strong class="text-lg">{customItemsCount}</strong>
                        </div>
                    </div>

                    <div class="rounded-[1.25rem] border border-white/10 bg-white/[0.06] px-4 py-3 text-white/72">
                        Fixed package dan custom package tetap memakai source data yang sama dengan flow booking utama.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
