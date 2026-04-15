<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import BookingStatusBadge from '@/components/admin/BookingStatusBadge.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { show } from '@/actions/App/Http/Controllers/Admin/BookingManagementController';
    import { formatCurrency } from '@/lib/utils';
    import type { AdminBookingListItem, PaginationMeta } from '@/types';

    let {
        bookings,
    }: {
        bookings: PaginationMeta<AdminBookingListItem>;
    } = $props();
</script>

<Card class="border-primary/16 bg-white/88 shadow-[0_24px_54px_-40px_rgb(var(--brand-primary-rgb)/0.36)] backdrop-blur-sm">
    <CardHeader
        class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between"
    >
        <div class="space-y-1">
            <CardTitle>Daftar booking</CardTitle>
            <p class="text-sm leading-6 text-muted-foreground">
                Menampilkan {bookings.from ?? 0}-{bookings.to ?? 0} dari {bookings.total}
                booking.
            </p>
        </div>
    </CardHeader>

    <CardContent class="space-y-4">
        {#if bookings.data.length === 0}
            <div
                class="rounded-2xl border border-dashed border-border px-4 py-10 text-center text-sm leading-6 text-muted-foreground"
            >
                Belum ada booking yang cocok dengan filter saat ini.
            </div>
        {:else}
            <div class="grid gap-3 lg:hidden">
                {#each bookings.data as booking (booking.id)}
                    <article
                        class="rounded-2xl border border-primary/12 bg-white/72 p-4 shadow-sm"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <p class="font-semibold text-foreground">
                                    {booking.bookingCode}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    {booking.customerName}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    {booking.customerPhone}
                                </p>
                            </div>

                            <BookingStatusBadge
                                status={booking.status}
                                label={booking.statusLabel}
                            />
                        </div>

                        <div class="mt-4 grid gap-3 text-sm">
                            <div>
                                <p class="text-muted-foreground">Layanan</p>
                                <p class="font-medium text-foreground">
                                    {booking.packageName}
                                </p>
                            </div>
                            <div>
                                <p class="text-muted-foreground">Jadwal</p>
                                <p class="font-medium text-foreground">
                                    {booking.serviceDateLabel ?? '-'}
                                </p>
                                <p class="text-muted-foreground">
                                    {booking.serviceTime}
                                </p>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-muted-foreground">Total</p>
                                    <p class="font-semibold text-foreground">
                                        {formatCurrency(booking.totalPrice)}
                                    </p>
                                </div>

                                <Button asChild size="sm" variant="outline">
                                    {#snippet children(props)}
                                        <Link
                                            href={show(booking.bookingCode).url}
                                            aria-label={`Buka detail booking ${booking.bookingCode}`}
                                            {...props}
                                        >
                                            Detail
                                        </Link>
                                    {/snippet}
                                </Button>
                            </div>

                            {#if booking.requiresManualReview}
                                <span
                                    class="inline-flex w-fit rounded-full border border-accent/45 bg-accent/25 px-2.5 py-1 text-xs font-medium text-accent-foreground"
                                >
                                    Perlu review manual
                                </span>
                            {/if}
                        </div>
                    </article>
                {/each}
            </div>

            <div class="hidden overflow-x-auto lg:block">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-muted-foreground">
                        <tr class="border-b border-border/70">
                            <th class="px-3 py-3 font-medium">Kode</th>
                            <th class="px-3 py-3 font-medium">Pelanggan</th>
                            <th class="px-3 py-3 font-medium">Layanan</th>
                            <th class="px-3 py-3 font-medium">Jadwal</th>
                            <th class="px-3 py-3 font-medium">Status</th>
                            <th class="px-3 py-3 font-medium text-right"
                                >Total</th
                            >
                            <th class="px-3 py-3 font-medium text-right"
                                >Aksi</th
                            >
                        </tr>
                    </thead>
                    <tbody>
                        {#each bookings.data as booking (booking.id)}
                            <tr
                                class="border-b border-border/60 last:border-b-0"
                            >
                                <td class="px-3 py-4 align-top">
                                    <div class="space-y-2">
                                        <p class="font-medium text-foreground">
                                            {booking.bookingCode}
                                        </p>
                                        {#if booking.requiresManualReview}
                                            <span
                                                class="inline-flex rounded-full border border-accent/45 bg-accent/25 px-2.5 py-1 text-xs font-medium text-accent-foreground"
                                            >
                                                Manual review
                                            </span>
                                        {/if}
                                    </div>
                                </td>
                                <td class="px-3 py-4 align-top">
                                    <p class="font-medium text-foreground">
                                        {booking.customerName}
                                    </p>
                                    <p class="text-muted-foreground">
                                        {booking.customerPhone}
                                    </p>
                                </td>
                                <td class="px-3 py-4 align-top">
                                    <p class="text-foreground">
                                        {booking.packageName}
                                    </p>
                                </td>
                                <td class="px-3 py-4 align-top">
                                    <p class="text-foreground">
                                        {booking.serviceDateLabel ?? '-'}
                                    </p>
                                    <p class="text-muted-foreground">
                                        {booking.serviceTime}
                                    </p>
                                </td>
                                <td class="px-3 py-4 align-top">
                                    <BookingStatusBadge
                                        status={booking.status}
                                        label={booking.statusLabel}
                                    />
                                </td>
                                <td
                                    class="px-3 py-4 text-right align-top font-medium text-foreground"
                                >
                                    {formatCurrency(booking.totalPrice)}
                                </td>
                                <td class="px-3 py-4 text-right align-top">
                                    <Button asChild size="sm" variant="outline">
                                        {#snippet children(props)}
                                            <Link
                                                href={show(booking.bookingCode)
                                                    .url}
                                                aria-label={`Buka detail booking ${booking.bookingCode}`}
                                                {...props}
                                            >
                                                Detail
                                            </Link>
                                        {/snippet}
                                    </Button>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        {/if}

        <div
            class="flex flex-col gap-3 border-t border-border/70 pt-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <p class="text-sm text-muted-foreground">
                Halaman {bookings.current_page} dari {bookings.last_page}
            </p>

            <div class="flex items-center gap-2">
                <Button
                    asChild
                    variant="outline"
                    size="sm"
                    disabled={!bookings.prev_page_url}
                >
                    {#snippet children(props)}
                        <Link
                            href={bookings.prev_page_url ?? '#'}
                            aria-label="Buka halaman booking sebelumnya"
                            {...props}
                        >
                            Sebelumnya
                        </Link>
                    {/snippet}
                </Button>
                <Button
                    asChild
                    variant="outline"
                    size="sm"
                    disabled={!bookings.next_page_url}
                >
                    {#snippet children(props)}
                        <Link
                            href={bookings.next_page_url ?? '#'}
                            aria-label="Buka halaman booking berikutnya"
                            {...props}
                        >
                            Berikutnya
                        </Link>
                    {/snippet}
                </Button>
            </div>
        </div>
    </CardContent>
</Card>
