import { defineConfig } from 'vite';
import path from 'path';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    root: '.',
    server: {
        port: 5341, // You can change this port if needed
        cors: true,
        strictPort: true,
        origin: 'http://localhost:5341',
    },
    plugins: [tailwindcss()],
    build: {
        outDir: 'build',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                app: path.resolve(__dirname, 'main.tailwind.css'),
            }
        },
    },
    css: {
        devSourcemap: true,
    },
});
