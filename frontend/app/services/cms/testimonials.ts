import cmsApi from '~/services/cms/api'
import type { Testimonial } from '~/types'

const NO_OVERLAY = { meta: { loader: 'skip' as const } }

export async function getTestimonials(): Promise<Testimonial[]> {
  const { data } = await cmsApi.get('/public/testimonials', NO_OVERLAY)
  return data.data ?? []
}
