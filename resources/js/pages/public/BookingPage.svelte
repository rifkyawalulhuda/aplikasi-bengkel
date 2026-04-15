<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import BookingForm from '@/components/public/BookingForm.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { home } from '@/routes';
    import type {
        BookingPagePrefill,
        CustomServiceItemSummary,
        SelectOption,
        SeoMetadata,
        ServicePackageSummary,
    } from '@/types';

    let {
        seo,
        packages,
        customItems,
        availableSlots,
        packageTypes,
        motorcycleTypes,
        prefill,
    }: {
        seo: SeoMetadata;
        packages: ServicePackageSummary[];
        customItems: CustomServiceItemSummary[];
        availableSlots: string[];
        packageTypes: SelectOption[];
        motorcycleTypes: SelectOption[];
        prefill: BookingPagePrefill;
    } = $props();
</script>

<AppHead
    title={seo.title}
    description={seo.description}
    keywords={seo.keywords ?? []}
    canonicalUrl={seo.canonicalUrl ?? ''}
/>

<section class="relative overflow-hidden border-b border-border/60 bg-[linear-gradient(135deg,#0d2430_0%,#143847_60%,#1d4d5e_100%)] text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(3,174,210,0.26),transparent_30%),radial-gradient(circle_at_bottom_left,rgba(244,91,38,0.24),transparent_28%)]"></div>

    <div class="relative mx-auto flex max-w-7xl flex-col gap-6 px-4 py-12 md:px-6 md:py-16">
        <Button asChild variant="link" class="h-auto w-fit px-0 py-0 text-sm">
            {#snippet children(props)}
                <Link href={home()} {...props} class="text-white/74 hover:text-white">Kembali ke beranda</Link>
            {/snippet}
        </Button>

        <div class="max-w-3xl space-y-4">
            <Badge class="rounded-full border-0 bg-accent px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.24em] text-accent-foreground">
                Booking Home Service
            </Badge>
            <div class="space-y-3">
                <h1 class="text-balance text-4xl font-black tracking-[-0.04em] text-white md:text-6xl">
                    Isi data booking servis motor di rumah dengan alur yang fokus dan jelas.
                </h1>
                <p class="max-w-2xl text-pretty text-base leading-7 text-white/76 md:text-lg">
                    Proses booking dipisahkan dari landing page supaya Anda bisa lebih tenang menyelesaikan 4 langkah: pilih paket, isi data pelanggan dan motor, tentukan lokasi dan jadwal, lalu review sebelum kirim.
                </p>
            </div>
        </div>

        <div class="grid gap-3 md:grid-cols-3">
            <Card class="border-white/12 bg-white/8 text-white shadow-[0_28px_60px_-42px_rgba(0,0,0,0.5)] backdrop-blur-sm">
                <CardContent class="space-y-2 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/56">
                        Paket aktif
                    </p>
                    <p class="text-2xl font-black">{packages.length}</p>
                    <p class="text-sm leading-6 text-white/72">
                        Paket tetap bisa dipilih langsung dari halaman ini.
                    </p>
                </CardContent>
            </Card>

            <Card class="border-white/12 bg-white/8 text-white shadow-[0_28px_60px_-42px_rgba(0,0,0,0.5)] backdrop-blur-sm">
                <CardContent class="space-y-2 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/56">
                        Item custom
                    </p>
                    <p class="text-2xl font-black">{customItems.length}</p>
                    <p class="text-sm leading-6 text-white/72">
                        Mode custom tetap mendukung preview subtotal secara langsung.
                    </p>
                </CardContent>
            </Card>

            <Card class="border-white/12 bg-white/8 text-white shadow-[0_28px_60px_-42px_rgba(0,0,0,0.5)] backdrop-blur-sm">
                <CardContent class="space-y-2 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/56">
                        Slot contoh
                    </p>
                    <p class="text-2xl font-black">
                        {availableSlots.slice(0, 2).join(' - ')}
                    </p>
                    <p class="text-sm leading-6 text-white/72">
                        Harga preview tetap terlihat agar keputusan booking lebih transparan.
                    </p>
                </CardContent>
            </Card>
        </div>
    </div>
</section>

<BookingForm
    {packages}
    {customItems}
    {availableSlots}
    {packageTypes}
    {motorcycleTypes}
    {prefill}
    headingEyebrow="Alur booking"
    headingTitle="Lengkapi data booking home service motor"
    headingDescription="Mulai dari paket yang diinginkan, lalu lanjutkan data pelanggan, motor, lokasi, jadwal, dan review akhir. Jika Anda datang dari kartu paket, pilihan awal akan otomatis kami siapkan."
/>
