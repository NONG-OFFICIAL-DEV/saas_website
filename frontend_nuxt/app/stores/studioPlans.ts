import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getStudioPlans } from '~/services/studioPlans'
import type { StudioPlan } from '~/types'

export const useStudioPlansStore = defineStore('studioPlans', () => {
  const plans = ref<StudioPlan[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchPlans() {
    if (plans.value.length) return
    loading.value = true
    error.value = null
    try {
      plans.value = await getStudioPlans()
    } catch (err: any) {
      error.value = err?.message ?? 'Failed to load plans'
    } finally {
      loading.value = false
    }
  }

  return { plans, loading, error, fetchPlans }
})
