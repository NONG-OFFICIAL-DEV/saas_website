import cmsApi from '@/services/cmsApi'

const NO_OVERLAY = { meta: { loader: 'skip' } }

export async function getSolutions() {
  const { data } = await cmsApi.get('/public/solutions', NO_OVERLAY)
  return data.data ?? []
}

export async function getSolutionBySlug(slug) {
  try {
    const { data } = await cmsApi.get(`/public/solutions/${slug}`, NO_OVERLAY)
    return data.data
  } catch (err) {
    if (err.status === 404) return null
    throw err
  }
}
