import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: 'shop.local',
        hmr: {
            host: 'shop.local',
        },
        watch: {
             //usePolling: true,
             //interval: 3000,  //     Optional: Polling-Intervall in Millisekunden
          },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
