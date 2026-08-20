<template>
  <div v-if="store.loadingProduct" class="detail-loading">
    <InlineLoader min-height="240px" />
  </div>

  <div v-else-if="!store.currentProduct" class="not-found">
    <v-container class="text-center">
      <v-icon icon="mdi-compass-off-outline" size="48" />
      <h2 class="section-title">{{ t('product_detail.not_found_title') }}</h2>
      <p class="section-sub mx-auto">
        {{ t('product_detail.not_found_desc') }}
      </p>
      <v-btn color="primary" rounded="lg" to="/products">
        {{ t('button.back_to_products') }}
      </v-btn>
    </v-container>
  </div>

  <template v-else>
    <!-- ── Hero ── -->
    <section
      class="section-pad hero"
      :style="{ '--accent': product.accent_color || '#6366F1' }"
    >
      <v-container>
        <v-row align="center">
          <v-col cols="12" md="7" data-aos="fade-right">
            <v-chip size="small" variant="flat" class="status-chip mb-4">
              {{ t(`common.status.${product.status}`) }}
            </v-chip>
            <h1 class="hero-title">{{ product.name }}</h1>
            <p class="hero-tagline">{{ product.tagline }}</p>
            <p class="section-sub hero-desc">{{ product.description }}</p>

            <div class="hero-actions">
              <v-btn
                color="primary"
                rounded="lg"
                variant="flat"
                @click="scrollToCta"
              >
                {{ product.cta_label || t('button.learn_more') }}
              </v-btn>
              <v-btn
                rounded="lg"

                variant="outlined"
                to="/products"
              >
                {{ t('button.all_products') }}
              </v-btn>
            </div>
          </v-col>

          <v-col cols="12" md="5" data-aos="fade-left">
            <v-img
              v-if="product.hero_image_url"
              :src="product.hero_image_url"
              :alt="product.name"
              rounded="lg"
              class="hero-image"
            />
            <div v-else class="hero-geo d-none d-md-block">
              <Geometric3D :accent="product.accent_color ?? undefined" />
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- ── Features ── -->
    <section
      v-if="product.product_features?.length"
      class="section-pad section-tint-mint"
    >
      <v-container>
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
              <v-icon :icon="f.icon || 'mdi-check-circle-outline'" size="22" />
            </div>
            <h3 class="feature-title">{{ f.title }}</h3>
            <p class="feature-desc">{{ f.description }}</p>
          </div>
        </div>
      </v-container>
    </section>

    <!-- ── Screenshots ── -->
    <section v-if="product.product_screenshots?.length" class="section-pad">
      <v-container>
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
            <v-img
              :src="s.url"
              :alt="s.alt_text || product.name"
              rounded="lg"
            />
            <figcaption v-if="s.caption">{{ s.caption }}</figcaption>
          </figure>
        </div>
      </v-container>
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

    <!-- ── Final CTA / waitlist form ── -->
    <section id="cta" class="section-pad">
      <v-container class="text-center">
        <h2 class="section-title" data-aos="fade-up">
          {{ t('product_detail.ready_title', { name: product.name }) }}
        </h2>

        <div
          v-if="product.cta_type === 'waitlist'"
          class="waitlist-form"
          data-aos="fade-up"
        >
          <v-form v-if="!waitlistSubmitted" @submit.prevent="submitWaitlist">
            <v-alert
              v-if="waitlistError"
              type="error"
              variant="tonal"
              rounded="lg"
              density="compact"
              class="mb-4"
            >
              {{ waitlistError }}
            </v-alert>
            <v-row dense>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="waitlist.name"
                  :label="t('product_detail.name_label')"
                  required
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="waitlist.email"
                  :label="t('product_detail.email_label')"
                  type="email"
                  required
                />
              </v-col>
            </v-row>
            <v-btn
              color="primary"
              variant="flat"
              rounded="lg"
              block
              type="submit"
              :loading="waitlistLoading"
            >
              {{ product.cta_label || t('button.join_waitlist') }}
            </v-btn>
          </v-form>
          <v-alert v-else type="success" variant="tonal" rounded="lg">
            {{ t('product_detail.waitlist_success') }}
          </v-alert>
        </div>

        <div
          v-else-if="product.cta_type === 'external_link'"
          data-aos="fade-up"
        >
          <v-btn
            color="primary"
            variant="flat"
            rounded="lg"
            :href="product.cta_url ?? undefined"
            target="_blank"
            rel="noopener"
          >
            {{ product.cta_label || t('button.visit_site') }}
          </v-btn>
        </div>

        <div v-else data-aos="fade-up">
          <v-btn
            color="primary"
            variant="flat"
            rounded="lg"
            v-bind="finalCtaLink"
          >
            {{ product.cta_label || t('button.start_free_trial') }}
          </v-btn>
        </div>
      </v-container>
    </section>
  </template>
</template>

<script setup lang="ts">
import type { Component } from 'vue'
import RestaurantPosSection from '~/components/sections/RestaurantPosSection.vue'
import InventorySection from '~/components/sections/InventorySection.vue'
import MobileQrSection from '~/components/sections/MobileQrSection.vue'
import { getTrialLink } from '~/config/productTrials'
import { submitLead } from '~/api/leads'

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

const waitlist = ref({ name: '', email: '' })
const waitlistLoading = ref(false)
const waitlistSubmitted = ref(false)
const waitlistError = ref<string | null>(null)

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
// real registration API server-side (carrying the chosen plan through),
// rather than scrolling to this site's waitlist form.
function goToStudioRegister(planCode?: string) {
  navigateTo({ path: '/onboarding/studio-management', query: planCode ? { plan: planCode } : {} })
}

async function submitWaitlist() {
  if (!waitlist.value.name || !waitlist.value.email) return
  waitlistLoading.value = true
  waitlistError.value = null
  try {
    await submitLead({
      name: waitlist.value.name,
      email: waitlist.value.email,
      source: product.value.lead_source || product.value.slug
    })
    waitlistSubmitted.value = true
  } catch (err: any) {
    waitlistError.value =
      err?.response?.data?.message ?? t('product_detail.waitlist_error')
  } finally {
    waitlistLoading.value = false
  }
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
    box-shadow: 0 18px 40px rgba(var(--v-theme-on-surface), 0.1);
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
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-theme-on-surface), 0.06);
    box-shadow: 0 12px 28px rgba(var(--v-theme-on-surface), 0.06);
    transition:
      transform 0.25s ease,
      box-shadow 0.25s ease;
  }
  .feature-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 36px rgba(var(--v-theme-on-surface), 0.1);
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
    color: rgba(var(--v-theme-on-surface), 0.6);
    line-height: 1.55;
    margin: 0;
  }

  .screenshots-grid {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  }
  .screenshot :deep(.v-img) {
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 14px 32px rgba(var(--v-theme-on-surface), 0.08);
  }
  .screenshot figcaption {
    text-align: center;
    font-size: 0.82rem;
    color: rgba(var(--v-theme-on-surface), 0.55);
    margin-top: 8px;
  }

  .waitlist-form {
    max-width: 560px;
    margin: 24px auto 0;
  }

  .not-found,
  .detail-loading {
    padding: 100px 0;
  }
</style>
