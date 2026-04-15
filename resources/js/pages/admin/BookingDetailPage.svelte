<script lang="ts">
    import { Form, Link } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import BookingDetailCard from '@/components/admin/BookingDetailCard.svelte';
    import BookingStatusBadge from '@/components/admin/BookingStatusBadge.svelte';
    import CopyValueButton from '@/components/admin/CopyValueButton.svelte';
    import InputError from '@/components/InputError.svelte';
    import StatusHistoryTimeline from '@/components/admin/StatusHistoryTimeline.svelte';
    import { Button } from '@/components/ui/button';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import {
        index,
        updateNotes,
        updateStatus,
    } from '@/actions/App/Http/Controllers/Admin/BookingManagementController';
    import { formatCurrency } from '@/lib/utils';
    import type { AdminBookingDetail, AdminBookingStatusOption } from '@/types';

    let {
        booking,
        statusOptions,
    }: {
        booking: AdminBookingDetail;
        statusOptions: AdminBookingStatusOption[];
    } = $props();
</script>

<AppHead title={`Booking ${booking.bookingCode}`} />

<div class="flex flex-col gap-6">
    <div
        class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between"
    >
        <div class="space-y-2">
            <Button asChild variant="link" class="h-auto px-0 py-0 text-sm">
                {#snippet children(props)}
                    <Link href={index().url} {...props}
                        >Kembali ke daftar booking</Link
                    >
                {/snippet}
            </Button>

            <div class="flex flex-wrap items-center gap-3">
                <h2
                    class="text-2xl font-semibold tracking-tight text-[#D12052]"
                >
                    {booking.bookingCode}
                </h2>
                <BookingStatusBadge
                    status={booking.status}
                    label={booking.statusLabel}
                />
                {#if booking.requiresManualReview}
                    <span
                        class="rounded-full border border-accent/45 bg-accent/25 px-3 py-1 text-xs font-semibold text-accent-foreground"
                    >
                        Perlu review manual
                    </span>
                {/if}
            </div>

            <p class="text-sm leading-6 text-[#D12052]">
                Jadwal servis {booking.service.serviceDateLabel ?? '-'} pukul {booking
                    .service.serviceTime}.
            </p>
        </div>

        <div class="grid gap-2 text-sm text-muted-foreground">
            <p>Dikonfirmasi: {booking.confirmedAt ?? '-'}</p>
            <p>Selesai: {booking.completedAt ?? '-'}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_0.8fr]">
        <div class="space-y-6">
            <div class="grid gap-6 lg:grid-cols-2">
                <BookingDetailCard
                    title="Info pelanggan"
                    description="Data kontak utama untuk koordinasi servis."
                >
                    <div class="space-y-3 text-sm leading-6">
                        <div>
                            <p class="text-muted-foreground">Nama</p>
                            <p class="font-medium text-foreground">
                                {booking.customer.name}
                            </p>
                        </div>
                        <div>
                            <p class="text-muted-foreground">Email</p>
                            <p class="font-medium text-foreground">
                                {booking.customer.email}
                            </p>
                        </div>
                        <div
                            class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between"
                        >
                            <p class="text-muted-foreground">
                                Telepon / WhatsApp
                            </p>
                            <div
                                class="flex flex-col items-start gap-2 sm:items-end"
                            >
                                <p class="font-medium text-foreground">
                                    {booking.customer.phone}
                                </p>
                                <CopyValueButton
                                    value={booking.customer.phone}
                                    label="nomor WhatsApp customer"
                                />
                            </div>
                        </div>
                    </div>
                </BookingDetailCard>

                <BookingDetailCard
                    title="Info motor"
                    description="Ringkasan kendaraan yang akan ditangani."
                >
                    <div class="space-y-3 text-sm leading-6">
                        <div>
                            <p class="text-muted-foreground">Jenis motor</p>
                            <p class="font-medium text-foreground">
                                {booking.motorcycle.typeLabel}
                            </p>
                        </div>
                        <div>
                            <p class="text-muted-foreground">Merek / model</p>
                            <p class="font-medium text-foreground">
                                {booking.motorcycle.brand}
                                {booking.motorcycle.model}
                            </p>
                        </div>
                        <div>
                            <p class="text-muted-foreground">
                                Tahun / plat nomor
                            </p>
                            <p class="font-medium text-foreground">
                                {booking.motorcycle.year ?? '-'} / {booking
                                    .motorcycle.plateNumber ?? '-'}
                            </p>
                        </div>
                    </div>
                </BookingDetailCard>
            </div>

            <BookingDetailCard
                title="Detail servis"
                description="Snapshot layanan yang tersimpan saat booking dibuat."
            >
                <div class="space-y-4 text-sm leading-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-muted-foreground">Tipe paket</p>
                            <p class="font-medium text-foreground">
                                {booking.service.packageTypeLabel}
                            </p>
                        </div>
                        <div>
                            <p class="text-muted-foreground">Nama paket</p>
                            <p class="font-medium text-foreground">
                                {booking.service.packageName}
                            </p>
                        </div>
                    </div>

                    {#if booking.service.customItems.length > 0}
                        <div class="space-y-2">
                            <p class="font-medium text-foreground">
                                Item custom
                            </p>
                            <div class="space-y-2">
                                {#each booking.service.customItems as item (item.id)}
                                    <div
                                        class="flex items-center justify-between rounded-xl border border-border/70 px-3 py-3"
                                    >
                                        <div>
                                            <p
                                                class="font-medium text-foreground"
                                            >
                                                {item.name}
                                            </p>
                                            <p class="text-muted-foreground">
                                                {item.qty}x item
                                            </p>
                                        </div>
                                        <p class="font-medium text-foreground">
                                            {formatCurrency(item.subtotal)}
                                        </p>
                                    </div>
                                {/each}
                            </div>
                        </div>
                    {:else}
                        <div
                            class="rounded-xl border border-dashed border-border/70 px-4 py-4 text-sm text-muted-foreground"
                        >
                            Booking ini tidak memiliki item custom tambahan.
                        </div>
                    {/if}

                    <div>
                        <p class="text-muted-foreground">Catatan customer</p>
                        <p class="font-medium text-foreground">
                            {booking.service.notes ?? '-'}
                        </p>
                    </div>
                </div>
            </BookingDetailCard>

            <BookingDetailCard
                title="Lokasi servis"
                description="Alamat dan patokan rumah yang dibagikan customer."
            >
                <div class="space-y-4 text-sm leading-6">
                    <div class="space-y-2">
                        <p class="text-muted-foreground">Alamat lengkap</p>
                        <p class="font-medium text-foreground">
                            {booking.location.addressText}
                        </p>
                        <CopyValueButton
                            value={booking.location.addressText}
                            label="alamat customer"
                        />
                    </div>
                    <div>
                        <p class="text-muted-foreground">Patokan rumah</p>
                        <p class="font-medium text-foreground">
                            {booking.location.houseLandmark}
                        </p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-muted-foreground">Latitude</p>
                            <p class="font-medium text-foreground">
                                {booking.location.latitude}
                            </p>
                        </div>
                        <div>
                            <p class="text-muted-foreground">Longitude</p>
                            <p class="font-medium text-foreground">
                                {booking.location.longitude}
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Button asChild variant="outline">
                            {#snippet children(props)}
                                <a
                                    href={booking.location.mapUrl}
                                    target="_blank"
                                    rel="noreferrer"
                                    aria-label="Buka lokasi customer di peta"
                                    {...props}
                                >
                                    Buka peta
                                </a>
                            {/snippet}
                        </Button>
                        <CopyValueButton
                            value={`${booking.location.latitude}, ${booking.location.longitude}`}
                            label="koordinat customer"
                        />
                    </div>
                </div>
            </BookingDetailCard>

            <BookingDetailCard
                title="Histori status"
                description="Audit trail perubahan status booking."
            >
                <StatusHistoryTimeline history={booking.statusHistory} />
            </BookingDetailCard>
        </div>

        <div class="space-y-6">
            <BookingDetailCard
                title="Ringkasan harga"
                description="Snapshot harga tetap mengacu ke data booking saat dibuat."
            >
                <div class="space-y-3 text-sm leading-6">
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">Harga paket</span>
                        <span class="font-medium text-foreground"
                            >{formatCurrency(
                                booking.pricing.packagePrice,
                            )}</span
                        >
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">Subtotal</span>
                        <span class="font-medium text-foreground"
                            >{formatCurrency(booking.pricing.subtotal)}</span
                        >
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">Biaya layanan</span>
                        <span class="font-medium text-foreground"
                            >{formatCurrency(booking.pricing.serviceFee)}</span
                        >
                    </div>
                    <div
                        class="flex items-center justify-between border-t border-border/70 pt-3 text-base"
                    >
                        <span class="font-semibold text-foreground">Total</span>
                        <span class="font-semibold text-foreground"
                            >{formatCurrency(booking.pricing.total)}</span
                        >
                    </div>
                </div>
            </BookingDetailCard>

            <BookingDetailCard
                title="Update status"
                description="Perubahan status otomatis menambah catatan di histori booking."
            >
                <Form
                    action={updateStatus(booking.bookingCode).url}
                    method="patch"
                    class="space-y-4"
                >
                    {#snippet children({ errors, processing })}
                        <div class="grid gap-2">
                            <Label for="status">Status terbaru</Label>
                            <select
                                id="status"
                                name="status"
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none"
                            >
                                {#each statusOptions as option (option.value)}
                                    <option
                                        value={option.value}
                                        selected={option.value ===
                                            booking.status}
                                    >
                                        {option.label}
                                    </option>
                                {/each}
                            </select>
                            <InputError message={errors.status} />
                        </div>

                        <div class="grid gap-2">
                            <Label for="note">Catatan status</Label>
                            <textarea
                                id="note"
                                name="note"
                                rows="4"
                                class="min-h-28 rounded-md border border-input bg-transparent px-3 py-2 text-sm outline-none"
                                placeholder="Contoh: jadwal sudah dikonfirmasi via WhatsApp."
                            ></textarea>
                            <InputError message={errors.note} />
                        </div>

                        <Button
                            type="submit"
                            disabled={processing}
                            aria-label="Simpan perubahan status booking"
                        >
                            {#if processing}
                                <Spinner class="size-4" />
                                Menyimpan...
                            {:else}
                                Simpan status
                            {/if}
                        </Button>
                    {/snippet}
                </Form>
            </BookingDetailCard>

            <BookingDetailCard
                title="Catatan admin"
                description="Catatan internal operasional, tidak ditampilkan ke publik."
            >
                <Form
                    action={updateNotes(booking.bookingCode).url}
                    method="patch"
                    class="space-y-4"
                >
                    {#snippet children({ errors, processing })}
                        <div class="grid gap-2">
                            <Label for="admin_notes">Catatan internal</Label>
                            <textarea
                                id="admin_notes"
                                name="admin_notes"
                                rows="6"
                                class="min-h-36 rounded-md border border-input bg-transparent px-3 py-2 text-sm outline-none"
                                >{booking.adminNotes ?? ''}</textarea
                            >
                            <InputError message={errors.admin_notes} />
                        </div>

                        <Button
                            type="submit"
                            disabled={processing}
                            aria-label="Simpan catatan internal booking"
                        >
                            {#if processing}
                                <Spinner class="size-4" />
                                Menyimpan...
                            {:else}
                                Simpan catatan
                            {/if}
                        </Button>
                    {/snippet}
                </Form>
            </BookingDetailCard>
        </div>
    </div>
</div>
