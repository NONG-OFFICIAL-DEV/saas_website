<template>
  <section class="section-pad help-page">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll as="div" class="header text-center">
        <span class="section-tag">{{ t('help_page.tag') }}</span>
        <h1 class="section-title">{{ t('help_page.title') }}</h1>
        <p class="section-sub header-sub">{{ t('help_page.sub') }}</p>
      </RevealOnScroll>

      <!-- ── Contact channels ── -->
      <RevealOnScroll as="div" class="contact-grid" :delay="80">
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
      </RevealOnScroll>

      <!-- ── FAQs, grouped per product ── -->
      <InlineLoader v-if="loading" class="faq-loading" min-height="80px" />

      <template v-else>
        <RevealOnScroll v-for="(group, i) in faqGroups" :key="group.slug" as="div" class="faq-group" :delay="i * 80">
          <h2 class="faq-group-title">{{ group.name }}</h2>
          <Accordion type="multiple" class="faq-panels">
            <AccordionItem v-for="faq in group.faqs" :key="faq.id" :value="String(faq.id)">
              <AccordionTrigger class="faq-question">{{ faq.question }}</AccordionTrigger>
              <AccordionContent class="faq-answer">{{ faq.answer }}</AccordionContent>
            </AccordionItem>
          </Accordion>
        </RevealOnScroll>
      </template>

      <!-- ── Still need help ── -->
      <RevealOnScroll as="div" class="still-need text-center">
        <h3 class="still-need-title">{{ t('help_page.still_need_help') }}</h3>
        <p class="section-sub still-need-sub">{{ t('help_page.still_need_help_sub') }}</p>
        <Button v-if="telegramHref" as="a" :href="telegramHref" target="_blank" rel="noopener">
          {{ t('help_page.chat_telegram') }}
        </Button>
        <Button v-else-if="footer.email" as="a" :href="`mailto:${footer.email}`">
          {{ footer.email }}
        </Button>
      </RevealOnScroll>
    </div>
  </section>
</template>

<script setup lang="ts">
  import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '~/components/ui/accordion'
  import { Button } from '~/components/ui/button'
  import { getProductBySlug } from '~/services/cms/products'

  const { t } = useI18n()
  const productsStore = useProductsStore()
  const siteContentStore = useSiteContentStore()

  const footer = computed(() => siteContentStore.footer)
  const telegramHref = computed(() => footer.value.socials?.find((s) => s.name === 'Telegram')?.href)

  // Awaited (not onMounted) so the FAQ groups (this page's primary content)
  // are present in the server-rendered HTML, not just after hydration.
  // `pending`/`data` (not a hand-rolled `loading` ref flipped inside the
  // callback) is what actually hydrates correctly — on client navigation
  // with a cached payload, the callback never re-runs, so a plain ref
  // toggled inside it would stay stuck at its initial value and desync
  // from the server-rendered state, causing a hydration mismatch.
  const { data: faqGroupsData, pending: loading } = await useAsyncData('help-page', async () => {
    await Promise.all([productsStore.fetchProducts(), siteContentStore.fetchFooter()])
    const details = await Promise.all(productsStore.products.map((p) => getProductBySlug(p.slug)))
    return details
      .filter((d) => d?.faqs?.length)
      .map((d) => ({ slug: d!.slug, name: d!.name, faqs: d!.faqs! }))
  })
  const faqGroups = computed(() => faqGroupsData.value ?? [])
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
    transition: transform var(--duration-base) var(--ease-out-quart), box-shadow var(--duration-base) var(--ease-out-quart);
  }
  .contact-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
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
