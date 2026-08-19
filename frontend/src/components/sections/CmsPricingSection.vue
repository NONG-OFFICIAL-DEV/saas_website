<template>
  <section v-if="plans?.length" id="pricing" class="section-pad section-tint-peach">
    <v-container>
      <div class="text-center mb-10" data-aos="fade-up">
        <span class="section-tag">{{ t('product_detail.pricing_tag') }}</span>
        <h2 class="section-title">{{ t('product_detail.pricing_title') }}</h2>
      </div>

      <!-- ── Billing cycle toggle ── -->
      <div v-if="hasYearlyOption" class="cycle-wrap">
        <div class="cycle-track" role="group" :aria-label="t('common.billing_cycle')">
          <button
            class="cycle-btn"
            :class="{ 'cycle-btn--active': cycle === 'monthly' }"
            :aria-pressed="cycle === 'monthly'"
            @click="cycle = 'monthly'"
          >
            {{ t('product_detail.pricing_monthly') }}
          </button>
          <button
            class="cycle-btn"
            :class="{ 'cycle-btn--active': cycle === 'yearly' }"
            :aria-pressed="cycle === 'yearly'"
            @click="cycle = 'yearly'"
          >
            {{ t('product_detail.pricing_yearly') }}
          </button>
        </div>
      </div>

      <div class="cards-grid" :data-count="plans.length" data-aos="fade-up">
        <div
          v-for="plan in plans"
          :key="plan.id"
          class="plan-card"
          :class="{ 'plan-card--featured': plan.is_popular }"
        >
          <v-chip v-if="plan.is_popular" class="popular-badge" color="primary" size="x-small" variant="flat" prepend-icon="mdi-star">
            {{ t('common.most_popular') }}
          </v-chip>

          <h3 class="plan-name">{{ plan.name }}</h3>
          <p v-if="plan.tagline" class="plan-tagline">{{ plan.tagline }}</p>

          <div class="price-block">
            <template v-if="priceForCycle(plan) === null">
              <div class="price-contact">{{ t('button.contact_sales') }}</div>
            </template>
            <template v-else>
              <div class="price-row">
                <span class="price-currency">$</span>
                <span class="price-amount">{{ priceForCycle(plan) }}</span>
              </div>
              <span class="price-per">{{ t('common.per_month') }}</span>
            </template>
          </div>

          <v-divider />

          <ul v-if="plan.features?.length" class="feature-list">
            <li v-for="(f, idx) in plan.features" :key="idx" class="feature-item">
              <v-avatar color="primary" variant="tonal" size="18" rounded="sm">
                <v-icon icon="mdi-check" size="10" />
              </v-avatar>
              <span>{{ f }}</span>
            </li>
          </ul>

          <v-btn
            :color="plan.is_popular ? 'primary' : undefined"
            :variant="plan.is_popular ? 'flat' : 'outlined'"
            rounded="lg"
            block
            append-icon="mdi-arrow-right"
            class="plan-cta"
            @click="$emit('select-plan', plan)"
          >
            {{ plan.cta_label || t('button.get_started') }}
          </v-btn>
        </div>
      </div>
    </v-container>
  </section>
</template>

<script setup>
  import { computed, ref } from 'vue'
  import { useI18n } from 'vue-i18n'

  const props = defineProps({
    plans: { type: Array, default: () => [] }
  })
  defineEmits(['select-plan'])

  const { t } = useI18n()
  const cycle = ref('monthly')

  const hasYearlyOption = computed(() =>
    props.plans.some(p => p.yearly_price !== null && p.yearly_price !== undefined)
  )

  function priceForCycle(plan) {
    if (cycle.value === 'yearly') {
      if (plan.yearly_price === null || plan.yearly_price === undefined) return null
      return (Number(plan.yearly_price) / 12).toFixed(2)
    }
    if (plan.monthly_price === null || plan.monthly_price === undefined) return null
    return Number(plan.monthly_price).toFixed(2)
  }
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
    background: rgba(var(--v-theme-on-surface), 0.06);
    border-radius: 999px;
    padding: 4px;
    gap: 2px;
  }
  .cycle-btn {
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
    border-radius: 22px;
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-theme-on-surface), 0.07);
    box-shadow: 0 12px 28px rgba(var(--v-theme-on-surface), 0.06);
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  .plan-card--featured {
    border-color: rgba(var(--v-theme-primary), 0.35);
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
  .plan-tagline {
    font-size: 0.82rem;
    color: rgba(var(--v-theme-on-surface), 0.55);
    line-height: 1.5;
    margin: 0;
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
  .price-per {
    font-size: 0.75rem;
    color: rgba(var(--v-theme-on-surface), 0.5);
  }
  .price-contact {
    font-size: 1.1rem;
    font-weight: 700;
    padding-top: 8px;
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
    flex: none !important;
  }
</style>
