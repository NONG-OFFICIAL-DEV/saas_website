<template>
  <section class="section-pad get-started">
    <v-container>
      <div class="header text-center" data-aos="fade-up">
        <span class="section-tag">{{ t('get_started_page.tag') }}</span>
        <h1 class="section-title">{{ t('get_started_page.title') }}</h1>
        <p class="section-sub header-sub">{{ t('get_started_page.sub') }}</p>
      </div>

      <div v-if="store.loading" class="cards-grid" data-aos="fade-up">
        <v-skeleton-loader v-for="i in 2" :key="i" type="card" rounded="lg" height="220" />
      </div>

      <div v-else class="cards-grid" data-aos="fade-up">
        <div
          v-for="product in store.products"
          :key="product.id"
          class="product-choice-card"
          :style="{ '--accent': product.accent_color || '#6366F1' }"
        >
          <div class="logo-wrap">
            <img v-if="product.logo_url" :src="product.logo_url" :alt="product.name" class="logo-img" />
            <v-icon v-else icon="mdi-apps" size="28" />
          </div>
          <h3 class="product-name">{{ product.name }}</h3>
          <p class="product-tagline">{{ product.tagline || product.summary }}</p>
          <component
            :is="linkFor(product).href ? 'a' : 'router-link'"
            v-bind="linkFor(product)"
            class="choice-cta"
          >
            {{ t('get_started_page.choose', { name: product.name }) }}
            <v-icon icon="mdi-arrow-right" size="16" />
          </component>
        </div>
      </div>

      <div class="explore-more text-center" data-aos="fade-up">
        <router-link to="/products" class="explore-link">
          {{ t('get_started_page.explore_more') }}
        </router-link>
      </div>
    </v-container>
  </section>
</template>

<script setup>
  import { onMounted } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useProductsStore } from '@/stores/products'

  const { t } = useI18n()
  const store = useProductsStore()

  // Products with a guided in-house onboarding wizard skip straight to it;
  // anything else falls back to that product's own marketing page (no
  // guided setup exists for it yet).
  const ONBOARDABLE_SLUGS = ['nexstack-pos', 'studio-management']

  onMounted(() => {
    store.fetchProducts()
  })

  function linkFor(product) {
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
    padding: 32px 28px;
    border-radius: 22px;
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-theme-on-surface), 0.07);
    box-shadow: 0 14px 32px rgba(var(--v-theme-on-surface), 0.07);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .product-choice-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 44px rgba(var(--v-theme-on-surface), 0.1);
  }

  .logo-wrap {
    width: 56px;
    height: 56px;
    border-radius: 16px;
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
    color: rgba(var(--v-theme-on-surface), 0.62);
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
    border-radius: 12px;
    background: var(--accent);
    color: white;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.9rem;
  }

  .explore-more {
    margin-top: 40px;
  }
  .explore-link {
    font-size: 0.9rem;
    font-weight: 700;
    color: rgb(var(--v-theme-primary));
    text-decoration: none;
  }
  .explore-link:hover {
    text-decoration: underline;
  }
</style>
