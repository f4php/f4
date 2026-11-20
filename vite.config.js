import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import f4 from 'vite-plugin-f4';
import composerJson from './composer.json' with { type: 'json' };
import packageJson from './package.json' with { type: 'json' };

export default defineConfig({
  plugins: [
    vue({
    }),
    f4({
      debug: false,
      backendUrl: `http://${composerJson.extra.f4.environments.local.server}`,
      dependencies: Object.keys(packageJson.dependencies),
    })
  ],
  clearScreen: false,
  build: {
    // assetsInlineLimit: 512000,
    // rolldownOptions: {
    // }
  },
});