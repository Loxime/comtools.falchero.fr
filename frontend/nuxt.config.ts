export default defineNuxtConfig({
  ssr: false,
  modules: ['@pinia/nuxt'],
  devtools: { enabled: true },
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8080/api'
    }
  },
  typescript: {
    strict: true
  }
})
