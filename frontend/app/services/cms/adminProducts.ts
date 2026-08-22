import cmsApi from '~/services/cms/api'
import type { Product } from '~/types'

// Calls whose caller already renders its own loading/saving spinner — skip
// the global overlay so it doesn't stack on top of those. Mutations with no
// local indicator today (delete, and the blur-triggered nested feature/
// screenshot saves) intentionally keep the default overlay.
const NO_OVERLAY = { meta: { loader: 'skip' as const } }

function mapProduct(p: any): Product | null {
  if (!p) return p
  return {
    ...p,
    product_features: p.features ?? [],
    product_screenshots: p.screenshots ?? []
  }
}

// ── Products ────────────────────────────────────────────────────────────

export async function listAllProducts(): Promise<Product[]> {
  const { data } = await cmsApi.get('/admin/products', NO_OVERLAY)
  return data.data ?? []
}

export async function getProductForEdit(id: string): Promise<Product | null> {
  try {
    const { data } = await cmsApi.get(`/admin/products/${id}`, NO_OVERLAY)
    return mapProduct(data.data)
  } catch (err: any) {
    if (err.status === 404) return null
    throw err
  }
}

export async function createProduct(payload: Record<string, any>) {
  const { data } = await cmsApi.post('/admin/products', payload, NO_OVERLAY)
  return mapProduct(data.data)
}

export async function updateProduct(id: string, payload: Record<string, any>) {
  const { data } = await cmsApi.put(`/admin/products/${id}`, payload, NO_OVERLAY)
  return mapProduct(data.data)
}

export async function deleteProduct(id: string) {
  await cmsApi.delete(`/admin/products/${id}`)
}

// ── Features ────────────────────────────────────────────────────────────

export async function createFeature(productId: string, payload: Record<string, any>) {
  const { data } = await cmsApi.post(`/admin/products/${productId}/features`, payload)
  return data.data
}

export async function updateFeature(id: string, payload: Record<string, any>) {
  await cmsApi.put(`/admin/features/${id}`, payload)
}

export async function deleteFeature(id: string) {
  await cmsApi.delete(`/admin/features/${id}`)
}

// ── Screenshots ─────────────────────────────────────────────────────────

export async function createScreenshot(productId: string, payload: Record<string, any>) {
  const { data } = await cmsApi.post(`/admin/products/${productId}/screenshots`, payload)
  return data.data
}

export async function updateScreenshot(id: string, payload: Record<string, any>) {
  await cmsApi.put(`/admin/screenshots/${id}`, payload)
}

export async function deleteScreenshot(id: string) {
  await cmsApi.delete(`/admin/screenshots/${id}`)
}

// ── FAQs ────────────────────────────────────────────────────────────────

export async function createFaq(productId: string, payload: Record<string, any>) {
  const { data } = await cmsApi.post(`/admin/products/${productId}/faqs`, payload)
  return data.data
}

export async function updateFaq(id: string, payload: Record<string, any>) {
  await cmsApi.put(`/admin/faqs/${id}`, payload)
}

export async function deleteFaq(id: string) {
  await cmsApi.delete(`/admin/faqs/${id}`)
}

// ── Media ───────────────────────────────────────────────────────────────

export async function uploadProductMedia(file: File): Promise<string> {
  const form = new FormData()
  form.append('file', file)
  const { data } = await cmsApi.post('/admin/media', form, {
    headers: { 'Content-Type': 'multipart/form-data' },
    meta: { loader: 'skip' }
  })
  return data.data.url
}
