import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { existsSync } from 'node:fs';

const isProd = process.env.NODE_ENV === 'production';
const interServerBuildDir = '/home/eyedeane/domains/pelicon.app/public_html/build';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        outDir: isProd && existsSync('/home/eyedeane/domains/pelicon.app/public_html')
            ? interServerBuildDir
            : 'public/build',
        emptyOutDir: true,
    },
});
