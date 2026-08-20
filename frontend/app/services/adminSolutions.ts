import cmsApi from '~/services/cmsApi'
import type { Solution } from '~/types'

const NO_OVERLAY = { meta: { loader: 'skip' as const } }

export async function listAllSolutions(): Promise<Solution[]> {
  const { data } = await cmsApi.get('/admin/solutions', NO_OVERLAY)
  return data.data ?? []
}

export async function getSolutionForEdit(id: string): Promise<Solution | null> {
  try {
    const { data } = await cmsApi.get(`/admin/solutions/${id}`, NO_OVERLAY)
    return data.data
  } catch (err: any) {
    if (err.status === 404) return null
    throw err
  }
}

export async function createSolution(payload: Record<string, any>) {
  const { data } = await cmsApi.post('/admin/solutions', payload, NO_OVERLAY)
  return data.data
}

export async function updateSolution(id: string, payload: Record<string, any>) {
  const { data } = await cmsApi.put(`/admin/solutions/${id}`, payload, NO_OVERLAY)
  return data.data
}

export async function deleteSolution(id: string) {
  await cmsApi.delete(`/admin/solutions/${id}`)
}
