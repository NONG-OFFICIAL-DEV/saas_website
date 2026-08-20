<template>
  <section v-if="faqs?.length" class="section-pad">
    <v-container>
      <div class="text-center mb-10" data-aos="fade-up">
        <span class="section-tag">{{ t('product_detail.faq_tag') }}</span>
        <h2 class="section-title">{{ t('product_detail.faq_title') }}</h2>
      </div>

      <v-expansion-panels class="faq-panels" variant="accordion" data-aos="fade-up">
        <v-expansion-panel v-for="faq in faqs" :key="faq.id" rounded="lg" class="faq-panel">
          <v-expansion-panel-title class="faq-question">{{ faq.question }}</v-expansion-panel-title>
          <v-expansion-panel-text class="faq-answer">{{ faq.answer }}</v-expansion-panel-text>
        </v-expansion-panel>
      </v-expansion-panels>
    </v-container>
  </section>
</template>

<script setup lang="ts">
  import type { ProductFaq } from '~/types'

  withDefaults(
    defineProps<{
      faqs?: ProductFaq[]
    }>(),
    { faqs: () => [] }
  )

  const { t } = useI18n()
</script>

<style scoped>
  .faq-panels {
    max-width: 760px;
    margin: 0 auto;
  }
  .faq-panel {
    margin-bottom: 10px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08) !important;
  }
  .faq-question {
    font-weight: 700;
    font-size: 0.95rem;
  }
  .faq-answer {
    color: rgba(var(--v-theme-on-surface), 0.65);
    line-height: 1.6;
  }
</style>
