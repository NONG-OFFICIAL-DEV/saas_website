import cmsApi from '~/services/cmsApi'
import type { Solution } from '~/types'

const NO_OVERLAY = { meta: { loader: 'skip' as const } }

export async function getSolutions(): Promise<Solution[]> {
  const { data } = await cmsApi.get('/public/solutions', NO_OVERLAY)
  return data.data ?? []
}

export async function getSolutionBySlug(slug: string): Promise<Solution | null> {
  try {
    const { data } = await cmsApi.get(`/public/solutions/${slug}`, NO_OVERLAY)
    return data.data
  } catch (err: any) {
    if (err.status === 404) return null
    throw err
  }
}
