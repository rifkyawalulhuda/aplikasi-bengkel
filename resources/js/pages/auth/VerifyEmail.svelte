<script module lang="ts">
    export const layout = {
        title: 'Verification not required',
        description: 'Email verification is disabled for the current admin-only foundation.',
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import TextLink from '@/components/TextLink.svelte';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
</script>

<AppHead title="Verification not required" />

<Card>
    <CardHeader>
        <CardTitle>Verifikasi email tidak diwajibkan</CardTitle>
    </CardHeader>
    <CardContent class="flex flex-col gap-4 text-sm leading-6 text-muted-foreground">
        <p>
            Fondasi admin-only ini menonaktifkan email verification agar alur login internal tetap sederhana.
        </p>
        <TextLink href="/admin/login">Kembali ke login admin</TextLink>
    </CardContent>
</Card>
