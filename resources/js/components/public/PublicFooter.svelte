<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { create as bookingCreate } from '@/routes/bookings';
    import PublicBrandMark from '@/components/public/PublicBrandMark.svelte';

    let {
        brandName,
        contactPhone,
        contactWhatsapp,
        serviceAreas,
    }: {
        brandName: string;
        contactPhone: string;
        contactWhatsapp: string;
        serviceAreas: string[];
    } = $props();

    function extractDigits(value: string): string {
        return value.replace(/\D/g, '');
    }

    function formatDisplayPhone(value: string): string {
        const digits = extractDigits(value);

        if (digits.startsWith('62')) {
            return `0${digits.slice(2)}`;
        }

        return digits || value;
    }

    function buildWhatsappUrl(value: string, fallback: string): string {
        const digits = extractDigits(value || fallback);

        if (digits === '') {
            return '#';
        }

        const normalizedDigits = digits.startsWith('0')
            ? `62${digits.slice(1)}`
            : digits;

        return `https://wa.me/${normalizedDigits}`;
    }

    const displayedPhone = $derived(formatDisplayPhone(contactPhone));
    const whatsappUrl = $derived(
        buildWhatsappUrl(contactWhatsapp, contactPhone),
    );
</script>

<footer
    class="border-t border-primary/20 bg-[linear-gradient(180deg,#03AED2_0%,#0295B4_100%)] text-white"
>
    <div
        class="mx-auto grid max-w-7xl gap-10 px-4 py-12 md:grid-cols-[1.15fr_0.85fr_0.9fr] md:px-6 md:py-14"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center gap-3">
                <PublicBrandMark
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white p-1 shadow-sm"
                    imageClass="h-full w-full rounded-[0.85rem] object-contain"
                    alt="Logo Bengkel Home Service"
                />
                <div>
                    <p
                        class="text-[11px] font-semibold uppercase tracking-[0.24em] text-white/80"
                    >
                        Home Service
                    </p>
                    <p class="text-lg font-bold tracking-[-0.02em] text-white">
                        {brandName}
                    </p>
                </div>
            </div>
            <p class="max-w-md text-sm leading-7 text-white/84">
                Jl. badami ciherang, teluk jambe barat, kab. karawang
            </p>
            <p class="text-sm font-medium text-white">
                Karawang dan sekitarnya
            </p>
        </div>

        <div class="flex flex-col gap-3">
            <p
                class="text-sm font-semibold uppercase tracking-[0.16em] text-white"
            >
                Navigasi
            </p>
            <div class="flex flex-col gap-2 text-sm text-white/84">
                <a class="transition-colors hover:text-accent" href="#services"
                    >Layanan</a
                >
                <a class="transition-colors hover:text-accent" href="#packages"
                    >Paket</a
                >
                <a class="transition-colors hover:text-accent" href="#faq"
                    >FAQ</a
                >
                <Link
                    class="transition-colors hover:text-accent"
                    href={bookingCreate()}>Booking Sekarang</Link
                >
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <p
                class="text-sm font-semibold uppercase tracking-[0.16em] text-white"
            >
                Kontak
            </p>
            <p class="text-sm leading-7 text-white/84">
                Area layanan : {serviceAreas.join(', ')}
            </p>
            <a
                href={whatsappUrl}
                target="_blank"
                rel="noreferrer"
                class="text-sm font-medium text-white/84 transition-colors hover:text-accent"
            >
                {displayedPhone}
            </a>
            <Link
                class="text-sm text-white/84 transition-colors hover:text-accent"
                href="/admin/login"
            >
                Login admin
            </Link>
        </div>
    </div>

    <div class="border-t border-white/20">
        <div
            class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 text-xs text-white/72 md:flex-row md:items-center md:justify-between md:px-6"
        >
            <p>
                &copy; 2026 {brandName}. Motor Karawang. All rights reserved.
            </p>
        </div>
    </div>
</footer>
