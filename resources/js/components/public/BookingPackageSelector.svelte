<script lang="ts">
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { formatCurrency } from '@/lib/utils';
    import type {
        BookingPackageType,
        SelectOption,
        ServicePackageSummary,
    } from '@/types';

    let {
        packageType = $bindable<BookingPackageType>('fixed_package'),
        servicePackageId = $bindable<number | null>(null),
        packageTypes,
        packages,
        onSelectionChange = () => {},
        errors = {},
    }: {
        packageType?: BookingPackageType;
        servicePackageId?: number | null;
        packageTypes: SelectOption[];
        packages: ServicePackageSummary[];
        onSelectionChange?: () => void;
        errors?: Record<string, string>;
    } = $props();

    const selectedPackage = $derived(
        packages.find((item) => item.id === servicePackageId) ?? null,
    );

    let expandedPackageId = $state<number | null>(null);

    $effect(() => {
        if (packageType !== 'fixed_package') {
            expandedPackageId = null;
            return;
        }

        if (
            servicePackageId !== null &&
            packages.some((item) => item.id === servicePackageId)
        ) {
            expandedPackageId = servicePackageId;
        }
    });

    function selectType(value: BookingPackageType) {
        packageType = value;

        if (value === 'custom_package') {
            servicePackageId = null;
            expandedPackageId = null;
        }

        onSelectionChange();
    }

    function selectPackage(packageId: number) {
        servicePackageId = packageId;
        expandedPackageId = packageId;
        onSelectionChange();
    }

    function togglePackagePanel(packageId: number) {
        expandedPackageId =
            expandedPackageId === packageId ? null : packageId;
        onSelectionChange();
    }
</script>

<div class="space-y-5">
    <div class="space-y-2">
        <h3 class="text-lg font-semibold text-foreground">
            1. Pilih jenis paket
        </h3>
        <p class="text-sm leading-6 text-muted-foreground">
            Anda bisa memilih paket tetap untuk estimasi cepat atau paket custom
            untuk kebutuhan yang lebih fleksibel.
        </p>
    </div>

    <div class="grid gap-3 sm:grid-cols-2">
        {#each packageTypes as option (option.value)}
            <button
                type="button"
                aria-pressed={packageType === option.value}
                aria-label={`Pilih ${option.label}`}
                class={`rounded-[1.5rem] border p-4 text-left transition ${
                    packageType === option.value
                        ? 'border-primary/25 bg-primary/8 shadow-sm'
                        : 'border-border/70 bg-card hover:border-primary/40'
                }`}
                onclick={() => selectType(option.value as BookingPackageType)}
            >
                <p class="text-sm font-semibold text-foreground">
                    {option.label}
                </p>
                <p class="mt-2 text-sm leading-6 text-muted-foreground">
                    {option.value === 'fixed_package'
                        ? 'Pilih salah satu paket aktif dengan harga.'
                        : 'Pilih custom order.'}
                </p>
            </button>
        {/each}
    </div>

    {#if errors.package_type}
        <p class="text-sm font-medium text-destructive">
            {errors.package_type}
        </p>
    {/if}

    {#if packageType === 'fixed_package'}
        <div class="space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h4
                    class="text-sm font-semibold uppercase tracking-[0.16em] text-muted-foreground"
                >
                    Paket aktif
                </h4>
                <Badge
                    variant="secondary"
                    class="rounded-full px-3 py-1 text-xs"
                >
                    {packages.length} paket
                </Badge>
            </div>

            {#if packages.length === 0}
                <div
                    class="rounded-[1.5rem] border border-dashed border-border/70 bg-background/80 px-4 py-5 text-sm leading-6 text-muted-foreground"
                >
                    Saat ini belum ada paket aktif yang bisa dipilih. Kamu tetap
                    bisa lanjut memakai paket custom jika dibutuhkan.
                </div>
            {:else}
                <div class="space-y-3 lg:hidden">
                    {#each packages as item (item.id)}
                        {@const isSelected = servicePackageId === item.id}
                        {@const isExpanded = expandedPackageId === item.id}

                        <article
                            class={`overflow-hidden rounded-[1.5rem] border transition ${
                                isSelected
                                    ? 'border-primary/25 bg-primary/8 shadow-sm'
                                    : 'border-border/70 bg-card'
                            }`}
                        >
                            <button
                                type="button"
                                aria-expanded={isExpanded}
                                aria-controls={`mobile-package-panel-${item.id}`}
                                aria-label={`Buka detail paket ${item.name}`}
                                class="flex w-full items-start justify-between gap-4 px-4 py-4 text-left"
                                onclick={() => togglePackagePanel(item.id)}
                            >
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-base font-semibold text-foreground">
                                            {item.name}
                                        </p>

                                        {#if isSelected}
                                            <Badge
                                                variant="secondary"
                                                class="rounded-full px-3 py-1 text-xs"
                                            >
                                                Terpilih
                                            </Badge>
                                        {/if}
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-primary">
                                            {formatCurrency(item.price)}
                                        </p>
                                        <Badge
                                            variant="secondary"
                                            class="rounded-full px-3 py-1 text-xs"
                                        >
                                            {Math.round(
                                                item.durationEstimateMinutes /
                                                    60,
                                            )} jam
                                        </Badge>
                                    </div>

                                    {#if item.shortDescription}
                                        <p
                                            class="max-h-12 overflow-hidden text-sm leading-6 text-muted-foreground"
                                        >
                                            {item.shortDescription}
                                        </p>
                                    {/if}
                                </div>

                                <span
                                    class={`mt-1 inline-flex size-9 shrink-0 items-center justify-center rounded-full border transition duration-200 ${
                                        isExpanded
                                            ? 'rotate-180 border-primary/25 bg-primary text-primary-foreground'
                                            : 'border-border/70 bg-muted text-muted-foreground'
                                    }`}
                                    aria-hidden="true"
                                >
                                    ▾
                                </span>
                            </button>

                            <div
                                id={`mobile-package-panel-${item.id}`}
                                class={`grid overflow-hidden transition-all duration-300 ease-out ${
                                    isExpanded
                                        ? 'grid-rows-[1fr] opacity-100'
                                        : 'grid-rows-[0fr] opacity-0'
                                }`}
                            >
                                <div class="min-h-0 px-4 pb-4">
                                    <div class="space-y-4 rounded-[1.25rem] border border-border/60 bg-background/80 p-4">
                                        <ul class="grid gap-2 text-sm text-muted-foreground">
                                            {#each item.items as packageItem (packageItem.id)}
                                                <li
                                                    class="rounded-2xl bg-muted px-3 py-2"
                                                >
                                                    {packageItem.name}
                                                </li>
                                            {/each}
                                        </ul>

                                        <Button
                                            type="button"
                                            variant={isSelected
                                                ? 'secondary'
                                                : 'outline'}
                                            class="h-11 w-full rounded-full"
                                            onclick={() => selectPackage(item.id)}
                                        >
                                            {isSelected
                                                ? 'Paket terpilih'
                                                : 'Pilih paket ini'}
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    {/each}
                </div>

                <div class="hidden gap-4 lg:grid lg:grid-cols-2">
                    {#each packages as item (item.id)}
                        <button
                            type="button"
                            aria-pressed={servicePackageId === item.id}
                            aria-label={`Pilih paket ${item.name}`}
                            class={`rounded-[1.5rem] border text-left transition ${
                                servicePackageId === item.id
                                    ? 'border-primary/25 bg-primary/8 shadow-sm'
                                    : 'border-border/70 bg-card hover:border-primary/40'
                            }`}
                            onclick={() => {
                                servicePackageId = item.id;
                                onSelectionChange();
                            }}
                        >
                            <Card
                                class="h-full border-0 bg-transparent shadow-none"
                            >
                                <CardHeader class="gap-3">
                                    <div
                                        class="flex items-start justify-between gap-4"
                                    >
                                        <CardTitle class="text-xl tracking-tight">
                                            {item.name}
                                        </CardTitle>
                                        <Badge
                                            variant="secondary"
                                            class="rounded-full px-3 py-1 text-xs"
                                        >
                                            {Math.round(
                                                item.durationEstimateMinutes /
                                                    60,
                                            )} jam
                                        </Badge>
                                    </div>
                                    {#if item.shortDescription}
                                        <p
                                            class="text-sm leading-6 text-muted-foreground"
                                        >
                                            {item.shortDescription}
                                        </p>
                                    {/if}
                                </CardHeader>
                                <CardContent class="space-y-4">
                                    <p class="text-lg font-semibold text-primary">
                                        {formatCurrency(item.price)}
                                    </p>

                                    <ul class="grid gap-2 text-sm text-muted-foreground">
                                        {#each item.items as packageItem (packageItem.id)}
                                            <li
                                                class="rounded-2xl bg-muted px-3 py-2"
                                            >
                                                {packageItem.name}
                                            </li>
                                        {/each}
                                    </ul>
                                </CardContent>
                            </Card>
                        </button>
                    {/each}
                </div>
            {/if}

            {#if errors.service_package_id}
                <p class="text-sm font-medium text-destructive">
                    {errors.service_package_id}
                </p>
            {/if}
        </div>
    {/if}

    <div
        class="rounded-[1.5rem] border border-dashed border-border/70 bg-background/80 p-4 text-sm leading-6 text-muted-foreground"
    >
        Harga yang tampil di sini adalah estimasi awal. Saat booking dikirim,
        Admin tetap akan mengecek ulang paket aktif, slot jadwal, dan total
        harga.
    </div>

    {#if packageType === 'fixed_package' && selectedPackage}
        <div class="flex justify-end">
            <Button
                type="button"
                variant="outline"
                onclick={() => {
                    servicePackageId = null;
                    onSelectionChange();
                }}
            >
                Reset pilihan paket
            </Button>
        </div>
    {/if}
</div>
