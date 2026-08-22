import cmsApi from '~/services/cms/api'
import type { DocumentationCategory, DocumentationArticle, DocumentationSearchResult } from '~/types'

const NO_OVERLAY = { meta: { loader: 'skip' as const } }

export async function getDocumentationCategories(): Promise<DocumentationCategory[]> {
  const { data } = await cmsApi.get('/public/documentation-categories', NO_OVERLAY)
  return data.data ?? []
}

export async function getDocumentationArticle(slug: string): Promise<DocumentationArticle | null> {
  try {
    const { data } = await cmsApi.get(`/public/documentation-articles/${slug}`, NO_OVERLAY)
    return data.data
  } catch (err: any) {
    if (err.status === 404) return null
    throw err
  }
}

export async function searchDocumentation(q: string): Promise<DocumentationSearchResult[]> {
  const { data } = await cmsApi.get('/public/documentation-search', { params: { q }, ...NO_OVERLAY })
  return data.data ?? []
}
