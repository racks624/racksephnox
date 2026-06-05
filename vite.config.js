import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/lottery/canvas-slot.js',
                'resources/js/lottery/sounds.js',
                'resources/js/lottery/svg-symbols.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            '@lottery': '/resources/js/lottery',
        },
    },
    optimizeDeps: {
        include: ['alpinejs', 'chart.js'],
    },
});
