<template>
  <div :class="['w-full px-4', fluid ? '' : 'max-w-[1280px] mx-auto']">
    <slot />
  </div>
</template>

<script setup lang="ts">
// Vuetify's <v-container> replacement — same fluid/contained prop,
// matching the 1280px max-width already used throughout the site's own
// scoped styles (e.g. AppFooter.vue's .footer-inner, AppNavbar.vue's
// .nav-inner).
defineProps<{
  fluid?: boolean
}>()
</script>
