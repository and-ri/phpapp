import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        tailwindcss(),
    ],
    build: {
        outDir: './www/assets',
        emptyOutDir: true,
        // keep CSS as a separate file (linked from header.twig)
        cssCodeSplit: false,
        rollupOptions: {
            input: {
                app: './static/js/app.js',
            },
            output: {
                // iife: plain <script src> works without type="module"
                format: 'iife',
                entryFileNames: 'js/[name].js',
                chunkFileNames: 'js/[name].js',
                assetFileNames: ({ name }) => {
                    // single CSS bundle (cssCodeSplit: false) is always app.css
                    if (name && name.endsWith('.css')) return 'css/app[extname]';
                    return '[name][extname]';
                },
            }
        },
    },
    css: {
        devSourcemap: true
    }
});
