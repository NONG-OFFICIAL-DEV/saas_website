<template>
  <Accordion type="multiple" class="faq-panels">
    <AccordionItem v-for="faq in faqs" :key="faq.id" :value="String(faq.id)">
      <AccordionTrigger class="faq-question">{{ faq.question }}</AccordionTrigger>
      <AccordionContent class="faq-answer">{{ faq.answer }}</AccordionContent>
    </AccordionItem>
  </Accordion>
</template>

<script setup lang="ts">
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '~/components/ui/accordion'
import type { ProductFaq } from '~/types'

// Shared by ProductFaqSection.vue (product detail page), help.vue (Help
// Center, one list per product), and FaqTeaserSection.vue (homepage) —
// previously each hand-rolled an identical copy of this markup/styling.
defineProps<{
  faqs: ProductFaq[]
}>()
</script>

<style scoped>
.faq-panels :deep([data-slot='accordion-item']) {
  margin-bottom: 10px;
  padding: 0 16px;
  border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent) !important;
  border-radius: var(--radius-md);
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
