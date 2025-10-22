import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/loading.css', 'resources/js/app.js', 'resources/js/image-picker.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
