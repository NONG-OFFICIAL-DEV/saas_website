<template>
  <TiltCard :max-tilt="4" :glare="false">
    <div class="plan-card clay-surface" :class="{ 'plan-card--featured': featured }">
      <Badge v-if="featured" class="popular-badge bg-primary text-primary-foreground border-transparent">
        <Icon name="mdi-star" size="12" />
        {{ t('common.most_popular') }}
      </Badge>

      <h3 class="plan-name">{{ plan.name }}</h3>
      <p class="plan-desc">{{ plan.description }}</p>

      <div class="price-block">
        <template v-if="isFree">
          <div class="price-row">
            <span class="price-currency">$</span>
            <span class="price-amount price-amount--free">0</span>
          </div>
          <div class="price-meta">
            <Badge class="bg-primary/10 text-primary border-transparent">
              <Icon name="mdi-gift-outline" size="12" />
              {{ t('studio_pricing.trial_days', { days: plan.trial_days || 14 }) }}
            </Badge>
          </div>
        </template>
        <template v-else>
          <div class="price-row">
            <span class="price-currency">$</span>
            <Transition name="price-flip" mode="out-in">
              <span :key="selectedMonths" class="price-amount">{{ priceDisplay }}</span>
            </Transition>
          </div>
          <div class="price-meta">
            <span class="price-per">{{ t('common.per_month') }}</span>
          </div>
        </template>
      </div>

      <div class="plan-divider" />

      <ul class="feature-list">
        <li v-for="f in features" :key="f" class="feature-item">
          <span class="plan-check-badge">
            <Icon name="mdi-check" size="10" />
          </span>
          <span>{{ f }}</span>
        </li>
      </ul>

      <Button
        :variant="featured ? 'default' : 'outline'"
        class="plan-cta w-full"
        @click="emit('select', plan.code, isFree ? undefined : cycleCode)"
      >
        {{ isFree ? t('button.start_free_trial') : t('button.get_started') }}
        <Icon name="mdi-arrow-right" size="16" />
      </Button>
    </div>
  </TiltCard>
</template>

<script setup lang="ts">
import { Badge } from '~/components/ui/badge'
import { Button } from '~/components/ui/button'
import type { StudioPlan } from '~/types'

defineProps<{
  plan: StudioPlan
  featured: boolean
  isFree: boolean
  /** Pre-formatted by useStudioPricing().monthlyPrice(plan) — already reflects the selected cycle. */
  priceDisplay: string
  /** Pre-resolved by the caller via useStudioPricing().planFeatures(plan) — callers may truncate the list themselves. */
  features: string[]
  selectedMonths: number
  cycleCode: string
}>()
const emit = defineEmits<{ select: [code: string, billingCycle: string | undefined] }>()

const { t } = useI18n()
</script>

<style scoped>
.plan-card {
  position: relative;
  padding: 28px 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  height: 100%;
}
.plan-card--featured {
  border-color: color-mix(in srgb, var(--primary) 35%, transparent) !important;
}
.popular-badge {
  position: absolute;
  top: -12px;
  left: 50%;
  transform: translateX(-50%);
  white-space: nowrap;
}

.plan-name {
  font-size: 1.05rem;
  font-weight: 800;
  margin: 0;
}
.plan-desc {
  font-size: 0.82rem;
  color: color-mix(in srgb, var(--foreground) 55%, transparent);
  line-height: 1.5;
  margin: 0;
  min-height: 40px;
}

.price-block {
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-height: 56px;
}
.price-row {
  display: flex;
  align-items: flex-start;
  gap: 2px;
}
.price-currency {
  font-size: 1rem;
  font-weight: 700;
  color: color-mix(in srgb, var(--foreground) 50%, transparent);
  margin-top: 4px;
}
.price-amount {
  font-size: 2.4rem;
  font-weight: 900;
  letter-spacing: -1.5px;
}
.price-amount--free {
  color: var(--success);
}
.price-per {
  font-size: 0.75rem;
  color: color-mix(in srgb, var(--foreground) 50%, transparent);
}

.feature-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 9px;
  flex-grow: 1;
}
.feature-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.82rem;
}
.plan-check-badge {
  width: 18px;
  height: 18px;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  background: color-mix(in srgb, var(--primary) 14%, transparent);
  color: var(--primary);
}
.plan-divider {
  height: 1px;
  background: color-mix(in srgb, var(--foreground) 10%, transparent);
}

.plan-cta {
  /* Inside this vertical flex column, an unconstrained button would
     stretch to fill leftover height. Pin it back down;
     .feature-list's flex-grow: 1 is what should absorb that space. */
  flex: none !important;
}

.price-flip-enter-active,
.price-flip-leave-active {
  transition: opacity var(--duration-fast) var(--ease-out-quart), transform var(--duration-fast) var(--ease-out-quart);
}
.price-flip-enter-from {
  opacity: 0;
  transform: translateY(6px);
}
.price-flip-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>
