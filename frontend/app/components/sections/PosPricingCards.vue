<template>
  <div class="pos-pricing-cards">
    <!-- ── Billing cycle toggle ── -->
    <div v-if="cycles.length > 1" class="cycle-wrap">
      <div class="cycle-track" role="group" :aria-label="t('common.billing_cycle')">
        <button
          v-for="c in cycles"
          :key="c.months"
          class="cycle-btn"
          :class="{ 'cycle-btn--active': selectedMonths === c.months }"
          :aria-pressed="selectedMonths === c.months"
          @click="selectedMonths = c.months"
        >
          <span>{{ c.label }}</span>
          <span v-if="Number(c.discount_percent) > 0" class="cycle-btn__badge">
            -{{ Number(c.discount_percent).toFixed(0) }}%
          </span>
        </button>
      </div>
    </div>

    <InlineLoader v-if="store.loading" min-height="320px" />
    <Alert
      v-else-if="!visiblePlans.length"
      class="flex items-center gap-2 border-info/30 bg-info/10 text-info mx-auto max-w-[480px]"
    >
      <Icon name="mdi-clock-outline" size="18" />
      <AlertDescription>{{ t('pricing.unavailable') }}</AlertDescription>
    </Alert>

    <RevealOnScroll v-else as="div" class="cards-grid" :data-count="visiblePlans.length">
      <TiltCard v-for="plan in visiblePlans" :key="plan.id" :max-tilt="4" :glare="false">
        <div class="plan-card clay-surface" :class="{ 'plan-card--featured': plan.popular }">
          <Badge v-if="plan.popular" class="popular-badge bg-primary text-primary-foreground border-transparent">
            <Icon name="mdi-star" size="12" />
            {{ t('common.most_popular') }}
          </Badge>

          <h3 class="plan-name">{{ plan.name }}</h3>
          <p class="plan-desc">{{ tagline(plan) }}</p>

          <div class="price-block">
            <template v-if="!available(plan)">
              <div class="price-unavailable">
                <Icon name="mdi-information-outline" size="14" />
                <span>{{ t('pricing.free_monthly_only') }}</span>
              </div>
            </template>
            <template v-else-if="isFree(plan)">
              <div class="price-row">
                <span class="price-currency">$</span>
                <span class="price-amount price-amount--free">0</span>
              </div>
              <div class="price-meta">
                <span class="price-per">{{ t('common.per_month') }}</span>
              </div>
            </template>
            <template v-else>
              <div class="price-row">
                <span class="price-currency">$</span>
                <span class="price-amount">{{ monthlyPrice(plan) }}</span>
              </div>
              <div class="price-meta">
                <span class="price-per">{{ t('common.per_month') }}</span>
                <Badge v-if="savingsPct(plan) > 0" class="bg-success/10 text-success border-transparent">
                  {{ t('pricing.save_pct', { pct: savingsPct(plan) }) }}
                </Badge>
              </div>
            </template>
          </div>

          <div class="plan-divider" />

          <ul class="feature-list">
            <li v-for="f in features(plan)" :key="f" class="feature-item">
              <Icon name="mdi-check" size="14" class="feature-check" />
              <span>{{ f }}</span>
            </li>
          </ul>

          <Button
            :variant="plan.popular ? 'default' : 'outline'"
            class="plan-cta w-full"
            :disabled="!available(plan)"
            @click="goToRegister"
          >
            {{ plan.code === 'enterprise' ? t('button.contact_sales') : plan.code === 'free' ? t('button.start_free') : t('button.get_started') }}
            <Icon name="mdi-arrow-right" size="16" />
          </Button>
        </div>
      </TiltCard>
    </RevealOnScroll>
  </div>
</template>

<script setup lang="ts">
  import { Alert, AlertDescription } from '~/components/ui/alert'
  import { Badge } from '~/components/ui/badge'
  import { Button } from '~/components/ui/button'
  import type { PosPlan, BillingCycle } from '~/types'

  const { t, locale } = useI18n()
  const store = usePosPlansStore()

  const POS_TAGLINES: Record<string, string> = {
    free: 'Get started for free',
    starter: 'For small teams',
    pro: 'Most popular choice',
    enterprise: 'For large organisations'
  }
  const tagline = (plan: PosPlan) => POS_TAGLINES[plan.code] ?? ''

  const visiblePlans = computed(() =>
    (store.plans ?? []).filter((p) => p.is_active).map((p) => ({ ...p, popular: p.code === 'pro' }))
  )

  const cycles = computed(() => {
    const seen = new Map<number, BillingCycle & { label?: string }>()
    ;(store.plans ?? []).forEach((plan: PosPlan) => {
      ;(plan.billing_cycles ?? [])
        .filter((c) => c.is_active)
        .forEach((c) => {
          if (!seen.has(c.months)) seen.set(c.months, c as BillingCycle & { label?: string })
        })
    })
    return [...seen.values()].sort((a, b) => a.months - b.months)
  })

  const selectedMonths = ref(1)

  const isFree = (plan: PosPlan) => parseFloat(String(plan.price_usd ?? 0)) === 0
  function cycleForSelected(plan: PosPlan) {
    return (plan.billing_cycles ?? []).find((c) => c.is_active && c.months === selectedMonths.value) ?? null
  }
  function available(plan: PosPlan) {
    if (isFree(plan)) return selectedMonths.value === 1
    return cycleForSelected(plan) !== null
  }
  function monthlyPrice(plan: PosPlan) {
    const base = Number(plan.price_usd ?? 0)
    const discount = Number(cycleForSelected(plan)?.discount_percent ?? 0) / 100
    return (base * (1 - discount)).toFixed(2)
  }
  function savingsPct(plan: PosPlan) {
    return Number(cycleForSelected(plan)?.discount_percent ?? 0)
  }
  function features(plan: PosPlan) {
    return posPlanBullets(plan, locale.value)
  }

  const localePath = useLocalePath()
  function goToRegister() {
    navigateTo(localePath('/onboarding/nexstack-pos'))
  }

  // Awaited (not onMounted) so live pricing is present in the server-rendered
  // HTML. fetchPlans() already no-ops on repeat calls once plans are cached,
  // so mounting this component again elsewhere (product page + pricing page)
  // never double-fetches.
  await useAsyncData('pos-plans', () => store.fetchPlans())
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
    gap: 5px;
    padding: 7px 16px;
    border: none;
    background: transparent;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 600;
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
    cursor: pointer;
    white-space: nowrap;
    transition: background var(--duration-base) var(--ease-out-quart), color var(--duration-base) var(--ease-out-quart);
  }
  .cycle-btn--active {
    background: var(--primary);
    color: #fff;
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

  .cards-grid {
    display: grid;
    gap: 18px;
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  }
  .cards-grid[data-count='1'] {
    max-width: 320px;
  }
  .cards-grid[data-count='2'] {
    max-width: 680px;
    grid-template-columns: repeat(2, 1fr);
  }
  .cards-grid[data-count='3'] {
    grid-template-columns: repeat(3, 1fr);
  }
  @media (max-width: 720px) {
    .cards-grid,
    .cards-grid[data-count='2'],
    .cards-grid[data-count='3'] {
      grid-template-columns: 1fr !important;
      max-width: 420px;
    }
  }

  .plan-card {
    position: relative;
    padding: 24px 22px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    height: 100%;
    transition: border-color var(--duration-base) var(--ease-out-quart), box-shadow var(--duration-base) var(--ease-out-quart);
  }
  .plan-card--featured {
    border-color: color-mix(in srgb, var(--primary) 45%, transparent) !important;
    box-shadow: var(--shadow-md), 0 0 0 1px color-mix(in srgb, var(--primary) 25%, transparent);
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
    font-size: 0.83rem;
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
    margin: 0;
    line-height: 1.5;
  }

  .price-block {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-height: 56px;
  }
  .price-row {
    display: flex;
    align-items: flex-start;
    gap: 2px;
    line-height: 1;
  }
  .price-currency {
    font-size: 0.95rem;
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
  .price-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }
  .price-per {
    font-size: 0.75rem;
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
  }
  .price-unavailable {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    font-style: italic;
    color: color-mix(in srgb, var(--foreground) 45%, transparent);
  }

  .plan-divider {
    height: 1px;
    background: color-mix(in srgb, var(--foreground) 10%, transparent);
  }

  .feature-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex-grow: 1;
  }
  .feature-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.82rem;
  }
  .feature-check {
    color: var(--primary);
    flex-shrink: 0;
  }

  .plan-cta {
    margin-top: auto;
  }
</style>
