<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { store as storeBooking } from '@/actions/App/Http/Controllers/Public/BookingController';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { Spinner } from '@/components/ui/spinner';
    import BookingCustomItemsSelector from './BookingCustomItemsSelector.svelte';
    import BookingLocationPicker from './BookingLocationPicker.svelte';
    import BookingMotorInfoFields from './BookingMotorInfoFields.svelte';
    import BookingPackageSelector from './BookingPackageSelector.svelte';
    import BookingPriceSummary from './BookingPriceSummary.svelte';
    import BookingReviewPanel from './BookingReviewPanel.svelte';
    import BookingSchedulePicker from './BookingSchedulePicker.svelte';
    import SectionHeading from './SectionHeading.svelte';
    import type {
        BookingCustomerForm,
        BookingCustomItemSelection,
        BookingLocationForm,
        BookingMotorcycleForm,
        BookingPagePrefill,
        BookingPackageType,
        BookingScheduleForm,
        CustomServiceItemSummary,
        SelectOption,
        ServicePackageSummary,
    } from '@/types';

    let {
        packages,
        customItems,
        availableSlots,
        packageTypes,
        motorcycleTypes,
        headingEyebrow = 'Booking form',
        headingTitle = 'Flow booking dibuat bertahap dalam satu halaman agar nyaman dipakai dari HP.',
        headingDescription = 'Step visual membantu pelanggan mengisi data secara runtut: pilih paket, isi data motor, tetapkan lokasi dan jadwal, lalu review sebelum submit.',
        prefill = {
            packageSlug: null,
            startsInCustomMode: false,
        } satisfies BookingPagePrefill,
    }: {
        packages: ServicePackageSummary[];
        customItems: CustomServiceItemSummary[];
        availableSlots: string[];
        packageTypes: SelectOption[];
        motorcycleTypes: SelectOption[];
        headingEyebrow?: string;
        headingTitle?: string;
        headingDescription?: string;
        prefill?: BookingPagePrefill;
    } = $props();

    let currentStep = $state(1);
    let validationScope = $state(0);
    let isSubmitting = $state(false);
    let submitMessage = $state('');
    let submitTone = $state<'info' | 'error'>('info');
    let backendErrors = $state<Record<string, string>>({});

    let packageType = $state<BookingPackageType>('fixed_package');
    let servicePackageId = $state<number | null>(null);
    let selectedCustomItems = $state<BookingCustomItemSelection[]>([]);
    let customer = $state<BookingCustomerForm>({
        name: '',
        email: '',
        phone: '',
    });
    let motorcycle = $state<BookingMotorcycleForm>({
        type: '',
        brand: '',
        model: '',
        year: '',
        plateNumber: '',
    });
    let location = $state<BookingLocationForm>({
        addressText: '',
        houseLandmark: '',
        latitude: '',
        longitude: '',
    });
    let schedule = $state<BookingScheduleForm>({
        serviceDate: '',
        serviceTime: '',
        notes: '',
    });
    let lastAppliedPrefillKey = $state<string | null>(null);

    const today = new Date();
    const localToday = new Date(
        today.getTime() - today.getTimezoneOffset() * 60000,
    );
    const minDate = localToday.toISOString().slice(0, 10);

    const selectedPackage = $derived(
        packages.find((item) => item.id === servicePackageId) ?? null,
    );

    const selectedCustomItemDetails = $derived(
        selectedCustomItems
            .map((selection) => {
                const item = customItems.find(
                    (customItem) => customItem.id === selection.id,
                );

                if (!item) {
                    return null;
                }

                return {
                    ...item,
                    qty: selection.qty,
                    subtotal: item.price * selection.qty,
                };
            })
            .filter(
                (
                    item,
                ): item is CustomServiceItemSummary & {
                    qty: number;
                    subtotal: number;
                } => item !== null,
            ),
    );

    const subtotal = $derived(
        packageType === 'fixed_package'
            ? (selectedPackage?.price ?? 0)
            : selectedCustomItemDetails.reduce(
                  (sum, item) => sum + item.subtotal,
                  0,
              ),
    );

    const serviceFee = $derived(0);
    const total = $derived(subtotal + serviceFee);

    $effect(() => {
        const normalizedSlug = prefill.packageSlug?.trim().toLowerCase() ?? '';
        const prefillKey = `${prefill.startsInCustomMode ? 'custom' : 'fixed'}:${normalizedSlug}`;

        if (prefillKey === lastAppliedPrefillKey) {
            return;
        }

        lastAppliedPrefillKey = prefillKey;

        if (prefill.startsInCustomMode) {
            packageType = 'custom_package';
            servicePackageId = null;

            return;
        }

        packageType = 'fixed_package';

        if (normalizedSlug === '') {
            servicePackageId = null;

            return;
        }

        const matchedPackage = packages.find(
            (item) => item.slug.toLowerCase() === normalizedSlug,
        );

        servicePackageId = matchedPackage?.id ?? null;
    });

    function packageErrors() {
        const errors: Record<string, string> = {};
        if (!packageType)
            errors.package_type = 'Pilih jenis paket terlebih dulu.';
        if (packageType === 'fixed_package' && !servicePackageId)
            errors.service_package_id =
                'Pilih salah satu paket aktif untuk lanjut.';
        if (
            packageType === 'custom_package' &&
            selectedCustomItems.length === 0
        )
            errors.custom_items =
                'Pilih minimal satu item custom untuk paket custom.';
        return errors;
    }

    function customerMotorErrors() {
        const errors: Record<string, string> = {};
        if (!customer.name.trim())
            errors.customer_name = 'Nama pelanggan wajib diisi.';
        if (!customer.email.trim())
            errors.customer_email = 'Email pelanggan wajib diisi.';
        if (
            customer.email.trim() &&
            !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(customer.email)
        )
            errors.customer_email = 'Format email belum valid.';
        if (!customer.phone.trim())
            errors.customer_phone = 'Nomor telepon / WhatsApp wajib diisi.';
        if (!motorcycle.type.trim())
            errors.motorcycle_type = 'Pilih jenis motor.';
        if (!motorcycle.brand.trim())
            errors.motorcycle_brand = 'Merek motor wajib diisi.';
        if (!motorcycle.model.trim())
            errors.motorcycle_model = 'Model motor wajib diisi.';
        if (motorcycle.year.trim() && !/^\d{4}$/.test(motorcycle.year.trim()))
            errors.motorcycle_year = 'Tahun motor harus 4 digit.';
        return errors;
    }

    function locationScheduleErrors() {
        const errors: Record<string, string> = {};
        if (!location.addressText.trim())
            errors.address_text = 'Alamat lengkap wajib diisi.';
        if (!location.houseLandmark.trim())
            errors.house_landmark = 'Patokan rumah wajib diisi.';

        const latitude = Number(location.latitude);
        const longitude = Number(location.longitude);

        if (!location.latitude.trim())
            errors.latitude = 'Latitude wajib diisi.';
        else if (Number.isNaN(latitude) || latitude < -90 || latitude > 90)
            errors.latitude = 'Latitude harus berupa angka valid.';

        if (!location.longitude.trim())
            errors.longitude = 'Longitude wajib diisi.';
        else if (Number.isNaN(longitude) || longitude < -180 || longitude > 180)
            errors.longitude = 'Longitude harus berupa angka valid.';

        if (!schedule.serviceDate)
            errors.service_date = 'Tanggal servis wajib dipilih.';
        else if (schedule.serviceDate < minDate)
            errors.service_date = 'Tanggal servis tidak boleh di masa lalu.';

        if (!schedule.serviceTime)
            errors.service_time = 'Slot jam servis wajib dipilih.';
        if (schedule.notes.length > 1000)
            errors.notes = 'Catatan tambahan terlalu panjang.';

        return errors;
    }

    const stepErrors = $derived.by(() => ({
        1: packageErrors(),
        2: customerMotorErrors(),
        3: locationScheduleErrors(),
    }));

    const visibleErrors = $derived.by(() => {
        const errors: Record<string, string> = { ...backendErrors };
        if (validationScope >= 1) Object.assign(errors, stepErrors[1]);
        if (validationScope >= 2) Object.assign(errors, stepErrors[2]);
        if (validationScope >= 3) Object.assign(errors, stepErrors[3]);
        return errors;
    });

    const steps = [
        { number: 1, title: 'Paket' },
        { number: 2, title: 'Pelanggan & motor' },
        { number: 3, title: 'Lokasi & jadwal' },
        { number: 4, title: 'Review' },
    ] as const;

    function stepHasErrors(step: number): boolean {
        return Object.keys(stepErrors[step as 1 | 2 | 3] ?? {}).length > 0;
    }

    function canGoToNextStep(): boolean {
        if (currentStep === 1) return !stepHasErrors(1);
        if (currentStep === 2) return !stepHasErrors(2);
        if (currentStep === 3) return !stepHasErrors(3);
        return true;
    }

    function goToNextStep() {
        validationScope = Math.max(validationScope, currentStep);
        backendErrors = {};
        submitMessage = '';
        if (!canGoToNextStep()) return;
        currentStep = Math.min(currentStep + 1, 4);
    }

    function goToPreviousStep() {
        backendErrors = {};
        submitMessage = '';
        currentStep = Math.max(currentStep - 1, 1);
    }

    function buildPayload() {
        return {
            package_type: packageType,
            service_package_id:
                packageType === 'fixed_package' ? servicePackageId : null,
            custom_items: selectedCustomItems.map((item) => ({
                id: item.id,
                qty: item.qty,
            })),
            customer_name: customer.name,
            customer_email: customer.email,
            customer_phone: customer.phone,
            motorcycle_type: motorcycle.type,
            motorcycle_brand: motorcycle.brand,
            motorcycle_model: motorcycle.model,
            motorcycle_year: motorcycle.year || null,
            plate_number: motorcycle.plateNumber || null,
            address_text: location.addressText,
            house_landmark: location.houseLandmark,
            latitude: location.latitude,
            longitude: location.longitude,
            service_date: schedule.serviceDate,
            service_time: schedule.serviceTime,
            notes: schedule.notes || null,
        };
    }

    function currentStepForErrors(errors: Record<string, string>): number {
        const errorKeys = Object.keys(errors);

        if (
            errorKeys.some((key) =>
                ['package_type', 'service_package_id', 'custom_items'].includes(
                    key,
                ),
            )
        ) {
            return 1;
        }

        if (
            errorKeys.some((key) =>
                [
                    'customer_name',
                    'customer_email',
                    'customer_phone',
                    'motorcycle_type',
                    'motorcycle_brand',
                    'motorcycle_model',
                    'motorcycle_year',
                    'plate_number',
                ].includes(key),
            )
        ) {
            return 2;
        }

        if (
            errorKeys.some((key) =>
                [
                    'address_text',
                    'house_landmark',
                    'latitude',
                    'longitude',
                    'service_date',
                    'service_time',
                    'notes',
                ].includes(key),
            )
        ) {
            return 3;
        }

        return 4;
    }

    function handleBookingSubmit() {
        validationScope = 3;
        backendErrors = {};
        submitMessage = '';

        if (stepHasErrors(1) || stepHasErrors(2) || stepHasErrors(3)) {
            submitTone = 'error';
            submitMessage =
                'Masih ada data booking yang perlu dilengkapi sebelum dikirim.';
            currentStep = stepHasErrors(1) ? 1 : stepHasErrors(2) ? 2 : 3;
            return;
        }

        isSubmitting = true;
        submitTone = 'info';
        submitMessage = 'Mengirim booking dan mengecek ulang slot servis...';

        router.post(storeBooking().url, buildPayload(), {
            preserveScroll: true,
            onError: (errors) => {
                backendErrors = errors as Record<string, string>;
                submitTone = 'error';
                submitMessage =
                    backendErrors.booking ??
                    'Periksa kembali data booking yang masih perlu diperbaiki.';
                currentStep = currentStepForErrors(backendErrors);
            },
            onFinish: () => {
                isSubmitting = false;
            },
        });
    }
</script>

<section
    id="booking-form"
    class="bg-[#FFF078] pt-14 pb-32 md:pt-20 md:pb-32 xl:py-20"
>
    <div class="mx-auto grid max-w-7xl gap-8 px-4 md:px-6 xl:grid-cols-[minmax(0,1fr)_24rem]">
        <div class="space-y-6">
            <SectionHeading
                eyebrow={headingEyebrow}
                title={headingTitle}
                description={headingDescription}
                titleClass="text-[#F45B26]"
                descriptionClass="text-[#F45B26]"
                titleStyle="color: #F45B26;"
                descriptionStyle="color: #F45B26;"
            />

            <div class="grid gap-3 sm:grid-cols-4">
                {#each steps as step}
                    <button
                        type="button"
                        aria-current={currentStep === step.number
                            ? 'step'
                            : undefined}
                        aria-label={`Buka step ${step.number}: ${step.title}`}
                            class={`rounded-[1.25rem] border px-4 py-3 text-left transition ${
                            currentStep === step.number
                                ? 'border-primary/25 bg-primary/8 shadow-sm'
                                : 'border-border/70 bg-card'
                        }`}
                        disabled={isSubmitting}
                        onclick={() => {
                            if (step.number <= currentStep)
                                currentStep = step.number;
                        }}
                    >
                        <p
                            class={`text-xs uppercase tracking-[0.16em] ${
                                currentStep === step.number
                                    ? 'text-[#D12052]'
                                    : 'text-muted-foreground'
                            }`}
                        >
                            Step {step.number}
                        </p>
                        <p
                            class={`mt-1 text-sm font-semibold ${
                                currentStep === step.number
                                    ? 'text-[#D12052]'
                                    : 'text-foreground'
                            }`}
                        >
                            {step.title}
                        </p>
                        {#if step.number < 4}
                            <p
                                class={`mt-2 text-xs ${
                                    stepHasErrors(step.number) &&
                                    validationScope >= step.number
                                        ? 'text-destructive'
                                        : currentStep === step.number
                                          ? 'text-[#D12052]'
                                          : 'text-muted-foreground'
                                }`}
                            >
                                {stepHasErrors(step.number) &&
                                validationScope >= step.number
                                    ? 'Perlu dilengkapi'
                                    : 'Siap lanjut'}
                            </p>
                        {/if}
                    </button>
                {/each}
            </div>

            <Card class="border-border/70 bg-card/95 shadow-sm">
                <CardHeader class="gap-3">
                    <div class="flex items-center justify-between gap-3">
                        <CardTitle class="text-xl"
                            >{steps.find((step) => step.number === currentStep)
                                ?.title}</CardTitle
                        >
                        <Badge
                            variant="secondary"
                            class="rounded-full px-3 py-1 text-xs"
                            >Step {currentStep} / 4</Badge
                        >
                    </div>
                </CardHeader>
                <CardContent class="space-y-6">
                    {#if currentStep === 1}
                        <BookingPackageSelector
                            bind:packageType
                            bind:servicePackageId
                            {packageTypes}
                            {packages}
                            errors={visibleErrors}
                        />
                        <BookingCustomItemsSelector
                            bind:selectedItems={selectedCustomItems}
                            {customItems}
                            visible={packageType === 'custom_package'}
                            errors={visibleErrors}
                        />
                    {/if}

                    {#if currentStep === 2}
                        <BookingMotorInfoFields
                            bind:customer
                            bind:motorcycle
                            {motorcycleTypes}
                            errors={visibleErrors}
                        />
                    {/if}

                    {#if currentStep === 3}
                        <div class="space-y-8">
                            <BookingLocationPicker
                                bind:location
                                errors={visibleErrors}
                            />
                            <BookingSchedulePicker
                                bind:schedule
                                {availableSlots}
                                {minDate}
                                errors={visibleErrors}
                            />
                        </div>
                    {/if}

                    {#if currentStep === 4}
                        <BookingReviewPanel
                            {packageType}
                            {selectedPackage}
                            selectedCustomItems={selectedCustomItemDetails}
                            {customer}
                            {motorcycle}
                            {location}
                            {schedule}
                            {subtotal}
                            {serviceFee}
                            {total}
                        />
                    {/if}

                    {#if submitMessage}
                        <div
                            role="status"
                            aria-live="polite"
                            class={`rounded-[1.25rem] px-4 py-3 text-sm leading-6 ${
                                submitTone === 'error'
                                    ? 'border border-destructive/30 bg-destructive/5 text-destructive'
                                    : 'border border-primary/22 bg-primary/8 text-foreground'
                            }`}
                        >
                            {submitMessage}
                        </div>
                    {/if}

                    <div
                        class="flex flex-col gap-3 border-t border-border/70 pt-5 sm:flex-row sm:justify-between"
                    >
                        <Button
                            type="button"
                            variant="outline"
                            onclick={goToPreviousStep}
                            disabled={currentStep === 1 || isSubmitting}
                        >
                            Kembali
                        </Button>

                        {#if currentStep < 4}
                            <Button
                                type="button"
                                onclick={goToNextStep}
                                disabled={isSubmitting}
                            >
                                Lanjut ke step berikutnya
                            </Button>
                        {:else}
                            <Button
                                type="button"
                                onclick={handleBookingSubmit}
                                disabled={isSubmitting}
                                aria-label="Kirim booking servis sekarang"
                            >
                                {#if isSubmitting}
                                    <Spinner class="size-4" />
                                    Mengirim booking...
                                {:else}
                                    Kirim booking sekarang
                                {/if}
                            </Button>
                        {/if}
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="space-y-4 xl:sticky xl:top-24 xl:self-start">
            <BookingPriceSummary
                {packageType}
                {selectedPackage}
                selectedCustomItems={selectedCustomItemDetails}
                {subtotal}
                {serviceFee}
                {total}
            />

            <div
                class="hidden rounded-[1.5rem] border border-border/70 bg-card/95 p-4 text-sm leading-6 text-muted-foreground shadow-sm xl:block"
            >
                <p class="font-semibold text-foreground">Panduan singkat</p>
                <ul class="mt-2 space-y-2">
                    <li>Isi form bertahap dari atas ke bawah.</li>
                    <li>Lihat estimasi harga di panel kanan.</li>
                    <li>Data tetap dicek ulang saat booking dikirim.</li>
                </ul>
            </div>
        </div>
    </div>
</section>
