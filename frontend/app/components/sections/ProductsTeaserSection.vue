<template>
  <section class="section-pad section-tint-sky" id="products">
    <Container>
      <div class="text-center mb-10" data-aos="fade-up">
        <span class="section-tag">{{ t('home.products_teaser.tag') }}</span>
        <h2 class="section-title">{{ t('home.products_teaser.title') }}</h2>
        <p class="section-sub teaser-sub">
          {{ t('home.products_teaser.sub') }}
        </p>
      </div>

      <InlineLoader v-if="store.loading" min-height="200px" />

      <div v-else class="teaser-grid" data-aos="fade-up">
        <ProductCard
          v-for="product in store.products"
          :key="product.id"
          :product="product"
        />
      </div>

      <div class="text-center mt-10" data-aos="fade-up">
        <Button as="NuxtLink" variant="outline" to="/products">
          {{ t('button.view_all_products') }}
          <Icon name="mdi-arrow-right" size="18" />
        </Button>
      </div>
    </Container>
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
