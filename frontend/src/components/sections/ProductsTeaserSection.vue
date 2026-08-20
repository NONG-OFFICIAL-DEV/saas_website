<template>
  <section class="section-pad section-tint-sky" id="products">
    <v-container>
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
        <v-btn
          variant="outlined"
          rounded="lg"
          to="/products"
          append-icon="mdi-arrow-right"
        >
          {{ t('button.view_all_products') }}
        </v-btn>
      </div>
    </v-container>
  </section>
</template>

<script setup>
  import { onMounted } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useProductsStore } from '@/stores/products'
  import ProductCard from '@/components/products/ProductCard.vue'
  import InlineLoader from '@/components/global/InlineLoader.vue'

  const { t } = useI18n()
  const store = useProductsStore()

  onMounted(() => store.fetchProducts())
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
