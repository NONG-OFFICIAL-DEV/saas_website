<template>
  <div class="inline-loader" :style="{ minHeight }">
    <v-progress-circular indeterminate color="primary" :size="size" width="3" />
  </div>
</template>

<script setup>
  // Small, localized "this section is loading" indicator — replaces large
  // v-skeleton-loader grids across the site. minHeight reserves roughly the
  // space the real content will take, so it doesn't pop in with a layout
  // shift once loaded (see CLAUDE.md's loading-UX conventions).
  defineProps({
    size: { type: [Number, String], default: 28 },
    minHeight: { type: String, default: '160px' }
  })
</script>

<style scoped>
  .inline-loader {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 24px 0;
  }
</style>
