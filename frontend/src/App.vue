<template>
  <v-app>
    <Notif ref="notifRef" dismissible :default-timeout="2000" />
    <Confirm ref="confirmRef" />
    <router-view />
    <Loading />
  </v-app>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, getCurrentInstance } from 'vue'
import Notif from '@/components/global/Notification.vue'
import Confirm from '@/components/global/Confirm.vue'
import Loading from '@/components/global/Loading.vue'

// Refs to components
const notifRef = ref(null)
const confirmRef = ref(null)

// Get current Vue instance
const instance = getCurrentInstance()

// Keyboard switcher
const handleKeyDown = (e) => {
  if (e.shiftKey && e.ctrlKey && e.key === 'L') {
    e.preventDefault()
    const currentLocale = instance.appContext.config.globalProperties.$i18n.locale
    const newLocale = currentLocale === 'en' ? 'km' : 'en'
    instance.appContext.config.globalProperties.$i18n.locale = newLocale
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
    instance.appContext.config.globalProperties.$i18n.locale = savedLang
  }

  // Bind keyboard event
  document.addEventListener('keydown', handleKeyDown)
})

onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleKeyDown)
})
</script>

<style>
/* In App.vue or a global CSS file */
html, body {
  overflow-y: auto !important;
}

.v-application__wrap {
  min-height: 100dvh !important;
  display: block !important; /* Allow normal document flow */
}
</style>
