<script lang="ts">
    import { Form, Link } from '@inertiajs/svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import type { AdminCustomServiceItemFormData } from '@/types';

    let {
        customServiceItem = null,
        formAction,
        formMethod,
        resetUrl,
    }: {
        customServiceItem?: AdminCustomServiceItemFormData | null;
        formAction: string;
        formMethod: 'get' | 'post';
        resetUrl: string;
    } = $props();

    const initialItem = (): AdminCustomServiceItemFormData | null =>
        customServiceItem ?? null;
    const isEditing = $derived(initialItem() !== null);

    let name = $state(initialItem()?.name ?? '');
    let category = $state(initialItem()?.category ?? '');
    let description = $state(initialItem()?.description ?? '');
    let price = $state(String(initialItem()?.price ?? 0));
    let unitLabel = $state(initialItem()?.unitLabel ?? '');
    let displayOrder = $state(String(initialItem()?.displayOrder ?? 0));
    let isActive = $state(initialItem()?.isActive ?? true);
</script>

<Card class="border-primary/16 bg-white/88 shadow-[0_24px_54px_-40px_rgb(var(--brand-primary-rgb)/0.36)] backdrop-blur-sm">
    <CardHeader class="gap-2">
        <CardTitle
            >{isEditing
                ? 'Edit item servis custom'
                : 'Tambah item servis custom'}</CardTitle
        >
        <p class="text-sm leading-6 text-muted-foreground">
            Harga terbaru hanya dipakai untuk booking baru. Snapshot pada
            booking lama tetap aman karena sudah tersimpan terpisah.
        </p>
    </CardHeader>

    <CardContent>
        <Form action={formAction} method={formMethod} class="space-y-5">
            {#snippet children({ errors, processing })}
                <div class="grid gap-5 md:grid-cols-2">
                    <div class="grid gap-2 md:col-span-2">
                        <Label for="name">Nama item</Label>
                        <Input
                            id="name"
                            name="name"
                            value={name}
                            oninput={(event) =>
                                (name = event.currentTarget.value)}
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div class="grid gap-2">
                        <Label for="category">Kategori</Label>
                        <Input
                            id="category"
                            name="category"
                            value={category}
                            oninput={(event) =>
                                (category = event.currentTarget.value)}
                        />
                        <InputError message={errors.category} />
                    </div>

                    <div class="grid gap-2">
                        <Label for="unit_label">Label unit</Label>
                        <Input
                            id="unit_label"
                            name="unit_label"
                            value={unitLabel}
                            placeholder="contoh: layanan / kunjungan"
                            oninput={(event) =>
                                (unitLabel = event.currentTarget.value)}
                        />
                        <InputError message={errors.unit_label} />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label for="description">Deskripsi</Label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="min-h-28 rounded-md border border-input bg-transparent px-3 py-2 text-sm outline-none"
                            oninput={(event) =>
                                (description = event.currentTarget.value)}
                            >{description}</textarea
                        >
                        <InputError message={errors.description} />
                    </div>

                    <div class="grid gap-2">
                        <Label for="price">Harga</Label>
                        <Input
                            id="price"
                            name="price"
                            type="number"
                            min="0"
                            value={price}
                            oninput={(event) =>
                                (price = event.currentTarget.value)}
                        />
                        <InputError message={errors.price} />
                    </div>

                    <div class="grid gap-2">
                        <Label for="display_order">Urutan tampil</Label>
                        <Input
                            id="display_order"
                            name="display_order"
                            type="number"
                            min="0"
                            value={displayOrder}
                            oninput={(event) =>
                                (displayOrder = event.currentTarget.value)}
                        />
                        <InputError message={errors.display_order} />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label>Status publik</Label>
                        <input type="hidden" name="is_active" value="0" />
                        <label
                            class="flex items-center gap-3 rounded-xl border border-border/70 px-4 py-3 text-sm"
                        >
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                checked={isActive}
                                onchange={(event) =>
                                    (isActive = event.currentTarget.checked)}
                            />
                            Aktifkan item agar muncul di form booking custom publik
                        </label>
                        <InputError message={errors.is_active} />
                    </div>
                </div>

                <div
                    class="flex flex-col gap-3 border-t border-border/70 pt-5 sm:flex-row sm:justify-between"
                >
                    <Button asChild type="button" variant="outline">
                        {#snippet children(props)}
                            <Link href={resetUrl} {...props}>
                                {isEditing ? 'Batal edit' : 'Reset form'}
                            </Link>
                        {/snippet}
                    </Button>

                    <Button type="submit" disabled={processing}>
                        {isEditing
                            ? 'Simpan perubahan item'
                            : 'Buat item custom'}
                    </Button>
                </div>
            {/snippet}
        </Form>
    </CardContent>
</Card>
