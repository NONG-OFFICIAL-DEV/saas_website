import cmsApi from '@/services/cmsApi'

const NO_OVERLAY = { meta: { loader: 'skip' } }

export async function getBlogPosts() {
  const { data } = await cmsApi.get('/public/blog-posts', NO_OVERLAY)
  return data.data ?? []
}

export async function getBlogPostBySlug(slug) {
  try {
    const { data } = await cmsApi.get(`/public/blog-posts/${slug}`, NO_OVERLAY)
    return data.data
  } catch (err) {
    if (err.status === 404) return null
    throw err
  }
}
