<template>
  <section class="section-pad section-tint-sky" id="products">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll as="div" class="text-center mb-10">
        <span class="section-tag">{{ t('home.products_teaser.tag') }}</span>
        <h2 class="section-title">{{ t('home.products_teaser.title') }}</h2>
        <p class="section-sub teaser-sub">
          {{ t('home.products_teaser.sub') }}
        </p>
      </RevealOnScroll>

      <InlineLoader v-if="store.loading" min-height="200px" />

      <RevealOnScroll v-else as="div" class="teaser-grid" :delay="80">
        <ProductCard
          v-for="product in store.products"
          :key="product.id"
          :product="product"
        />
      </RevealOnScroll>

      <RevealOnScroll as="div" class="text-center mt-10" :delay="160">
        <Button as="NuxtLink" variant="outline" to="/products">
          {{ t('button.view_all_products') }}
          <Icon name="mdi-arrow-right" size="18" />
        </Button>
      </RevealOnScroll>
    </div>
  </section>
</template>

<script setup lang="ts">
  import { Button } from '~/components/ui/button'

  // Data is fetched by the parent page (app/pages/index.vue) via an awaited
  // useAsyncData call, so it's present in the server-rendered HTML — this
  // component only ever reads the store reactively.
  const { t } = useI18n()
  const store = useProductsStore()
</script>

<style scoped>
  .teaser-sub {
    max-width: 480px;
    margin: 0 auto;
  }
  .teaser-grid {
    display: grid;
    gap: 28px;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    max-width: 780px;
    margin: 0 auto;
  }
</style>
