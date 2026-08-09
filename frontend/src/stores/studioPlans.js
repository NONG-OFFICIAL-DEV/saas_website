// stores/studioPlans.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getStudioPlans } from '@/services/studioPlans'

export const useStudioPlansStore = defineStore('studioPlans', () => {
  const plans = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchPlans() {
    if (plans.value.length) return
    loading.value = true
    error.value = null
    try {
      plans.value = await getStudioPlans()
    } catch (err) {
      error.value = err?.message ?? 'Failed to load plans'
    } finally {
      loading.value = false
    }
  }

  return { plans, loading, error, fetchPlans }
})
