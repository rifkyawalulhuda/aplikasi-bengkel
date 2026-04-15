<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import ArrowRight from 'lucide-svelte/icons/arrow-right';
    import CalendarDays from 'lucide-svelte/icons/calendar-days';
    import CircleCheckBig from 'lucide-svelte/icons/circle-check-big';
    import Clock3 from 'lucide-svelte/icons/clock-3';
    import MessageCircle from 'lucide-svelte/icons/message-circle';
    import ShieldCheck from 'lucide-svelte/icons/shield-check';
    import AppHead from '@/components/AppHead.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { home } from '@/routes';
    import { show } from '@/routes/bookings/public';
    import { toUrl } from '@/lib/utils';

    let {
        booking,
        bookingCode,
        whatsAppUrl,
    }: {
        booking: {
            code: string;
            serviceDate: string | null;
            serviceTime: string;
            status: string;
            statusLabel: string;
        } | null;
        bookingCode: string;
        whatsAppUrl: string;
    } = $props();

    const code = $derived(booking?.code ?? bookingCode ?? '-');
    const summaryUrl = $derived(booking?.code ? toUrl(show(booking.code)) : null);
    const scheduleItems = $derived([
        {
            label: 'Tanggal servis',
            value: booking?.serviceDate ?? 'Menunggu konfirmasi admin',
            icon: CalendarDays,
        },
        {
            label: 'Jam servis',
            value: booking?.serviceTime ?? 'Akan dikonfirmasi admin',
            icon: Clock3,
        },
        {
            label: 'Status saat ini',
            value: booking?.statusLabel ?? 'Booking diterima',
            icon: ShieldCheck,
        },
    ]);
</script>

<AppHead title="Booking Berhasil" />

<section class="relative overflow-hidden px-4 py-10 md:px-6 md:py-14">
    <div class="absolute inset-x-0 top-0 -z-10 h-72 bg-[radial-gradient(circle_at_top,rgb(var(--brand-primary-rgb)/0.18),transparent_55%),radial-gradient(circle_at_top_left,rgb(var(--brand-accent-rgb)/0.14),transparent_40%)]"></div>

    <div class="mx-auto flex max-w-3xl flex-col gap-6">
        <Card class="overflow-hidden border-border/70 shadow-sm">
            <CardHeader class="gap-5 bg-[linear-gradient(135deg,rgba(var(--brand-primary-rgb),0.12),rgba(255,253,248,1)_46%,rgba(var(--brand-accent-rgb),0.18))]">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary/14 text-primary">
                    <CircleCheckBig class="size-7" />
                </div>

                <div class="space-y-3">
                    <Badge variant="secondary" class="rounded-full px-3 py-1 text-[11px] uppercase tracking-[0.2em]">
                        Booking diterima
                    </Badge>
                    <CardTitle class="max-w-xl text-2xl leading-tight md:text-3xl">
                        Booking servis kamu sudah masuk ke sistem dan siap diproses admin.
                    </CardTitle>
                    <p class="max-w-2xl text-sm leading-7 text-muted-foreground md:text-base">
                        Simpan kode booking di bawah ini. Admin akan memakai kode ini saat konfirmasi jadwal dan tindak lanjut layanan.
                    </p>
                </div>
            </CardHeader>

            <CardContent class="flex flex-col gap-6 p-5 md:p-7">
                <div class="rounded-3xl border border-dashed border-primary/28 bg-primary/8 p-5 text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-primary">
                        Kode booking
                    </p>
                    <p class="mt-3 text-2xl font-semibold tracking-[0.18em] text-foreground md:text-3xl">
                        {code}
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    {#each scheduleItems as item}
                        <div class="rounded-2xl border border-border/70 bg-card px-4 py-4">
                            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                <item.icon class="size-5" />
                            </div>
                            <p class="text-xs font-medium uppercase tracking-[0.16em] text-muted-foreground">
                                {item.label}
                            </p>
                            <p class="mt-2 text-sm font-semibold leading-6 text-foreground">
                                {item.value}
                            </p>
                        </div>
                    {/each}
                </div>

                <div class="rounded-2xl border border-border/70 bg-muted p-4 text-sm leading-7 text-muted-foreground">
                    <p class="font-semibold text-foreground">Langkah selanjutnya</p>
                    <p>
                        Jika kamu butuh konfirmasi lebih cepat, kirim kode booking ini ke WhatsApp admin. Halaman publik hanya menampilkan informasi aman seperti kode, jadwal, dan status booking.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <Button asChild class="w-full sm:w-auto">
                        {#snippet children(props)}
                            <a href={whatsAppUrl} target="_blank" rel="noreferrer" {...props}>
                                <MessageCircle class="size-4" />
                                Chat admin via WhatsApp
                            </a>
                        {/snippet}
                    </Button>

                    {#if summaryUrl}
                        <Button asChild variant="outline" class="w-full sm:w-auto">
                            {#snippet children(props)}
                                <Link href={summaryUrl} {...props}>
                                    Lihat ringkasan booking
                                    <ArrowRight class="size-4" />
                                </Link>
                            {/snippet}
                        </Button>
                    {/if}

                    <Button asChild variant="ghost" class="w-full sm:w-auto">
                        {#snippet children(props)}
                            <Link href={home()} {...props}>Kembali ke beranda</Link>
                        {/snippet}
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</section>
