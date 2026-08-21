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
          <NuxtLink v-else :to="section.to" class="nav-link">
            {{ section.label }}
          </NuxtLink>
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

        <ThemeToggle class="hidden md:flex" />

        <!-- Auth actions -->
        <Button as="NuxtLink" to="/login" variant="ghost" size="sm" class="hidden md:flex">
          {{ t('button.log_in') }}
        </Button>
        <Button as="NuxtLink" to="/get-started" size="sm" class="cta-nav-btn hidden md:flex px-5">
          {{ t('button.get_started') }}
        </Button>

        <!-- Hamburger (mobile) -->
        <Button
          variant="ghost"
          size="icon"
          class="md:hidden"
          :aria-label="mobileOpen ? t('common.close_menu') : t('common.open_menu')"
          @click="mobileOpen = !mobileOpen"
        >
          <Icon :name="mobileOpen ? 'mdi-close' : 'mdi-menu'" size="20" />
        </Button>
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

<script setup lang="ts">
  import { Button } from '~/components/ui/button'

  const { t } = useI18n()
  const { isDark } = useColorMode()
  const { menuOpen: langMenuOpen, languages, currentLang, selectLang, locale } = useLanguageSwitcher()
  const { sections } = useNavSections()

  const mobileOpen = ref(false)
  const isScrolled = ref(false)

  function onSelectLang(code: string) {
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
    background: color-mix(in srgb, var(--card) 82%, transparent);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    border-bottom: 1px solid color-mix(in srgb, var(--foreground) 6%, transparent);
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
    display: grid;
    /* Equal outer columns so the middle (nav) column is truly centered on
       the bar, regardless of how wide the logo vs. controls end up being —
       a plain flex `justify-content: space-between` can't do this since it
       only pushes content to the extremes, not center the middle item. */
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    column-gap: 32px;
  }

  .logo-link {
    display: flex;
    align-items: center;
    justify-self: start;
  }
  .logo-img {
    height: 34px;
    width: auto;
  }

  /* ── Desktop Nav ── */
  .desktop-links {
    display: none;
    align-items: center;
    justify-self: center;
    gap: 32px;
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
    color: color-mix(in srgb, var(--foreground) 60%, transparent);
    text-decoration: none;
    transition: color 0.15s;
    cursor: pointer;
  }
  .nav-link:hover,
  .nav-link.active {
    color: var(--primary);
  }

  /* ── Controls ── */
  .nav-controls {
    display: flex;
    align-items: center;
    justify-self: center;
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
    border: 1px solid color-mix(in srgb, var(--foreground) 10%, transparent);
    border-radius: 999px;
    background: color-mix(in srgb, var(--foreground) 4%, transparent);
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--foreground);
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
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
    border-radius: 16px;
    box-shadow: 0 14px 34px color-mix(in srgb, var(--foreground) 14%, transparent);
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
    color: var(--foreground);
    font-size: 0.85rem;
    text-align: left;
  }
  .lang-option:hover {
    background: color-mix(in srgb, var(--primary) 8%, transparent);
  }
  .lang-option.active {
    color: var(--primary);
    font-weight: 700;
    background: color-mix(in srgb, var(--primary) 5%, transparent);
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
