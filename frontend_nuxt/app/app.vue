<template>
  <v-app>
    <Notif ref="notifRef" dismissible :default-timeout="2000" />
    <Confirm ref="confirmRef" />
    <NuxtLoadingIndicator color="primary" />
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
    <Loading />
  </v-app>
</template>

<script setup lang="ts">
import Notif from '~/components/global/Notification.vue'
import Confirm from '~/components/global/Confirm.vue'
import Loading from '~/components/global/Loading.vue'

const notifRef = ref<InstanceType<typeof Notif> | null>(null)
const confirmRef = ref<InstanceType<typeof Confirm> | null>(null)

const instance = getCurrentInstance()!
const { locale } = useI18n()

// Keyboard switcher
const handleKeyDown = (e: KeyboardEvent) => {
  if (e.shiftKey && e.ctrlKey && e.key === 'L') {
    e.preventDefault()
    const newLocale = locale.value === 'en' ? 'km' : 'en'
    locale.value = newLocale as typeof locale.value
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
  if (savedLang) {
    locale.value = savedLang as typeof locale.value
  }

  // Bind keyboard event
  document.addEventListener('keydown', handleKeyDown)
})

onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleKeyDown)
})
</script>

<style>
/* In app.vue or a global CSS file */
html, body {
  overflow-y: auto !important;
}

.v-application__wrap {
  min-height: 100dvh !important;
  display: block !important; /* Allow normal document flow */
}
</style>
