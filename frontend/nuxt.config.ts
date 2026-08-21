// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from '@tailwindcss/vite'

export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  vite: {
    plugins: [tailwindcss()]
  },

  // Several pages/composables await more than one composable (useAsyncData,
  // etc.) in sequence within the same setup — without this, only the FIRST
  // await in a setup function keeps Nuxt's context; every await after that
  // loses it (NUXT_E1001) unless manually wrapped in nuxtApp.runWithContext.
  // AsyncLocalStorage-based context tracking fixes this at the root instead.
  experimental: {
    asyncContext: true
  },

  modules: ['@pinia/nuxt', '@nuxtjs/i18n', '@nuxtjs/sitemap'],

  // @mdi/font stays (see Icon.vue) — the app's own ~90 icon call sites keep
  // Material Design Icons rather than switching icon sets, so the visual
  // design doesn't shift as part of this Tailwind/shadcn-vue migration.
  css: ['@mdi/font/css/materialdesignicons.css', '~/assets/css/tailwind.css'],

  // The original app used flat, un-prefixed component names throughout
  // (AppNavbar, HeroSection, ProductCard, ...) regardless of which
  // subdirectory they lived in. Nuxt's default auto-import would prefix
  // these by directory (LayoutAppNavbar, SectionsHeroSection, ...) — turn
  // that off so every template can keep referencing components by their
  // original name with zero changes.
  //
  // components/ui/** (shadcn-vue's generated primitives) is excluded from
  // this auto-scan entirely — each ui/x/ folder has both an index.ts
  // barrel and the Xxx.vue file itself, which collide under a flattened
  // naming scheme. shadcn-vue's own convention is explicit imports from
  // the barrel anyway (e.g. `import { Button } from '~/components/ui/button'`),
  // not auto-import, so this matches its intended usage.
  components: [
    { path: '~/components', pathPrefix: false, ignore: ['**/ui/**'] }
  ],

  // The admin CMS is auth-gated (Sanctum token in localStorage), has zero
  // SEO value, and depends on the Tiptap rich-text editor (a real DOM
  // dependency) — force it fully client-rendered instead of guarding every
  // individual localStorage/DOM call site against running during SSR.
  routeRules: {
    '/admin/**': { ssr: false },
    // Mirrors the old router's `{ path: 'docs', redirect: '/documentation' }`.
    '/docs': { redirect: '/documentation' }
  },

  // Mirrors the current app's VITE_APP_* build-time env vars, but as
  // runtime config — same built image can point at different backends per
  // environment via container env vars (NUXT_PUBLIC_*), no rebuild needed.
  runtimeConfig: {
    public: {
      smartStoreApiUrl: '',
      smartStoreAppUrl: 'https://admin.nexstacktech.com',
      cmsApiUrl: 'http://127.0.0.1:8000/api/v1',
      studioApiUrl: '',
      studioAppUrl: 'https://photo-studio.nexstacktech.com'
    },
  },

  app: {
    head: {
      htmlAttrs: { lang: 'en' },
      title: 'Nexstack',
      link: [
        { rel: 'icon', type: 'image/png', href: '/icon.png' },
        { rel: 'shortcut icon', href: '/icon.png' },
        {
          rel: 'stylesheet',
          href: 'https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&family=Hanuman:wght@100..900&display=swap'
        }
      ],
      meta: [
        { name: 'robots', content: 'index, follow' },
        { name: 'google-site-verification', content: 'ZeyAQDc9FJsKbQc9IyCNfbyR2zNPZOGezQNTPKT5MBM' }
      ]
    }
  },

  site: {
    url: 'https://www.nexstacktech.com'
  },

  i18n: {
    locales: [
      { code: 'en', file: 'en.json', name: 'English' },
      { code: 'km', file: 'km.json', name: 'Khmer' }
    ],
    defaultLocale: 'en',
    strategy: 'no_prefix',
    langDir: 'locales'
  }
})
