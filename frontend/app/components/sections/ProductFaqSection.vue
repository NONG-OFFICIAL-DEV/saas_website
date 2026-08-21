<template>
  <section v-if="faqs?.length" class="section-pad">
    <Container>
      <div class="text-center mb-10" data-aos="fade-up">
        <span class="section-tag">{{ t('product_detail.faq_tag') }}</span>
        <h2 class="section-title">{{ t('product_detail.faq_title') }}</h2>
      </div>

      <Accordion type="multiple" class="faq-panels" data-aos="fade-up">
        <AccordionItem v-for="faq in faqs" :key="faq.id" :value="String(faq.id)">
          <AccordionTrigger class="faq-question">{{ faq.question }}</AccordionTrigger>
          <AccordionContent class="faq-answer">{{ faq.answer }}</AccordionContent>
        </AccordionItem>
      </Accordion>
    </Container>
  </section>
</template>

<script setup lang="ts">
  import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '~/components/ui/accordion'
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
  .faq-panels :deep([data-slot='accordion-item']) {
    margin-bottom: 10px;
    padding: 0 16px;
    border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent) !important;
    border-radius: 10px;
  }
  .faq-panels :deep([data-slot='accordion-item']:last-child) {
    margin-bottom: 0;
  }
  .faq-question {
    font-weight: 700;
    font-size: 0.95rem;
  }
  .faq-answer {
    color: color-mix(in srgb, var(--foreground) 65%, transparent);
    line-height: 1.6;
  }
</style>
