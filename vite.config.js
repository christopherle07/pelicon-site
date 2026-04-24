import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const isProd = process.env.NODE_ENV === 'production';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        outDir: isProd
            ? '/home/eyedeane/domains/pelicon.app/public_html/build'
            : 'public/build',
        emptyOutDir: true,
    },
});
