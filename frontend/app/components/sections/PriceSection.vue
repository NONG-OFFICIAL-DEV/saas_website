<template>
  <section id="pricing" class="section-pad section-tint-peach">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll as="div" class="pricing-container">
        <!-- ── Header ── -->
        <div class="text-center pricing-header">
          <span class="section-tag">{{ t('pricing.eyebrow') }}</span>
          <h2 class="section-title">{{ t('pricing.title') }}</h2>
          <p class="section-sub">{{ t('pricing.subtitle') }}</p>
        </div>

        <!-- Cycle toggle + plan cards — same component as the dedicated
             /pricing page's Nexstack POS tab, so the plan-selection
             experience is identical wherever it appears. -->
        <PosPricingCards />
      </RevealOnScroll>
    </div>
  </section>
</template>

<script setup lang="ts">
  const { t } = useI18n()
  const store = usePosPlansStore()

  // fetchPlans() no-ops on repeat calls once cached — PosPricingCards
  // fetches independently too, this just lets this section render without
  // waiting on that child component's own request on client navigations.
  await useAsyncData('pos-plans', () => store.fetchPlans())
</script>

<style scoped>
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

  .section-sub {
    font-size: 1rem;
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
    line-height: 1.65;
    margin: 0;
  }

  .pricing-footer {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    color: color-mix(in srgb, var(--foreground) 42%, transparent);
  }
</style>
