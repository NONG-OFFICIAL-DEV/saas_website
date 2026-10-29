<template>
  <section v-if="teaserFaqs.length" class="section-pad section-tint-neutral" id="faq">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll as="div" class="text-center mb-10">
        <span class="section-tag">{{ t('home.faq_teaser.tag') }}</span>
        <h2 class="section-title">{{ t('home.faq_teaser.title') }}</h2>
        <p class="section-sub faq-sub mx-auto">{{ t('home.faq_teaser.sub') }}</p>
      </RevealOnScroll>

      <RevealOnScroll as="div" class="faq-wrap" :delay="80">
        <FaqAccordionList :faqs="teaserFaqs" />
      </RevealOnScroll>

      <RevealOnScroll as="div" class="text-center mt-8">
        <NuxtLinkLocale to="/help" class="view-all-link">
          {{ t('home.faq_teaser.view_all') }}
          <Icon name="mdi-arrow-right" size="16" />
        </NuxtLinkLocale>
      </RevealOnScroll>
    </div>
  </section>
</template>

<script setup lang="ts">
  const { t } = useI18n()

  // Self-contained fetch (same pattern as PriceSection.vue/StudioPriceSection.vue)
  // — a small sample across products, not page-owned state reused elsewhere.
  // A handful per product, capped overall, keeps this a teaser rather than
  // duplicating the full Help Center list.
  const { data: groups } = await useAsyncData('home-faq-teaser', () => fetchFaqGroups())
  const teaserFaqs = computed(() =>
    (groups.value ?? [])
      .flatMap((g) => g.faqs.slice(0, 2))
      .slice(0, 6)
  )
</script>

<style scoped>
  .faq-sub {
    max-width: 480px;
  }
  .faq-wrap {
    max-width: 760px;
    margin: 0 auto;
  }
  .view-all-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--primary);
    text-decoration: none;
    transition: gap var(--duration-fast) var(--ease-out-quart);
  }
  .view-all-link:hover {
    gap: 10px;
  }
</style>
