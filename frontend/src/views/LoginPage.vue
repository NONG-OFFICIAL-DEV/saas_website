<template>
  <section class="section-pad login-page">
    <v-container>
      <div class="header text-center" data-aos="fade-up">
        <span class="section-tag">{{ t('login_page.tag') }}</span>
        <h1 class="section-title">{{ t('login_page.title') }}</h1>
        <p class="section-sub header-sub">{{ t('login_page.sub') }}</p>
      </div>

      <InlineLoader v-if="store.loading" min-height="180px" />

      <div v-else-if="loginableProducts.length" class="cards-grid" data-aos="fade-up">
        <a
          v-for="product in loginableProducts"
          :key="product.id"
          :href="getLoginLink(product.slug)"
          class="product-choice-card"
          :style="{ '--accent': product.accent_color || '#6366F1' }"
        >
          <div class="logo-wrap">
            <img v-if="product.logo_url" :src="product.logo_url" :alt="product.name" class="logo-img" />
            <v-icon v-else icon="mdi-apps" size="28" />
          </div>
          <h3 class="product-name">{{ product.name }}</h3>
          <span class="choice-cta">
            {{ t('login_page.choose', { name: product.name }) }}
            <v-icon icon="mdi-arrow-right" size="16" />
          </span>
        </a>
      </div>

      <p v-else class="empty-note text-center">{{ t('login_page.no_products') }}</p>
    </v-container>
  </section>
</template>

<script setup>
  import { computed, onMounted } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useProductsStore } from '@/stores/products'
  import { getLoginLink } from '@/config/productTrials'
  import InlineLoader from '@/components/global/InlineLoader.vue'

  const { t } = useI18n()
  const store = useProductsStore()

  const loginableProducts = computed(() => store.products.filter(p => getLoginLink(p.slug)))

  onMounted(() => {
    store.fetchProducts()
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
    padding: 32px 28px;
    border-radius: 22px;
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-theme-on-surface), 0.07);
    box-shadow: 0 14px 32px rgba(var(--v-theme-on-surface), 0.07);
    text-decoration: none;
    color: rgb(var(--v-theme-on-surface));
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
    color: rgba(var(--v-theme-on-surface), 0.55);
  }
</style>
