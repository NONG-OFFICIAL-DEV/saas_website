<template>
  <div v-if="!store.currentSolution && store.loadingSolution" class="detail-loading">
    <InlineLoader min-height="240px" />
  </div>

  <div v-else-if="!store.currentSolution" class="not-found">
    <div class="w-full px-4 max-w-[1280px] mx-auto text-center">
      <Icon name="mdi-compass-off-outline" size="48" />
      <h2 class="section-title">{{ t('solution_detail.not_found_title') }}</h2>
      <p class="section-sub mx-auto">{{ t('solution_detail.not_found_desc') }}</p>
      <Button as="NuxtLink" to="/solutions">
        {{ t('solution_detail.back_to_solutions') }}
      </Button>
    </div>
  </div>

  <template v-else>
    <section class="section-pad hero">
      <div class="w-full px-4 max-w-[1280px] mx-auto">
        <RevealOnScroll as="div" class="max-w-3xl">
          <div class="icon-wrap mb-4">
            <Icon :name="solution.icon || 'mdi-apps'" size="30" />
          </div>
          <h1 class="hero-title">{{ solution.name }}</h1>
          <p class="hero-tagline">{{ solution.tagline }}</p>
          <p class="section-sub hero-desc">{{ solution.description }}</p>
        </RevealOnScroll>
      </div>
    </section>

    <section v-if="solution.products?.length" class="section-pad section-tint-mint">
      <div class="w-full px-4 max-w-[1280px] mx-auto">
        <RevealOnScroll as="div" class="text-center mb-10">
          <span class="section-tag">{{ t('solution_detail.products_tag') }}</span>
          <h2 class="section-title">{{ t('solution_detail.products_title') }}</h2>
        </RevealOnScroll>
        <RevealOnScroll as="div" class="products-grid" :delay="80">
          <ProductCard v-for="product in solution.products" :key="product.id" :product="product" />
        </RevealOnScroll>
      </div>
    </section>
  </template>
</template>

<script setup lang="ts">
  import { Button } from '~/components/ui/button'

  const { t } = useI18n()
  const route = useRoute()
  const store = useSolutionsStore()

  const slug = computed(() => String(route.params.slug))
  const solution = computed(() => store.currentSolution!)

  // Awaited (not onMounted) so this dynamic route's content is present in
  // the server-rendered HTML. `watch: [slug]` re-runs the fetch on
  // client-side navigation between two solution pages.
  await useAsyncData(
    () => `solution-${slug.value}`,
    async () => {
      await store.fetchSolutionBySlug(slug.value)
      return true
    },
    { watch: [slug] }
  )
</script>

<style scoped>
  .hero {
    position: relative;
  }
  .icon-wrap {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--primary) 12%, transparent);
    color: var(--primary);
  }
  .hero-title {
    font-size: var(--text-h1);
    font-weight: var(--text-h1--font-weight);
    letter-spacing: var(--text-h1--letter-spacing);
    line-height: var(--text-h1--line-height);
    margin: 0 0 8px;
  }
  .hero-tagline {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary);
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
