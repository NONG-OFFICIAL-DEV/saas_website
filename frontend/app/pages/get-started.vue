<template>
  <section class="section-pad get-started">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll as="div" class="header text-center">
        <span class="section-tag">{{ t('get_started_page.tag') }}</span>
        <h1 class="section-title">{{ t('get_started_page.title') }}</h1>
        <p class="section-sub header-sub">{{ t('get_started_page.sub') }}</p>
      </RevealOnScroll>

      <InlineLoader v-if="store.loading" min-height="220px" />

      <div v-else class="cards-grid">
        <RevealOnScroll v-for="(product, i) in store.products" :key="product.id" :delay="(i % 3) * 80">
          <TiltCard :max-tilt="6" :glare="false">
            <div
              class="product-choice-card"
              :style="{ '--accent': product.accent_color || '#6366F1' }"
            >
              <div class="logo-wrap">
                <img v-if="product.logo_url" :src="product.logo_url" :alt="product.name" class="logo-img" />
                <Icon v-else name="mdi-apps" size="28" />
              </div>
              <h3 class="product-name">{{ product.name }}</h3>
              <p class="product-tagline">{{ product.tagline || product.summary }}</p>
              <component
                :is="linkFor(product).href ? 'a' : NuxtLinkLocale"
                v-bind="linkFor(product)"
                class="choice-cta"
              >
                {{ t('get_started_page.choose', { name: product.name }) }}
                <Icon name="mdi-arrow-right" size="16" />
              </component>
            </div>
          </TiltCard>
        </RevealOnScroll>
      </div>

      <RevealOnScroll as="div" class="explore-more text-center">
        <NuxtLinkLocale to="/products" class="explore-link">
          {{ t('get_started_page.explore_more') }}
        </NuxtLinkLocale>
      </RevealOnScroll>
    </div>
  </section>
</template>

<script setup lang="ts">
  import { NuxtLinkLocale } from '#components'
  import { ONBOARDABLE_SLUGS } from '~/config/onboardableProducts'
  import type { Product } from '~/types'

  const { t } = useI18n()
  const store = useProductsStore()

  // Awaited (not onMounted) — this is the whole page's content, so it must
  // be present in the server-rendered HTML, not just after hydration.
  await useAsyncData('get-started', async () => {
    await store.fetchProducts()
    return true
  })

  function linkFor(product: Product): { to: string; href?: string } {
    if (ONBOARDABLE_SLUGS.includes(product.slug)) {
      return { to: `/onboarding/${product.slug}` }
    }
    return { to: `/products/${product.slug}` }
  }
</script>

<style scoped>
  .get-started {
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
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    max-width: 760px;
    margin: 0 auto;
  }

  .product-choice-card {
    display: flex;
    flex-direction: column;
    gap: 12px;
    height: 100%;
    padding: 32px 28px;
    border-radius: var(--radius-2xl);
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
    box-shadow: var(--shadow-sm);
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
    font-size: 1.2rem;
    font-weight: 800;
    margin: 0;
  }
  .product-tagline {
    font-size: 0.88rem;
    color: color-mix(in srgb, var(--foreground) 62%, transparent);
    line-height: 1.6;
    margin: 0;
    flex-grow: 1;
  }

  .choice-cta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 8px;
    padding: 12px;
    border-radius: var(--radius-md);
    background: var(--accent);
    color: white;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.9rem;
    transition: transform var(--duration-fast) var(--ease-out-quart);
  }
  .choice-cta:hover {
    transform: translateY(-1px);
  }

  .explore-more {
    margin-top: 40px;
  }
  .explore-link {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--primary);
    text-decoration: none;
  }
  .explore-link:hover {
    text-decoration: underline;
  }
</style>
