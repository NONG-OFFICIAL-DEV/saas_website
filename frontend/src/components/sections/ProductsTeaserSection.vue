<template>
  <section class="section-pad section-tint-sky" id="products">
    <v-container>
      <div class="text-center mb-10" data-aos="fade-up">
        <span class="section-tag">{{ t('products_teaser.tag') }}</span>
        <h2 class="section-title">{{ t('products_teaser.title') }}</h2>
        <p class="section-sub teaser-sub">
          {{ t('products_teaser.sub') }}
        </p>
      </div>

      <div v-if="store.loading" class="teaser-grid" data-aos="fade-up">
        <v-skeleton-loader
          v-for="i in 2"
          :key="i"
          type="card"
          rounded="lg"
          height="200"
        />
      </div>

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
          {{ t('products_teaser.view_all') }}
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
