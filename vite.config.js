import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { quasar, transformAssetUrls } from '@quasar/vite-plugin';
import tailwindcss from 'tailwindcss';
import path from "path";

const isProduction = process.env.APP_ENV === 'production';

export default defineConfig({
    resolve: {
        alias: {
            "ziggy-js": path.resolve(__dirname, "vendor/tightenco/ziggy/dist/index.js")
        },
        optimizeDeps: ["ziggy-js", "ziggy-vue"]
    },
    plugins: [
        laravel({
            input:'resources/js/app.ts',
            refresh: !isProduction,
        }),
        vue({
            template: { transformAssetUrls },
            script: {
                defineModel: true,
                propsDestructure: true
            }
        }),
        quasar(),
        tailwindcss('tailwind.config.js'),
    ],
});
