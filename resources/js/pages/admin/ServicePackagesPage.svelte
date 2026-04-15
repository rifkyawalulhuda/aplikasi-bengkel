<script lang="ts">
    import { Form, Link } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import ServicePackageForm from '@/components/admin/ServicePackageForm.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import {
        activate,
        deactivate,
        destroy,
        edit,
        index,
        store,
        update,
    } from '@/actions/App/Http/Controllers/Admin/ServicePackageController';
    import type { AdminServicePackageFormData, AdminServicePackageSummary } from '@/types';

    let {
        packages,
        editingPackage = null,
    }: {
        packages: AdminServicePackageSummary[];
        editingPackage?: AdminServicePackageFormData | null;
    } = $props();

    const formDefinition = $derived(
        editingPackage ? update.form(editingPackage.id) : store.form(),
    );

    const currencyFormatter = new Intl.NumberFormat('id-ID');
</script>

<AppHead title="Paket Servis Admin" />

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <h2 class="text-2xl font-semibold tracking-tight text-[#D12052]">Kelola paket servis</h2>
        <p class="text-sm leading-6 text-[#D12052]">
            Paket aktif akan muncul di halaman publik. Paket inactive tetap bisa disimpan untuk arsip admin, diaktifkan kembali kapan saja, atau dihapus permanen jika sudah tidak dipakai.
        </p>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
        <Card class="border-primary/16 bg-white/88 shadow-[0_24px_54px_-40px_rgb(var(--brand-primary-rgb)/0.36)] backdrop-blur-sm">
            <CardHeader class="gap-2">
                <CardTitle>Daftar paket servis</CardTitle>
                <p class="text-sm leading-6 text-muted-foreground">
                    Total {packages.length} paket tercatat di admin.
                </p>
            </CardHeader>
            <CardContent class="space-y-4">
                {#if packages.length === 0}
                    <div class="rounded-2xl border border-dashed border-border px-4 py-10 text-center text-sm leading-6 text-muted-foreground">
                        Belum ada paket servis. Tambahkan paket pertama dari form di sisi kanan.
                    </div>
                {:else}
                    {#each packages as servicePackage (servicePackage.id)}
                        <div class="rounded-2xl border border-primary/12 bg-white/72 p-4">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div class="space-y-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-lg font-semibold text-foreground">{servicePackage.name}</h3>
                                        <Badge
                                            variant="outline"
                                            class={servicePackage.isActive
                                                ? 'border-primary/20 bg-primary/8 text-primary'
                                                : 'border-strong/22 bg-strong/8 text-strong'}
                                        >
                                            {servicePackage.isActive ? 'Active' : 'Inactive'}
                                        </Badge>
                                        {#if servicePackage.isFeatured}
                                            <Badge class="border-0 bg-secondary text-secondary-foreground">
                                                Paling Diminati
                                            </Badge>
                                        {/if}
                                    </div>

                                    <p class="text-sm leading-6 text-muted-foreground">
                                        {servicePackage.shortDescription ?? 'Belum ada deskripsi singkat.'}
                                    </p>

                                    <div class="flex flex-wrap gap-2 text-xs text-muted-foreground">
                                        <span class="rounded-full bg-muted px-3 py-1">
                                            Rp {currencyFormatter.format(servicePackage.price)}
                                        </span>
                                        <span class="rounded-full bg-muted px-3 py-1">
                                            {servicePackage.durationEstimateMinutes} menit
                                        </span>
                                        <span class="rounded-full bg-muted px-3 py-1">
                                            {servicePackage.itemsCount} item
                                        </span>
                                        <span class="rounded-full bg-muted px-3 py-1">
                                            {servicePackage.bookingsCount} booking
                                        </span>
                                    </div>

                                    <ul class="space-y-2 text-sm leading-6 text-foreground">
                                        {#each servicePackage.items.slice(0, 4) as item, index (index)}
                                            <li>{index + 1}. {item.name}</li>
                                        {/each}
                                    </ul>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <Button asChild variant="outline" size="sm">
                                        {#snippet children(props)}
                                            <Link href={edit(servicePackage.id).url} {...props}>Edit</Link>
                                        {/snippet}
                                    </Button>

                                    {#if servicePackage.isActive}
                                        <Form {...deactivate.form(servicePackage.id)}>
                                            {#snippet children({ processing })}
                                                <Button type="submit" size="sm" disabled={processing}>
                                                    Nonaktifkan
                                                </Button>
                                            {/snippet}
                                        </Form>
                                    {:else}
                                        <Form {...activate.form(servicePackage.id)}>
                                            {#snippet children({ processing })}
                                                <Button type="submit" size="sm" disabled={processing}>
                                                    Aktifkan kembali
                                                </Button>
                                            {/snippet}
                                        </Form>

                                        <Form {...destroy.form(servicePackage.id)}>
                                            {#snippet children({ processing })}
                                                <Button type="submit" variant="destructive" size="sm" disabled={processing}>
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

        <ServicePackageForm
            servicePackage={editingPackage}
            formAction={formDefinition.action}
            formMethod={formDefinition.method}
            resetUrl={index().url}
        />
    </div>
</div>
