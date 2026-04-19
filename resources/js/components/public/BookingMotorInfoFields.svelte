<script lang="ts">
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import type {
        BookingCustomerForm,
        BookingMotorcycleForm,
        SelectOption,
    } from '@/types';

    let {
        customer = $bindable<BookingCustomerForm>({
            name: '',
            phone: '',
        }),
        motorcycle = $bindable<BookingMotorcycleForm>({
            type: '',
            brand: '',
            model: '',
            year: '',
            plateNumber: '',
        }),
        motorcycleTypes,
        errors = {},
    }: {
        customer?: BookingCustomerForm;
        motorcycle?: BookingMotorcycleForm;
        motorcycleTypes: SelectOption[];
        errors?: Record<string, string>;
    } = $props();
</script>

<div class="space-y-6">
    <div class="space-y-2">
        <h3 class="text-lg font-semibold text-foreground">
            2. Data pelanggan dan motor
        </h3>
        <p class="text-sm leading-6 text-muted-foreground">
            Booking harus lengkap agar admin bisa memverifikasi jadwal dan
            kebutuhan servis tanpa bolak-balik konfirmasi.
        </p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="space-y-2">
            <Label for="customer-name">Nama pelanggan</Label>
            <Input
                id="customer-name"
                bind:value={customer.name}
                aria-invalid={Boolean(errors.customer_name)}
                autocomplete="name"
                placeholder="Contoh: Budi Santoso"
            />
            {#if errors.customer_name}
                <p class="text-sm font-medium text-destructive">
                    {errors.customer_name}
                </p>
            {/if}
        </div>

        <div class="space-y-2">
            <Label for="customer-phone">Nomor telepon / WhatsApp</Label>
            <Input
                id="customer-phone"
                bind:value={customer.phone}
                aria-invalid={Boolean(errors.customer_phone)}
                autocomplete="tel"
                inputmode="tel"
                placeholder="08xxxxxxxxxx"
            />
            {#if errors.customer_phone}
                <p class="text-sm font-medium text-destructive">
                    {errors.customer_phone}
                </p>
            {/if}
        </div>
    </div>

    <div class="rounded-[1.5rem] border border-border/70 bg-muted p-4">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="space-y-2">
                <Label for="motorcycle-type">Jenis motor</Label>
                <select
                    id="motorcycle-type"
                    bind:value={motorcycle.type}
                    aria-invalid={Boolean(errors.motorcycle_type)}
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                >
                    <option value="">Pilih jenis motor</option>
                    {#each motorcycleTypes as option (option.value)}
                        <option value={option.value}>{option.label}</option>
                    {/each}
                </select>
                {#if errors.motorcycle_type}
                    <p class="text-sm font-medium text-destructive">
                        {errors.motorcycle_type}
                    </p>
                {/if}
            </div>

            <div class="space-y-2">
                <Label for="motorcycle-brand">Merek motor</Label>
                <Input
                    id="motorcycle-brand"
                    bind:value={motorcycle.brand}
                    aria-invalid={Boolean(errors.motorcycle_brand)}
                    placeholder="Honda / Yamaha / Suzuki"
                />
                {#if errors.motorcycle_brand}
                    <p class="text-sm font-medium text-destructive">
                        {errors.motorcycle_brand}
                    </p>
                {/if}
            </div>

            <div class="space-y-2">
                <Label for="motorcycle-model">Model motor</Label>
                <Input
                    id="motorcycle-model"
                    bind:value={motorcycle.model}
                    aria-invalid={Boolean(errors.motorcycle_model)}
                    placeholder="Beat / NMAX / Vario"
                />
                {#if errors.motorcycle_model}
                    <p class="text-sm font-medium text-destructive">
                        {errors.motorcycle_model}
                    </p>
                {/if}
            </div>

            <div class="space-y-2">
                <Label for="motorcycle-year">Tahun motor (opsional)</Label>
                <Input
                    id="motorcycle-year"
                    bind:value={motorcycle.year}
                    aria-invalid={Boolean(errors.motorcycle_year)}
                    inputmode="numeric"
                    placeholder="2022"
                />
                {#if errors.motorcycle_year}
                    <p class="text-sm font-medium text-destructive">
                        {errors.motorcycle_year}
                    </p>
                {/if}
            </div>

            <div class="space-y-2 md:col-span-2">
                <Label for="plate-number">Plat nomor (opsional)</Label>
                <Input
                    id="plate-number"
                    bind:value={motorcycle.plateNumber}
                    aria-invalid={Boolean(errors.plate_number)}
                    placeholder="B 1234 XYZ"
                />
                {#if errors.plate_number}
                    <p class="text-sm font-medium text-destructive">
                        {errors.plate_number}
                    </p>
                {/if}
            </div>
        </div>
    </div>
</div>
