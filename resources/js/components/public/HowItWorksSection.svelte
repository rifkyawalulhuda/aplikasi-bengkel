<script lang="ts">
    import type { LandingStep } from '@/types';

    let {
        steps,
    }: {
        steps: LandingStep[];
    } = $props();

    const stepColors = [
        'bg-primary text-primary-foreground',
        'bg-secondary text-secondary-foreground',
        'bg-accent text-accent-foreground',
    ];
</script>

<section class="bg-background">
    <div class="mx-auto flex max-w-7xl flex-col gap-12 px-4 py-16 md:px-6 md:py-24">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-primary">
                Langkah Mudah Servis Motor
            </p>
            <h2 class="mt-4 text-balance text-3xl font-black tracking-[-0.04em] text-foreground md:text-5xl">
                Tiga langkah singkat untuk mulai booking dan tunggu konfirmasi admin.
            </h2>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            {#each steps.slice(0, 3) as step, index (step.title)}
                <article class="relative rounded-[2rem] border border-border/70 bg-card px-6 py-8 text-center shadow-[0_24px_60px_-46px_rgba(15,23,42,0.22)]">
                    <div class="mx-auto flex w-fit flex-col items-center gap-5">
                        <span
                            class={`flex h-14 w-14 items-center justify-center rounded-2xl text-lg font-black ${stepColors[index] ?? stepColors[0]}`}
                        >
                            0{index + 1}
                        </span>
                        <div class="space-y-3">
                            <h3 class="text-xl font-bold tracking-[-0.02em] text-foreground">
                                {step.title}
                            </h3>
                            <p class="text-sm leading-7 text-muted-foreground">
                                {step.description}
                            </p>
                        </div>
                    </div>
                </article>
            {/each}
        </div>
    </div>
</section>
