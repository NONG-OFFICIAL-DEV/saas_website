import { getProductBySlug } from '~/services/cms/products'
import type { ProductFaq } from '~/types'

export interface FaqGroup {
  slug: string
  name: string
  faqs: ProductFaq[]
}

// Shared by help.vue (every product's FAQs) and FaqTeaserSection.vue (a
// homepage sample) — previously help.vue had the only copy of this fetch.
// Each product's full detail (including faqs) has to be fetched
// individually since the products list endpoint doesn't embed them.
export async function fetchFaqGroups(): Promise<FaqGroup[]> {
  const productsStore = useProductsStore()
  await productsStore.fetchProducts()
  const details = await Promise.all(productsStore.products.map((p) => getProductBySlug(p.slug)))
  return details
    .filter((d) => d?.faqs?.length)
    .map((d) => ({ slug: d!.slug, name: d!.name, faqs: d!.faqs! }))
}
