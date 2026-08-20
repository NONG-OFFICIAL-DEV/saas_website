import cmsApi from '~/services/cmsApi'
import type { BlogPost } from '~/types'

const NO_OVERLAY = { meta: { loader: 'skip' as const } }

export async function getBlogPosts(): Promise<BlogPost[]> {
  const { data } = await cmsApi.get('/public/blog-posts', NO_OVERLAY)
  return data.data ?? []
}

export async function getBlogPostBySlug(slug: string): Promise<BlogPost | null> {
  try {
    const { data } = await cmsApi.get(`/public/blog-posts/${slug}`, NO_OVERLAY)
    return data.data
  } catch (err: any) {
    if (err.status === 404) return null
    throw err
  }
}
