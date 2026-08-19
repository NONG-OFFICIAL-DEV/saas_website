import cmsApi from '@/services/cmsApi'

const NO_OVERLAY = { meta: { loader: 'skip' } }

export async function getOnboardingBusinessTypes() {
  const { data } = await cmsApi.get('/public/onboarding/business-types', NO_OVERLAY)
  return data.data ?? []
}

export async function provisionOnboarding(payload) {
  const { data } = await cmsApi.post('/public/onboarding/provision', payload, NO_OVERLAY)
  return data
}
