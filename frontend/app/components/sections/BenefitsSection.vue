<template>
  <section class="section-pad section-tint-mint" id="benefits">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll as="div" class="text-center mb-12">
        <span class="section-tag">{{ t('home.benefits.eyebrow') }}</span>
        <h2 class="section-title">{{ t('home.benefits.title') }}</h2>
      </RevealOnScroll>

      <div class="benefits-grid">
        <RevealOnScroll
          v-for="(b, i) in items"
          :key="b.key"
          :delay="(i % 4) * 80"
        >
          <TiltCard :max-tilt="6" :glare="false">
            <div class="benefit-item clay-surface clay-surface--interactive">
              <div class="benefit-icon">
                <Icon :name="b.icon" size="20" />
              </div>
              <h3 class="benefit-title">{{ t(`home.benefits.items.${b.key}.title`) }}</h3>
              <p class="benefit-desc">{{ t(`home.benefits.items.${b.key}.desc`) }}</p>
            </div>
          </TiltCard>
        </RevealOnScroll>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
  const { t } = useI18n()

  const items = [
    { key: 'save_time', icon: 'mdi-clock-fast' },
    { key: 'reduce_errors', icon: 'mdi-check-decagram-outline' },
    { key: 'increase_sales', icon: 'mdi-trending-up' },
    { key: 'real_time_reports', icon: 'mdi-chart-timeline-variant' },
    { key: 'easy_to_use', icon: 'mdi-gesture-tap' },
    { key: 'cloud_based', icon: 'mdi-cloud-outline' },
    { key: 'secure_data', icon: 'mdi-shield-check-outline' },
    { key: 'affordable', icon: 'mdi-tag-heart-outline' }
  ]
</script>

<style scoped>
.benefits-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 28px 20px;
}
.benefit-item {
  text-align: center;
  padding: 26px 20px;
}
.benefit-icon {
  width: 44px;
  height: 44px;
  margin: 0 auto 14px;
  border-radius: var(--radius-md);
  background: color-mix(in srgb, var(--primary) 10%, transparent);
  color: var(--primary);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform var(--duration-base) var(--ease-spring);
}
.benefit-item:hover .benefit-icon {
  transform: scale(1.1);
}
.benefit-title {
  font-size: 0.88rem;
  font-weight: 800;
  letter-spacing: -0.2px;
  margin-bottom: 5px;
}
.benefit-desc {
  font-size: var(--text-caption);
  opacity: 0.55;
  line-height: 1.6;
  margin: 0;
}

@media (max-width: 900px) {
  .benefits-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
