import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['Backend/resources/css/app.css', 'Backend/resources/js/app.js'],
            refresh: true,
        }),
    ],
});
