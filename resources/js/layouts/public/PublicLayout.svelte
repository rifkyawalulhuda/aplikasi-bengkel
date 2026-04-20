<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import type { Snippet } from 'svelte';
    import PublicFooter from '@/components/public/PublicFooter.svelte';
    import PublicHeader from '@/components/public/PublicHeader.svelte';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();

    const workshop = $derived(page.props.workshop as {
        brandName: string;
        contactPhone: string;
        contactWhatsapp: string;
        footerLocation: {
            address: string;
            latitude: string;
            longitude: string;
        };
        serviceAreas: string[];
    });
</script>

<div class="min-h-screen">
    <PublicHeader
        brandName={workshop.brandName}
        contactPhone={workshop.contactPhone}
        contactWhatsapp={workshop.contactWhatsapp}
    />

    <main>{@render children?.()}</main>

    <PublicFooter
        brandName={workshop.brandName}
        contactPhone={workshop.contactPhone}
        contactWhatsapp={workshop.contactWhatsapp}
        footerLocation={workshop.footerLocation}
        serviceAreas={workshop.serviceAreas}
    />
</div>
