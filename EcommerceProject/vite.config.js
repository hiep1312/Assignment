import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/loading.css',
                'resources/css/message-center.css',
                'resources/css/products.css',
                'resources/css/pagination.css',
                'resources/css/product-grid.css',
                'resources/css/product-card.css',
                'resources/css/product-filter.css',
                'resources/js/app.js',
                'resources/js/image-picker.js',
                'resources/js/editor-handler.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
