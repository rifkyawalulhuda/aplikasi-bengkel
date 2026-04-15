<script lang="ts">
    import { Form, Link } from '@inertiajs/svelte';
    import InputError from '@/components/InputError.svelte';
    import ServicePackageItemsEditor from '@/components/admin/ServicePackageItemsEditor.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import type { AdminServicePackageFormData, AdminServicePackageItem } from '@/types';

    let {
        servicePackage = null,
        formAction,
        formMethod,
        resetUrl,
    }: {
        servicePackage?: AdminServicePackageFormData | null;
        formAction: string;
        formMethod: 'get' | 'post';
        resetUrl: string;
    } = $props();

    const initialServicePackage = (): AdminServicePackageFormData | null => servicePackage ?? null;
    const isEditing = $derived(initialServicePackage() !== null);

    let name = $state(initialServicePackage()?.name ?? '');
    let shortDescription = $state(initialServicePackage()?.shortDescription ?? '');
    let description = $state(initialServicePackage()?.description ?? '');
    let price = $state(String(initialServicePackage()?.price ?? 85000));
    let durationEstimateMinutes = $state(String(initialServicePackage()?.durationEstimateMinutes ?? 60));
    let displayOrder = $state(String(initialServicePackage()?.displayOrder ?? 0));
    let isActive = $state(initialServicePackage()?.isActive ?? true);
    let isFeatured = $state(initialServicePackage()?.isFeatured ?? false);
    let items = $state<AdminServicePackageItem[]>(
        initialServicePackage()?.items?.length
            ? initialServicePackage()!.items.map((item) => ({
                name: item.name,
                description: item.description,
            }))
            : [{ name: '', description: '' }],
    );
</script>

<Card class="border-primary/16 bg-white/88 shadow-[0_24px_54px_-40px_rgb(var(--brand-primary-rgb)/0.36)] backdrop-blur-sm">
    <CardHeader class="gap-2">
        <CardTitle>{isEditing ? 'Edit paket servis' : 'Tambah paket servis'}</CardTitle>
        <p class="text-sm leading-6 text-muted-foreground">
            Kelola harga snapshot dasar, urutan tampil, status aktif, dan daftar item paket dalam satu form.
        </p>
    </CardHeader>

    <CardContent>
        <Form action={formAction} method={formMethod} class="space-y-5">
            {#snippet children({ errors, processing })}
                <div class="grid gap-5 md:grid-cols-2">
                    <div class="grid gap-2 md:col-span-2">
                        <Label for="name">Nama paket</Label>
                        <Input id="name" name="name" value={name} oninput={(event) => (name = event.currentTarget.value)} />
                        <InputError message={errors.name} />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label for="short_description">Deskripsi singkat</Label>
                        <textarea
                            id="short_description"
                            name="short_description"
                            rows="3"
                            class="min-h-24 rounded-md border border-input bg-transparent px-3 py-2 text-sm outline-none"
                            oninput={(event) => (shortDescription = event.currentTarget.value)}
                        >{shortDescription}</textarea>
                        <InputError message={errors.short_description} />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label for="description">Deskripsi lengkap</Label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="min-h-28 rounded-md border border-input bg-transparent px-3 py-2 text-sm outline-none"
                            oninput={(event) => (description = event.currentTarget.value)}
                        >{description}</textarea>
                        <InputError message={errors.description} />
                    </div>

                    <div class="grid gap-2">
                        <Label for="price">Harga paket</Label>
                        <Input id="price" name="price" type="number" min="0" value={price} oninput={(event) => (price = event.currentTarget.value)} />
                        <InputError message={errors.price} />
                    </div>

                    <div class="grid gap-2">
                        <Label for="duration_estimate_minutes">Estimasi durasi (menit)</Label>
                        <Input
                            id="duration_estimate_minutes"
                            name="duration_estimate_minutes"
                            type="number"
                            min="15"
                            value={durationEstimateMinutes}
                            oninput={(event) => (durationEstimateMinutes = event.currentTarget.value)}
                        />
                        <InputError message={errors.duration_estimate_minutes} />
                    </div>

                    <div class="grid gap-2">
                        <Label for="display_order">Urutan tampil</Label>
                        <Input
                            id="display_order"
                            name="display_order"
                            type="number"
                            min="0"
                            value={displayOrder}
                            oninput={(event) => (displayOrder = event.currentTarget.value)}
                        />
                        <InputError message={errors.display_order} />
                    </div>

                    <div class="grid gap-2">
                        <Label>Status publik</Label>
                        <input type="hidden" name="is_active" value="0" />
                        <label class="flex items-center gap-3 rounded-xl border border-border/70 px-4 py-3 text-sm">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                checked={isActive}
                                onchange={(event) => (isActive = event.currentTarget.checked)}
                            />
                            Aktifkan paket agar tampil di landing page
                        </label>
                        <InputError message={errors.is_active} />
                    </div>

                    <div class="grid gap-2">
                        <Label>Badge landing page</Label>
                        <input type="hidden" name="is_featured" value="0" />
                        <label class="flex items-center gap-3 rounded-xl border border-border/70 px-4 py-3 text-sm">
                            <input
                                type="checkbox"
                                name="is_featured"
                                value="1"
                                checked={isFeatured}
                                onchange={(event) => (isFeatured = event.currentTarget.checked)}
                            />
                            Tandai paket ini sebagai “Paling Diminati” di landing page
                        </label>
                        <InputError message={errors.is_featured} />
                    </div>
                </div>

                <ServicePackageItemsEditor bind:items {errors} />

                <div class="flex flex-col gap-3 border-t border-border/70 pt-5 sm:flex-row sm:justify-between">
                    <Button asChild type="button" variant="outline">
                        {#snippet children(props)}
                            <Link href={resetUrl} {...props}>
                                {isEditing ? 'Batal edit' : 'Reset form'}
                            </Link>
                        {/snippet}
                    </Button>

                    <Button type="submit" disabled={processing}>
                        {isEditing ? 'Simpan perubahan paket' : 'Buat paket servis'}
                    </Button>
                </div>
            {/snippet}
        </Form>
    </CardContent>
</Card>
