// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  // Several pages/composables await more than one composable (useAsyncData,
  // etc.) in sequence within the same setup — without this, only the FIRST
  // await in a setup function keeps Nuxt's context; every await after that
  // loses it (NUXT_E1001) unless manually wrapped in nuxtApp.runWithContext.
  // AsyncLocalStorage-based context tracking fixes this at the root instead.
  experimental: {
    asyncContext: true
  },

  modules: ['vuetify-nuxt-module', '@pinia/nuxt', '@nuxtjs/i18n', '@nuxtjs/sitemap'],

  css: ['@mdi/font/css/materialdesignicons.css'],

  // The original app used flat, un-prefixed component names throughout
  // (AppNavbar, HeroSection, ProductCard, ...) regardless of which
  // subdirectory they lived in. Nuxt's default auto-import would prefix
  // these by directory (LayoutAppNavbar, SectionsHeroSection, ...) — turn
  // that off so every template can keep referencing components by their
  // original name with zero changes.
  components: [{ path: '~/components', pathPrefix: false }],

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
      apiBaseUrl: '',
      adminAppUrl: 'https://admin.nexstacktech.com',
      cmsApiUrl: 'http://127.0.0.1:8000/api/v1',
      studioApiUrl: '',
      studioAppUrl: 'https://photo-studio.nexstacktech.com'
    }
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
  },

  vuetify: {
    moduleOptions: {
      // App code still imports the icon font directly (matches the
      // current app's @mdi/font usage) rather than the module's own
      // CDN/icon-set fetching.
      styles: true
    },
    vuetifyOptions: {
      defaults: {
        VBtn: {
          rounded: 'lg'
        },
        VSelect: {
          density: 'comfortable',
          variant: 'outlined',
          color: 'primary'
        },
        VTextField: {
          variant: 'outlined',
          density: 'comfortable',
          color: 'primary'
        },
        VTextarea: {
          variant: 'outlined',
          density: 'comfortable',
          color: 'primary',
          autoGrow: true,
          rows: 3
        },
        VAutocomplete: {
          variant: 'outlined',
          density: 'comfortable',
          color: 'primary'
        },
        VDataTableServer: {
          class: 'rounded-lg'
        }
      },
      theme: {
        // TODO(Phase 4/5 - ThemeToggle): the original app reads
        // localStorage.getItem('theme') at module scope (defaulting to
        // 'dark') to persist the user's choice. That's an SSR hazard here,
        // so this is hardcoded for now until ThemeToggle.vue is ported with
        // a client-safe (cookie/plugin-based) persistence pattern.
        defaultTheme: 'dark',
        themes: {
          light: {
            dark: false,
            colors: {
              primary: '#3B5BDB',
              secondary: '#6C757D',
              surface: '#FFFFFF',
              background: '#FFFFFF',
              success: '#099268',
              warning: '#F76707',
              error: '#C92A2A',
              info: '#1971C2'
            }
          },
          dark: {
            dark: true,
            colors: {
              primary: '#748FFC',
              secondary: '#ADB5BD',
              surface: '#1E1E2E',
              background: '#151521',
              success: '#2F9E44',
              warning: '#F59F00',
              error: '#E03131',
              info: '#228BE6'
            }
          }
        }
      }
    }
  }
})
