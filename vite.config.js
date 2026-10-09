import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

// 5173 is often taken by other projects; keep this app on its own port (VITE_PORT in .env)
const port = Number(process.env.VITE_PORT || 5180);

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        // Runs inside the `node` container; the browser reaches it through the published port
        host: '0.0.0.0',
        port,
        strictPort: true,
        origin: `http://localhost:${port}`,
        // The page is served by nginx on another port, so it must be allowed to load scripts from here
        cors: { origin: /^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?$/ },
        hmr: { host: 'localhost', clientPort: port },
        watch: {
            ignored: ['**/storage/framework/views/**', '**/docker/data/**', '**/vendor/**'],
        },
    },
});
