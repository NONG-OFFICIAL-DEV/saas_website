<template>
  <section class="section-pad">
    <Container>
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

      <Alert v-else-if="store.error" variant="destructive" class="mt-6">
        <AlertDescription>{{ store.error }}</AlertDescription>
      </Alert>

      <div v-else class="empty-state">
        <Icon name="mdi-package-variant" size="40" />
        <p>{{ t('products_hub.empty') }}</p>
      </div>
    </Container>
  </section>
</template>

<script setup lang="ts">
  import { Alert, AlertDescription } from '~/components/ui/alert'

  const { t } = useI18n()
  const store = useProductsStore()

  // Awaited (not onMounted) — this is the whole page's content, so it must
  // be present in the server-rendered HTML, not just after hydration.
  await useAsyncData('products-hub', async () => {
    await store.fetchProducts()
    return true
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
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
  }
</style>
