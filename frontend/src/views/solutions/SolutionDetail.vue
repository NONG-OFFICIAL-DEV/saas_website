<template>
  <div v-if="store.loadingSolution" class="detail-loading">
    <v-container>
      <v-skeleton-loader type="heading, text, article" />
    </v-container>
  </div>

  <div v-else-if="!store.currentSolution" class="not-found">
    <v-container class="text-center">
      <v-icon icon="mdi-compass-off-outline" size="48" />
      <h2 class="section-title">{{ t('solution_detail.not_found_title') }}</h2>
      <p class="section-sub mx-auto">{{ t('solution_detail.not_found_desc') }}</p>
      <v-btn color="primary" rounded="lg" to="/solutions">
        {{ t('solution_detail.back_to_solutions') }}
      </v-btn>
    </v-container>
  </div>

  <template v-else>
    <section class="section-pad hero">
      <v-container>
        <v-row align="center">
          <v-col cols="12" md="8" data-aos="fade-right">
            <div class="icon-wrap mb-4">
              <v-icon :icon="solution.icon || 'mdi-apps'" size="30" />
            </div>
            <h1 class="hero-title">{{ solution.name }}</h1>
            <p class="hero-tagline">{{ solution.tagline }}</p>
            <p class="section-sub hero-desc">{{ solution.description }}</p>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <section v-if="solution.products?.length" class="section-pad section-tint-mint">
      <v-container>
        <div class="text-center mb-10" data-aos="fade-up">
          <span class="section-tag">{{ t('solution_detail.products_tag') }}</span>
          <h2 class="section-title">{{ t('solution_detail.products_title') }}</h2>
        </div>
        <div class="products-grid" data-aos="fade-up">
          <ProductCard v-for="product in solution.products" :key="product.id" :product="product" />
        </div>
      </v-container>
    </section>
  </template>
</template>

<script setup>
  import { computed, onMounted, watch } from 'vue'
  import { useRoute } from 'vue-router'
  import { useI18n } from 'vue-i18n'
  import { useSolutionsStore } from '@/stores/solutions'
  import ProductCard from '@/components/products/ProductCard.vue'

  const { t } = useI18n()
  const route = useRoute()
  const store = useSolutionsStore()

  const solution = computed(() => store.currentSolution)

  function load(slug) {
    store.fetchSolutionBySlug(slug)
  }

  onMounted(() => load(route.params.slug))
  watch(() => route.params.slug, slug => load(slug))
</script>

<style scoped>
  .hero {
    position: relative;
  }
  .icon-wrap {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(var(--v-theme-primary), 0.12);
    color: rgb(var(--v-theme-primary));
  }
  .hero-title {
    font-size: clamp(1.8rem, 4.5vw, 3rem);
    font-weight: 900;
    letter-spacing: -1px;
    margin: 0 0 8px;
  }
  .hero-tagline {
    font-size: 1.1rem;
    font-weight: 600;
    color: rgb(var(--v-theme-primary));
    margin: 0 0 14px;
  }
  .hero-desc {
    max-width: 640px;
    margin: 0;
  }

  .products-grid {
    display: grid;
    gap: 24px;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  }

  .not-found,
  .detail-loading {
    padding: 100px 0;
  }
</style>
