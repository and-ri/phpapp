import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        tailwindcss(),
    ],
    root: './',
    build: {
        outDir: './www/assets',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                app: './static/js/app.js',
            },
            output: {
                entryFileNames: 'js/[name].js',
                chunkFileNames: 'js/[name].js',
                assetFileNames: ({ name }) => {
                    if (name && name.endsWith('.css')) return 'css/[name][extname]';
                    return '[name][extname]';
                },
            }
        },
    },
    css: {
        devSourcemap: true
    },
    optimizeDeps: {
    include: ['@tailwindcss/vite']
  }
});
