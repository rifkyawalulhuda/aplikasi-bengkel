<script lang="ts">
    let {
        class: className = '',
        imageClass = '',
        alt = 'Logo bengkel home service',
    }: {
        class?: string;
        imageClass?: string;
        alt?: string;
    } = $props();
</script>

<div class={className}>
    <img
        src="/images/logo.png"
        {alt}
        class={imageClass}
        loading="eager"
        decoding="async"
    />
</div>
