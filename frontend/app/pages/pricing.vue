<template>
  <section class="section-pad">
    <Container>
      <div class="hub-header text-center" data-aos="fade-up">
        <span class="section-tag">{{ t('pricing_hub.tag') }}</span>
        <h1 class="section-title">{{ t('pricing_hub.title') }}</h1>
        <p class="section-sub hub-sub">{{ t('pricing_hub.sub') }}</p>
      </div>

      <InlineLoader v-if="store.loading" min-height="200px" />

      <div v-else class="hub-grid" data-aos="fade-up">
        <NuxtLink
          v-for="product in store.products"
          :key="product.id"
          :to="`/products/${product.slug}#pricing`"
          class="pricing-teaser-card"
          :style="{ '--accent': product.accent_color || '#6366F1' }"
        >
          <div class="teaser-top">
            <div class="logo-wrap">
              <img v-if="product.logo_url" :src="product.logo_url" :alt="product.name" class="logo-img" />
              <Icon v-else name="mdi-apps" size="26" />
            </div>
            <span class="status-chip" :class="`status-chip--${product.status}`">
              {{ t(`common.status.${product.status}`) }}
            </span>
          </div>
          <h3 class="product-name">{{ product.name }}</h3>
          <p class="product-tagline">{{ product.tagline || product.summary }}</p>
          <div class="teaser-cta">
            <span>{{ t('pricing_hub.view_plans') }}</span>
            <Icon name="mdi-arrow-right" size="16" />
          </div>
        </NuxtLink>
      </div>
    </Container>
  </section>
</template>

<script setup lang="ts">
  const { t } = useI18n()
  const store = useProductsStore()

  // Awaited (not onMounted) — this is the whole page's content, so it must
  // be present in the server-rendered HTML, not just after hydration.
  await useAsyncData('pricing-hub', async () => {
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
    gap: 24px;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    max-width: 900px;
    margin: 0 auto;
  }

  .pricing-teaser-card {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 30px 28px;
    border-radius: 22px;
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
    box-shadow: 0 14px 32px color-mix(in srgb, var(--foreground) 7%, transparent);
    text-decoration: none;
    color: var(--foreground);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .pricing-teaser-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 44px color-mix(in srgb, var(--foreground) 10%, transparent);
  }

  .teaser-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .logo-wrap {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--accent) 16%, transparent);
    color: var(--accent);
  }
  .logo-img {
    width: 28px;
    height: 28px;
    object-fit: contain;
  }

  .status-chip {
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.65rem;
    letter-spacing: 0.04em;
    padding: 5px 12px;
    border-radius: 999px;
  }
  .status-chip--live {
    background: color-mix(in srgb, var(--success) 14%, transparent);
    color: var(--success);
  }
  .status-chip--beta {
    background: color-mix(in srgb, var(--info) 14%, transparent);
    color: var(--info);
  }
  .status-chip--coming_soon {
    background: color-mix(in srgb, var(--foreground) 8%, transparent);
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
  }

  .product-name {
    font-size: 1.15rem;
    font-weight: 800;
    margin: 0;
  }
  .product-tagline {
    font-size: 0.87rem;
    color: color-mix(in srgb, var(--foreground) 62%, transparent);
    line-height: 1.6;
    margin: 0;
  }

  .teaser-cta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.83rem;
    font-weight: 700;
    color: var(--accent);
    margin-top: 4px;
    transition: gap 0.15s;
  }
  .pricing-teaser-card:hover .teaser-cta {
    gap: 10px;
  }
</style>
