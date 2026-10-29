<template>
  <div v-if="!store.currentProduct && store.loadingProduct" class="detail-loading">
    <InlineLoader min-height="240px" />
  </div>

  <div v-else-if="!store.currentProduct" class="not-found">
    <div class="w-full px-4 max-w-[1280px] mx-auto text-center">
      <Icon name="mdi-compass-off-outline" size="48" />
      <h2 class="section-title">{{ t('product_detail.not_found_title') }}</h2>
      <p class="section-sub mx-auto">
        {{ t('product_detail.not_found_desc') }}
      </p>
      <Button as="NuxtLink" to="/products">
        {{ t('button.back_to_products') }}
      </Button>
    </div>
  </div>

  <template v-else>
    <!-- ── Hero ── -->
    <section
      class="section-pad hero"
      :style="{ '--accent': product.accent_color || '#6366F1' }"
    >
      <div class="w-full px-4 max-w-[1280px] mx-auto">
        <div class="grid grid-cols-12 gap-6 items-center">
          <RevealOnScroll as="div" class="col-span-12 md:col-span-7">
            <Badge class="status-chip mb-4">
              {{ t(`common.status.${product.status}`) }}
            </Badge>
            <h1 class="hero-title">{{ product.name }}</h1>
            <p class="hero-tagline">{{ product.tagline }}</p>
            <p class="section-sub hero-desc">{{ product.description }}</p>

            <div class="hero-actions">
              <Button @click="scrollToCta">
                {{ product.cta_label || t('button.learn_more') }}
              </Button>
              <Button as="NuxtLink" variant="outline" to="/products">
                {{ t('button.all_products') }}
              </Button>
            </div>
          </RevealOnScroll>

          <RevealOnScroll as="div" class="col-span-12 md:col-span-5" :delay="100">
            <img
              v-if="product.hero_image_url"
              :src="product.hero_image_url"
              :alt="product.name"
              class="hero-image rounded-lg"
            />
            <div v-else class="hero-geo hidden md:block">
              <Geometric3D :accent="product.accent_color ?? undefined" />
            </div>
          </RevealOnScroll>
        </div>
      </div>
    </section>

    <!-- ── Feature Showcase (CMS-driven: grid/showcase/detail/workflow
         sections) — currently only Nexstack POS has this content; every
         other product still shows the generic Features/Screenshots grids
         below until it gets its own feature sections authored in the CMS. ── -->
    <ProductFeatureSections v-if="product.feature_sections?.length" :sections="product.feature_sections" />

    <template v-else>
      <!-- ── Features ── -->
      <section
        v-if="product.product_features?.length"
        class="section-pad section-tint-mint"
      >
        <div class="w-full px-4 max-w-[1280px] mx-auto">
          <RevealOnScroll as="div" class="text-center mb-10">
            <span class="section-tag">
              {{ t('product_detail.features_tag') }}
            </span>
            <h2 class="section-title">
              {{ t('product_detail.features_title', { name: product.name }) }}
            </h2>
          </RevealOnScroll>
          <div class="features-grid">
            <RevealOnScroll
              v-for="(f, i) in product.product_features"
              :key="f.id"
              :delay="(i % 3) * 80"
            >
              <TiltCard :max-tilt="6" :glare="false">
                <div class="feature-card">
                  <div
                    class="feature-icon"
                    :style="{ '--accent': product.accent_color }"
                  >
                    <Icon :name="f.icon || 'mdi-check-circle-outline'" size="22" />
                  </div>
                  <h3 class="feature-title">{{ f.title }}</h3>
                  <p class="feature-desc">{{ f.description }}</p>
                </div>
              </TiltCard>
            </RevealOnScroll>
          </div>
        </div>
      </section>

      <!-- ── Screenshots ── -->
      <section v-if="product.product_screenshots?.length" class="section-pad">
        <div class="w-full px-4 max-w-[1280px] mx-auto">
          <RevealOnScroll as="div" class="text-center mb-10">
            <span class="section-tag">
              {{ t('product_detail.screenshots_tag') }}
            </span>
            <h2 class="section-title">
              {{ t('product_detail.screenshots_title') }}
            </h2>
          </RevealOnScroll>
          <div class="screenshots-grid">
            <RevealOnScroll
              v-for="(s, i) in product.product_screenshots"
              :key="s.id"
              as="figure"
              class="screenshot"
              :delay="(i % 3) * 80"
            >
              <img
                :src="s.url"
                :alt="s.alt_text || product.name"
                class="rounded-lg w-full"
              />
              <figcaption v-if="s.caption">{{ s.caption }}</figcaption>
            </RevealOnScroll>
          </div>
        </div>
      </section>
    </template>

    <!-- ── Pricing ──
         Pricing is controlled entirely within each product's own SaaS
         backend — this site only ever displays it via that product's own
         live API, one bespoke component per product. A product with no
         live pricing component yet simply shows no pricing section here;
         there is no CMS-authored placeholder pricing. ── -->
    <PriceSection v-if="product.slug === 'nexstack-pos'" />
    <StudioPriceSection
      v-else-if="product.slug === 'studio-management'"
      @select-plan="goToStudioRegister"
    />

    <!-- ── FAQ ── -->
    <ProductFaqSection :faqs="product.faqs" />

  </template>
</template>

<script setup lang="ts">
import { Badge } from '~/components/ui/badge'
import { Button } from '~/components/ui/button'

const { t, locale } = useI18n()

const route = useRoute()
const store = useProductsStore()

const slug = computed(() => String(route.params.slug))
const product = computed(() => store.currentProduct!)

// Registered before the data fetch below — useSeoMeta only needs to be
// declared once with reactive getters, it doesn't need product data to
// already be loaded. (Composable calls after an awaited useAsyncData can
// lose Nuxt's async context, so anything else needed from here on is
// resolved above this point.)
useSeoMeta({
  title: () => (product.value ? product.value.seo_title || `${product.value.name} · Nexstack` : undefined),
  description: () => (product.value ? product.value.seo_description || product.value.summary || undefined : undefined)
})

// Awaited (not onMounted) so this dynamic route's content is present in the
// server-rendered HTML — this is the whole page's content, and a crawler
// hitting /products/some-slug directly must see the real product, not the
// not-found state. `watch: [slug, locale]` re-runs the fetch on client-side
// navigation between two product pages (Vue Router reuses this page's
// component instance across param-only changes, so a plain top-level call
// wouldn't re-fire on its own) and when the visitor switches language while
// already on the page, so CMS content re-resolves to the new locale.
await useAsyncData(
  () => `product-${slug.value}-${locale.value}`,
  async () => {
    await store.fetchProductBySlug(slug.value)
    return true
  },
  { watch: [slug, locale] }
)

function scrollToCta() {
  document.getElementById('pricing')?.scrollIntoView({ behavior: 'smooth' })
}

// Hands off to this site's own onboarding wizard, which calls Studio's
// real registration API server-side, carrying the chosen plan AND billing
// cycle through — both flow through as query params into the wizard.
const localePath = useLocalePath()
function goToStudioRegister(planCode?: string, billingCycle?: string) {
  const query: Record<string, string> = {}
  if (planCode) query.plan = planCode
  if (billingCycle) query.cycle = billingCycle
  navigateTo(localePath({ path: '/onboarding/studio-management', query }))
}
</script>

<style scoped>
  .hero {
    position: relative;
    overflow: hidden;
  }

  .status-chip {
    background: color-mix(
      in srgb,
      var(--accent, #6366f1) 16%,
      transparent
    ) !important;
    color: var(--accent, #6366f1) !important;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.68rem;
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
    color: var(--accent, #6366f1);
    margin: 0 0 14px;
  }
  .hero-desc {
    max-width: 560px;
    margin: 0 0 26px;
  }
  .hero-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
  }
  .hero-image {
    max-height: 360px;
    border-radius: var(--radius-xl) !important;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
  }
  .hero-geo {
    width: 100%;
    max-width: 340px;
    height: 320px;
    margin: 0 auto;
  }

  .features-grid {
    display: grid;
    gap: 22px;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  }
  .feature-card {
    padding: 26px 24px;
    border-radius: var(--radius-xl);
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 6%, transparent);
    box-shadow: var(--shadow-sm);
    transition:
      box-shadow var(--duration-base) var(--ease-out-quart);
  }
  .feature-card:hover {
    box-shadow: var(--shadow-md);
  }
  .feature-icon {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--accent, #6366f1) 14%, transparent);
    color: var(--accent, #6366f1);
    margin-bottom: 14px;
    transition: transform var(--duration-base) var(--ease-spring);
  }
  .feature-card:hover .feature-icon {
    transform: scale(1.08);
  }
  .feature-title {
    font-size: 1rem;
    font-weight: 800;
    margin: 0 0 6px;
  }
  .feature-desc {
    font-size: 0.86rem;
    color: color-mix(in srgb, var(--foreground) 60%, transparent);
    line-height: 1.55;
    margin: 0;
  }

  .screenshots-grid {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  }
  .screenshot img {
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-md);
  }
  .screenshot figcaption {
    text-align: center;
    font-size: 0.82rem;
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
    margin-top: 8px;
  }

  .not-found,
  .detail-loading {
    padding: 100px 0;
  }
</style>
