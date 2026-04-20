<script lang="ts">
    import { Form, Link } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import BookingServiceFeeCard from '@/components/admin/BookingServiceFeeCard.svelte';
    import CustomServiceItemForm from '@/components/admin/CustomServiceItemForm.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import {
        deactivate,
        destroy,
        edit,
        index,
        store,
        update,
    } from '@/actions/App/Http/Controllers/Admin/CustomServiceItemController';
    import type {
        AdminCustomServiceItemFormData,
        AdminCustomServiceItemSummary,
    } from '@/types';

    let {
        items,
        serviceFee,
        editingItem = null,
    }: {
        items: AdminCustomServiceItemSummary[];
        serviceFee: number;
        editingItem?: AdminCustomServiceItemFormData | null;
    } = $props();

    const formDefinition = $derived(
        editingItem ? update.form(editingItem.id) : store.form(),
    );

    const currencyFormatter = new Intl.NumberFormat('id-ID');
</script>

<AppHead title="Custom Service Items Admin" />

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <h2 class="text-2xl font-semibold tracking-tight text-[#D12052]">
            Kelola item servis custom
        </h2>
        <p class="text-sm leading-6 text-[#D12052]">
            Hanya item aktif yang tampil di form booking publik. Perubahan harga
            akan dipakai untuk booking baru, sementara snapshot booking lama
            tetap mengikuti harga saat transaksi dibuat.
        </p>
    </div>

    <BookingServiceFeeCard {serviceFee} />

    <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
        <Card class="border-primary/16 bg-white/88 shadow-[0_24px_54px_-40px_rgb(var(--brand-primary-rgb)/0.36)] backdrop-blur-sm">
            <CardHeader class="gap-2">
                <CardTitle>Daftar item servis custom</CardTitle>
                <p class="text-sm leading-6 text-muted-foreground">
                    Total {items.length} item tercatat di admin.
                </p>
            </CardHeader>
            <CardContent class="space-y-4">
                {#if items.length === 0}
                    <div
                        class="rounded-2xl border border-dashed border-border px-4 py-10 text-center text-sm leading-6 text-muted-foreground"
                    >
                        Belum ada item servis custom. Tambahkan item pertama
                        dari form di sisi kanan.
                    </div>
                {:else}
                    {#each items as item (item.id)}
                        <div
                            class="rounded-2xl border border-primary/12 bg-white/72 p-4"
                        >
                            <div
                                class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between"
                            >
                                <div class="space-y-3">
                                    <div
                                        class="flex flex-wrap items-center gap-2"
                                    >
                                        <h3
                                            class="text-lg font-semibold text-foreground"
                                        >
                                            {item.name}
                                        </h3>
                                        <Badge
                                            variant="outline"
                                            class={item.isActive
                                                ? 'border-primary/20 bg-primary/8 text-primary'
                                                : 'border-strong/22 bg-strong/8 text-strong'}
                                        >
                                            {item.isActive
                                                ? 'Active'
                                                : 'Inactive'}
                                        </Badge>
                                    </div>

                                    <div
                                        class="flex flex-wrap gap-2 text-xs text-muted-foreground"
                                    >
                                        <span
                                            class="rounded-full bg-muted px-3 py-1"
                                        >
                                            {item.category}
                                        </span>
                                        <span
                                            class="rounded-full bg-muted px-3 py-1"
                                        >
                                            Rp {currencyFormatter.format(
                                                item.price,
                                            )}
                                            {#if item.unitLabel}
                                                / {item.unitLabel}
                                            {/if}
                                        </span>
                                        <span
                                            class="rounded-full bg-muted px-3 py-1"
                                        >
                                            Urutan {item.displayOrder}
                                        </span>
                                        <span
                                            class="rounded-full bg-muted px-3 py-1"
                                        >
                                            {item.bookingsCount} snapshot booking
                                        </span>
                                    </div>

                                    <p
                                        class="text-sm leading-6 text-muted-foreground"
                                    >
                                        {item.description ??
                                            'Belum ada deskripsi item.'}
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <Button asChild variant="outline" size="sm">
                                        {#snippet children(props)}
                                            <Link
                                                href={edit(item.id).url}
                                                {...props}>Edit</Link
                                            >
                                        {/snippet}
                                    </Button>

                                    {#if item.isActive}
                                        <Form {...deactivate.form(item.id)}>
                                            {#snippet children({ processing })}
                                                <Button
                                                    type="submit"
                                                    size="sm"
                                                    disabled={processing}
                                                >
                                                    Nonaktifkan
                                                </Button>
                                            {/snippet}
                                        </Form>
                                    {:else}
                                        <Form {...destroy.form(item.id)}>
                                            {#snippet children({ processing })}
                                                <Button
                                                    type="submit"
                                                    variant="destructive"
                                                    size="sm"
                                                    disabled={processing}
                                                >
                                                    Hapus
                                                </Button>
                                            {/snippet}
                                        </Form>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    {/each}
                {/if}
            </CardContent>
        </Card>

        <CustomServiceItemForm
            customServiceItem={editingItem}
            formAction={formDefinition.action}
            formMethod={formDefinition.method}
            resetUrl={index().url}
        />
    </div>
</div>
