<template>
  <section class="section-pad">
    <v-container>
      <div class="hub-header text-center" data-aos="fade-up">
        <span class="section-tag">{{ t('products_hub.tag') }}</span>
        <h1 class="section-title">{{ t('products_hub.title') }}</h1>
        <p class="section-sub hub-sub">
          {{ t('products_hub.sub') }}
        </p>
      </div>

      <InlineLoader v-if="store.loading" min-height="220px" />

      <div v-else-if="store.products.length" class="hub-grid" data-aos="fade-up">
        <ProductCard
          v-for="product in store.products"
          :key="product.id"
          :product="product"
        />
      </div>

      <v-alert
        v-else-if="store.error"
        type="error"
        variant="tonal"
        rounded="lg"
        class="mt-6"
      >
        {{ store.error }}
      </v-alert>

      <div v-else class="empty-state">
        <v-icon icon="mdi-package-variant" size="40" />
        <p>{{ t('products_hub.empty') }}</p>
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

  onMounted(() => {
    store.fetchProducts()
  })
</script>

<style scoped>
  .hub-header {
    max-width: 620px;
    margin: 0 auto 48px;
  }
  .hub-sub {
    max-width: 480px;
    margin: 0 auto;
  }

  .hub-grid {
    display: grid;
    gap: 28px;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  }

  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 64px 0;
    color: rgba(var(--v-theme-on-surface), 0.5);
  }
</style>
