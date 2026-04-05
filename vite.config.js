import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'node_modules/@fortawesome/fontawesome-free/css/all.min.css'
            ],
            refresh: true,
        }),
    ],
    build: {
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
        rollupOptions: {
            output: {
                manualChunks: {
                    chart: ['chart.js'],
                    alpine: ['alpinejs'],
                    fontawesome: ['@fortawesome/fontawesome-free'],
                },
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/vendor/**'],
        },
    },
});
