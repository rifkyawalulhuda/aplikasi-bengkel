<script module lang="ts">
    export const layout = {
        title: 'Registration disabled',
        description: 'Admin accounts are provisioned internally for this application.',
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import TextLink from '@/components/TextLink.svelte';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
</script>

<AppHead title="Registration disabled" />

<Card>
    <CardHeader>
        <CardTitle>Registrasi publik dinonaktifkan</CardTitle>
    </CardHeader>
    <CardContent class="flex flex-col gap-4 text-sm leading-6 text-muted-foreground">
        <p>
            Aplikasi ini menggunakan area admin internal, jadi akun baru tidak dibuat dari sisi publik.
        </p>
        <TextLink href="/admin/login">Kembali ke login admin</TextLink>
    </CardContent>
</Card>
