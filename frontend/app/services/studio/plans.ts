import studioApi from '~/services/studio/api'
import type { StudioPlan } from '~/types'

export async function getStudioPlans(): Promise<StudioPlan[]> {
  const { data } = await studioApi.get('/plans', { meta: { loader: 'skip' } })
  return (data.data ?? []).filter((p: StudioPlan) => p.is_active)
}
