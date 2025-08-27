import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
 // import PrimeVue from 'primevue/config';
 // import Aura from '@primeuix/themes/aura';
import Components from 'unplugin-vue-components/vite';
import { PrimeVueResolver } from '@primevue/auto-import-resolver'
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/assets/styles.scss',],
            refresh: true,
        }),
        // PrimeVue, {
        //     theme: {
        //         preset: Aura
        //     }
        // },
        tailwindcss(),
        vue(),
        Components({
            resolvers: [PrimeVueResolver()]
        })
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
            ziggy: path.resolve('vendor/tightenco/ziggy/dist/index.es.js'),
        }
    }

});
