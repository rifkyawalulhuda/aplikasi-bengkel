<script lang="ts">
    let {
        eyebrow,
        title,
        description = '',
        titleClass = '',
        descriptionClass = '',
        titleStyle = '',
        descriptionStyle = '',
    }: {
        eyebrow: string;
        title: string;
        description?: string;
        titleClass?: string;
        descriptionClass?: string;
        titleStyle?: string;
        descriptionStyle?: string;
    } = $props();
</script>

<div class="flex max-w-2xl flex-col gap-3">
    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-primary">
        {eyebrow}
    </p>
    <div class="flex flex-col gap-2">
        <h2
            class={`text-balance text-3xl font-semibold tracking-tight text-foreground md:text-4xl ${titleClass}`}
            style={titleStyle}
        >
            {title}
        </h2>
        {#if description}
            <p
                class={`text-pretty text-base leading-7 text-muted-foreground md:text-lg ${descriptionClass}`}
                style={descriptionStyle}
            >
                {description}
            </p>
        {/if}
    </div>
</div>
