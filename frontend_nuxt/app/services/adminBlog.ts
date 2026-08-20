import cmsApi from '~/services/cmsApi'
import type { BlogPost } from '~/types'

const NO_OVERLAY = { meta: { loader: 'skip' as const } }

export async function listAllBlogPosts(): Promise<BlogPost[]> {
  const { data } = await cmsApi.get('/admin/blog-posts', NO_OVERLAY)
  return data.data ?? []
}

export async function getBlogPostForEdit(id: string): Promise<BlogPost | null> {
  try {
    const { data } = await cmsApi.get(`/admin/blog-posts/${id}`, NO_OVERLAY)
    return data.data
  } catch (err: any) {
    if (err.status === 404) return null
    throw err
  }
}

export async function createBlogPost(payload: Record<string, any>) {
  const { data } = await cmsApi.post('/admin/blog-posts', payload, NO_OVERLAY)
  return data.data
}

export async function updateBlogPost(id: string, payload: Record<string, any>) {
  const { data } = await cmsApi.put(`/admin/blog-posts/${id}`, payload, NO_OVERLAY)
  return data.data
}

export async function deleteBlogPost(id: string) {
  await cmsApi.delete(`/admin/blog-posts/${id}`)
}
