<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import { create as bookingCreate } from '@/routes/bookings';
    import { Button } from '@/components/ui/button';
    import PublicBrandMark from '@/components/public/PublicBrandMark.svelte';

    let {
        brandName,
        contactPhone,
        contactWhatsapp,
    }: {
        brandName: string;
        contactPhone: string;
        contactWhatsapp: string;
    } = $props();

    const auth = $derived(page.props.auth as { user: { name: string } | null });
    const currentUrl = $derived((page.url ?? '') as string);
    const isHomePage = $derived(currentUrl === '/');

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

<header
    class="sticky top-0 z-40 border-b border-border/70 bg-background/86 backdrop-blur-xl"
>
    <div
        class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 md:px-6"
    >
        <Link href="/" class="flex min-w-0 items-center gap-3">
            <PublicBrandMark
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white/90 p-1 shadow-sm ring-1 ring-black/5"
                imageClass="h-full w-full rounded-[0.85rem] object-contain"
            />
            <span class="flex min-w-0 flex-col">
                <span
                    class="truncate text-[11px] font-semibold uppercase tracking-[0.26em] text-primary"
                >
                    ASM MOTOR
                </span>
                <span class="truncate text-base font-semibold text-foreground">
                    {brandName}
                </span>
            </span>
        </Link>

        {#if isHomePage}
            <nav class="hidden items-center gap-6 lg:flex">
                <a
                    href="#services"
                    class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                >
                    Layanan
                </a>
                <a
                    href="#packages"
                    class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                >
                    Paket
                </a>
                <a
                    href="#faq"
                    class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                >
                    FAQ
                </a>
            </nav>
        {/if}

        <div class="flex items-center gap-2 md:gap-3">
            {#if !isHomePage}
                <Button
                    asChild
                    variant="ghost"
                    size="sm"
                    class="hidden rounded-full px-4 text-muted-foreground hover:text-foreground md:inline-flex"
                >
                    {#snippet children(props)}
                        <Link href="/" {...props}>Beranda</Link>
                    {/snippet}
                </Button>
            {/if}

            <a
                href={whatsappUrl}
                target="_blank"
                rel="noreferrer"
                class="hidden text-sm font-medium text-muted-foreground transition-colors hover:text-primary xl:inline-flex"
            >
                {displayedPhone}
            </a>

            <Button
                asChild
                size="sm"
                class="h-11 rounded-full bg-secondary px-5 text-sm font-semibold text-secondary-foreground shadow-[0_20px_40px_-22px_rgb(var(--brand-secondary-rgb)/0.85)] hover:bg-secondary/92"
            >
                {#snippet children(props)}
                    <Link href={bookingCreate()} {...props}
                        >Booking Sekarang</Link
                    >
                {/snippet}
            </Button>

            {#if auth.user}
                <Button
                    asChild
                    variant="outline"
                    size="sm"
                    class="hidden rounded-full px-4 md:inline-flex"
                >
                    {#snippet children(props)}
                        <Link href="/admin/dashboard" {...props}>Admin</Link>
                    {/snippet}
                </Button>
            {/if}
        </div>
    </div>
</header>
