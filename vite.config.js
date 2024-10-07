import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { defineConfig } from 'vite';


export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
            '@': path.resolve('resources/js'),
            '~': path.resolve('resources/js/Utils'),
            'ziggy-js': path.resolve('vendor/tightenco/ziggy'),
            'ziggy-vue': path.resolve('vendor/tightenco/ziggy/dist/vue.m.js'),
            'ziggy-router': path.resolve('vendor/tightenco/ziggy/src/js/Router.js'),
        },
    },
});
