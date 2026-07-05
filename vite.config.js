import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

/** Paths that trigger a full browser reload (Blade, PHP, Filament). */
const fullReloadPaths = [
    'resources/views/**',
    'resources/css/filament/**',
    'app/Filament/**',
    'app/Providers/Filament/**',
    'routes/**',
];

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/metronic.css',
                'resources/css/filament/workspace-shell.css',
                'resources/css/filament/dashboard.css',
                'resources/css/filament/posters-list.css',
                'resources/css/filament/poster-record-modal.css',
                'resources/css/filament/home-editor.css',
                'resources/css/filament/auth.css',
                'resources/css/filament/profile.css',
                'resources/css/filament/change-password.css',
                'resources/js/filament/metronic.js',
                'resources/js/filament/workspace-shell.js',
                'resources/js/filament/portfolio-alerts.js',
            ],
            refresh: fullReloadPaths,
        }),
        tailwindcss(),
    ],
    server: {
        host: '127.0.0.1',
        port: 5173,
        strictPort: true,
        hmr: {
            host: '127.0.0.1',
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
