// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },
  pages: true,
  typescript: {
    strict: true
  },
  modules: [
    '@nuxt/ui',
    '@vueuse/nuxt'
  ],
  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.API_BASE_URL,
      storagePath: process.env.SERVER_STORAGE_PATH,
      PUSHER_APP_KEY: process.env.PUSHER_APP_KEY,
      PUSHER_APP_CLUSTER: process.env.PUSHER_APP_CLUSTER
    }
  },
  build: {
    transpile: ['@vuepic/vue-datepicker']
  },
  components: [
    {
      path: '~/components',
      pathPrefix: false
    }
  ]
})