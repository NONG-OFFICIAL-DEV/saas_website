<template>
  <section class="section-pad section-tint-mint" id="how-it-works">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll as="div" class="text-center mb-12">
        <span class="section-tag">{{ t('home.how_it_works.tag') }}</span>
        <h2 class="section-title">{{ t('home.how_it_works.title') }}</h2>
        <p class="section-sub steps-sub mx-auto">{{ t('home.how_it_works.sub') }}</p>
      </RevealOnScroll>

      <div class="steps-track">
        <template v-for="(step, i) in steps" :key="step.key">
          <RevealOnScroll as="div" class="step" :delay="i * 120">
            <TiltCard :max-tilt="4" :glare="false">
              <div class="step-card clay-surface">
                <span class="step-num">{{ String(i + 1).padStart(2, '0') }}</span>
                <div class="step-icon">
                  <Icon :name="step.icon" size="22" />
                </div>
                <h3 class="step-title">{{ t(`home.how_it_works.${step.key}_title`) }}</h3>
                <p class="step-desc">{{ t(`home.how_it_works.${step.key}_desc`) }}</p>
              </div>
            </TiltCard>
          </RevealOnScroll>
          <div v-if="i < steps.length - 1" class="step-connector" aria-hidden="true">
            <Icon name="mdi-arrow-right" size="18" class="connector-icon connector-icon--h" />
            <Icon name="mdi-arrow-down" size="18" class="connector-icon connector-icon--v" />
          </div>
        </template>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
  const { t } = useI18n()

  // Static, not CMS-driven — this describes the actual onboarding flow
  // (pages/onboarding/[slug].vue's two-step wizard + instant provisioning),
  // the same for every product, so there's nothing per-product to fetch.
  const steps = [
    { key: 'step1', icon: 'mdi-shape-outline' },
    { key: 'step2', icon: 'mdi-form-select' },
    { key: 'step3', icon: 'mdi-rocket-launch-outline' }
  ]
</script>

<style scoped>
  .steps-sub {
    max-width: 480px;
  }

  .steps-track {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 1000px;
    margin: 0 auto;
  }
  @media (min-width: 900px) {
    .steps-track {
      flex-direction: row;
      align-items: stretch;
      justify-content: center;
    }
  }

  .step {
    width: 100%;
    max-width: 280px;
  }

  .step-card {
    height: 100%;
    padding: 28px 24px;
    text-align: center;
    position: relative;
  }

  .step-num {
    position: absolute;
    top: 10px;
    right: 18px;
    font-size: 1.8rem;
    font-weight: 900;
    line-height: 1;
    color: color-mix(in srgb, var(--primary) 12%, transparent);
  }

  .step-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 16px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--primary) 12%, transparent);
    color: var(--primary);
  }

  .step-title {
    font-size: 1rem;
    font-weight: 800;
    margin: 0 0 6px;
  }
  .step-desc {
    font-size: 0.86rem;
    color: color-mix(in srgb, var(--foreground) 60%, transparent);
    line-height: 1.6;
    margin: 0;
  }

  .step-connector {
    display: flex;
    align-items: center;
    justify-content: center;
    color: color-mix(in srgb, var(--primary) 45%, transparent);
    padding: 10px 0;
  }
  .connector-icon--h {
    display: none;
  }
  @media (min-width: 900px) {
    .step-connector {
      padding: 0 8px;
      align-self: center;
    }
    .connector-icon--h {
      display: block;
    }
    .connector-icon--v {
      display: none;
    }
  }
</style>
