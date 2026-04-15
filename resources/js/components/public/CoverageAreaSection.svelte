<script lang="ts">
    import { Badge } from '@/components/ui/badge';
    import { Card, CardContent } from '@/components/ui/card';
    import type { CoverageContent } from '@/types';
    import SectionHeading from './SectionHeading.svelte';

    let {
        serviceAreas,
        coverageContent,
    }: {
        serviceAreas: string[];
        coverageContent: CoverageContent;
    } = $props();
</script>

<section class="border-y border-border/60 bg-card/70">
    <div class="mx-auto flex max-w-7xl flex-col gap-8 px-4 py-14 md:px-6 md:py-20">
        <SectionHeading
            eyebrow="Coverage"
            title={coverageContent.title}
            description={coverageContent.description}
        />

        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_22rem]">
            <div class="flex flex-wrap gap-3">
                {#each serviceAreas as area}
                    <Badge variant="secondary" class="rounded-full px-4 py-1.5 text-sm">
                        {area}
                    </Badge>
                {/each}
            </div>

            <Card class="border-border/70 bg-background/90 shadow-sm">
                <CardContent class="space-y-3 pt-6">
                    <p class="text-sm font-semibold text-foreground">Catatan coverage</p>
                    <p class="text-sm leading-6 text-muted-foreground">
                        {coverageContent.note}
                    </p>
                </CardContent>
            </Card>
        </div>
    </div>
</section>
