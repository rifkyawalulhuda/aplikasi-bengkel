<script lang="ts">
    import type { Snippet } from 'svelte';

    let {
        title = '',
        description = '',
        keywords = [],
        canonicalUrl = '',
        children,
    }: {
        title?: string;
        description?: string;
        keywords?: string[];
        canonicalUrl?: string;
        children?: Snippet;
    } = $props();

    const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
    const fullTitle = $derived(title ? `${title} - ${appName}` : appName);
</script>

<svelte:head>
    <title>{fullTitle}</title>
    {#if description}
        <meta name="description" content={description} />
        <meta property="og:description" content={description} />
        <meta name="twitter:description" content={description} />
    {/if}
    {#if keywords.length > 0}
        <meta name="keywords" content={keywords.join(', ')} />
    {/if}
    {#if canonicalUrl}
        <link rel="canonical" href={canonicalUrl} />
        <meta property="og:url" content={canonicalUrl} />
    {/if}
    <meta property="og:title" content={fullTitle} />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content={fullTitle} />
    {@render children?.()}
</svelte:head>
