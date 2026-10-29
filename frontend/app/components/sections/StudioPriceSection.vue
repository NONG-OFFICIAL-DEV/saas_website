<template>
  <section id="pricing" class="section-pad section-tint-peach">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll as="div" class="text-center mb-10">
        <span class="section-tag">{{ t('studio_pricing.eyebrow') }}</span>
        <h2 class="section-title">{{ t('studio_pricing.title') }}</h2>
        <p class="section-sub studio-pricing-sub">{{ t('studio_pricing.subtitle') }}</p>
      </RevealOnScroll>

      <!-- ── Billing cycle toggle ── -->
      <StudioCycleToggle
        v-if="!store.loading && hasPaidPlans"
        v-model:selected-months="selectedMonths"
        :cycles="CYCLES"
        :savings-pct="savingsPct"
      />

      <InlineLoader v-if="store.loading" min-height="380px" />

      <Alert
        v-else-if="store.error"
        class="flex items-center gap-2 border-info/30 bg-info/10 text-info mx-auto max-w-[480px]"
      >
        <Icon name="mdi-clock-outline" size="18" />
        <AlertDescription>{{ t('studio_pricing.unavailable') }}</AlertDescription>
      </Alert>

      <!-- ── Plan cards ── -->
      <RevealOnScroll v-else as="div" class="cards-grid" :data-count="store.plans.length">
        <StudioPlanCard
          v-for="plan in store.plans"
          :key="plan.id"
          :plan="plan"
          :featured="plan.code === 'professional'"
          :is-free="isFree(plan)"
          :price-display="monthlyPrice(plan)"
          :features="planFeatures(plan)"
          :selected-months="selectedMonths"
          :cycle-code="cycleCode"
          @select="(code, cycle) => emit('select-plan', code, cycle)"
        />
      </RevealOnScroll>
    </div>
  </section>
</template>

<script setup lang="ts">
  import { Alert, AlertDescription } from '~/components/ui/alert'

  const { t } = useI18n()
  const { store, CYCLES, selectedMonths, cycleCode, hasPaidPlans, isFree, monthlyPrice, savingsPct, planFeatures } = useStudioPricing()

  // billingCycle is omitted (undefined) for the free trial plan — a cycle
  // is meaningless for a trial signup, so it's never sent for that button.
  const emit = defineEmits<{ 'select-plan': [code: string, billingCycle?: string] }>()

  // Awaited (not onMounted) so live pricing is present in the server-rendered
  // HTML instead of flashing an empty grid/spinner before a client-only fetch
  // resolves. store.fetchPlans() already no-ops on repeat calls once plans
  // are cached, so this only ever does real work on first load.
  await useAsyncData('studio-plans', () => store.fetchPlans())
</script>

<style scoped>
  .studio-pricing-sub {
    max-width: 480px;
    margin: 0 auto;
  }

  /* Per-card styling now lives in StudioPlanCard.vue; the cycle toggle's
     in StudioCycleToggle.vue — this section only owns the grid container. */
  .cards-grid {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    max-width: 1080px;
    margin: 0 auto;
  }
</style>
