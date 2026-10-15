import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Core
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/bootstrap.js',

                // Theme & Effects
                'resources/js/theme.js',
                'resources/js/cosmic-particles.js',
                'resources/js/echo.js',

                // Lottery
                'resources/js/lottery-canvas.js',
                'resources/js/lottery-sounds.js',

                // Trading — KLineChart wrapper
                'resources/js/trading/chart.js',
            ],
            refresh: [
                'resources/views/**',
                'routes/**',
                'app/**',
                'config/**',
                'lang/**',
            ],
        }),
    ],

    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '~': path.resolve(__dirname, 'resources'),
        },
    },

    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: false,
        cors: true,
        hmr: { host: 'localhost', protocol: 'ws' },
        watch: {
            usePolling: true,
            interval: 1000,
            ignored: [
                '**/.git/**',
                '**/node_modules/**',
                '**/vendor/**',
                '**/storage/**',
                '**/bootstrap/cache/**',
                '**/public/build/**',
                '**/database/*.sqlite',
                '**/*.log',
                '**/backup_migrations/**',
                '**/tests/**',
                '**/.env*',
            ],
            awaitWriteFinish: { stabilityThreshold: 500, pollInterval: 100 },
        },
    },

    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        sourcemap: false,
        manifest: true,
        target: 'es2020',
        minify: 'esbuild',
        cssMinify: true,
        cssCodeSplit: true,
        chunkSizeWarningLimit: 2000,
        reportCompressedSize: false,
        rollupOptions: {
            output: {
                manualChunks: {
                    'klinecharts': ['klinecharts'],
                },
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]',
            },
        },
    },

    css: { postcss: './postcss.config.js', devSourcemap: false },

    optimizeDeps: {
        include: ['klinecharts'],
        esbuildOptions: { target: 'es2020' },
    },

    logLevel: 'info',
    clearScreen: false,
});
