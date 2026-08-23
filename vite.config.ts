import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';

export default defineConfig({
    optimizeDeps: {
        // The dependency scanner spends minutes crawling the barrel files of
        // @lucide/vue and reka-ui, which stalls Inertia's SSR warmup. Declaring
        // the dependencies up front lets us skip the scan entirely.
        noDiscovery: true,
        include: [
            '@inertiajs/core',
            '@inertiajs/vue3',
            '@laravel/passkeys',
            '@lucide/vue',
            '@vueuse/core',
            'class-variance-authority',
            'clsx',
            'reka-ui',
            'tailwind-merge',
            'vue',
            'vue-input-otp',
            'vue-sonner',
        ],
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                    optimizedFallbacks: false,
                }),
            ],
        }),
        inertia(),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        wayfinder({
            formVariants: true,
        }),
    ],
});
