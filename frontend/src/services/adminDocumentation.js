import cmsApi from '@/services/cmsApi'

const NO_OVERLAY = { meta: { loader: 'skip' } }

// ── Categories ──────────────────────────────────────────────────────────

export async function listAllDocCategories() {
  const { data } = await cmsApi.get('/admin/documentation-categories', NO_OVERLAY)
  return data.data ?? []
}

export async function getDocCategoryForEdit(id) {
  try {
    const { data } = await cmsApi.get(`/admin/documentation-categories/${id}`, NO_OVERLAY)
    return data.data
  } catch (err) {
    if (err.status === 404) return null
    throw err
  }
}

export async function createDocCategory(payload) {
  const { data } = await cmsApi.post('/admin/documentation-categories', payload, NO_OVERLAY)
  return data.data
}

export async function updateDocCategory(id, payload) {
  const { data } = await cmsApi.put(`/admin/documentation-categories/${id}`, payload, NO_OVERLAY)
  return data.data
}

export async function deleteDocCategory(id) {
  await cmsApi.delete(`/admin/documentation-categories/${id}`)
}

// ── Articles ────────────────────────────────────────────────────────────

export async function listAllDocArticles() {
  const { data } = await cmsApi.get('/admin/documentation-articles', NO_OVERLAY)
  return data.data ?? []
}

export async function getDocArticleForEdit(id) {
  try {
    const { data } = await cmsApi.get(`/admin/documentation-articles/${id}`, NO_OVERLAY)
    return data.data
  } catch (err) {
    if (err.status === 404) return null
    throw err
  }
}

export async function createDocArticle(payload) {
  const { data } = await cmsApi.post('/admin/documentation-articles', payload, NO_OVERLAY)
  return data.data
}

export async function updateDocArticle(id, payload) {
  const { data } = await cmsApi.put(`/admin/documentation-articles/${id}`, payload, NO_OVERLAY)
  return data.data
}

export async function deleteDocArticle(id) {
  await cmsApi.delete(`/admin/documentation-articles/${id}`)
}
