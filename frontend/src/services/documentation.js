import cmsApi from '@/services/cmsApi'

const NO_OVERLAY = { meta: { loader: 'skip' } }

export async function getDocumentationCategories() {
  const { data } = await cmsApi.get('/public/documentation-categories', NO_OVERLAY)
  return data.data ?? []
}

export async function getDocumentationArticle(slug) {
  try {
    const { data } = await cmsApi.get(`/public/documentation-articles/${slug}`, NO_OVERLAY)
    return data.data
  } catch (err) {
    if (err.status === 404) return null
    throw err
  }
}

export async function searchDocumentation(q) {
  const { data } = await cmsApi.get('/public/documentation-search', { params: { q }, ...NO_OVERLAY })
  return data.data ?? []
}
