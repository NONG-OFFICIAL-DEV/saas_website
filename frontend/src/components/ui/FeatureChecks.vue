<template>
  <div class="feature-checks">
    <div v-for="item in items" :key="item" class="check-item">
      <div class="check-icon">
        <v-icon icon="mdi-check" size="13" color="primary" />
      </div>
      <span>{{ item }}</span>
    </div>
  </div>
</template>

<script setup>
/**
 * FeatureChecks — a vertical list of checkmark bullet points.
 *
 * Props:
 *   items: string[]  — Already-translated strings to display.
 *
 * Usage:
 *   <FeatureChecks :items="['Fast checkout', 'Multi-unit pricing']" />
 */
defineProps({
  items: {
    type: Array,
    required: true,
  },
})
</script>

<style scoped>
.feature-checks {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.check-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  font-size: 0.875rem;
  line-height: 1.5;
  opacity: 0.75;
}
.check-icon {
  width: 22px;
  height: 22px;
  background: rgba(37, 99, 235, 0.1);
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 1px;
}
</style>