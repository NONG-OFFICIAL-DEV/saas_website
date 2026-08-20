import cmsApi from '~/services/cmsApi'
import type { OnboardingSubmission } from '~/types'

const NO_OVERLAY = { meta: { loader: 'skip' as const } }

export async function listOnboardingSubmissions(): Promise<OnboardingSubmission[]> {
  const { data } = await cmsApi.get('/admin/onboarding-submissions', NO_OVERLAY)
  return data.data ?? []
}

export async function deleteOnboardingSubmission(id: string) {
  await cmsApi.delete(`/admin/onboarding-submissions/${id}`)
}
