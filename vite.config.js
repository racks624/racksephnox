import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Exclude vendor directory from watching
            refreshPaths: [
                'resources/**',
                'routes/**',
                'app/**',
                'config/**',
                'database/**'
            ],
        }),
        vue(),
    ],
    server: {
        watch: {
            ignored: ['**/vendor/**', '**/node_modules/**', '**/storage/**']
        }
    }
});
