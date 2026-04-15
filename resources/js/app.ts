import { createInertiaApp } from '@inertiajs/svelte';
import AuthLayout from '@/layouts/AuthLayout.svelte';
import AdminAuthLayout from '@/layouts/admin/AdminAuthLayout.svelte';
import AdminLayout from '@/layouts/admin/AdminLayout.svelte';
import PublicLayout from '@/layouts/public/PublicLayout.svelte';
import SettingsLayout from '@/layouts/settings/Layout.svelte';
import { initializeFlashToast } from '@/lib/flash-toast';
import { initializeTheme } from '@/lib/theme.svelte';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name.startsWith('public/'):
                return PublicLayout;
            case name.startsWith('admin/auth/'):
                return AdminAuthLayout;
            case name.startsWith('admin/'):
                return AdminLayout;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AdminLayout, SettingsLayout];
            default:
                return null;
        }
    },
    progress: {
        color: '#c2410c',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
