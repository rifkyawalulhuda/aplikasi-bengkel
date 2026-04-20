<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import BookingStatusBadge from '@/components/admin/BookingStatusBadge.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import {
        index,
        destroy as destroyBooking,
        show,
    } from '@/actions/App/Http/Controllers/Admin/BookingManagementController';
    import { formatCurrency } from '@/lib/utils';
    import type {
        AdminBookingListFilters,
        AdminBookingListItem,
        PaginationMeta,
    } from '@/types';

    let {
        bookings,
        filters,
    }: {
        bookings: PaginationMeta<AdminBookingListItem>;
        filters: AdminBookingListFilters;
    } = $props();

    const sortConfig = {
        bookingCode: {
            default: 'booking_code_desc',
            asc: 'booking_code_asc',
            desc: 'booking_code_desc',
        },
        customerName: {
            default: 'customer_name_asc',
            asc: 'customer_name_asc',
            desc: 'customer_name_desc',
        },
        serviceDate: {
            default: 'service_date_asc',
            asc: 'service_date_asc',
            desc: 'service_date_desc',
        },
        totalPrice: {
            default: 'total_price_desc',
            asc: 'total_price_asc',
            desc: 'total_price_desc',
        },
    } as const;

    function buildFiltersQuery(sort: string): string {
        const params = new URLSearchParams();

        if (filters.search.trim() !== '') {
            params.set('search', filters.search.trim());
        }

        if (filters.status.trim() !== '') {
            params.set('status', filters.status.trim());
        }

        if (filters.date.trim() !== '') {
            params.set('date', filters.date.trim());
        }

        params.set('sort', sort);

        const queryString = params.toString();

        return queryString === ''
            ? index().url
            : `${index().url}?${queryString}`;
    }

    function sortHref(
        currentSort: string,
        defaultSort: string,
        ascSort: string,
        descSort: string,
    ): string {
        const nextSort =
            currentSort === ascSort
                ? descSort
                : currentSort === descSort
                  ? ascSort
                  : defaultSort;

        return buildFiltersQuery(nextSort);
    }

    function sortIndicator(
        currentSort: string,
        ascSort: string,
        descSort: string,
    ): string {
        if (currentSort === ascSort) {
            return '↑';
        }

        if (currentSort === descSort) {
            return '↓';
        }

        return '↕';
    }

    function confirmDeleteBooking(bookingCode: string): void {
        const confirmed = window.confirm(
            `Hapus booking ${bookingCode}? Data booking akan dihapus permanen dan tidak bisa dikembalikan.`,
        );

        if (!confirmed) {
            return;
        }

        router.delete(destroyBooking(bookingCode).url, {
            preserveScroll: true,
        });
    }
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
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <p class="text-muted-foreground">Total</p>
                                    <p class="font-semibold text-foreground">
                                        {formatCurrency(booking.totalPrice)}
                                    </p>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
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

                                    <Button
                                        type="button"
                                        size="sm"
                                        variant="destructive"
                                        onclick={() =>
                                            confirmDeleteBooking(
                                                booking.bookingCode,
                                            )}
                                        aria-label={`Hapus booking ${booking.bookingCode}`}
                                    >
                                        Hapus
                                    </Button>
                                </div>
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
                            <th class="px-3 py-3 font-medium">
                                <Link
                                    href={sortHref(
                                        filters.sort,
                                        sortConfig.bookingCode.default,
                                        sortConfig.bookingCode.asc,
                                        sortConfig.bookingCode.desc,
                                    )}
                                    class="inline-flex items-center gap-1 rounded-md px-1 py-0.5 transition hover:text-foreground"
                                    aria-label="Urutkan berdasarkan kode booking"
                                >
                                    Kode
                                    <span class="text-xs">
                                        {sortIndicator(
                                            filters.sort,
                                            sortConfig.bookingCode.asc,
                                            sortConfig.bookingCode.desc,
                                        )}
                                    </span>
                                </Link>
                            </th>
                            <th class="px-3 py-3 font-medium">
                                <Link
                                    href={sortHref(
                                        filters.sort,
                                        sortConfig.customerName.default,
                                        sortConfig.customerName.asc,
                                        sortConfig.customerName.desc,
                                    )}
                                    class="inline-flex items-center gap-1 rounded-md px-1 py-0.5 transition hover:text-foreground"
                                    aria-label="Urutkan berdasarkan nama pelanggan"
                                >
                                    Pelanggan
                                    <span class="text-xs">
                                        {sortIndicator(
                                            filters.sort,
                                            sortConfig.customerName.asc,
                                            sortConfig.customerName.desc,
                                        )}
                                    </span>
                                </Link>
                            </th>
                            <th class="px-3 py-3 font-medium">Layanan</th>
                            <th class="px-3 py-3 font-medium">
                                <Link
                                    href={sortHref(
                                        filters.sort,
                                        sortConfig.serviceDate.default,
                                        sortConfig.serviceDate.asc,
                                        sortConfig.serviceDate.desc,
                                    )}
                                    class="inline-flex items-center gap-1 rounded-md px-1 py-0.5 transition hover:text-foreground"
                                    aria-label="Urutkan berdasarkan tanggal servis"
                                >
                                    Jadwal
                                    <span class="text-xs">
                                        {sortIndicator(
                                            filters.sort,
                                            sortConfig.serviceDate.asc,
                                            sortConfig.serviceDate.desc,
                                        )}
                                    </span>
                                </Link>
                            </th>
                            <th class="px-3 py-3 font-medium">Status</th>
                            <th class="px-3 py-3 font-medium text-right"
                            >
                                <Link
                                    href={sortHref(
                                        filters.sort,
                                        sortConfig.totalPrice.default,
                                        sortConfig.totalPrice.asc,
                                        sortConfig.totalPrice.desc,
                                    )}
                                    class="inline-flex items-center justify-end gap-1 rounded-md px-1 py-0.5 transition hover:text-foreground"
                                    aria-label="Urutkan berdasarkan total harga"
                                >
                                    Total
                                    <span class="text-xs">
                                        {sortIndicator(
                                            filters.sort,
                                            sortConfig.totalPrice.asc,
                                            sortConfig.totalPrice.desc,
                                        )}
                                    </span>
                                </Link>
                            </th>
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
                                    <div class="flex items-center justify-end gap-2">
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

                                        <Button
                                            type="button"
                                            size="sm"
                                            variant="destructive"
                                            onclick={() =>
                                                confirmDeleteBooking(
                                                    booking.bookingCode,
                                                )}
                                            aria-label={`Hapus booking ${booking.bookingCode}`}
                                        >
                                            Hapus
                                        </Button>
                                    </div>
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
