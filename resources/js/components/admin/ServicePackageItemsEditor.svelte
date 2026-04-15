<script lang="ts">
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Label } from '@/components/ui/label';
    import type { AdminServicePackageItem } from '@/types';

    let {
        items = $bindable<AdminServicePackageItem[]>(),
        errors = {},
    }: {
        items: AdminServicePackageItem[];
        errors?: Record<string, string>;
    } = $props();

    function addItem(): void {
        items = [...items, { name: '', description: '' }];
    }

    function removeItem(index: number): void {
        if (items.length === 1) {
            return;
        }

        items = items.filter((_, currentIndex) => currentIndex !== index);
    }

    function moveItem(index: number, direction: 'up' | 'down'): void {
        const targetIndex = direction === 'up' ? index - 1 : index + 1;

        if (targetIndex < 0 || targetIndex >= items.length) {
            return;
        }

        const reorderedItems = [...items];
        const currentItem = reorderedItems[index];

        reorderedItems[index] = reorderedItems[targetIndex];
        reorderedItems[targetIndex] = currentItem;

        items = reorderedItems;
    }

    function updateItem(index: number, field: 'name' | 'description', value: string): void {
        items = items.map((item, currentIndex) =>
            currentIndex === index ? { ...item, [field]: value } : item,
        );
    }
</script>

<div class="space-y-4">
    <div class="flex items-center justify-between gap-3">
        <div>
            <Label>Item paket</Label>
            <p class="text-sm leading-6 text-muted-foreground">
                Susun item layanan sesuai urutan yang ingin ditampilkan di admin dan publik.
            </p>
        </div>

        <Button type="button" variant="outline" size="sm" onclick={addItem}>
            Tambah item
        </Button>
    </div>

    <div class="space-y-3">
        {#each items as item, index (index)}
        <div class="rounded-2xl border border-primary/12 bg-white/72 p-4">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <p class="text-sm font-semibold text-foreground">Item {index + 1}</p>

                    <div class="flex items-center gap-2">
                        <Button type="button" variant="outline" size="sm" onclick={() => moveItem(index, 'up')} disabled={index === 0}>
                            Naik
                        </Button>
                        <Button type="button" variant="outline" size="sm" onclick={() => moveItem(index, 'down')} disabled={index === items.length - 1}>
                            Turun
                        </Button>
                        <Button type="button" variant="destructive" size="sm" onclick={() => removeItem(index)} disabled={items.length === 1}>
                            Hapus
                        </Button>
                    </div>
                </div>

                <div class="grid gap-4">
                    <div class="grid gap-2">
                        <Label for={`item-name-${index}`}>Nama item</Label>
                        <input
                            id={`item-name-${index}`}
                            name={`items[${index}][name]`}
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none"
                            value={item.name}
                            oninput={(event) => updateItem(index, 'name', event.currentTarget.value)}
                        />
                        <InputError message={errors[`items.${index}.name`]} />
                    </div>

                    <div class="grid gap-2">
                        <Label for={`item-description-${index}`}>Deskripsi item</Label>
                        <textarea
                            id={`item-description-${index}`}
                            name={`items[${index}][description]`}
                            rows="3"
                            class="min-h-24 rounded-md border border-input bg-transparent px-3 py-2 text-sm outline-none"
                            oninput={(event) => updateItem(index, 'description', event.currentTarget.value)}
                        >{item.description ?? ''}</textarea>
                        <InputError message={errors[`items.${index}.description`]} />
                    </div>
                </div>
            </div>
        {/each}
    </div>

    <InputError message={errors.items} />
</div>
