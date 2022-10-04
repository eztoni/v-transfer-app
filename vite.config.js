import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'node_modules/dropzone/dist/dropzone.css',
                'node_modules/fullcalendar/main.css',
                'resources/css/app.css',
                'resources/css/styles.scss',
                'node_modules/chart.js/dist/chart.js',
                'resources/js/app.js',
                'resources/js/imports.js',
                'resources/js/init-alpine.js',
            ],
            refresh: true,
        }),
    ],
});
