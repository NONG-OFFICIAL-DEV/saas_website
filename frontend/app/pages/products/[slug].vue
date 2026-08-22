<template>
  <div v-if="!store.currentProduct && store.loadingProduct" class="detail-loading">
    <InlineLoader min-height="240px" />
  </div>

  <div v-else-if="!store.currentProduct" class="not-found">
    <Container class="text-center">
      <Icon name="mdi-compass-off-outline" size="48" />
      <h2 class="section-title">{{ t('product_detail.not_found_title') }}</h2>
      <p class="section-sub mx-auto">
        {{ t('product_detail.not_found_desc') }}
      </p>
      <Button as="NuxtLink" to="/products">
        {{ t('button.back_to_products') }}
      </Button>
    </Container>
  </div>

  <template v-else>
    <!-- ── Hero ── -->
    <section
      class="section-pad hero"
      :style="{ '--accent': product.accent_color || '#6366F1' }"
    >
      <Container>
        <Row align="center">
          <Col cols="12" md="7" data-aos="fade-up">
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
          </Col>

          <Col cols="12" md="5" data-aos="fade-up" data-aos-delay="100">
            <img
              v-if="product.hero_image_url"
              :src="product.hero_image_url"
              :alt="product.name"
              class="hero-image rounded-lg"
            />
            <div v-else class="hero-geo hidden md:block">
              <Geometric3D :accent="product.accent_color ?? undefined" />
            </div>
          </Col>
        </Row>
      </Container>
    </section>

    <!-- ── Features ── -->
    <section
      v-if="product.product_features?.length"
      class="section-pad section-tint-mint"
    >
      <Container>
        <div class="text-center mb-10" data-aos="fade-up">
          <span class="section-tag">
            {{ t('product_detail.features_tag') }}
          </span>
          <h2 class="section-title">
            {{ t('product_detail.features_title', { name: product.name }) }}
          </h2>
        </div>
        <div class="features-grid">
          <div
            v-for="f in product.product_features"
            :key="f.id"
            class="feature-card"
            data-aos="fade-up"
          >
            <div
              class="feature-icon"
              :style="{ '--accent': product.accent_color }"
            >
              <Icon :name="f.icon || 'mdi-check-circle-outline'" size="22" />
            </div>
            <h3 class="feature-title">{{ f.title }}</h3>
            <p class="feature-desc">{{ f.description }}</p>
          </div>
        </div>
      </Container>
    </section>

    <!-- ── Screenshots ── -->
    <section v-if="product.product_screenshots?.length" class="section-pad">
      <Container>
        <div class="text-center mb-10" data-aos="fade-up">
          <span class="section-tag">
            {{ t('product_detail.screenshots_tag') }}
          </span>
          <h2 class="section-title">
            {{ t('product_detail.screenshots_title') }}
          </h2>
        </div>
        <div class="screenshots-grid">
          <figure
            v-for="s in product.product_screenshots"
            :key="s.id"
            class="screenshot"
            data-aos="fade-up"
          >
            <img
              :src="s.url"
              :alt="s.alt_text || product.name"
              class="rounded-lg w-full"
            />
            <figcaption v-if="s.caption">{{ s.caption }}</figcaption>
          </figure>
        </div>
      </Container>
    </section>

    <!-- ── Deep-dive extras (bespoke per-product sections, e.g. POS mockups) ── -->
    <component :is="extra" v-for="(extra, idx) in deepDiveExtras" :key="idx" />

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

    <!-- ── Final CTA ── -->
    <section id="cta" class="section-pad">
      <Container class="text-center">
        <h2 class="section-title" data-aos="fade-up">
          {{ t('product_detail.ready_title', { name: product.name }) }}
        </h2>

        <div
          v-if="product.cta_type === 'external_link'"
          data-aos="fade-up"
        >
          <Button
            as="a"
            :href="product.cta_url ?? undefined"
            target="_blank"
            rel="noopener"
          >
            {{ product.cta_label || t('button.visit_site') }}
          </Button>
        </div>

        <div v-else data-aos="fade-up">
          <Button :as="'to' in finalCtaLink ? 'NuxtLink' : 'a'" v-bind="finalCtaLink">
            {{ product.cta_label || t('button.start_free_trial') }}
          </Button>
        </div>
      </Container>
    </section>
  </template>
</template>

<script setup lang="ts">
import type { Component } from 'vue'
import { Alert, AlertDescription } from '~/components/ui/alert'
import { Badge } from '~/components/ui/badge'
import { Button } from '~/components/ui/button'
import RestaurantPosSection from '~/components/sections/RestaurantPosSection.vue'
import InventorySection from '~/components/sections/InventorySection.vue'
import MobileQrSection from '~/components/sections/MobileQrSection.vue'
import { getTrialLink } from '~/config/productTrials'

// Products with a guided in-house onboarding wizard skip straight to it;
// anything else falls back to that product's own external signup (see
// config/productTrials.ts).
const ONBOARDABLE_SLUGS = ['nexstack-pos', 'studio-management']

const { t } = useI18n()

// Bespoke, hand-built sections that only exist for specific products —
// everything else renders purely from CMS data above.
//
// BizTypesSection and FeatureCardsSection were dropped from this list:
// both duplicated content shown elsewhere (BizTypesSection's icon grid
// FeatureCardsSection re-listed features already shown in the generic
// CMS "Features" section above). Cutting them shortens the page without
// losing any information a visitor doesn't already see.
const DEEP_DIVE_EXTRAS: Record<string, Component[]> = {
  'nexstack-pos': [
    RestaurantPosSection,
    InventorySection,
    MobileQrSection
  ]
}

const route = useRoute()
const store = useProductsStore()

const slug = computed(() => String(route.params.slug))
const product = computed(() => store.currentProduct!)
const deepDiveExtras = computed(
  () => DEEP_DIVE_EXTRAS[slug.value] ?? []
)

// Default ("register") CTA hands off to this site's own onboarding
// wizard for products that have one; otherwise falls back to that
// product's own external signup (see config/productTrials.ts).
const finalCtaLink = computed(() => {
  if (ONBOARDABLE_SLUGS.includes(product.value.slug)) {
    return { to: `/onboarding/${product.value.slug}` }
  }
  const link = getTrialLink(product.value.slug)
  return 'href' in link ? { href: link.href, target: '_blank', rel: 'noopener' } : { to: link.to }
})

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
// not-found state. `watch: [slug]` re-runs the fetch on client-side
// navigation between two product pages (Vue Router reuses this page's
// component instance across param-only changes, so a plain top-level call
// wouldn't re-fire on its own).
await useAsyncData(
  () => `product-${slug.value}`,
  async () => {
    await store.fetchProductBySlug(slug.value)
    return true
  },
  { watch: [slug] }
)

function scrollToCta() {
  document.getElementById('cta')?.scrollIntoView({ behavior: 'smooth' })
}

// Hands off to this site's own onboarding wizard, which calls Studio's
// real registration API server-side, carrying the chosen plan through.
function goToStudioRegister(planCode?: string) {
  navigateTo({ path: '/onboarding/studio-management', query: planCode ? { plan: planCode } : {} })
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
    font-size: clamp(1.8rem, 4.5vw, 3rem);
    font-weight: 900;
    letter-spacing: -1px;
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
    border-radius: 20px !important;
    overflow: hidden;
    box-shadow: 0 18px 40px color-mix(in srgb, var(--foreground) 10%, transparent);
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
    border-radius: 20px;
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 6%, transparent);
    box-shadow: 0 12px 28px color-mix(in srgb, var(--foreground) 6%, transparent);
    transition:
      transform 0.25s ease,
      box-shadow 0.25s ease;
  }
  .feature-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 36px color-mix(in srgb, var(--foreground) 10%, transparent);
  }
  .feature-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--accent, #6366f1) 14%, transparent);
    color: var(--accent, #6366f1);
    margin-bottom: 14px;
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
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 14px 32px color-mix(in srgb, var(--foreground) 8%, transparent);
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
