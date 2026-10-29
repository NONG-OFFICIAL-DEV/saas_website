<template>
  <div class="cycle-wrap">
    <div class="cycle-track" role="group" :aria-label="t('common.billing_cycle')">
      <button
        v-for="c in cycles"
        :key="c.months"
        class="cycle-btn"
        :class="{ 'cycle-btn--active': selectedMonths === c.months }"
        :aria-pressed="selectedMonths === c.months"
        @click="emit('update:selectedMonths', c.months)"
      >
        <span>{{ t(c.labelKey) }}</span>
        <span v-if="c.months > 1" class="cycle-btn__badge">-{{ savingsPct(c.months) }}%</span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  cycles: { months: number, code: string, labelKey: string }[]
  selectedMonths: number
  savingsPct: (months: number) => number
}>()
const emit = defineEmits<{ 'update:selectedMonths': [months: number] }>()

const { t } = useI18n()
</script>

<style scoped>
.cycle-wrap {
  display: flex;
  justify-content: center;
  margin-bottom: 32px;
}
.cycle-track {
  display: inline-flex;
  align-items: center;
  background: color-mix(in srgb, var(--foreground) 6%, transparent);
  border-radius: 999px;
  padding: 4px;
  gap: 2px;
  overflow-x: auto;
  max-width: 100%;
}
.cycle-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 16px;
  border: none;
  background: transparent;
  border-radius: 999px;
  font-size: 0.78rem;
  font-weight: 600;
  color: color-mix(in srgb, var(--foreground) 55%, transparent);
  cursor: pointer;
  white-space: nowrap;
  transition: background var(--duration-base) var(--ease-out-quart), color var(--duration-base) var(--ease-out-quart);
}
.cycle-btn--active {
  background: var(--primary);
  color: #fff;
  box-shadow: var(--shadow-sm);
}
.cycle-btn__badge {
  font-size: 0.65rem;
  font-weight: 700;
  padding: 1px 6px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.22);
}
.cycle-btn:not(.cycle-btn--active) .cycle-btn__badge {
  background: color-mix(in srgb, var(--success) 15%, transparent);
  color: var(--success);
}
</style>
