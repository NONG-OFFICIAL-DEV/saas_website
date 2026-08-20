<template>
  <v-progress-linear
    v-if="loadingStore.isLoading && loadingStore.mode === 'bar'"
    class="global-progress-bar"
    color="primary"
    height="3"
    indeterminate
  />
</template>

<script setup lang="ts">
  // Deliberately not a full-screen overlay — see stores/loadingStore.ts and
  // the api clients' interceptors for why the global loader is opt-in only,
  // reserved for the rare operation with no local loading UI of its own.
  // This bar never blocks clicks (no backdrop, not `persistent`) and sits
  // above everything only visually.
  const loadingStore = useLoadingStore()
</script>

<style scoped>
  .global-progress-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 3000;
    margin: 0;
  }
</style>
