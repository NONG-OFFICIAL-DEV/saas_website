import cmsApi from '@/services/cmsApi'

// Vue components (ProductDetail.vue) expect the old relation key names —
// map the Laravel resource's flat keys back to those so nothing downstream
// needs to change.
function mapProduct(p) {
  if (!p) return p
  return {
    ...p,
    product_features: p.features ?? [],
    product_pricing_tiers: p.pricing_tiers ?? [],
    product_screenshots: p.screenshots ?? []
  }
}

export async function getProducts() {
  const { data } = await cmsApi.get('/public/products')
  return data.data ?? []
}

export async function getProductBySlug(slug) {
  try {
    const { data } = await cmsApi.get(`/public/products/${slug}`)
    return mapProduct(data.data)
  } catch (err) {
    if (err.status === 404) return null
    throw err
  }
}
