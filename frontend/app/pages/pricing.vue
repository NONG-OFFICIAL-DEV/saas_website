<template>
  <section class="section-pad pricing-page">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <!-- ── Header ── -->
      <RevealOnScroll as="div" class="hub-header text-center">
        <span class="section-tag">{{ t('pricing_hub.tag') }}</span>
        <h1 class="section-title">{{ t('pricing_hub.title') }}</h1>
        <p class="section-sub hub-sub">{{ t('pricing_hub.sub') }}</p>
      </RevealOnScroll>

      <InlineLoader v-if="productsStore.loading" min-height="200px" />

      <template v-else-if="tabs.length">
        <!-- ── Product selector ── -->
        <RevealOnScroll as="div" class="product-selector-wrap">
          <Tabs v-model="activeSlug">
            <TabsList class="product-selector">
              <TabsTrigger
                v-for="p in tabs"
                :key="p.slug"
                :value="p.slug"
                class="product-selector-trigger"
                :style="{ '--accent': p.accent_color || '#6366F1' }"
              >
                <span class="trigger-logo">
                  <img v-if="p.logo_url" :src="p.logo_url" :alt="p.name" />
                  <Icon v-else name="mdi-apps" size="14" />
                </span>
                {{ p.name }}
              </TabsTrigger>
            </TabsList>
          </Tabs>
        </RevealOnScroll>

        <!-- ═══════════════════ Nexstack POS ═══════════════════ -->
        <template v-if="activeSlug === 'nexstack-pos'">
          <!-- Same cycle-toggle + plan cards component used on the
               Nexstack POS product detail page's pricing section. -->
          <PosPricingCards />

          <!-- ── Feature comparison ── -->
          <ComparisonTable
            v-if="posVisiblePlans.length"
            class="mt-16"
            :title="t('pricing_hub.compare_title', { product: activeProductName })"
            :subtitle="t('pricing_hub.compare_sub')"
            :plans="posVisiblePlans.map((p) => ({ code: p.code, name: p.name }))"
            :rows="posFeatureRows"
          />
        </template>

        <!-- ═══════════════════ Studio Management ═══════════════════ -->
        <template v-else-if="activeSlug === 'studio-management'">
          <StudioCycleToggle
            v-if="studioPricing.hasPaidPlans"
            v-model:selected-months="studioPricing.selectedMonths"
            :cycles="studioPricing.CYCLES"
            :savings-pct="studioPricing.savingsPct"
          />

          <InlineLoader v-if="studioStore.loading" min-height="320px" />
          <Alert
            v-else-if="studioStore.error"
            class="flex items-center gap-2 border-info/30 bg-info/10 text-info mx-auto max-w-[480px]"
          >
            <Icon name="mdi-clock-outline" size="18" />
            <AlertDescription>{{ t('studio_pricing.unavailable') }}</AlertDescription>
          </Alert>

          <template v-else>
            <RevealOnScroll as="div" class="cards-grid" :data-count="studioStore.plans.length">
              <StudioPlanCard
                v-for="plan in studioStore.plans"
                :key="plan.id"
                :plan="plan"
                :featured="plan.code === 'professional'"
                :is-free="studioPricing.isFree(plan)"
                :price-display="studioPricing.monthlyPrice(plan)"
                :features="studioPricing.planFeatures(plan).slice(0, 7)"
                :selected-months="studioPricing.selectedMonths"
                :cycle-code="studioPricing.cycleCode"
                @select="goToStudioRegister"
              />
            </RevealOnScroll>

            <ComparisonTable
              class="mt-16"
              :title="t('pricing_hub.compare_title', { product: activeProductName })"
              :subtitle="t('pricing_hub.compare_sub')"
              :plans="studioStore.plans.map((p) => ({ code: p.code, name: p.name }))"
              :rows="studioFeatureRows"
            />
          </template>
        </template>
      </template>
    </div>
  </section>
</template>

<script setup lang="ts">
  import { Alert, AlertDescription } from '~/components/ui/alert'
  import { Tabs, TabsList, TabsTrigger } from '~/components/ui/tabs'
  import ComparisonTable from '~/components/sections/PricingComparisonTable.vue'

  const { t, locale } = useI18n()
  const productsStore = useProductsStore()
  const posStore = usePosPlansStore()
  // reactive() so `studioPricing.xxx` auto-unwraps refs in both the
  // template and this script, while still reading as one namespaced
  // object (this page already has its own `activeSlug`/tab-switching
  // state, so the composable's exports are kept under one name rather
  // than destructured flat to avoid colliding with it).
  const studioPricing = reactive(useStudioPricing())
  const studioStore = studioPricing.store

  // Only products with a real, distinct pricing dataset get a tab here —
  // every other CMS product (if any are added later with no pricing
  // component of their own yet) simply doesn't show up, same rule
  // `pages/products/[slug].vue` already follows for its own pricing section.
  const PRICING_SLUGS = ['nexstack-pos', 'studio-management']
  const tabs = computed(() => productsStore.products.filter((p) => PRICING_SLUGS.includes(p.slug)))

  const activeSlug = ref('nexstack-pos')
  watch(
    tabs,
    (list) => {
      if (list.length && !list.some((p) => p.slug === activeSlug.value)) activeSlug.value = list[0]!.slug
    },
    { immediate: true }
  )
  const activeProductName = computed(() => tabs.value.find((p) => p.slug === activeSlug.value)?.name ?? '')

  // Studio's cycle selection (POS's own PosPricingCards.vue manages its
  // own internal cycle state) — reset to monthly whenever the visitor
  // switches away from and back to this tab, so a stale cycle never
  // carries over from a previous visit.
  watch(activeSlug, () => {
    studioPricing.selectedMonths = 1
  })

  await useAsyncData('pricing-page', async () => {
    await Promise.all([productsStore.fetchProducts(), posStore.fetchPlans(), studioStore.fetchPlans()])
    return true
  })

  // ── Nexstack POS ─────────────────────────────────────────────────────
  // Cycle-toggle + plan cards themselves live in PosPricingCards.vue (the
  // same component used on the product detail page) — only the feature
  // comparison table's own data is computed here.
  const posVisiblePlans = computed(() =>
    (posStore.plans ?? []).filter((p) => p.is_active).map((p) => ({ ...p, popular: p.code === 'pro' }))
  )
  // Rows are keyed by `key` — Smart Store's PlanFeatureListing catalog id,
  // genuinely shared across every plan, so rows align correctly with no
  // text-matching. A plan that doesn't have a given key just leaves that
  // cell unset — ComparisonTable already renders a missing value as a dash.
  const posFeatureRows = computed(() => {
    const rows: { key: string; label: string; values: Record<string, string | boolean> }[] = []
    const rowIndexByKey = new Map<string, number>()
    for (const plan of posVisiblePlans.value) {
      for (const f of posPlanComparisonFeatures(plan, locale.value)) {
        let index = rowIndexByKey.get(f.key)
        if (index === undefined) {
          index = rows.length
          rowIndexByKey.set(f.key, index)
          rows.push({ key: f.key, label: f.label, values: {} })
        }
        rows[index]!.values[plan.code] = f.value
      }
    }
    return rows
  })

  // ── Studio Management ────────────────────────────────────────────────
  // Cycle/price math, plan-card rendering, and the cycle toggle all live
  // in useStudioPricing() + StudioPlanCard.vue + StudioCycleToggle.vue
  // (shared with StudioPriceSection.vue on the product detail page) —
  // only the feature comparison table's own data is computed here.
  //
  // Rows are keyed by `key` — Studio's PlanFeatureListing catalog id,
  // genuinely shared across every plan (unlike the old per-row render id
  // this used to key by), so rows align correctly with no text-matching.
  // A plan that doesn't have a given key just leaves that cell unset —
  // ComparisonTable already renders a missing value as a dash.
  const studioFeatureRows = computed(() => {
    const rows: { key: string; label: string; values: Record<string, string | boolean> }[] = []
    const rowIndexByKey = new Map<string, number>()
    for (const plan of studioStore.plans) {
      for (const f of studioPlanComparisonFeatures(plan, locale.value)) {
        let index = rowIndexByKey.get(f.key)
        if (index === undefined) {
          index = rows.length
          rowIndexByKey.set(f.key, index)
          rows.push({ key: f.key, label: f.label, values: {} })
        }
        rows[index]!.values[plan.code] = f.value
      }
    }
    return rows
  })

  // Same hand-off as pages/products/[slug].vue's goToStudioRegister — this
  // site's onboarding wizard, which calls Studio's real registration API.
  const localePath = useLocalePath()
  function goToStudioRegister(planCode?: string, billingCycle?: string) {
    const query: Record<string, string> = {}
    if (planCode) query.plan = planCode
    if (billingCycle) query.cycle = billingCycle
    navigateTo(localePath({ path: '/onboarding/studio-management', query }))
  }
</script>

<style scoped>
  .hub-header {
    max-width: 620px;
    margin: 0 auto 36px;
  }
  .hub-sub {
    max-width: 480px;
    margin: 0 auto;
  }

  /* ── Product selector ── */
  .product-selector-wrap {
    display: flex;
    justify-content: center;
    margin-bottom: 40px;
    overflow-x: auto;
    -webkit-mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);
    mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);
  }
  .product-selector {
    flex-wrap: nowrap;
    height: auto;
    gap: 4px;
    padding: 6px;
    border-radius: 999px;
    background: color-mix(in srgb, var(--foreground) 5%, transparent);
    border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
  }
  .product-selector :deep([data-slot='tabs-trigger']) {
    white-space: nowrap;
  }
  .product-selector-trigger {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 10px 22px !important;
    border-radius: 999px !important;
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
    font-size: 0.9rem;
    font-weight: 700;
    color: color-mix(in srgb, var(--foreground) 55%, transparent) !important;
    transition: background-color var(--duration-base) var(--ease-out-quart), color var(--duration-base) var(--ease-out-quart);
  }
  .product-selector-trigger:hover {
    color: rgb(var(--foreground)) !important;
  }
  .product-selector-trigger[data-state='active'] {
    background: var(--accent) !important;
    color: #fff !important;
  }
  .trigger-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    border-radius: 7px;
    flex-shrink: 0;
    overflow: hidden;
    background: color-mix(in srgb, var(--foreground) 8%, transparent);
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
    transition: background-color var(--duration-base) var(--ease-out-quart), color var(--duration-base) var(--ease-out-quart);
  }
  .trigger-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 3px;
  }
  .product-selector-trigger[data-state='active'] .trigger-logo {
    background: rgba(255, 255, 255, 0.25);
    color: #fff;
  }
  @media (max-width: 480px) {
    .product-selector {
      width: 100%;
      padding: 4px;
    }
    .product-selector-trigger {
      flex: 1 1 0;
      gap: 6px;
      padding: 8px 12px !important;
      font-size: 0.78rem;
    }
    .trigger-logo {
      width: 18px;
      height: 18px;
    }
  }

  /* Billing cycle toggle markup/styles now live in StudioCycleToggle.vue;
     per-card styles in StudioPlanCard.vue — this page only owns the grid
     container (including its count-aware responsive max-width). */
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

</style>
