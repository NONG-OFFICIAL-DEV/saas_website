<template>
  <FeatureShowcaseSection v-if="showcaseSection" :section="showcaseSection" />
</template>

<script setup lang="ts">
import { getProductBySlug } from '~/services/cms/products'
import type { ProductFeatureSection } from '~/types'

// Reuses the real, already-interactive FeatureShowcaseSection (tab selector
// + live CMS content, see products/[slug].vue) on the homepage instead of
// inventing a new "product demo" pattern. Tries the flagship product first,
// then falls back to the first live product with real showcase content —
// renders nothing if no product has authored a feature-showcase section
// yet, rather than fabricating placeholder screenshots.
const productsStore = useProductsStore()

const { data: showcaseSection } = await useAsyncData<ProductFeatureSection | null>('home-product-showcase', async () => {
  await productsStore.fetchProducts()
  const candidates = [
    'nexstack-pos',
    ...productsStore.products.filter((p) => p.status === 'live').map((p) => p.slug)
  ]
  const tried = new Set<string>()
  for (const slug of candidates) {
    if (tried.has(slug)) continue
    tried.add(slug)
    const product = await getProductBySlug(slug)
    const section = product?.feature_sections?.find((s) => s.is_active && s.type === 'feature-showcase')
    if (section) return section
  }
  return null
})
</script>
