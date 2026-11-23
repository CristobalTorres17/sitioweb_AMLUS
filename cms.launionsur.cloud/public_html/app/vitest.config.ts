import { defineConfig } from 'vitest/config';
import path from 'path';

export default defineConfig({
    test: {
        environment: 'jsdom',
        globals: true,
        setupFiles: ['./resources/js/Tests/setupTests.ts'],
        include: ['resources/js/Tests/**/*.test.{ts,tsx,js}'],
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js')
        }
    }
});
