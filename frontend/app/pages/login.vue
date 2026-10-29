<template>
  <section class="section-pad login-page">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll as="div" class="header text-center">
        <span class="section-tag">{{ t('login_page.tag') }}</span>
        <h1 class="section-title">{{ t('login_page.title') }}</h1>
        <p class="section-sub header-sub">{{ t('login_page.sub') }}</p>
      </RevealOnScroll>

      <InlineLoader v-if="store.loading" min-height="180px" />

      <div v-else-if="loginableProducts.length" class="cards-grid">
        <RevealOnScroll v-for="(product, i) in loginableProducts" :key="product.id" :delay="(i % 3) * 80">
          <TiltCard :max-tilt="6" :glare="false">
            <a
              :href="getLoginLink(product.slug) ?? undefined"
              target="_blank"
              rel="noopener noreferrer"
              class="product-choice-card"
              :style="{ '--accent': product.accent_color || '#6366F1' }"
            >
              <div class="logo-wrap">
                <img v-if="product.logo_url" :src="product.logo_url" :alt="product.name" class="logo-img" />
                <Icon v-else name="mdi-apps" size="28" />
              </div>
              <h3 class="product-name">{{ product.name }}</h3>
              <span class="choice-cta">
                {{ t('login_page.choose', { name: product.name }) }}
                <Icon name="mdi-arrow-right" size="16" />
              </span>
            </a>
          </TiltCard>
        </RevealOnScroll>
      </div>

      <p v-else class="empty-note text-center">{{ t('login_page.no_products') }}</p>
    </div>
  </section>
</template>

<script setup lang="ts">
  import { getLoginLink } from '~/config/productTrials'

  const { t } = useI18n()
  const store = useProductsStore()

  const loginableProducts = computed(() => store.products.filter((p) => getLoginLink(p.slug)))

  // Awaited (not onMounted) — this is the whole page's content, so it must
  // be present in the server-rendered HTML, not just after hydration.
  await useAsyncData('login-page', async () => {
    await store.fetchProducts()
    return true
  })
</script>

<style scoped>
  .login-page {
    padding-top: 120px;
  }
  .header {
    max-width: 620px;
    margin: 0 auto 48px;
  }
  .header-sub {
    max-width: 480px;
    margin: 0 auto;
  }

  .cards-grid {
    display: grid;
    gap: 24px;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    max-width: 640px;
    margin: 0 auto;
  }

  .product-choice-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 12px;
    height: 100%;
    padding: 32px 28px;
    border-radius: var(--radius-2xl);
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
    box-shadow: var(--shadow-sm);
    text-decoration: none;
    color: var(--foreground);
    transition: box-shadow var(--duration-base) var(--ease-out-quart);
  }
  .product-choice-card:hover {
    box-shadow: var(--shadow-lg);
  }

  .logo-wrap {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--accent) 16%, transparent);
    color: var(--accent);
  }
  .logo-img {
    width: 30px;
    height: 30px;
    object-fit: contain;
  }

  .product-name {
    font-size: 1.15rem;
    font-weight: 800;
    margin: 0;
  }

  .choice-cta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.86rem;
    font-weight: 700;
    color: var(--accent);
  }

  .empty-note {
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
  }
</style>
