<template>
  <section id="pricing" class="section-pad section-tint-peach">
    <Container>
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

        <InlineLoader v-if="store.loading" min-height="420px" />

        <!-- ── Empty / unavailable state ── -->
        <Alert
          v-else-if="!visiblePlans.length"
          class="flex items-center gap-2 border-info/30 bg-info/10 text-info mx-auto max-w-[480px]"
        >
          <Icon name="mdi-clock-outline" size="18" />
          <AlertDescription>
            Pricing is temporarily unavailable — please check back shortly or
            {{ ' ' }}<a href="/#contact">contact me</a> for current plans.
          </AlertDescription>
        </Alert>

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

            <Badge
              v-if="plan.popular"
              class="popular-badge bg-primary text-primary-foreground border-transparent"
            >
              <Icon name="mdi-star" size="12" />
              {{ t('common.most_popular') }}
            </Badge>

            <!-- Header -->
            <div class="plan-header">
              <span
                class="plan-icon-badge"
                :style="planIconStyle(PLAN_UI[plan.code]?.color ?? 'muted')"
              >
                <Icon
                  :name="PLAN_UI[plan.code]?.icon ?? 'mdi-help-circle-outline'"
                  size="18"
                />
              </span>
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
                  <Badge class="bg-primary/10 text-primary border-transparent">
                    <Icon name="mdi-gift-outline" size="12" />
                    14-day trial
                  </Badge>
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
                    <Badge v-if="getSavingsPct(plan) > 0" class="bg-success/10 text-success border-transparent">
                      <Icon name="mdi-tag-outline" size="12" />
                      {{ t('pricing.save_pct', { pct: getSavingsPct(plan) }) }}
                    </Badge>
                  </Transition>
                </div>
              </template>
            </div>

            <div class="plan-divider" />

            <!-- Features -->
            <ul class="feature-list">
              <li
                v-for="f in plan.features"
                :key="f.id ?? f.key"
                class="feature-item"
              >
                <span
                  class="plan-check-badge"
                  :style="planIconStyle(PLAN_UI[plan.code]?.color ?? 'primary')"
                >
                  <Icon name="mdi-check" size="10" />
                </span>
                <span class="feature-text">
                  {{ (locale === 'en' || locale === 'km' ? f[locale] : undefined) ?? f.en ?? f.key }}
                </span>
              </li>
            </ul>

            <!-- CTA -->
            <Button
              :variant="plan.popular ? 'default' : 'outline'"
              size="sm"
              class="plan-cta w-full"
              :disabled="!isPlanAvailableForCycle(plan)"
              :style="
                plan.popular
                  ? 'box-shadow: 0 6px 20px color-mix(in srgb, var(--primary) 35%, transparent)'
                  : ''
              "
              @click="goToRegister(plan)"
            >
              {{
                plan.code === 'enterprise'
                  ? t('button.contact_sales')
                  : plan.code === 'free'
                    ? t('button.start_free')
                    : t('button.get_started')
              }}
              <Icon name="mdi-arrow-right" size="16" />
            </Button>
          </div>
        </div>

        <!-- ── Discount note ── -->
        <Alert
          v-if="visiblePlans.length"
          class="flex items-center gap-2 border-primary/30 bg-primary/10 text-primary"
        >
          <Icon name="mdi-percent-outline" size="16" />
          <AlertDescription>
            {{ t('pricing.discount_note_3m') }} &nbsp;·&nbsp;
            {{ t('pricing.discount_note_1y') }}
          </AlertDescription>
        </Alert>

        <!-- ── Footer ── -->
        <div v-if="visiblePlans.length" class="pricing-footer">
          <Icon name="mdi-lock-outline" size="13" />
          {{ t('pricing.footer_note') }}
        </div>
      </div>
    </Container>
  </section>
</template>

<script setup lang="ts">
  import { Alert, AlertDescription } from '~/components/ui/alert'
  import { Badge } from '~/components/ui/badge'
  import { Button } from '~/components/ui/button'
  import type { PosPlan, BillingCycle } from '~/types'

  const { t, locale } = useI18n()
  const store = usePosPlansStore()

  // Color values are this site's own design-token names (see
  // app/assets/css/tailwind.css), not Vuetify's built-in palette —
  // `planIconStyle()` below reads them into a `color-mix()` background.
  const PLAN_UI: Record<string, { icon: string; color: string; tagline: string }> = {
    free: {
      icon: 'mdi-gift-outline',
      color: 'muted-foreground',
      tagline: 'Get started for free'
    },
    starter: {
      icon: 'mdi-star-half-full',
      color: 'info',
      tagline: 'For small teams'
    },
    pro: { icon: 'mdi-star', color: 'primary', tagline: 'Most popular choice' },
    enterprise: {
      icon: 'mdi-crown',
      color: 'warning',
      tagline: 'For large organisations'
    }
  }

  // Replaces Vuetify's <v-avatar color="x" variant="tonal"> for the
  // square icon badges here (plan header icon, feature-list check bullet)
  // — shadcn's Avatar is hardcoded circular (rounded-full), which doesn't
  // match these square/rounded-lg badges, so a plain styled span is used
  // instead, matching the `.feature-icon`/`.benefit-icon` pattern used
  // elsewhere in this migration.
  function planIconStyle(colorToken: string) {
    return {
      background: `color-mix(in srgb, var(--${colorToken}) 14%, transparent)`,
      color: `var(--${colorToken})`
    }
  }

  const visiblePlans = computed(() =>
    (store.plans ?? [])
      .filter((p) => p.is_active)
      .map((p) => ({ ...p, popular: p.code === 'pro' }))
  )

  const unifiedCycles = computed(() => {
    const seen = new Map<number, BillingCycle>()
    ;(store.plans ?? []).forEach((plan: PosPlan) => {
      ;(plan.billing_cycles ?? [])
        .filter((c) => c.is_active)
        .forEach((c) => {
          if (!seen.has(c.months)) seen.set(c.months, c as BillingCycle & { label?: string })
        })
    })
    return [...seen.values()].sort((a, b) => a.months - b.months) as (BillingCycle & { label?: string })[]
  })

  const selectedMonths = ref(1)

  const isFree = (plan: PosPlan) => parseFloat(String(plan.price_usd ?? 0)) === 0

  function planCycleForSelected(plan: PosPlan) {
    return (
      (plan.billing_cycles ?? []).find(
        (c) => c.is_active && c.months === selectedMonths.value
      ) ?? null
    )
  }

  function isPlanAvailableForCycle(plan: PosPlan) {
    if (isFree(plan)) return selectedMonths.value === 1
    return planCycleForSelected(plan) !== null
  }

  function getMonthlyPrice(plan: PosPlan) {
    const base = Number(plan.price_usd ?? 0)
    const discount =
      Number(planCycleForSelected(plan)?.discount_percent ?? 0) / 100
    return (base * (1 - discount)).toFixed(2)
  }

  function getCycleTotal(plan: PosPlan) {
    return (parseFloat(getMonthlyPrice(plan)) * selectedMonths.value).toFixed(2)
  }

  function getSavingsPct(plan: PosPlan) {
    return Number(planCycleForSelected(plan)?.discount_percent ?? 0)
  }

  // Hands off to this site's own onboarding wizard, which calls Smart
  // Store's real registration API server-side. Note: Smart Store's
  // self-service endpoint always assigns the free plan regardless of which
  // card was clicked (paid plans are chosen after logging in) — this isn't
  // a limitation introduced here, it's how that product's own signup works.
  function goToRegister(plan: PosPlan) {
    if (!isPlanAvailableForCycle(plan)) return
    navigateTo('/onboarding/nexstack-pos')
  }

  // Awaited (not onMounted) so live pricing is present in the server-rendered
  // HTML instead of flashing an "unavailable"/empty state before a client-only
  // fetch resolves. store.fetchPlans() already no-ops on repeat calls once
  // plans are cached, so this only ever does real work on first load.
  await useAsyncData('pos-plans', () => store.fetchPlans())
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
    background: color-mix(in srgb, var(--foreground) 6%, transparent);
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
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
    cursor: pointer;
    transition:
      background 0.2s,
      color 0.2s,
      box-shadow 0.2s;
    white-space: nowrap;
  }

  .cycle-btn--active {
    background: var(--primary);
    color: #fff;
    box-shadow: 0 2px 12px color-mix(in srgb, var(--primary) 40%, transparent);
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
    background: color-mix(in srgb, var(--success) 15%, transparent);
    color: var(--success);
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
    background: color-mix(in srgb, var(--card) 85%, transparent);
    backdrop-filter: blur(20px);
    border: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
    box-shadow: 0 12px 28px color-mix(in srgb, var(--foreground) 6%, transparent);
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
    box-shadow: 0 20px 44px color-mix(in srgb, var(--foreground) 10%, transparent);
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
    border-color: color-mix(in srgb, var(--primary) 40%, transparent);
    background: color-mix(in srgb, var(--card) 94%, transparent);
    box-shadow:
      0 0 0 1px color-mix(in srgb, var(--primary) 18%, transparent),
      0 14px 44px color-mix(in srgb, var(--primary) 10%, transparent);
  }

  .glow-ring {
    position: absolute;
    inset: -2px;
    border-radius: 24px;
    background: linear-gradient(135deg, var(--primary), #f59e0b);
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
    box-shadow: 0 3px 14px color-mix(in srgb, var(--primary) 38%, transparent);
  }

  /* ── Card header ── */
  .plan-header {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .plan-icon-badge {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .plan-check-badge {
    width: 18px;
    height: 18px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .plan-divider {
    height: 1px;
    background: color-mix(in srgb, var(--foreground) 10%, transparent);
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
    color: color-mix(in srgb, var(--foreground) 48%, transparent);
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
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
    margin-top: 5px;
  }
  .price-amount {
    font-size: 3rem;
    font-weight: 900;
    letter-spacing: -3px;
    color: var(--foreground);
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
    /* Inside this vertical flex column, an unconstrained button would
       stretch to fill leftover height. Pin it back down;
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
    color: color-mix(in srgb, var(--foreground) 42%, transparent);
  }

  /* ── Section text ── */
  .section-sub {
    font-size: 1rem;
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
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
