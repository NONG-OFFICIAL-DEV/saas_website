import cmsApi from '~/services/cmsApi'
import type { Product } from '~/types'

// Vue components (ProductDetail.vue) expect the old relation key names —
// map the Laravel resource's flat keys back to those so nothing downstream
// needs to change.
function mapProduct(p: any): Product | null {
  if (!p) return p
  return {
    ...p,
    product_features: p.features ?? [],
    product_screenshots: p.screenshots ?? []
  }
}

// Both routes already render their own skeleton loader tied to the
// products store's loading state — skip the global overlay so we don't
// stack a second loading indicator on top of it.
const NO_OVERLAY = { meta: { loader: 'skip' as const } }

export async function getProducts(): Promise<Product[]> {
  const { data } = await cmsApi.get('/public/products', NO_OVERLAY)
  return data.data ?? []
}

export async function getProductBySlug(slug: string): Promise<Product | null> {
  try {
    const { data } = await cmsApi.get(`/public/products/${slug}`, NO_OVERLAY)
    return mapProduct(data.data)
  } catch (err: any) {
    if (err.status === 404) return null
    throw err
  }
}
