import cmsApi from '~/services/cms/api'
import type { Testimonial } from '~/types'

const NO_OVERLAY = { meta: { loader: 'skip' as const } }

export async function listAllTestimonials(): Promise<Testimonial[]> {
  const { data } = await cmsApi.get('/admin/testimonials', NO_OVERLAY)
  return data.data ?? []
}

export async function getTestimonialForEdit(id: string): Promise<Testimonial | null> {
  try {
    const { data } = await cmsApi.get(`/admin/testimonials/${id}`, NO_OVERLAY)
    return data.data
  } catch (err: any) {
    if (err.status === 404) return null
    throw err
  }
}

export async function createTestimonial(payload: Record<string, any>) {
  const { data } = await cmsApi.post('/admin/testimonials', payload, NO_OVERLAY)
  return data.data
}

export async function updateTestimonial(id: string, payload: Record<string, any>) {
  const { data } = await cmsApi.put(`/admin/testimonials/${id}`, payload, NO_OVERLAY)
  return data.data
}

export async function deleteTestimonial(id: string) {
  await cmsApi.delete(`/admin/testimonials/${id}`)
}
