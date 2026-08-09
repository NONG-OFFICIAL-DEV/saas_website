<template>
  <section class="section-pad" id="benefits">
    <v-container>
      <div class="text-center mb-12" data-aos="fade-up">
        <span class="section-tag">{{ t('benefits.eyebrow') }}</span>
        <h2 class="section-title">{{ t('benefits.title') }}</h2>
      </div>

      <div class="benefits-grid">
        <div
          v-for="(b, i) in items"
          :key="b.key"
          class="benefit-item"
          data-aos="fade-up"
          :data-aos-delay="(i % 4) * 80"
        >
          <div class="benefit-icon">
            <v-icon :icon="b.icon" size="20" />
          </div>
          <h3 class="benefit-title">{{ t(`benefits.items.${b.key}.title`) }}</h3>
          <p class="benefit-desc">{{ t(`benefits.items.${b.key}.desc`) }}</p>
        </div>
      </div>
    </v-container>
  </section>
</template>

<script setup>
  import { useI18n } from 'vue-i18n'
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
}
.benefit-icon {
  width: 44px;
  height: 44px;
  margin: 0 auto 14px;
  border-radius: 12px;
  background: rgba(var(--v-theme-primary), 0.1);
  color: rgb(var(--v-theme-primary));
  display: flex;
  align-items: center;
  justify-content: center;
}
.benefit-title {
  font-size: 0.88rem;
  font-weight: 800;
  letter-spacing: -0.2px;
  margin-bottom: 5px;
}
.benefit-desc {
  font-size: 0.78rem;
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
