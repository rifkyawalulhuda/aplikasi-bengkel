<script lang="ts">
    import { Badge } from '@/components/ui/badge';
    import { Card, CardContent } from '@/components/ui/card';
    import SectionHeading from './SectionHeading.svelte';
    import type { TestimonialItem } from '@/types';

    let {
        testimonials,
    }: {
        testimonials: TestimonialItem[];
    } = $props();
</script>

<section class="mx-auto flex max-w-7xl flex-col gap-8 px-4 py-14 md:px-6 md:py-20">
    <SectionHeading
        eyebrow="Testimoni"
        title="Nada komunikasinya dibuat dekat dengan kebutuhan pengguna harian motor."
        description="Testimoni ini berfungsi sebagai social proof awal sambil menunggu data pelanggan riil setelah MVP berjalan."
    />

    <div class="grid gap-4 lg:grid-cols-3">
        {#each testimonials as item (item.name)}
            <Card class="border-border/70 bg-card/95 shadow-sm">
                <CardContent class="flex h-full flex-col gap-5 pt-6">
                    <Badge variant="secondary" class="w-fit rounded-full px-3 py-1 text-xs">
                        {item.vehicle}
                    </Badge>

                    <blockquote class="text-pretty text-base leading-7 text-foreground">
                        "{item.quote}"
                    </blockquote>

                    <p class="mt-auto text-sm font-semibold text-primary">{item.name}</p>
                </CardContent>
            </Card>
        {/each}
    </div>
</section>
