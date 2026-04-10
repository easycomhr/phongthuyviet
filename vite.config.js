import { defineConfig } from 'vitest/config'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.js'],
      refresh: true,
    }),
    vue(),
    tailwindcss(),
  ],
  resolve: {
    alias: {
      vue: 'vue/dist/vue.esm-bundler.js',
      '@': '/Users/THAMND/Documents/Data/Freelancer/Sources/phongthuy/resources/js',
    },
  },
  test: {
    environment: 'happy-dom',
    globals: true,
  },
})
