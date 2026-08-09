import cmsApi from '@/services/cmsApi'

function mapProduct(p) {
  if (!p) return p
  return {
    ...p,
    product_features: p.features ?? [],
    product_pricing_tiers: p.pricing_tiers ?? [],
    product_screenshots: p.screenshots ?? []
  }
}

// ── Products ────────────────────────────────────────────────────────────

export async function listAllProducts() {
  const { data } = await cmsApi.get('/admin/products')
  return data.data ?? []
}

export async function getProductForEdit(id) {
  try {
    const { data } = await cmsApi.get(`/admin/products/${id}`)
    return mapProduct(data.data)
  } catch (err) {
    if (err.status === 404) return null
    throw err
  }
}

export async function createProduct(payload) {
  const { data } = await cmsApi.post('/admin/products', payload)
  return mapProduct(data.data)
}

export async function updateProduct(id, payload) {
  const { data } = await cmsApi.put(`/admin/products/${id}`, payload)
  return mapProduct(data.data)
}

export async function deleteProduct(id) {
  await cmsApi.delete(`/admin/products/${id}`)
}

// ── Features ────────────────────────────────────────────────────────────

export async function createFeature(productId, payload) {
  const { data } = await cmsApi.post(`/admin/products/${productId}/features`, payload)
  return data.data
}

export async function updateFeature(id, payload) {
  await cmsApi.put(`/admin/features/${id}`, payload)
}

export async function deleteFeature(id) {
  await cmsApi.delete(`/admin/features/${id}`)
}

// ── Pricing tiers ───────────────────────────────────────────────────────

export async function createPricingTier(productId, payload) {
  const { data } = await cmsApi.post(`/admin/products/${productId}/pricing-tiers`, payload)
  return data.data
}

export async function updatePricingTier(id, payload) {
  await cmsApi.put(`/admin/pricing-tiers/${id}`, payload)
}

export async function deletePricingTier(id) {
  await cmsApi.delete(`/admin/pricing-tiers/${id}`)
}

// ── Screenshots ─────────────────────────────────────────────────────────

export async function createScreenshot(productId, payload) {
  const { data } = await cmsApi.post(`/admin/products/${productId}/screenshots`, payload)
  return data.data
}

export async function updateScreenshot(id, payload) {
  await cmsApi.put(`/admin/screenshots/${id}`, payload)
}

export async function deleteScreenshot(id) {
  await cmsApi.delete(`/admin/screenshots/${id}`)
}

// ── Media ───────────────────────────────────────────────────────────────

export async function uploadProductMedia(file) {
  const form = new FormData()
  form.append('file', file)
  const { data } = await cmsApi.post('/admin/media', form, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
  return data.data.url
}
