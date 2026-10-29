<template>
  <Teleport to="body">
    <Transition name="backdrop-fade">
      <div v-if="modelValue" class="mobile-backdrop" @click="close" />
    </Transition>

    <Transition name="drawer-slide">
      <div v-if="modelValue" class="mobile-drawer">
        <div class="drawer-header py-3">
          <img
            :src="isDark ? '/logo_white.png' : '/logo.png'"
            alt="Nexstack"
            class="drawer-logo"
          />
          <Button variant="secondary" size="icon-sm" @click="close">
            <Icon name="mdi-close" size="18" />
          </Button>
        </div>

        <nav class="drawer-nav">
          <template v-for="section in sections" :key="section.key">
            <NuxtLinkLocale v-if="section.type === 'link'" :to="section.to" class="drawer-link" @click="close">
              {{ section.label }}
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
            </NuxtLinkLocale>

            <div v-else class="drawer-group">
              <button class="drawer-link drawer-group-trigger" @click="toggleSection(section.key)">
                {{ section.label }}
                <svg
                  class="drawer-chevron"
                  :class="{ open: openSections[section.key] }"
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
                  <polyline points="6 9 12 15 18 9" />
                </svg>
              </button>

              <div v-if="openSections[section.key]" class="drawer-subitems">
                <NuxtLinkLocale
                  v-for="item in section.items"
                  :key="item.to"
                  :to="item.to"
                  class="drawer-subitem"
                  @click="close"
                >
                  <Icon v-if="item.icon" :name="item.icon" size="16" />
                  {{ item.label }}
                </NuxtLinkLocale>
                <NuxtLinkLocale v-if="section.viewAllTo" :to="section.viewAllTo" class="drawer-subitem drawer-view-all" @click="close">
                  {{ section.viewAllLabel }}
                </NuxtLinkLocale>
              </div>
            </div>
          </template>
        </nav>

        <div class="drawer-divider" />
        <div class="drawer-section-label">{{ t('common.language') || 'Language' }}</div>
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
          <span class="drawer-switch-label">
            {{ isDark ? t('common.theme_dark') || 'Dark Mode' : t('common.theme_light') || 'Light Mode' }}
          </span>
          <Switch :model-value="isDark" @update:model-value="toggleTheme" />
        </div>

        <NuxtLinkLocale to="/login" class="drawer-login" @click="close">
          {{ t('button.log_in') }}
        </NuxtLinkLocale>
        <NuxtLinkLocale to="/get-started" class="drawer-cta" @click="close">
          {{ t('button.get_started') }}
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
        </NuxtLinkLocale>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
  import { Button } from '~/components/ui/button'
  import { Switch } from '~/components/ui/switch'
  import type { NavSection } from '~/types'

  withDefaults(
    defineProps<{
      modelValue?: boolean
      sections: NavSection[]
    }>(),
    { modelValue: false }
  )
  const emit = defineEmits(['update:modelValue'])

  const { t } = useI18n()
  const { isDark, toggle: toggleTheme } = useColorMode()
  const { languages, selectLang, locale } = useLanguageSwitcher()

  const openSections = reactive<Record<string, boolean>>({})
  function toggleSection(key: string) {
    openSections[key] = !openSections[key]
  }

  function close() {
    emit('update:modelValue', false)
  }
</script>

<style scoped>
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
    background: var(--card);
    box-shadow: var(--shadow-xl);
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
    border-bottom: 1px solid color-mix(in srgb, var(--foreground) 5%, transparent);
  }
  .drawer-logo {
    height: 32px;
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
    color: var(--foreground);
    font-weight: 600;
    transition:
      background var(--duration-fast) var(--ease-out-quart),
      color var(--duration-fast) var(--ease-out-quart);
  }
  .drawer-link:hover {
    background: color-mix(in srgb, var(--primary) 5%, transparent);
    color: var(--primary);
  }
  .drawer-group-trigger {
    width: 100%;
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: inherit;
  }
  .drawer-chevron {
    opacity: 0.6;
    transition: transform var(--duration-fast) var(--ease-out-quart);
  }
  .drawer-chevron.open {
    transform: rotate(180deg);
  }
  .drawer-subitems {
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: 2px 8px 8px 24px;
  }
  .drawer-subitem {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 10px;
    text-decoration: none;
    color: color-mix(in srgb, var(--foreground) 75%, transparent);
    font-size: 0.85rem;
    font-weight: 600;
  }
  .drawer-subitem:hover {
    background: color-mix(in srgb, var(--primary) 5%, transparent);
    color: var(--primary);
  }
  .drawer-view-all {
    color: var(--primary);
    font-weight: 700;
  }
  .drawer-login {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 16px 20px 0;
    padding: 14px;
    border-radius: 14px;
    border: 1px solid color-mix(in srgb, var(--foreground) 12%, transparent);
    color: var(--foreground);
    text-decoration: none;
    font-weight: 700;
  }
  .drawer-divider {
    height: 1px;
    background: color-mix(in srgb, var(--foreground) 8%, transparent);
    margin: 15px 20px;
  }
  .drawer-section-label {
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: color-mix(in srgb, var(--foreground) 40%, transparent);
    padding: 0 20px;
    margin-bottom: 12px;
  }
  .drawer-switch-label {
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: color-mix(in srgb, var(--foreground) 40%, transparent);
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
    border: 1px solid color-mix(in srgb, var(--foreground) 10%, transparent);
    background: transparent;
    text-decoration: none;
    color: var(--foreground);
    text-align: left;
  }
  .drawer-lang-btn.active {
    border-color: var(--primary);
    background: color-mix(in srgb, var(--primary) 5%, transparent);
    color: var(--primary);
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
    margin: 10px 20px 20px;
    padding: 16px;
    border-radius: 14px;
    background: var(--primary);
    color: white;
    text-align: center;
    font-weight: 700;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    box-shadow: var(--shadow-sm);
    transition:
      transform var(--duration-base) var(--ease-out-quart),
      box-shadow var(--duration-base) var(--ease-out-quart);
  }
  .drawer-cta:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
  }
  .backdrop-fade-enter-active,
  .backdrop-fade-leave-active {
    transition: opacity var(--duration-base) var(--ease-out-quart);
  }
  .backdrop-fade-enter-from,
  .backdrop-fade-leave-to {
    opacity: 0;
  }
  .drawer-slide-enter-active,
  .drawer-slide-leave-active {
    transition: var(--duration-slow) var(--ease-out-expo);
  }
  .drawer-slide-enter-from,
  .drawer-slide-leave-to {
    transform: translateX(100%);
  }
</style>
