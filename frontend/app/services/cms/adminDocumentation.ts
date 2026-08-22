import cmsApi from '~/services/cms/api'
import type { DocumentationCategory, DocumentationArticle } from '~/types'

const NO_OVERLAY = { meta: { loader: 'skip' as const } }

// ── Categories ──────────────────────────────────────────────────────────

export async function listAllDocCategories(): Promise<DocumentationCategory[]> {
  const { data } = await cmsApi.get('/admin/documentation-categories', NO_OVERLAY)
  return data.data ?? []
}

export async function getDocCategoryForEdit(id: string): Promise<DocumentationCategory | null> {
  try {
    const { data } = await cmsApi.get(`/admin/documentation-categories/${id}`, NO_OVERLAY)
    return data.data
  } catch (err: any) {
    if (err.status === 404) return null
    throw err
  }
}

export async function createDocCategory(payload: Record<string, any>) {
  const { data } = await cmsApi.post('/admin/documentation-categories', payload, NO_OVERLAY)
  return data.data
}

export async function updateDocCategory(id: string, payload: Record<string, any>) {
  const { data } = await cmsApi.put(`/admin/documentation-categories/${id}`, payload, NO_OVERLAY)
  return data.data
}

export async function deleteDocCategory(id: string) {
  await cmsApi.delete(`/admin/documentation-categories/${id}`)
}

// ── Articles ────────────────────────────────────────────────────────────

export async function listAllDocArticles(): Promise<DocumentationArticle[]> {
  const { data } = await cmsApi.get('/admin/documentation-articles', NO_OVERLAY)
  return data.data ?? []
}

export async function getDocArticleForEdit(id: string): Promise<DocumentationArticle | null> {
  try {
    const { data } = await cmsApi.get(`/admin/documentation-articles/${id}`, NO_OVERLAY)
    return data.data
  } catch (err: any) {
    if (err.status === 404) return null
    throw err
  }
}

export async function createDocArticle(payload: Record<string, any>) {
  const { data } = await cmsApi.post('/admin/documentation-articles', payload, NO_OVERLAY)
  return data.data
}

export async function updateDocArticle(id: string, payload: Record<string, any>) {
  const { data } = await cmsApi.put(`/admin/documentation-articles/${id}`, payload, NO_OVERLAY)
  return data.data
}

export async function deleteDocArticle(id: string) {
  await cmsApi.delete(`/admin/documentation-articles/${id}`)
}
