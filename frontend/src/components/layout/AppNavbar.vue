<template>
  <header class="glass-nav" :class="{ scrolled: isScrolled }">
    <div class="nav-inner">
      <!-- Logo -->
      <a href="/" class="logo-link">
        <img
          :src="isDark ? '/logo_white.png' : '/logo.png'"
          alt="Nexstack Logo"
          class="logo-img"
        />
      </a>

      <!-- Desktop links (hidden on mobile) -->
      <nav class="desktop-links">
        <component
          :is="link.to ? 'router-link' : 'a'"
          v-for="link in navLinks"
          :key="link.to || link.href"
          :to="link.to"
          :href="link.href"
          class="nav-link"
        >
          {{ t(link.key) }}
        </component>
      </nav>

      <!-- Right side controls -->
      <div class="nav-controls">
        <!-- ── Language switcher (visible at every width) ─────────── -->
        <div class="lang-switcher">
          <button
            class="lang-trigger"
            :aria-label="t('lang.label')"
            @click="langMenuOpen = !langMenuOpen"
          >
            <img
              :src="currentLang.imgSrc"
              :alt="currentLang.alt"
              class="flag-img"
            />
            <span class="lang-label">{{ currentLang.code.toUpperCase() }}</span>
            <svg
              class="chevron"
              :class="{ open: langMenuOpen }"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              width="12"
              height="12"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polyline points="6 9 12 15 18 9" />
            </svg>
          </button>

          <Transition name="lang-drop">
            <div v-if="langMenuOpen" class="lang-dropdown" @click.stop>
              <button
                v-for="lang in languages"
                :key="lang.code"
                class="lang-option"
                :class="{ active: locale === lang.code }"
                @click="selectLang(lang.code)"
              >
                <img :src="lang.imgSrc" :alt="lang.alt" class="flag-img" />
                <span class="lang-option-label">{{ lang.label }}</span>
                <svg
                  v-if="locale === lang.code"
                  class="check-icon"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  width="13"
                  height="13"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <polyline points="20 6 9 17 4 12" />
                </svg>
              </button>
            </div>
          </Transition>
        </div>

        <!-- Theme toggle -->
        <button
          class="theme-btn d-none d-md-flex"
          :aria-label="isDark ? t('nav.theme_light') : t('nav.theme_dark')"
          @click="toggleTheme"
        >
          <Transition name="icon-swap" mode="out-in">
            <svg
              v-if="isDark"
              key="sun"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              width="18"
              height="18"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <circle cx="12" cy="12" r="5" />
              <line x1="12" y1="1" x2="12" y2="3" />
              <line x1="12" y1="21" x2="12" y2="23" />
              <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
              <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
              <line x1="1" y1="12" x2="3" y2="12" />
              <line x1="21" y1="12" x2="23" y2="12" />
              <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
              <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
            </svg>
            <svg
              v-else
              key="moon"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              width="18"
              height="18"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
            </svg>
          </Transition>
        </button>

        <!-- CTA -->
        <v-btn
          color="primary"
          rounded="lg"
          variant="flat"
          size="small"
          class="cta-nav-btn d-none d-md-flex px-5"
          to="/auth/register?intent=trial"
        >
          {{ t('landing.hero.cta_primary') }}
        </v-btn>

        <!-- Hamburger (mobile) -->
        <v-btn
          :icon="mobileOpen ? 'mdi-close' : 'mdi-menu'"
          :aria-label="mobileOpen ? t('nav.close_menu') : t('nav.open_menu')"
          variant="text"
          size="small"
          rounded="lg"
          class="d-md-none"
          @click="mobileOpen = !mobileOpen"
        />
      </div>
    </div>

    <!-- Overlay for language click-outside -->
    <div
      v-if="langMenuOpen"
      class="lang-overlay"
      @click="langMenuOpen = false"
    />
  </header>

  <!-- ── Mobile drawer ──────────────────────────────────────────────── -->
  <Teleport to="body">
    <Transition name="backdrop-fade">
      <div
        v-if="mobileOpen"
        class="mobile-backdrop"
        @click="mobileOpen = false"
      />
    </Transition>

    <Transition name="drawer-slide">
      <div v-if="mobileOpen" class="mobile-drawer">
        <div class="drawer-header py-3">
          <img
            :src="isDark ? '/logo_white.png' : '/logo.png'"
            alt="Nexstack"
            class="drawer-logo"
          />
          <v-btn
            icon="mdi-close"
            size="small"
            variant="tonal"
            @click="mobileOpen = false"
          ></v-btn>
        </div>

        <nav class="drawer-nav">
          <component
            :is="link.to ? 'router-link' : 'a'"
            v-for="link in navLinks"
            :key="link.to || link.href"
            :to="link.to"
            :href="link.href"
            class="drawer-link"
            @click="mobileOpen = false"
          >
            {{ t(link.key) }}
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              width="14"
              height="14"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polyline points="9 18 15 12 9 6" />
            </svg>
          </component>
        </nav>

        <div class="drawer-divider" />
        <div class="drawer-section-label">
          {{ t('lang.label') || 'Language' }}
        </div>
        <div class="drawer-lang-col">
          <button
            v-for="lang in languages"
            :key="lang.code"
            class="drawer-lang-btn"
            :class="{ active: locale === lang.code }"
            @click="selectLang(lang.code)"
          >
            <img :src="lang.imgSrc" :alt="lang.alt" class="drawer-flag" />
            <span>{{ lang.label }}</span>
            <svg
              v-if="locale === lang.code"
              class="drawer-lang-check"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              width="13"
              height="13"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polyline points="20 6 9 17 4 12" />
            </svg>
          </button>
        </div>

        <div class="drawer-divider" />
        <div class="drawer-theme-row">
          <span class="drawer-switch-label ma-0">
            {{
              isDark
                ? t('nav.theme_dark') || 'Dark Mode'
                : t('nav.theme_light') || 'Light Mode'
            }}
          </span>
          <v-switch
            hide-details
            inset
            density="compact"
            color="primary"
            @click="toggleTheme"
          ></v-switch>
        </div>

        <router-link to="/auth/register?intent=trial" class="drawer-cta" @click="mobileOpen = false">
          {{ t('landing.hero.cta_primary') }}
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            width="15"
            height="15"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <line x1="5" y1="12" x2="19" y2="12" />
            <polyline points="12 5 19 12 12 19" />
          </svg>
        </router-link>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
  import { ref, computed, onMounted, onUnmounted } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useTheme } from 'vuetify'

  const { t, locale } = useI18n()
  const theme = useTheme()

  const mobileOpen = ref(false)
  const langMenuOpen = ref(false)
  const isScrolled = ref(false)

  const isDark = computed(() => theme.global.name.value === 'dark')
  function toggleTheme() {
    const next = isDark.value ? 'light' : 'dark'
    theme.global.name.value = next
    localStorage.setItem('theme', next)
  }

  const onScroll = () => {
    isScrolled.value = window.scrollY > 12
  }
  onMounted(() =>
    window.addEventListener('scroll', onScroll, { passive: true })
  )
  onUnmounted(() => window.removeEventListener('scroll', onScroll))

  const navLinks = [
    { to: '/', key: 'nav.home' },
    { to: '/products', key: 'nav.products' },
    { to: '/about', key: 'nav.about' },
    { href: '/#contact', key: 'landing.nav.contact' }
  ]

  const languages = computed(() => [
    {
      code: 'km',
      label: t('lang.km') || 'ខ្មែរ',
      imgSrc: 'https://flagcdn.com/w80/kh.png',
      alt: 'Khmer'
    },
    {
      code: 'en',
      label: t('lang.en') || 'English',
      imgSrc: 'https://flagcdn.com/w80/gb.png',
      alt: 'English'
    }
  ])

  const currentLang = computed(
    () =>
      languages.value.find(l => l.code === locale.value) ?? languages.value[0]
  )

  function selectLang(code) {
    locale.value = code
    langMenuOpen.value = false
    mobileOpen.value = false
  }
</script>

<style scoped>
  .glass-nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    height: 68px;
    background: rgba(var(--v-theme-surface), 0.82);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.06);
    transition: box-shadow 0.25s ease;
  }
  .glass-nav.scrolled {
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
  }

  .nav-inner {
    max-width: 1280px;
    margin: 0 auto;
    height: 100%;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .logo-link {
    display: flex;
    align-items: center;
    margin-right: 40px;
  }
  .logo-img {
    height: 34px;
    width: auto;
  }

  /* ── Desktop Nav ── */
  .desktop-links {
    display: none;
    align-items: center;
    gap: 32px;
    margin-right: auto;
    height: 100%;
  }
  @media (min-width: 960px) {
    .desktop-links {
      display: flex;
    }
  }

  .nav-link {
    font-size: 0.875rem;
    font-weight: 600;
    color: rgba(var(--v-theme-on-surface), 0.6);
    text-decoration: none;
    transition: color 0.15s;
    cursor: pointer;
  }
  .nav-link:hover,
  .nav-link.active {
    color: rgb(var(--v-theme-primary));
  }

  /* ── Controls ── */
  .nav-controls {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .lang-switcher {
    position: relative;
  }
  .lang-trigger {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px 5px 6px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.1);
    border-radius: 999px;
    background: rgba(var(--v-theme-on-surface), 0.04);
    font-size: 0.75rem;
    font-weight: 700;
    color: rgb(var(--v-theme-on-surface));
    cursor: pointer;
  }
  .flag-img {
    width: 20px;
    height: 14px;
    border-radius: 2px;
    object-fit: cover;
  }
  .chevron {
    opacity: 0.5;
    transition: transform 0.2s;
  }
  .chevron.open {
    transform: rotate(180deg);
  }

  .lang-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    min-width: 150px;
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
    border-radius: 16px;
    box-shadow: 0 14px 34px rgba(var(--v-theme-on-surface), 0.14);
    padding: 6px;
    z-index: 2000;
  }
  .lang-option {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: none;
    background: transparent;
    cursor: pointer;
    color: rgb(var(--v-theme-on-surface));
    font-size: 0.85rem;
    text-align: left;
  }
  .lang-option:hover {
    background: rgba(var(--v-theme-primary), 0.08);
  }
  .lang-option.active {
    color: rgb(var(--v-theme-primary));
    font-weight: 700;
    background: rgba(var(--v-theme-primary), 0.05);
  }

  .theme-btn {
    padding: 8px;
    border-radius: 10px;
    border: none;
    background: transparent;
    cursor: pointer;
    color: rgb(var(--v-theme-on-surface));
  }
  .theme-btn:hover {
    background: rgba(var(--v-theme-on-surface), 0.05);
  }

  /* ── Mobile ── */
  .mobile-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(4px);
    z-index: 1100;
  }
  .mobile-drawer {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: 300px;
    max-width: 85vw;
    background: rgb(var(--v-theme-surface));
    z-index: 1200;
    display: flex;
    flex-direction: column;
    padding-bottom: 40px;
    overflow-y: auto;
  }
  .drawer-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    height: 68px;
    border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.05);
  }
  .drawer-logo {
    height: 32px;
  }
  .drawer-close {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: none;
    background: rgba(var(--v-theme-on-surface), 0.05);
    color: rgb(var(--v-theme-on-surface));
  }

  .drawer-nav {
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .drawer-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-radius: 12px;
    text-decoration: none;
    color: rgb(var(--v-theme-on-surface));
    font-weight: 600;
  }
  .drawer-link:hover {
    background: rgba(var(--v-theme-primary), 0.05);
    color: rgb(var(--v-theme-primary));
  }

  .drawer-divider {
    height: 1px;
    background: rgba(var(--v-theme-on-surface), 0.08);
    margin: 15px 20px;
  }
  .drawer-section-label {
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(var(--v-theme-on-surface), 0.4);
    padding: 0 20px;
    margin-bottom: 12px;
  }

  .drawer-switch-label {
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(var(--v-theme-on-surface), 0.4);
  }

  .drawer-lang-col {
    padding: 0 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }
  .drawer-flag {
    width: 30px;
  }
  .drawer-lang-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-radius: 12px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.1);
    background: transparent;
    text-decoration: none;
    color: rgb(var(--v-theme-on-surface));
    text-align: left;
  }
  .drawer-lang-btn.active {
    border-color: rgb(var(--v-theme-primary));
    background: rgba(var(--v-theme-primary), 0.05);
    color: rgb(var(--v-theme-primary));
  }
  .drawer-lang-check {
    margin-left: auto;
  }

  .drawer-theme-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
  }

  .drawer-cta {
    margin: 20px;
    padding: 16px;
    border-radius: 14px;
    background: rgb(var(--v-theme-primary));
    color: white;
    text-align: center;
    font-weight: 700;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }

  /* Transitions */
  .lang-drop-enter-active,
  .lang-drop-leave-active {
    transition: all 0.2s ease;
  }
  .lang-drop-enter-from,
  .lang-drop-leave-to {
    opacity: 0;
    transform: translate(0%, 10px) scale(0.95);
  }
  .drawer-slide-enter-active,
  .drawer-slide-leave-active {
    transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .drawer-slide-enter-from,
  .drawer-slide-leave-to {
    transform: translateX(100%);
  }
</style>
