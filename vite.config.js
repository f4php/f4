import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import f4 from 'vite-plugin-f4';
import { extra as composerExtra } from './composer.json';
import packageJson from './package.json';

export default defineConfig({
  plugins: [
    vue({
    }),
    f4({
      debug: true,
      backendUrl: `http://${composerExtra.f4.environments.local.server}`,
      dependencies: Object.keys(packageJson.dependencies),
    })
  ],
  clearScreen: false,
  build: {
    // assetsInlineLimit: 512000,
    rollupOptions: {
    }
  },
});