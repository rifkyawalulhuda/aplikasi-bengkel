<script lang="ts">
    import { Button } from '@/components/ui/button';
    import { toast } from 'svelte-sonner';

    let {
        value,
        label,
        copiedLabel = 'Tersalin',
        class: className = '',
    }: {
        value: string;
        label: string;
        copiedLabel?: string;
        class?: string;
    } = $props();

    async function handleCopy(): Promise<void> {
        if (!value.trim()) {
            toast.error(`${label} belum tersedia.`);

            return;
        }

        if (!navigator.clipboard) {
            toast.error('Browser ini belum mendukung salin otomatis.');

            return;
        }

        await navigator.clipboard.writeText(value);
        toast.success(`${copiedLabel}: ${label}`);
    }
</script>

<Button
    type="button"
    variant="outline"
    size="sm"
    class={className}
    aria-label={`Salin ${label}`}
    onclick={handleCopy}
>
    Salin
</Button>
