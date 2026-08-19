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
        <template v-for="section in sections" :key="section.key">
          <NavDropdown
            v-if="section.type === 'dropdown'"
            :label="section.label"
            :items="section.items"
            :view-all-label="section.viewAllLabel"
            :view-all-to="section.viewAllTo"
          />
          <router-link v-else :to="section.to" class="nav-link">
            {{ section.label }}
          </router-link>
        </template>
      </nav>

      <!-- Right side controls -->
      <div class="nav-controls">
        <!-- ── Language switcher (visible at every width) ─────────── -->
        <div class="lang-switcher">
          <button
            class="lang-trigger"
            :aria-label="t('common.language')"
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
                @click="onSelectLang(lang.code)"
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

        <ThemeToggle class="d-none d-md-flex" />

        <!-- Auth actions -->
        <v-btn
          to="/login"
          variant="text"
          rounded="lg"
          size="small"
          class="d-none d-md-flex"
        >
          {{ t('button.log_in') }}
        </v-btn>
        <v-btn
          to="/get-started"
          color="primary"
          rounded="lg"
          variant="flat"
          size="small"
          class="cta-nav-btn d-none d-md-flex px-5"
        >
          {{ t('button.get_started') }}
        </v-btn>

        <!-- Hamburger (mobile) -->
        <v-btn
          :icon="mobileOpen ? 'mdi-close' : 'mdi-menu'"
          :aria-label="mobileOpen ? t('common.close_menu') : t('common.open_menu')"
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

  <MobileNavDrawer v-model="mobileOpen" :sections="sections" />
</template>

<script setup>
  import { ref, computed, onMounted, onUnmounted } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useTheme } from 'vuetify'
  import { useLanguageSwitcher } from '@/composables/useLanguageSwitcher'
  import { useNavSections } from '@/composables/useNavSections'
  import ThemeToggle from '@/components/nav/ThemeToggle.vue'
  import MobileNavDrawer from '@/components/nav/MobileNavDrawer.vue'
  import NavDropdown from '@/components/nav/NavDropdown.vue'

  const { t } = useI18n()
  const theme = useTheme()
  const { menuOpen: langMenuOpen, languages, currentLang, selectLang, locale } = useLanguageSwitcher()
  const { sections } = useNavSections()

  const isDark = computed(() => theme.global.name.value === 'dark')

  const mobileOpen = ref(false)
  const isScrolled = ref(false)

  function onSelectLang(code) {
    selectLang(code)
    mobileOpen.value = false
  }

  const onScroll = () => {
    isScrolled.value = window.scrollY > 12
  }
  onMounted(() =>
    window.addEventListener('scroll', onScroll, { passive: true })
  )
  onUnmounted(() => window.removeEventListener('scroll', onScroll))
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
</style>
