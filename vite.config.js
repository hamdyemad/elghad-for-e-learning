import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/sass/bootstrap.scss',
                'resources/sass/icons.scss',
                'resources/sass/app-dark.scss',
                'resources/sass/app-rtl.scss',
                'resources/sass/bootstrap-dark.scss'
            ],
            refresh: true,
        }),
    ],
});
