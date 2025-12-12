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
                'resources/css/filter-sidebar.css',
                'resources/css/filter-section.css',
                'resources/css/product-show.css',
                'resources/css/empty-state-client.css',
                'resources/css/review-section.css',
                'resources/css/review-list.css',
                'resources/css/review-card.css',
                'resources/css/auth.css',
                'resources/css/404.css',
                'resources/css/500.css',
                'resources/css/cart.css',
                'resources/css/toast.css',
                'resources/css/confirm-modal-client.css',
                'resources/js/app.js',
                'resources/js/image-picker.js',
                'resources/js/editor-handler.js',
                'resources/js/scSortable.js',
                'resources/js/core.js',
                'resources/js/user-manager.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
