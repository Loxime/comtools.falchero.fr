export default defineNuxtConfig({
  ssr: false,
  modules: ['@pinia/nuxt', '@nuxtjs/tailwindcss', '@nuxtjs/i18n'],
  css: ['~/assets/css/main.css'],
  devtools: { enabled: true },
  i18n: {
    strategy: 'no_prefix',
    defaultLocale: 'fr',
    locales: [
      { code: 'fr', name: 'Français' },
      { code: 'en', name: 'English' }
    ],
    vueI18n: './i18n.config.ts'
  },
  tailwindcss: {
    cssPath: '~/assets/css/main.css'
  },
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8080/api'
    }
  },
  typescript: {
    strict: true
  }
})
