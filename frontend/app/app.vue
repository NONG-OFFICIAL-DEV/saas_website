<template>
  <div>
    <Notif ref="notifRef" dismissible :default-timeout="2000" />
    <Confirm ref="confirmRef" />
    <NuxtLoadingIndicator color="var(--primary)" />
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
    <Loading />
  </div>
</template>

<script setup lang="ts">
import Notif from '~/components/global/Notification.vue'
import Confirm from '~/components/global/Confirm.vue'
import Loading from '~/components/global/Loading.vue'

const notifRef = ref<InstanceType<typeof Notif> | null>(null)
const confirmRef = ref<InstanceType<typeof Confirm> | null>(null)

const instance = getCurrentInstance()!
const { locale, setLocale } = useI18n()

// Sets <html class="dark"> during SSR itself (cookie is readable on the
// server, unlike localStorage) — see useColorMode.ts for why this replaces
// Vuetify's useTheme().
const { isDark } = useColorMode()
useHead({
  htmlAttrs: {
    class: computed(() => (isDark.value ? 'dark' : ''))
  }
})

// Keyboard switcher
const handleKeyDown = (e: KeyboardEvent) => {
  if (e.shiftKey && e.ctrlKey && e.key === 'L') {
    e.preventDefault()
    const newLocale = locale.value === 'en' ? 'km' : 'en'
    // setLocale() (not locale.value = ...) — required to trigger the
    // lazy-loaded locale messages import; see useLanguageSwitcher.ts.
    setLocale(newLocale)
    localStorage.setItem('lang', newLocale)
  }
}

onMounted(() => {
  const app = instance.appContext.app

  // Register global methods for both Options and Composition APIs
  app.config.globalProperties.$notif = notifRef.value?.newAlert
  app.config.globalProperties.$confirm = confirmRef.value?.open

  // Restore saved language preference
  const savedLang = localStorage.getItem('lang')
  if (savedLang === 'en' || savedLang === 'km') {
    setLocale(savedLang)
  }

  // Bind keyboard event
  document.addEventListener('keydown', handleKeyDown)
})

onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleKeyDown)
})
</script>

<style>
html, body {
  overflow-y: auto !important;
}
</style>
