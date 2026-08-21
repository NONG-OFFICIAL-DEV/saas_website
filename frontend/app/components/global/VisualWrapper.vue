<template>
  <div class="visual-wrapper" :class="`visual-${color}`">
    <slot />
  </div>
</template>

<script setup lang="ts">
/**
 * VisualWrapper — gradient container for section mock UIs.
 *
 * Props:
 *   color: 'blue' | 'indigo' | 'teal'
 */
withDefaults(
  defineProps<{
    color?: 'blue' | 'indigo' | 'teal'
  }>(),
  { color: 'blue' }
)
</script>

<style scoped>
.visual-wrapper {
  border-radius: 24px;
  padding: 32px;
  position: relative;
  overflow: hidden;
  min-height: 340px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.visual-blue   { background: linear-gradient(135deg, #eff6ff, #dbeafe); }
.visual-indigo { background: linear-gradient(135deg, #eef2ff, #e0e7ff); }
.visual-teal   { background: linear-gradient(135deg, #f0fdfa, #ccfbf1); }
</style>
