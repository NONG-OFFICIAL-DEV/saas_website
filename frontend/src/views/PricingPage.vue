<template>
  <section class="section-pad">
    <v-container>
      <div class="hub-header text-center" data-aos="fade-up">
        <span class="section-tag">{{ t('pricing_hub.tag') }}</span>
        <h1 class="section-title">{{ t('pricing_hub.title') }}</h1>
        <p class="section-sub hub-sub">{{ t('pricing_hub.sub') }}</p>
      </div>

      <div v-if="store.loading" class="hub-grid" data-aos="fade-up">
        <v-skeleton-loader v-for="i in 2" :key="i" type="card" rounded="lg" height="200" />
      </div>

      <div v-else class="hub-grid" data-aos="fade-up">
        <router-link
          v-for="product in store.products"
          :key="product.id"
          :to="`/products/${product.slug}#pricing`"
          class="pricing-teaser-card"
          :style="{ '--accent': product.accent_color || '#6366F1' }"
        >
          <div class="teaser-top">
            <div class="logo-wrap">
              <img v-if="product.logo_url" :src="product.logo_url" :alt="product.name" class="logo-img" />
              <v-icon v-else icon="mdi-apps" size="26" />
            </div>
            <span class="status-chip" :class="`status-chip--${product.status}`">
              {{ t(`common.status.${product.status}`) }}
            </span>
          </div>
          <h3 class="product-name">{{ product.name }}</h3>
          <p class="product-tagline">{{ product.tagline || product.summary }}</p>
          <div class="teaser-cta">
            <span>{{ t('pricing_hub.view_plans') }}</span>
            <v-icon icon="mdi-arrow-right" size="16" />
          </div>
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
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-theme-on-surface), 0.07);
    box-shadow: 0 14px 32px rgba(var(--v-theme-on-surface), 0.07);
    text-decoration: none;
    color: rgb(var(--v-theme-on-surface));
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .pricing-teaser-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 44px rgba(var(--v-theme-on-surface), 0.1);
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
    background: rgba(var(--v-theme-success), 0.14);
    color: rgb(var(--v-theme-success));
  }
  .status-chip--beta {
    background: rgba(var(--v-theme-info), 0.14);
    color: rgb(var(--v-theme-info));
  }
  .status-chip--coming_soon {
    background: rgba(var(--v-theme-on-surface), 0.08);
    color: rgba(var(--v-theme-on-surface), 0.55);
  }

  .product-name {
    font-size: 1.15rem;
    font-weight: 800;
    margin: 0;
  }
  .product-tagline {
    font-size: 0.87rem;
    color: rgba(var(--v-theme-on-surface), 0.62);
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
