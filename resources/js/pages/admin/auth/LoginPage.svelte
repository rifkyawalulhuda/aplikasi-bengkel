<script lang="ts">
    import { Form } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import InputError from '@/components/InputError.svelte';
    import PasswordInput from '@/components/PasswordInput.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import { store } from '@/routes/login';
    import { request } from '@/routes/password';

    let {
        status = '',
        canResetPassword,
    }: {
        status?: string;
        canResetPassword: boolean;
    } = $props();
</script>

<AppHead title="Admin Login" />

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <h2 class="text-2xl font-semibold tracking-tight text-foreground">Masuk ke dashboard admin</h2>
        <p class="text-sm leading-6 text-muted-foreground">
            Gunakan akun admin untuk mengakses booking, paket servis, item custom, dan visitor analytics.
        </p>
    </div>

    {#if status}
        <div class="rounded-xl border border-border/70 bg-muted px-4 py-3 text-sm text-foreground">
            {status}
        </div>
    {/if}

    <Form {...store.form()} class="flex flex-col gap-6">
        {#snippet children({ errors, processing })}
            <div class="grid gap-5">
                <div class="grid gap-2">
                    <Label for="email">Email admin</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autocomplete="email"
                        placeholder="admin@bengkel.test"
                        class="text-black caret-black selection:bg-slate-200 selection:text-black"
                    />
                    <InputError message={errors.email} />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between gap-3">
                        <Label for="password">Password</Label>
                        {#if canResetPassword}
                            <a href={request().url} class="text-sm text-primary">
                                Lupa password?
                            </a>
                        {/if}
                    </div>
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Password"
                        class="text-black caret-black selection:bg-slate-200 selection:text-black"
                    />
                    <InputError message={errors.password} />
                </div>

                <Button type="submit" disabled={processing} class="w-full">
                    {#if processing}<Spinner />{/if}
                    Masuk admin
                </Button>
            </div>
        {/snippet}
    </Form>
</div>
