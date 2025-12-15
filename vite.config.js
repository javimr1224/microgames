import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const isProduction = process.env.NODE_ENV === 'production';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: !isProduction, // solo HMR en desarrollo
        }),
    ],
    build: isProduction
        ? {
              manifest: true,
              outDir: 'public/build',
              rollupOptions: {
                  input: 'resources/js/app.js',
              },
          }
        : undefined,
    server: !isProduction
        ? {
              host: true,
              port: 5173,
              strictPort: true,
              hmr: { host: 'localhost', port: 5173 },
              watch: {
                  usePolling: true,
                  interval: 100,
                  ignored: [
                      '**/docker/**',
                      '**/storage/**',
                      '**/vendor/**',
                      '**/node_modules/**',
                  ],
              },
          }
        : undefined,
});
