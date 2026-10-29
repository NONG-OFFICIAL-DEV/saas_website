<template>
  <section v-if="faqs?.length" class="section-pad">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll as="div" class="text-center mb-10">
        <span class="section-tag">{{ t('product_detail.faq_tag') }}</span>
        <h2 class="section-title">{{ t('product_detail.faq_title') }}</h2>
      </RevealOnScroll>

      <RevealOnScroll as="div" class="faq-wrap" :delay="80">
        <FaqAccordionList :faqs="faqs" />
      </RevealOnScroll>
    </div>
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
  .faq-wrap {
    max-width: 760px;
    margin: 0 auto;
  }
</style>
