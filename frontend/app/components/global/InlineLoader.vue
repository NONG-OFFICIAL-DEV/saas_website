<template>
  <div class="flex w-full items-center justify-center py-6" :style="{ minHeight }">
    <span
      class="inline-block animate-spin rounded-full border-primary border-t-transparent"
      :style="{ width: `${size}px`, height: `${size}px`, borderWidth: '3px' }"
      role="status"
      aria-label="Loading"
    />
  </div>
</template>

<script setup lang="ts">
  // Small, localized "this section is loading" indicator — replaces large
  // skeleton grids across the site. minHeight reserves roughly the space
  // the real content will take, so it doesn't pop in with a layout shift
  // once loaded (see CLAUDE.md's loading-UX conventions).
  defineProps({
    size: { type: [Number, String], default: 28 },
    minHeight: { type: String, default: '160px' }
  })
</script>
