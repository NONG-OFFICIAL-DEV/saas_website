import cmsApi from '@/services/cmsApi'

const NO_OVERLAY = { meta: { loader: 'skip' } }

export async function getTestimonials() {
  const { data } = await cmsApi.get('/public/testimonials', NO_OVERLAY)
  return data.data ?? []
}
