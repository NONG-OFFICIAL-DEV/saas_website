<template>
  <section id="pricing" class="section-pad section-tint-peach">
    <v-container>
      <div class="text-center mb-10" data-aos="fade-up">
        <span class="section-tag">{{ t('studio_pricing.eyebrow') }}</span>
        <h2 class="section-title">{{ t('studio_pricing.title') }}</h2>
        <p class="section-sub studio-pricing-sub">{{ t('studio_pricing.subtitle') }}</p>
      </div>

      <!-- ── Billing cycle toggle ── -->
      <div v-if="!store.loading && hasPaidPlans" class="cycle-wrap">
        <div class="cycle-track" role="group" :aria-label="t('common.billing_cycle')">
          <button
            v-for="c in CYCLES"
            :key="c.months"
            class="cycle-btn"
            :class="{ 'cycle-btn--active': selectedMonths === c.months }"
            :aria-pressed="selectedMonths === c.months"
            @click="selectedMonths = c.months"
          >
            <span>{{ t(c.labelKey) }}</span>
            <span v-if="c.months > 1" class="cycle-btn__badge">
              -{{ savingsPct(c.months) }}%
            </span>
          </button>
        </div>
      </div>

      <InlineLoader v-if="store.loading" min-height="380px" />


      <v-alert
        v-else-if="store.error"
        type="info"
        variant="tonal"
        rounded="lg"
        icon="mdi-clock-outline"
        class="mx-auto"
        max-width="480"
      >
        {{ t('studio_pricing.unavailable') }}
      </v-alert>

      <!-- ── Plan cards ── -->
      <div v-else class="cards-grid" :data-count="store.plans.length" data-aos="fade-up">
        <div
          v-for="plan in store.plans"
          :key="plan.id"
          class="plan-card clay-surface"
          :class="{ 'plan-card--featured': plan.code === 'professional' }"
        >
          <v-chip v-if="plan.code === 'professional'" class="popular-badge" color="primary" size="x-small" variant="flat" prepend-icon="mdi-star">
            {{ t('common.most_popular') }}
          </v-chip>

          <h3 class="plan-name">{{ plan.name }}</h3>
          <p class="plan-desc">{{ plan.description }}</p>

          <div class="price-block">
            <template v-if="isFree(plan)">
              <div class="price-row">
                <span class="price-currency">$</span>
                <span class="price-amount price-amount--free">0</span>
              </div>
              <div class="price-meta">
                <v-chip size="x-small" color="primary" variant="tonal" prepend-icon="mdi-gift-outline">
                  {{ t('studio_pricing.trial_days', { days: plan.trial_days || 14 }) }}
                </v-chip>
              </div>
            </template>
            <template v-else>
              <div class="price-row">
                <span class="price-currency">$</span>
                <Transition name="price-flip" mode="out-in">
                  <span :key="selectedMonths" class="price-amount">{{ monthlyPrice(plan) }}</span>
                </Transition>
              </div>
              <div class="price-meta">
                <span class="price-per">{{ t('common.per_month') }}</span>
              </div>
            </template>
          </div>

          <v-divider />

          <ul class="feature-list">
            <li v-for="f in planFeatures(plan)" :key="f" class="feature-item">
              <v-avatar color="primary" variant="tonal" size="18" rounded="sm">
                <v-icon icon="mdi-check" size="10" />
              </v-avatar>
              <span>{{ f }}</span>
            </li>
          </ul>

          <v-btn
            :color="plan.code === 'professional' ? 'primary' : undefined"
            :variant="plan.code === 'professional' ? 'flat' : 'outlined'"
            rounded="lg"
            block
            append-icon="mdi-arrow-right"
            class="plan-cta"
            @click="$emit('select-plan', plan.code)"
          >
            {{ isFree(plan) ? t('button.start_free_trial') : t('button.get_started') }}
          </v-btn>
        </div>
      </div>
    </v-container>
  </section>
</template>

<script setup>
  import { computed, onMounted, ref } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useStudioPlansStore } from '@/stores/studioPlans'
  import InlineLoader from '@/components/global/InlineLoader.vue'

  const { t } = useI18n()
  const store = useStudioPlansStore()

  defineEmits(['select-plan'])

  onMounted(() => store.fetchPlans())

  const CYCLES = [
    { months: 1, labelKey: 'studio_pricing.monthly' },
    { months: 3, labelKey: 'studio_pricing.quarterly' },
    { months: 12, labelKey: 'studio_pricing.yearly' }
  ]
  const selectedMonths = ref(1)

  const hasPaidPlans = computed(() => store.plans.some(p => Number(p.price_monthly) > 0))

  const isFree = plan => Number(plan.price_monthly) === 0

  // The API gives price_monthly/price_quarterly/price_yearly as flat
  // totals for that billing period — normalize to an effective
  // per-month price so cards stay comparable across cycles.
  function totalForCycle(plan, months) {
    if (months === 1) return Number(plan.price_monthly)
    if (months === 3) return Number(plan.price_quarterly)
    return Number(plan.price_yearly)
  }
  function monthlyPrice(plan) {
    return (totalForCycle(plan, selectedMonths.value) / selectedMonths.value).toFixed(2)
  }
  function savingsPct(months) {
    const referencePlan = store.plans.find(p => Number(p.price_monthly) > 0)
    if (!referencePlan) return 0
    const monthly = Number(referencePlan.price_monthly)
    const perMonthAtCycle = totalForCycle(referencePlan, months) / months
    if (!monthly) return 0
    return Math.round((1 - perMonthAtCycle / monthly) * 100)
  }

  function planFeatures(plan) {
    const items = []
    items.push(
      plan.max_users
        ? t('studio_pricing.feature_users', { n: plan.max_users })
        : t('studio_pricing.feature_users_unlimited')
    )
    items.push(
      plan.storage_limit_gb
        ? t('studio_pricing.feature_storage', { n: plan.storage_limit_gb })
        : t('studio_pricing.feature_storage_unlimited')
    )
    items.push(
      plan.monthly_order_limit
        ? t('studio_pricing.feature_orders', { n: plan.monthly_order_limit })
        : t('studio_pricing.feature_orders_unlimited')
    )
    if (plan.has_online_gallery) items.push(t('studio_pricing.feature_online_gallery'))
    if (plan.has_reports) items.push(t('studio_pricing.feature_reports'))
    if (plan.has_telegram) items.push(t('studio_pricing.feature_telegram'))
    if (plan.has_api_access) items.push(t('studio_pricing.feature_api'))
    return items
  }
</script>

<style scoped>
  .studio-pricing-sub {
    max-width: 480px;
    margin: 0 auto;
  }

  .cycle-wrap {
    display: flex;
    justify-content: center;
    margin-bottom: 32px;
  }
  .cycle-track {
    display: inline-flex;
    align-items: center;
    background: rgba(var(--v-theme-on-surface), 0.06);
    border-radius: 999px;
    padding: 4px;
    gap: 2px;
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
    color: rgba(var(--v-theme-on-surface), 0.55);
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
  }
  .cycle-btn--active {
    background: rgb(var(--v-theme-primary));
    color: #fff;
    box-shadow: 0 2px 12px rgba(var(--v-theme-primary), 0.4);
  }
  .cycle-btn__badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.22);
  }
  .cycle-btn:not(.cycle-btn--active) .cycle-btn__badge {
    background: rgba(var(--v-theme-success), 0.15);
    color: rgb(var(--v-theme-success));
  }

  .cards-grid {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    max-width: 1080px;
    margin: 0 auto;
  }

  .plan-card {
    position: relative;
    padding: 28px 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  .plan-card--featured {
    border-color: rgba(var(--v-theme-primary), 0.35) !important;
  }
  .popular-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
  }

  .plan-name {
    font-size: 1.05rem;
    font-weight: 800;
    margin: 0;
  }
  .plan-desc {
    font-size: 0.82rem;
    color: rgba(var(--v-theme-on-surface), 0.55);
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
    color: rgba(var(--v-theme-on-surface), 0.5);
    margin-top: 4px;
  }
  .price-amount {
    font-size: 2.4rem;
    font-weight: 900;
    letter-spacing: -1.5px;
  }
  .price-amount--free {
    color: rgb(var(--v-theme-success));
  }
  .price-per {
    font-size: 0.75rem;
    color: rgba(var(--v-theme-on-surface), 0.5);
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

  .plan-cta {
    /* v-btn's `block` prop sets flex: 1 0 auto internally (meant for a
       horizontal row) — inside this vertical flex column that makes the
       button itself stretch to fill leftover height. Pin it back down;
       .feature-list's flex-grow: 1 is what should absorb that space. */
    flex: none !important;
  }

  .price-flip-enter-active,
  .price-flip-leave-active {
    transition: opacity 0.16s ease, transform 0.16s ease;
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
