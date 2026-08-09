import cmsApi from '@/services/cmsApi'

// Calls whose caller already renders its own loading/saving spinner — skip
// the global overlay so it doesn't stack on top of those. Mutations with no
// local indicator today (delete, and the blur-triggered nested feature/
// screenshot saves) intentionally keep the default overlay.
const NO_OVERLAY = { meta: { loader: 'skip' } }

function mapProduct(p) {
  if (!p) return p
  return {
    ...p,
    product_features: p.features ?? [],
    product_screenshots: p.screenshots ?? []
  }
}

// ── Products ────────────────────────────────────────────────────────────

export async function listAllProducts() {
  const { data } = await cmsApi.get('/admin/products', NO_OVERLAY)
  return data.data ?? []
}

export async function getProductForEdit(id) {
  try {
    const { data } = await cmsApi.get(`/admin/products/${id}`, NO_OVERLAY)
    return mapProduct(data.data)
  } catch (err) {
    if (err.status === 404) return null
    throw err
  }
}

export async function createProduct(payload) {
  const { data } = await cmsApi.post('/admin/products', payload, NO_OVERLAY)
  return mapProduct(data.data)
}

export async function updateProduct(id, payload) {
  const { data } = await cmsApi.put(`/admin/products/${id}`, payload, NO_OVERLAY)
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
    headers: { 'Content-Type': 'multipart/form-data' },
    meta: { loader: 'skip' }
  })
  return data.data.url
}
