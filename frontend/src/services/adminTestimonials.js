import cmsApi from '@/services/cmsApi'

const NO_OVERLAY = { meta: { loader: 'skip' } }

export async function listAllTestimonials() {
  const { data } = await cmsApi.get('/admin/testimonials', NO_OVERLAY)
  return data.data ?? []
}

export async function getTestimonialForEdit(id) {
  try {
    const { data } = await cmsApi.get(`/admin/testimonials/${id}`, NO_OVERLAY)
    return data.data
  } catch (err) {
    if (err.status === 404) return null
    throw err
  }
}

export async function createTestimonial(payload) {
  const { data } = await cmsApi.post('/admin/testimonials', payload, NO_OVERLAY)
  return data.data
}

export async function updateTestimonial(id, payload) {
  const { data } = await cmsApi.put(`/admin/testimonials/${id}`, payload, NO_OVERLAY)
  return data.data
}

export async function deleteTestimonial(id) {
  await cmsApi.delete(`/admin/testimonials/${id}`)
}
