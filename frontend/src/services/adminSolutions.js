import cmsApi from '@/services/cmsApi'

const NO_OVERLAY = { meta: { loader: 'skip' } }

export async function listAllSolutions() {
  const { data } = await cmsApi.get('/admin/solutions', NO_OVERLAY)
  return data.data ?? []
}

export async function getSolutionForEdit(id) {
  try {
    const { data } = await cmsApi.get(`/admin/solutions/${id}`, NO_OVERLAY)
    return data.data
  } catch (err) {
    if (err.status === 404) return null
    throw err
  }
}

export async function createSolution(payload) {
  const { data } = await cmsApi.post('/admin/solutions', payload, NO_OVERLAY)
  return data.data
}

export async function updateSolution(id, payload) {
  const { data } = await cmsApi.put(`/admin/solutions/${id}`, payload, NO_OVERLAY)
  return data.data
}

export async function deleteSolution(id) {
  await cmsApi.delete(`/admin/solutions/${id}`)
}
