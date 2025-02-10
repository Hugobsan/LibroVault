import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { quasar, transformAssetUrls } from '@quasar/vite-plugin';
import tailwindcss from 'tailwindcss';
import path from "path";

export default defineConfig({
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"), // ✅ Corrige o alias "@"
            'ziggy-js': path.resolve('vendor/tightenco/ziggy/dist/index.js'),
        }
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        vue({
            template: { transformAssetUrls },
        }),
        quasar(),
        tailwindcss('tailwind.config.js'),
    ],
});
