<template>
  <div
    v-if="loadingStore.isLoading && loadingStore.mode === 'bar'"
    class="global-progress-bar fixed inset-x-0 top-0 z-[3000] h-[3px] overflow-hidden bg-primary/20"
  >
    <div class="global-progress-bar-indeterminate h-full w-1/3 bg-primary" />
  </div>
</template>

<script setup lang="ts">
  // Deliberately not a full-screen overlay — see stores/loadingStore.ts and
  // the api clients' interceptors for why the global loader is opt-in only,
  // reserved for the rare operation with no local loading UI of its own.
  // This bar never blocks clicks (no backdrop) and sits above everything
  // only visually.
  const loadingStore = useLoadingStore()
</script>

<style scoped>
  .global-progress-bar-indeterminate {
    animation: global-progress-slide 1.2s ease-in-out infinite;
  }
  @keyframes global-progress-slide {
    0% {
      transform: translateX(-100%);
    }
    50% {
      transform: translateX(150%);
    }
    100% {
      transform: translateX(150%);
    }
  }
</style>
