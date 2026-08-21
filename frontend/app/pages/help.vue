<template>
  <section class="section-pad help-page">
    <Container>
      <div class="header text-center" data-aos="fade-up">
        <span class="section-tag">{{ t('help_page.tag') }}</span>
        <h1 class="section-title">{{ t('help_page.title') }}</h1>
        <p class="section-sub header-sub">{{ t('help_page.sub') }}</p>
      </div>

      <!-- ── Contact channels ── -->
      <div class="contact-grid" data-aos="fade-up">
        <a v-if="footer.email" :href="`mailto:${footer.email}`" class="contact-card">
          <Icon name="mdi-email-outline" size="20" />
          <span>{{ footer.email }}</span>
        </a>
        <a v-if="telegramHref" :href="telegramHref" target="_blank" rel="noopener" class="contact-card">
          <Icon name="mdi-send-outline" size="20" />
          <span>{{ t('help_page.chat_telegram') }}</span>
        </a>
        <a v-if="footer.phone" :href="`tel:${footer.phone.replace(/\s+/g, '')}`" class="contact-card">
          <Icon name="mdi-phone-outline" size="20" />
          <span>{{ footer.phone }}</span>
        </a>
      </div>

      <!-- ── FAQs, grouped per product ── -->
      <InlineLoader v-if="loading" class="faq-loading" min-height="80px" />

      <template v-else>
        <div v-for="group in faqGroups" :key="group.slug" class="faq-group" data-aos="fade-up">
          <h2 class="faq-group-title">{{ group.name }}</h2>
          <Accordion type="multiple" class="faq-panels">
            <AccordionItem v-for="faq in group.faqs" :key="faq.id" :value="String(faq.id)">
              <AccordionTrigger class="faq-question">{{ faq.question }}</AccordionTrigger>
              <AccordionContent class="faq-answer">{{ faq.answer }}</AccordionContent>
            </AccordionItem>
          </Accordion>
        </div>
      </template>

      <!-- ── Still need help ── -->
      <div class="still-need text-center" data-aos="fade-up">
        <h3 class="still-need-title">{{ t('help_page.still_need_help') }}</h3>
        <p class="section-sub still-need-sub">{{ t('help_page.still_need_help_sub') }}</p>
        <Button v-if="telegramHref" as="a" :href="telegramHref" target="_blank" rel="noopener">
          {{ t('help_page.chat_telegram') }}
        </Button>
        <Button v-else-if="footer.email" as="a" :href="`mailto:${footer.email}`">
          {{ footer.email }}
        </Button>
      </div>
    </Container>
  </section>
</template>

<script setup lang="ts">
  import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '~/components/ui/accordion'
  import { Button } from '~/components/ui/button'
  import { getProductBySlug } from '~/services/products'
  import type { ProductFaq } from '~/types'

  const { t } = useI18n()
  const productsStore = useProductsStore()
  const siteContentStore = useSiteContentStore()

  const footer = computed(() => siteContentStore.footer)
  const telegramHref = computed(() => footer.value.socials?.find((s) => s.name === 'Telegram')?.href)

  const loading = ref(true)
  const faqGroups = ref<{ slug: string; name: string; faqs: ProductFaq[] }[]>([])

  // Awaited (not onMounted) so the FAQ groups (this page's primary content)
  // are present in the server-rendered HTML, not just after hydration.
  await useAsyncData('help-page', async () => {
    await Promise.all([productsStore.fetchProducts(), siteContentStore.fetchFooter()])
    const details = await Promise.all(productsStore.products.map((p) => getProductBySlug(p.slug)))
    faqGroups.value = details
      .filter((d) => d?.faqs?.length)
      .map((d) => ({ slug: d!.slug, name: d!.name, faqs: d!.faqs! }))
    loading.value = false
    return true
  })
</script>

<style scoped>
  .help-page {
    padding-top: 120px;
  }
  .header {
    max-width: 620px;
    margin: 0 auto 40px;
  }
  .header-sub {
    max-width: 480px;
    margin: 0 auto;
  }

  .contact-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 14px;
    max-width: 760px;
    margin: 0 auto 56px;
  }
  .contact-card {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 999px;
    border: 1px solid color-mix(in srgb, var(--foreground) 10%, transparent);
    background: var(--card);
    color: var(--foreground);
    font-size: 0.86rem;
    font-weight: 600;
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .contact-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 26px color-mix(in srgb, var(--foreground) 8%, transparent);
  }

  .faq-loading {
    display: flex;
    justify-content: center;
    padding: 40px 0;
  }

  .faq-group {
    max-width: 760px;
    margin: 0 auto 44px;
  }
  .faq-group-title {
    font-size: 1.05rem;
    font-weight: 800;
    margin: 0 0 16px;
  }
  .faq-panels {
    margin-bottom: 4px;
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

  .still-need {
    max-width: 480px;
    margin: 60px auto 0;
    padding-top: 40px;
    border-top: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
  }
  .still-need-title {
    font-size: 1.2rem;
    font-weight: 800;
    margin: 0 0 8px;
  }
  .still-need-sub {
    margin: 0 auto 20px;
  }
</style>
