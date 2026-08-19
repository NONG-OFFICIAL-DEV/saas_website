<template>
  <section id="pricing" class="section-pad section-tint-peach">
    <v-container>
      <div class="pricing-container" data-aos="fade-up">
        <!-- ── Header ── -->
        <div class="text-center pricing-header">
          <span class="section-tag">{{ t('pricing.eyebrow') }}</span>
          <h2 class="section-title">{{ t('pricing.title') }}</h2>
          <p class="section-sub">{{ t('pricing.subtitle') }}</p>
        </div>

        <!-- ── Billing cycle toggle ── -->
        <div v-if="unifiedCycles.length > 1" class="cycle-wrap">
          <!--
            Scrollable on mobile so any number of cycles fits without wrapping.
            Uses a native pill — no v-btn-toggle — to avoid its flex-wrap bug.
          -->
          <div
            class="cycle-track"
            role="group"
            :aria-label="t('common.billing_cycle')"
          >
            <button
              v-for="c in unifiedCycles"
              :key="c.months"
              class="cycle-btn"
              :class="{ 'cycle-btn--active': selectedMonths === c.months }"
              :aria-pressed="selectedMonths === c.months"
              @click="selectedMonths = c.months"
            >
              <span class="cycle-btn__label">{{ c.label }}</span>
              <span
                v-if="Number(c.discount_percent) > 0"
                class="cycle-btn__badge"
              >
                -{{ Number(c.discount_percent).toFixed(0) }}%
              </span>
            </button>
          </div>
        </div>

        <!-- ── Skeleton loading ── -->
        <div v-if="loading" class="cards-grid">
          <v-skeleton-loader
            v-for="i in 4"
            :key="i"
            type="card"
            rounded="lg"
            height="420"
          />
        </div>

        <!-- ── Empty / unavailable state ── -->
        <v-alert
          v-else-if="!visiblePlans.length"
          type="info"
          variant="tonal"
          rounded="lg"
          icon="mdi-clock-outline"
          class="mx-auto"
          max-width="480"
        >
          Pricing is temporarily unavailable — please check back shortly or
          {{ ' ' }}<a href="/#contact">contact me</a> for current plans.
        </v-alert>

        <!-- ── Plan cards ── -->
        <!--
          data-count drives the CSS grid column count so the layout is
          always optimal regardless of how many plans the API returns.
        -->
        <div
          v-else
          class="cards-grid"
          :data-count="visiblePlans.length"
          data-aos="fade-up"
        >
          <div
            v-for="(plan, idx) in visiblePlans"
            :key="plan.id"
            class="plan-card"
            :class="{ 'plan-card--featured': plan.popular }"
            :style="{ '--delay': `${idx * 80}ms` }"
          >
            <div v-if="plan.popular" class="glow-ring" aria-hidden="true" />

            <v-chip
              v-if="plan.popular"
              class="popular-badge"
              color="primary"
              size="x-small"
              variant="flat"
              prepend-icon="mdi-star"
            >
              {{ t('common.most_popular') }}
            </v-chip>

            <!-- Header -->
            <div class="plan-header">
              <v-avatar
                :color="PLAN_UI[plan.code]?.color ?? 'grey'"
                variant="tonal"
                size="40"
                rounded="lg"
              >
                <v-icon
                  :icon="PLAN_UI[plan.code]?.icon ?? 'mdi-help-circle-outline'"
                  size="18"
                />
              </v-avatar>
              <div class="plan-header__text">
                <div class="plan-name">{{ plan.name }}</div>
                <div class="plan-tagline">
                  {{ PLAN_UI[plan.code]?.tagline }}
                </div>
              </div>
            </div>

            <!-- Price -->
            <div class="price-block">
              <template v-if="!isPlanAvailableForCycle(plan)">
                <div class="price-unavailable">
                  <v-icon icon="mdi-information-outline" size="14" />
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
                  <v-chip
                    size="x-small"
                    color="primary"
                    variant="tonal"
                    prepend-icon="mdi-gift-outline"
                  >
                    14-day trial
                  </v-chip>
                </div>
              </template>

              <template v-else>
                <div class="price-row">
                  <span class="price-currency">$</span>
                  <Transition name="price-flip" mode="out-in">
                    <span :key="selectedMonths" class="price-amount">
                      {{ getMonthlyPrice(plan) }}
                    </span>
                  </Transition>
                </div>
                <div class="price-meta">
                  <span class="price-per">
                    {{
                      t('pricing.billed_every_months', {
                        price: getCycleTotal(plan),
                        months: selectedMonths
                      })
                    }}
                  </span>
                  <Transition name="fade">
                    <v-chip
                      v-if="getSavingsPct(plan) > 0"
                      size="x-small"
                      color="success"
                      variant="tonal"
                      prepend-icon="mdi-tag-outline"
                    >
                      {{ t('pricing.save_pct', { pct: getSavingsPct(plan) }) }}
                    </v-chip>
                  </Transition>
                </div>
              </template>
            </div>

            <v-divider />

            <!-- Features -->
            <ul class="feature-list">
              <li
                v-for="f in plan.features"
                :key="f.id ?? f.key"
                class="feature-item"
              >
                <v-avatar
                  :color="PLAN_UI[plan.code]?.color ?? 'primary'"
                  variant="tonal"
                  size="18"
                  rounded="sm"
                >
                  <v-icon icon="mdi-check" size="10" />
                </v-avatar>
                <span class="feature-text">
                  {{ f[locale] ?? f.en ?? f.key }}
                </span>
              </li>
            </ul>

            <!-- CTA -->
            <v-btn
              :color="plan.popular ? 'primary' : undefined"
              :variant="plan.popular ? 'flat' : 'outlined'"
              rounded="lg"
              size="small"
              block
              :disabled="!isPlanAvailableForCycle(plan)"
              append-icon="mdi-arrow-right"
              class="plan-cta"
              :style="
                plan.popular
                  ? 'box-shadow: 0 6px 20px rgba(var(--v-theme-primary),0.35)'
                  : ''
              "
              @click="goToRegister(plan)"
            >
              {{
                plan.cta ??
                (plan.code === 'enterprise'
                  ? t('button.contact_sales')
                  : plan.code === 'free'
                    ? t('button.start_free')
                    : t('button.get_started'))
              }}
            </v-btn>
          </div>
        </div>

        <!-- ── Discount note ── -->
        <v-alert
          v-if="visiblePlans.length"
          variant="tonal"
          color="primary"
          density="compact"
          rounded="lg"
          icon="mdi-percent-outline"
        >
          {{ t('pricing.discount_note_3m') }} &nbsp;·&nbsp;
          {{ t('pricing.discount_note_1y') }}
        </v-alert>

        <!-- ── Footer ── -->
        <div v-if="visiblePlans.length" class="pricing-footer">
          <v-icon icon="mdi-lock-outline" size="13" />
          {{ t('pricing.footer_note') }}
        </div>
      </div>
    </v-container>
  </section>
</template>

<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { usePosPlansStore } from '@/stores/posPlans'
  import { getTrialLink } from '@/config/productTrials'

  const { t, locale } = useI18n()
  const store = usePosPlansStore()
  const loading = ref(false)

  const PLAN_UI = {
    free: {
      icon: 'mdi-gift-outline',
      color: 'grey',
      tagline: 'Get started for free'
    },
    starter: {
      icon: 'mdi-star-half-full',
      color: 'blue',
      tagline: 'For small teams'
    },
    pro: { icon: 'mdi-star', color: 'primary', tagline: 'Most popular choice' },
    enterprise: {
      icon: 'mdi-crown',
      color: 'warning',
      tagline: 'For large organisations'
    }
  }

  const visiblePlans = computed(() =>
    (store.plans ?? [])
      .filter(p => p.is_active)
      .map(p => ({ ...p, popular: p.code === 'pro' }))
  )

  const unifiedCycles = computed(() => {
    const seen = new Map()
    ;(store.plans ?? []).forEach(plan => {
      ;(plan.billing_cycles ?? [])
        .filter(c => c.is_active)
        .forEach(c => {
          if (!seen.has(c.months)) seen.set(c.months, c)
        })
    })
    return [...seen.values()].sort((a, b) => a.months - b.months)
  })

  const selectedMonths = ref(1)

  const isFree = plan => parseFloat(plan.price_usd ?? 0) === 0

  function planCycleForSelected(plan) {
    return (
      (plan.billing_cycles ?? []).find(
        c => c.is_active && c.months === selectedMonths.value
      ) ?? null
    )
  }

  function isPlanAvailableForCycle(plan) {
    if (isFree(plan)) return selectedMonths.value === 1
    return planCycleForSelected(plan) !== null
  }

  function getMonthlyPrice(plan) {
    const base = Number(plan.price_usd ?? 0)
    const discount =
      Number(planCycleForSelected(plan)?.discount_percent ?? 0) / 100
    return (base * (1 - discount)).toFixed(2)
  }

  function getCycleTotal(plan) {
    return (parseFloat(getMonthlyPrice(plan)) * selectedMonths.value).toFixed(2)
  }

  function getSavingsPct(plan) {
    return Number(planCycleForSelected(plan)?.discount_percent ?? 0)
  }

  // Registration itself happens on the real admin app, not this site —
  // same handoff convention as Studio Management's pricing section.
  function goToRegister(plan) {
    if (!isPlanAvailableForCycle(plan)) return
    const { href } = getTrialLink('nexstack-pos', plan.code)
    window.open(href, '_blank', 'noopener')
  }

  onMounted(async () => {
    loading.value = true
    try {
      await store.fetchPlans()
    } finally {
      loading.value = false
    }
  })
</script>

<style scoped>

  /* ── Outer container ── */
  .pricing-container {
    position: relative;
    z-index: 1;
    max-width: 1280px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 36px;
  }

  .pricing-header {
    max-width: 860px;
    width: 100%;
  }

  /* ── Billing cycle toggle ──────────────────────────────────────────────────────
   .cycle-wrap clips the scroll shadow; .cycle-track scrolls on mobile.
   This handles 2, 3, 4, 5… cycles without ever wrapping.
──────────────────────────────────────────────────────────────────────────── */
  .cycle-wrap {
    width: 100%;
    max-width: 600px;
    /* Fade out edges on mobile to hint scrollability */
    -webkit-mask-image: linear-gradient(
      to right,
      transparent 0%,
      black 5%,
      black 95%,
      transparent 100%
    );
    mask-image: linear-gradient(
      to right,
      transparent 0%,
      black 5%,
      black 95%,
      transparent 100%
    );
  }

  .cycle-track {
    display: flex;
    flex-direction: row;
    align-items: center;
    background: rgba(var(--v-theme-on-surface), 0.06);
    border-radius: 999px;
    padding: 4px;
    gap: 2px;
    /* Scroll horizontally on very small screens instead of wrapping */
    overflow-x: auto;
    overflow-y: visible;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none;
    white-space: nowrap;
  }
  .cycle-track::-webkit-scrollbar {
    display: none;
  }

  .cycle-btn {
    flex: 1 0 auto; /* grow but don't shrink below content */
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 7px 16px;
    border: none;
    background: transparent;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: rgba(var(--v-theme-on-surface), 0.5);
    cursor: pointer;
    transition:
      background 0.2s,
      color 0.2s,
      box-shadow 0.2s;
    white-space: nowrap;
  }

  .cycle-btn--active {
    background: rgb(var(--v-theme-primary));
    color: #fff;
    box-shadow: 0 2px 12px rgba(var(--v-theme-primary), 0.4);
  }

  .cycle-btn__badge {
    flex-shrink: 0;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 999px;
    line-height: 1.5;
    background: rgba(255, 255, 255, 0.22);
    color: inherit;
  }
  .cycle-btn:not(.cycle-btn--active) .cycle-btn__badge {
    background: rgba(var(--v-theme-success), 0.15);
    color: rgb(var(--v-theme-success));
  }

  /* ── Plan cards grid ───────────────────────────────────────────────────────────
   data-count attribute drives column count so any number of plans looks right.
   1 plan  → centered single column
   2 plans → 2 cols
   3 plans → 3 cols
   4 plans → 4 cols  (slightly narrower cards)
   5+      → wrap at 200px min
──────────────────────────────────────────────────────────────────────────── */
  .cards-grid {
    display: grid;
    gap: 18px;
    width: 100%;
    align-items: start;
    /* Default: auto-fit, never smaller than 220px */
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  }

  .cards-grid[data-count='1'] {
    max-width: 340px;
    grid-template-columns: 1fr;
  }
  .cards-grid[data-count='2'] {
    max-width: 720px;
    grid-template-columns: repeat(2, 1fr);
  }
  .cards-grid[data-count='3'] {
    max-width: 960px;
    grid-template-columns: repeat(3, 1fr);
  }
  .cards-grid[data-count='4'] {
    grid-template-columns: repeat(4, 1fr);
  }

  /* Tablet — 4 cols → 2 cols */
  @media (max-width: 1024px) {
    .cards-grid[data-count='4'] {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  /* Tablet — 3 cols → 2 cols */
  @media (max-width: 860px) {
    .cards-grid[data-count='3'] {
      grid-template-columns: repeat(2, 1fr);
    }
    .cycle-btn {
      padding: 7px 12px;
      font-size: 0.73rem;
    }
  }
  /* Mobile — always single column */
  @media (max-width: 600px) {
    .cards-grid,
    .cards-grid[data-count='2'],
    .cards-grid[data-count='3'],
    .cards-grid[data-count='4'] {
      grid-template-columns: 1fr !important;
      max-width: 480px;
    }
    .cycle-btn {
      padding: 6px 10px;
      font-size: 0.7rem;
    }
    .cycle-btn__badge {
      font-size: 0.6rem;
      padding: 1px 4px;
    }
  }

  /* ── Plan card ── */
  .plan-card {
    position: relative;
    border-radius: 22px;
    padding: 28px 24px;
    background: rgba(var(--v-theme-surface), 0.85);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(var(--v-theme-on-surface), 0.07);
    box-shadow: 0 12px 28px rgba(var(--v-theme-on-surface), 0.06);
    display: flex;
    flex-direction: column;
    gap: 18px;
    transition:
      transform 0.22s ease,
      box-shadow 0.22s ease;
    animation: card-rise 0.45s ease both;
    animation-delay: var(--delay);
    overflow: visible;
  }
  .plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 44px rgba(var(--v-theme-on-surface), 0.1);
  }
  @keyframes card-rise {
    from {
      opacity: 0;
      transform: translateY(24px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  .plan-card--featured {
    border-color: rgba(var(--v-theme-primary), 0.4);
    background: rgba(var(--v-theme-surface), 0.94);
    box-shadow:
      0 0 0 1px rgba(var(--v-theme-primary), 0.18),
      0 14px 44px rgba(var(--v-theme-primary), 0.1);
  }

  .glow-ring {
    position: absolute;
    inset: -2px;
    border-radius: 24px;
    background: linear-gradient(135deg, rgb(var(--v-theme-primary)), #f59e0b);
    z-index: -1;
    opacity: 0.18;
    filter: blur(8px);
  }

  .popular-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    z-index: 1;
    box-shadow: 0 3px 14px rgba(var(--v-theme-primary), 0.38);
  }

  /* ── Card header ── */
  .plan-header {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .plan-header__text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
  }
  .plan-name {
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: -0.3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .plan-tagline {
    font-size: 0.75rem;
    color: rgba(var(--v-theme-on-surface), 0.48);
  }

  /* ── Price block ── */
  .price-block {
    display: flex;
    flex-direction: column;
    gap: 5px;
    min-height: 68px;
  }
  .price-row {
    display: flex;
    align-items: flex-start;
    gap: 2px;
    line-height: 1;
  }
  .price-currency {
    font-size: 1rem;
    font-weight: 700;
    color: rgba(var(--v-theme-on-surface), 0.5);
    margin-top: 5px;
  }
  .price-amount {
    font-size: 3rem;
    font-weight: 900;
    letter-spacing: -3px;
    color: rgb(var(--v-theme-on-surface));
  }
  .price-amount--free {
    color: rgb(var(--v-theme-success));
  }
  .price-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }
  .price-per {
    font-size: 0.75rem;
    color: rgba(var(--v-theme-on-surface), 0.5);
  }
  .price-unavailable {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    font-style: italic;
    color: rgba(var(--v-theme-on-surface), 0.45);
  }

  /* ── Features ── */
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
  }
  .feature-text {
    font-size: 0.83rem;
    line-height: 1.4;
  }

  /* ── CTA ── */
  .plan-cta {
    /* v-btn's `block` prop sets flex: 1 0 auto internally (meant for a
       horizontal row) — inside this vertical flex column that makes the
       button itself stretch to fill leftover height. Pin it back down;
       .feature-list's flex-grow: 1 is what should absorb that space. */
    flex: none !important;
    margin-top: auto;
  }

  /* ── Bottom bits ── */
  .pricing-footer {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    color: rgba(var(--v-theme-on-surface), 0.42);
  }

  /* ── Section text ── */
  .section-sub {
    font-size: 1rem;
    color: rgba(var(--v-theme-on-surface), 0.55);
    line-height: 1.65;
    margin: 0;
  }

  /* ── Transitions ── */
  .price-flip-enter-active,
  .price-flip-leave-active {
    transition:
      opacity 0.16s ease,
      transform 0.16s ease;
  }
  .price-flip-enter-from {
    opacity: 0;
    transform: translateY(8px);
  }
  .price-flip-leave-to {
    opacity: 0;
    transform: translateY(-8px);
  }
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.18s;
  }
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
</style>
