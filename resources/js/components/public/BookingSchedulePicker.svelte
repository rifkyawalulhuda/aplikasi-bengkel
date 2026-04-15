<script lang="ts">
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import type { BookingScheduleForm } from '@/types';

    let {
        schedule = $bindable<BookingScheduleForm>({
            serviceDate: '',
            serviceTime: '',
            notes: '',
        }),
        availableSlots,
        minDate,
        errors = {},
    }: {
        schedule?: BookingScheduleForm;
        availableSlots: string[];
        minDate: string;
        errors?: Record<string, string>;
    } = $props();
</script>

<div class="space-y-6">
    <div class="space-y-2">
        <h3 class="text-lg font-semibold text-foreground">4. Jadwal servis</h3>
        <p class="text-sm leading-6 text-muted-foreground">
            Tanggal tidak boleh di masa lalu, dan slot jam hanya bisa dipilih
            dari jam operasional yang tersedia.
        </p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="space-y-2">
            <Label for="service-date">Tanggal servis</Label>
            <Input
                id="service-date"
                type="date"
                bind:value={schedule.serviceDate}
                aria-invalid={Boolean(errors.service_date)}
                min={minDate}
            />
            {#if errors.service_date}
                <p class="text-sm font-medium text-destructive">
                    {errors.service_date}
                </p>
            {/if}
        </div>

        <div class="space-y-2">
            <Label for="service-time">Jam servis</Label>
            <select
                id="service-time"
                bind:value={schedule.serviceTime}
                aria-invalid={Boolean(errors.service_time)}
                disabled={availableSlots.length === 0}
                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
            >
                <option value=""
                    >{availableSlots.length === 0
                        ? 'Belum ada slot tersedia'
                        : 'Pilih jam servis'}</option
                >
                {#each availableSlots as slot}
                    <option value={slot}>{slot}</option>
                {/each}
            </select>
            {#if errors.service_time}
                <p class="text-sm font-medium text-destructive">
                    {errors.service_time}
                </p>
            {:else if availableSlots.length === 0}
                <p class="text-sm font-medium text-muted-foreground">
                    Slot servis belum tersedia. Hubungi admin untuk penjadwalan
                    manual.
                </p>
            {/if}
        </div>
    </div>

    <div class="space-y-2">
        <Label for="notes">Catatan tambahan</Label>
        <textarea
            id="notes"
            bind:value={schedule.notes}
            aria-invalid={Boolean(errors.notes)}
            rows="4"
            class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
            placeholder="Contoh: mohon servis dimulai sebelum jam 11 atau fokus cek bagian tertentu"
        ></textarea>
        {#if errors.notes}
            <p class="text-sm font-medium text-destructive">{errors.notes}</p>
        {/if}
    </div>
</div>
