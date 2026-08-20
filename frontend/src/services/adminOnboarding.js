import cmsApi from '@/services/cmsApi'

const NO_OVERLAY = { meta: { loader: 'skip' } }

export async function listOnboardingSubmissions() {
  const { data } = await cmsApi.get('/admin/onboarding-submissions', NO_OVERLAY)
  return data.data ?? []
}

export async function deleteOnboardingSubmission(id) {
  await cmsApi.delete(`/admin/onboarding-submissions/${id}`)
}
