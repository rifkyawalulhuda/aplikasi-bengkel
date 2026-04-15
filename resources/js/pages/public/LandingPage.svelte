<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import BookingCtaSection from '@/components/public/BookingCtaSection.svelte';
    import FaqSection from '@/components/public/FaqSection.svelte';
    import HeroSection from '@/components/public/HeroSection.svelte';
    import HowItWorksSection from '@/components/public/HowItWorksSection.svelte';
    import PackageCardsSection from '@/components/public/PackageCardsSection.svelte';
    import ServiceHighlights from '@/components/public/ServiceHighlights.svelte';
    import type {
        CustomServiceItemSummary,
        FaqItem,
        LandingCta,
        LandingHighlight,
        LandingStep,
        SeoMetadata,
        ServicePackageSummary,
    } from '@/types';

    let {
        seo,
        tagline,
        serviceAreas,
        highlights,
        howItWorks,
        faqs,
        cta,
        packages,
        customItems,
        availableSlots,
    }: {
        seo: SeoMetadata;
        tagline: string;
        serviceAreas: string[];
        highlights: LandingHighlight[];
        howItWorks: LandingStep[];
        faqs: FaqItem[];
        cta: LandingCta;
        packages: ServicePackageSummary[];
        customItems: CustomServiceItemSummary[];
        availableSlots: string[];
    } = $props();

    const organizationSchema = $derived(
        JSON.stringify({
            '@context': 'https://schema.org',
            '@type': 'LocalBusiness',
            name: seo.title,
            description: seo.description,
            areaServed: serviceAreas,
            keywords: seo.keywords ?? [],
            url: seo.canonicalUrl,
        }),
    );

    const faqSchema = $derived(
        JSON.stringify({
            '@context': 'https://schema.org',
            '@type': 'FAQPage',
            mainEntity: faqs.map((faq) => ({
                '@type': 'Question',
                name: faq.question,
                acceptedAnswer: {
                    '@type': 'Answer',
                    text: faq.answer,
                },
            })),
        }),
    );
</script>

<AppHead
    title={seo.title}
    description={seo.description}
    keywords={seo.keywords ?? []}
    canonicalUrl={seo.canonicalUrl ?? ''}
>
    {#snippet children()}
        <script type="application/ld+json">
            {organizationSchema}
        </script>
        <script type="application/ld+json">
            {faqSchema}
        </script>
    {/snippet}
</AppHead>

<HeroSection
    {tagline}
    {serviceAreas}
    packageCount={packages.length}
    customItemsCount={customItems.length}
/>

<ServiceHighlights {highlights} />

<PackageCardsSection
    {packages}
    {customItems}
/>

<HowItWorksSection steps={howItWorks} />

<FaqSection {faqs} />

<BookingCtaSection
    {cta}
    {availableSlots}
    packageCount={packages.length}
    customItemsCount={customItems.length}
/>
