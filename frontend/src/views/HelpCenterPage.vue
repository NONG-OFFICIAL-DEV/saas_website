<template>
  <section class="section-pad help-page">
    <v-container>
      <div class="header text-center" data-aos="fade-up">
        <span class="section-tag">{{ t('help_page.tag') }}</span>
        <h1 class="section-title">{{ t('help_page.title') }}</h1>
        <p class="section-sub header-sub">{{ t('help_page.sub') }}</p>
      </div>

      <!-- ── Contact channels ── -->
      <div class="contact-grid" data-aos="fade-up">
        <a v-if="footer.email" :href="`mailto:${footer.email}`" class="contact-card">
          <v-icon icon="mdi-email-outline" size="20" />
          <span>{{ footer.email }}</span>
        </a>
        <a v-if="telegramHref" :href="telegramHref" target="_blank" rel="noopener" class="contact-card">
          <v-icon icon="mdi-send-outline" size="20" />
          <span>{{ t('help_page.chat_telegram') }}</span>
        </a>
        <a v-if="footer.phone" :href="`tel:${footer.phone.replace(/\s+/g, '')}`" class="contact-card">
          <v-icon icon="mdi-phone-outline" size="20" />
          <span>{{ footer.phone }}</span>
        </a>
      </div>

      <!-- ── FAQs, grouped per product ── -->
      <div v-if="loading" class="faq-loading">
        <v-progress-circular indeterminate color="primary" />
      </div>

      <template v-else>
        <div v-for="group in faqGroups" :key="group.slug" class="faq-group" data-aos="fade-up">
          <h2 class="faq-group-title">{{ group.name }}</h2>
          <v-expansion-panels variant="accordion" class="faq-panels">
            <v-expansion-panel v-for="faq in group.faqs" :key="faq.id" rounded="lg">
              <v-expansion-panel-title class="faq-question">{{ faq.question }}</v-expansion-panel-title>
              <v-expansion-panel-text class="faq-answer">{{ faq.answer }}</v-expansion-panel-text>
            </v-expansion-panel>
          </v-expansion-panels>
        </div>
      </template>

      <!-- ── Still need help ── -->
      <div class="still-need text-center" data-aos="fade-up">
        <h3 class="still-need-title">{{ t('help_page.still_need_help') }}</h3>
        <p class="section-sub still-need-sub">{{ t('help_page.still_need_help_sub') }}</p>
        <v-btn v-if="telegramHref" color="primary" rounded="lg" :href="telegramHref" target="_blank" rel="noopener">
          {{ t('help_page.chat_telegram') }}
        </v-btn>
        <v-btn v-else-if="footer.email" color="primary" rounded="lg" :href="`mailto:${footer.email}`">
          {{ footer.email }}
        </v-btn>
      </div>
    </v-container>
  </section>
</template>

<script setup>
  import { computed, onMounted, ref } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useProductsStore } from '@/stores/products'
  import { useSiteContentStore } from '@/stores/siteContent'
  import { getProductBySlug } from '@/services/products'

  const { t } = useI18n()
  const productsStore = useProductsStore()
  const siteContentStore = useSiteContentStore()

  const footer = computed(() => siteContentStore.footer)
  const telegramHref = computed(() => footer.value.socials?.find(s => s.name === 'Telegram')?.href)

  const loading = ref(true)
  const faqGroups = ref([])

  onMounted(async () => {
    await Promise.all([productsStore.fetchProducts(), siteContentStore.fetchFooter()])
    const details = await Promise.all(productsStore.products.map(p => getProductBySlug(p.slug)))
    faqGroups.value = details
      .filter(d => d?.faqs?.length)
      .map(d => ({ slug: d.slug, name: d.name, faqs: d.faqs }))
    loading.value = false
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
    border: 1px solid rgba(var(--v-theme-on-surface), 0.1);
    background: rgb(var(--v-theme-surface));
    color: rgb(var(--v-theme-on-surface));
    font-size: 0.86rem;
    font-weight: 600;
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .contact-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 26px rgba(var(--v-theme-on-surface), 0.08);
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
  .faq-panels :deep(.v-expansion-panel) {
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

  .still-need {
    max-width: 480px;
    margin: 60px auto 0;
    padding-top: 40px;
    border-top: 1px solid rgba(var(--v-theme-on-surface), 0.08);
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
