<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import BookingForm from '@/components/public/BookingForm.svelte';
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
