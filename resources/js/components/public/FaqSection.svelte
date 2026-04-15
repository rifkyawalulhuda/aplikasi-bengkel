<script lang="ts">
    import type { FaqItem } from '@/types';

    let {
        faqs,
    }: {
        faqs: FaqItem[];
    } = $props();
</script>

<section id="faq" class="bg-surface-soft/45">
    <div class="mx-auto flex max-w-5xl flex-col gap-10 px-4 py-16 md:px-6 md:py-24">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-primary">
                Pertanyaan Umum
            </p>
            <h2 class="mt-4 text-balance text-3xl font-black tracking-[-0.04em] text-foreground md:text-5xl">
                Informasi penting dibuat ringkas supaya pelanggan cepat yakin sebelum masuk ke flow booking.
            </h2>
            <p class="mt-4 text-base leading-7 text-muted-foreground md:text-lg">
                Jawaban di bawah ini sengaja disusun singkat, jelas, dan tetap ramah dibaca dari layar ponsel.
            </p>
        </div>

        <div class="grid gap-4">
            {#each faqs as faq (faq.question)}
                <details class="group rounded-[1.75rem] border border-border/70 bg-card px-5 py-5 shadow-[0_24px_60px_-48px_rgba(15,23,42,0.28)] md:px-7">
                    <summary class="flex cursor-pointer list-none items-start justify-between gap-5 text-left marker:hidden">
                        <span class="text-base font-bold tracking-[-0.02em] text-foreground md:text-lg">
                            {faq.question}
                        </span>
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-lg font-semibold text-primary transition-transform duration-300 group-open:rotate-45">
                            +
                        </span>
                    </summary>
                    <p class="pt-4 pr-1 text-sm leading-7 text-muted-foreground md:text-base">
                        {faq.answer}
                    </p>
                </details>
            {/each}
        </div>
    </div>
</section>
