<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import CalendarDays from 'lucide-svelte/icons/calendar-days';
    import Clock3 from 'lucide-svelte/icons/clock-3';
    import MessageCircle from 'lucide-svelte/icons/message-circle';
    import ReceiptText from 'lucide-svelte/icons/receipt-text';
    import ShieldCheck from 'lucide-svelte/icons/shield-check';
    import Wrench from 'lucide-svelte/icons/wrench';
    import AppHead from '@/components/AppHead.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { home } from '@/routes';

    let {
        booking,
        whatsAppUrl,
    }: {
        booking: {
            code: string;
            packageName: string;
            serviceDate: string | null;
            serviceTime: string;
            status: string;
            statusLabel: string;
            totalPrice: number;
            customItems: { name: string; qty: number; subtotal: number }[];
        };
        whatsAppUrl: string;
    } = $props();

    const summaryCards = $derived([
        {
            label: 'Tanggal servis',
            value: booking.serviceDate ?? '-',
            icon: CalendarDays,
        },
        {
            label: 'Jam servis',
            value: booking.serviceTime,
            icon: Clock3,
        },
        {
            label: 'Status booking',
            value: booking.statusLabel,
            icon: ShieldCheck,
        },
        {
            label: 'Total estimasi',
            value: `Rp ${booking.totalPrice.toLocaleString('id-ID')}`,
            icon: ReceiptText,
        },
    ]);
</script>

<AppHead title={`Ringkasan ${booking.code}`} />

<section class="relative overflow-hidden px-4 py-10 md:px-6 md:py-14">
    <div class="absolute inset-x-0 top-0 -z-10 h-80 bg-[radial-gradient(circle_at_top,rgb(var(--brand-accent-rgb)/0.18),transparent_55%),radial-gradient(circle_at_top_right,rgb(var(--brand-primary-rgb)/0.14),transparent_42%)]"></div>

    <div class="mx-auto flex max-w-4xl flex-col gap-6">
        <Card class="overflow-hidden border-border/70 shadow-sm">
            <CardHeader class="gap-4 bg-[linear-gradient(135deg,rgba(var(--brand-accent-rgb),0.18),rgba(255,253,248,1)_44%,rgba(var(--brand-primary-rgb),0.1))]">
                <Badge variant="outline" class="rounded-full px-3 py-1 text-[11px] uppercase tracking-[0.2em]">
                    Ringkasan publik
                </Badge>
                <div class="space-y-2">
                    <CardTitle class="text-2xl leading-tight md:text-3xl">
                        No Booking : {booking.code}
                    </CardTitle>
                    <p class="max-w-2xl text-sm leading-7 text-muted-foreground md:text-base">
                        Halaman ini hanya menampilkan informasi publik yang diperlukan untuk pengecekan status booking. Detail pribadi seperti alamat, nomor telepon, dan catatan internal tidak ditampilkan di sini.
                    </p>
                </div>
            </CardHeader>

            <CardContent class="flex flex-col gap-6 p-5 md:p-7">
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    {#each summaryCards as item}
                        <div class="rounded-2xl border border-border/70 bg-card p-4">
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

                <div class="rounded-3xl border border-border/70 bg-muted p-5">
                    <div class="flex items-start gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-background shadow-sm">
                            <Wrench class="size-5 text-foreground" />
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm font-semibold text-foreground">Paket layanan</p>
                            <p class="text-sm leading-7 text-muted-foreground">
                                {booking.packageName}
                            </p>
                        </div>
                    </div>
                </div>

                {#if booking.customItems.length > 0}
                    <div class="rounded-3xl border border-border/70 bg-card p-5">
                        <div class="mb-4 space-y-1">
                            <p class="text-sm font-semibold text-foreground">Item custom</p>
                            <p class="text-sm text-muted-foreground">
                                Rincian berikut berasal dari snapshot booking dan tidak berubah walau harga master di admin nanti diperbarui.
                            </p>
                        </div>

                        <div class="flex flex-col divide-y divide-border/70">
                            {#each booking.customItems as item}
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <div>
                                        <p class="text-sm font-medium text-foreground">{item.name}</p>
                                        <p class="text-xs uppercase tracking-[0.14em] text-muted-foreground">
                                            Qty {item.qty}
                                        </p>
                                    </div>
                                    <p class="text-sm font-semibold text-foreground">
                                        Rp {item.subtotal.toLocaleString('id-ID')}
                                    </p>
                                </div>
                            {/each}
                        </div>
                    </div>
                {/if}

                <div class="flex flex-col gap-3 sm:flex-row">
                    <Button asChild class="w-full sm:w-auto">
                        {#snippet children(props)}
                            <a href={whatsAppUrl} target="_blank" rel="noreferrer" {...props}>
                                <MessageCircle class="size-4" />
                                Hubungi admin
                            </a>
                        {/snippet}
                    </Button>

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
