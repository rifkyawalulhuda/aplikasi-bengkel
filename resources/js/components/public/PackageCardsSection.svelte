<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { create as bookingCreate } from '@/routes/bookings';
    import { Button } from '@/components/ui/button';
    import type { CustomServiceItemSummary, ServicePackageSummary } from '@/types';

    let {
        packages,
        customItems,
    }: {
        packages: ServicePackageSummary[];
        customItems: CustomServiceItemSummary[];
    } = $props();

    const featuredPackages = $derived.by(() => {
        const manuallyFeaturedPackages = packages.filter((item) => item.isFeatured);
        const remainingPackages = packages.filter((item) => !item.isFeatured);

        return [...manuallyFeaturedPackages, ...remainingPackages].slice(0, 3);
    });
</script>

<section id="packages" class="bg-surface-soft/50">
    <div class="mx-auto flex max-w-7xl flex-col gap-10 px-4 py-16 md:px-6 md:py-24">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-primary">
                Paket Servis Unggulan
            </p>
            <h2 class="mt-4 text-balance text-3xl font-black tracking-[-0.04em] text-foreground md:text-5xl">
                Pilih paket yang paling pas, lalu lanjutkan ke <span class="text-primary">booking</span> dalam satu alur yang ringkas.
            </h2>
        </div>

        {#if featuredPackages.length > 0}
            <div class="grid gap-6 xl:grid-cols-3">
                {#each featuredPackages as item, index (item.id)}
                    <article
                        class={`relative overflow-hidden rounded-[2rem] border bg-card p-6 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.22)] md:p-8 ${
                            item.isFeatured
                                ? 'border-secondary shadow-[0_34px_85px_-40px_rgb(var(--brand-secondary-rgb)/0.5)]'
                                : 'border-border/70'
                        }`}
                    >
                        {#if item.isFeatured}
                            <span class="absolute right-6 top-6 rounded-full bg-secondary px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-secondary-foreground">
                                Paling Diminati
                            </span>
                        {/if}

                        <div class="space-y-6">
                            <div class="space-y-3">
                                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-muted-foreground">
                                    Paket {String.fromCharCode(65 + index)}
                                </p>
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                    <div>
                                        <h3 class="text-3xl font-black tracking-[-0.04em] text-foreground">
                                            {item.name}
                                        </h3>
                                        <p class="mt-2 max-w-md text-sm leading-7 text-muted-foreground">
                                            {item.shortDescription ?? 'Paket servis untuk kebutuhan perawatan motor harian di rumah.'}
                                        </p>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <p class="text-sm font-medium uppercase tracking-[0.18em] text-muted-foreground">
                                            Mulai dari
                                        </p>
                                        <p class="mt-1 text-4xl font-black tracking-[-0.05em] text-primary">
                                            Rp {item.price.toLocaleString('id-ID')}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <ul class="grid gap-3 border-t border-border/70 pt-6">
                                {#each item.items.slice(0, 4) as packageItem (packageItem.id)}
                                    <li class="flex items-start gap-3 text-sm leading-7 text-muted-foreground">
                                        <span class="mt-2 h-2.5 w-2.5 rounded-full bg-primary"></span>
                                        <span>
                                            <span class="font-semibold text-foreground">{packageItem.name}</span>
                                            {#if packageItem.description}
                                                <span class="block text-muted-foreground">{packageItem.description}</span>
                                            {/if}
                                        </span>
                                    </li>
                                {/each}
                            </ul>

                            <Button
                                asChild
                                class={`h-12 w-full rounded-full px-6 text-sm font-semibold ${
                                    item.isFeatured
                                        ? 'bg-secondary text-secondary-foreground hover:bg-secondary/92'
                                        : 'bg-primary text-primary-foreground hover:bg-primary/92'
                                }`}
                            >
                                {#snippet children(props)}
                                    <Link
                                        href={bookingCreate({
                                            query: {
                                                package: item.slug,
                                            },
                                        })}
                                        {...props}
                                    >
                                        Pilih Paket Ini
                                    </Link>
                                {/snippet}
                            </Button>
                        </div>
                    </article>
                {/each}
            </div>
        {:else}
            <div class="rounded-[2rem] border border-dashed border-border/80 bg-card px-6 py-14 text-center text-sm leading-7 text-muted-foreground">
                Paket aktif belum tersedia. Begitu paket diaktifkan dari admin, kartu pricing di area ini akan langsung terisi.
            </div>
        {/if}

        <div class="rounded-[2rem] border border-border/70 bg-card px-6 py-6 shadow-[0_24px_60px_-44px_rgba(15,23,42,0.2)] md:px-8">
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                <div class="max-w-2xl space-y-2">
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-secondary">
                        Paket Custom
                    </p>
                    <h3 class="text-2xl font-black tracking-[-0.03em] text-foreground">
                        Butuh kombinasi servis yang lebih fleksibel?
                    </h3>
                    <p class="text-sm leading-7 text-muted-foreground">
                        Tersedia {customItems.length} item custom aktif untuk pelanggan yang ingin menyusun servis sesuai kebutuhan motor dan kondisi lapangan.
                    </p>
                </div>

                <Button
                    asChild
                    variant="outline"
                    class="h-12 rounded-full border-secondary/20 bg-secondary/8 px-6 text-sm font-semibold text-secondary hover:bg-secondary/14"
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
                            Susun Paket Custom
                        </Link>
                    {/snippet}
                </Button>
            </div>
        </div>
    </div>
</section>
