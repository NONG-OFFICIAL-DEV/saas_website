import studioApi from '@/services/studioApi'

export async function getStudioPlans() {
  const { data } = await studioApi.get('/plans', { meta: { loader: 'skip' } })
  return (data.data ?? []).filter(p => p.is_active)
}
