import cmsApi from '@/services/cmsApi'

const NO_OVERLAY = { meta: { loader: 'skip' } }

export async function listAllBlogPosts() {
  const { data } = await cmsApi.get('/admin/blog-posts', NO_OVERLAY)
  return data.data ?? []
}

export async function getBlogPostForEdit(id) {
  try {
    const { data } = await cmsApi.get(`/admin/blog-posts/${id}`, NO_OVERLAY)
    return data.data
  } catch (err) {
    if (err.status === 404) return null
    throw err
  }
}

export async function createBlogPost(payload) {
  const { data } = await cmsApi.post('/admin/blog-posts', payload, NO_OVERLAY)
  return data.data
}

export async function updateBlogPost(id, payload) {
  const { data } = await cmsApi.put(`/admin/blog-posts/${id}`, payload, NO_OVERLAY)
  return data.data
}

export async function deleteBlogPost(id) {
  await cmsApi.delete(`/admin/blog-posts/${id}`)
}
