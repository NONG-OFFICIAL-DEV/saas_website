// stores/posPlans.js
//
// Live Nexstack POS plans for the marketing site's pricing display
// (components/sections/PriceSection.vue) — display-only, same convention as
// stores/studioPlans.js. Registration itself happens on the real admin app
// (see config/productTrials.js), not on this site.
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getAllPlansApi } from '@/api/plans'

export const usePosPlansStore = defineStore('posPlans', () => {
  const plans = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchPlans() {
    if (plans.value.length) return
    loading.value = true
    error.value = null
    try {
      const { data } = await getAllPlansApi()
      plans.value = data.data ?? []
    } catch (err) {
      error.value = err?.response?.data?.message ?? 'Failed to load plans'
    } finally {
      loading.value = false
    }
  }

  return { plans, loading, error, fetchPlans }
})
