<script lang="ts">
    import { Form, Link } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import BookingsTable from '@/components/admin/BookingsTable.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import { index } from '@/actions/App/Http/Controllers/Admin/BookingManagementController';
    import type {
        AdminBookingListFilters,
        AdminBookingListItem,
        AdminBookingStatusOption,
        PaginationMeta,
    } from '@/types';

    let {
        bookings,
        filters,
        statusOptions,
    }: {
        bookings: PaginationMeta<AdminBookingListItem>;
        filters: AdminBookingListFilters;
        statusOptions: AdminBookingStatusOption[];
    } = $props();
</script>

<AppHead title="Manajemen Booking" />

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <h2 class="text-2xl font-semibold tracking-tight text-[#D12052]">
            Manajemen booking
        </h2>
        <p class="text-sm leading-6 text-[#D12052]">
            Cari booking berdasarkan kode, nama pelanggan, atau nomor telepon
            lalu buka detail untuk update status dan catatan operasional.
        </p>
    </div>

    <Card class="border-primary/16 bg-white/88 shadow-[0_24px_54px_-40px_rgb(var(--brand-primary-rgb)/0.36)] backdrop-blur-sm">
        <CardContent class="pt-6">
            <Form
                action={index().url}
                method="get"
                class="grid gap-4 lg:grid-cols-[minmax(0,1.6fr)_0.8fr_0.8fr_auto_auto] lg:items-end"
            >
                {#snippet children({ processing })}
                    <div class="grid gap-2">
                        <Label for="search">Cari booking</Label>
                        <Input
                            id="search"
                            name="search"
                            value={filters.search}
                            placeholder="Kode booking, nama, atau nomor telepon"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="status">Status</Label>
                        <select
                            id="status"
                            name="status"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none"
                        >
                            <option value="">Semua status</option>
                            {#each statusOptions as option (option.value)}
                                <option
                                    value={option.value}
                                    selected={filters.status === option.value}
                                >
                                    {option.label}
                                </option>
                            {/each}
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <Label for="date">Tanggal servis</Label>
                        <Input
                            id="date"
                            name="date"
                            type="date"
                            value={filters.date}
                        />
                    </div>

                    <Button type="submit" disabled={processing}>
                        {#if processing}
                            <Spinner class="size-4" />
                            Memuat...
                        {:else}
                            Terapkan
                        {/if}
                    </Button>

                    <Button asChild variant="outline" disabled={processing}>
                        {#snippet children(props)}
                            <Link
                                href={index().url}
                                aria-label="Reset filter booking"
                                {...props}>Reset</Link
                            >
                        {/snippet}
                    </Button>
                {/snippet}
            </Form>
        </CardContent>
    </Card>

    <BookingsTable {bookings} />
</div>
