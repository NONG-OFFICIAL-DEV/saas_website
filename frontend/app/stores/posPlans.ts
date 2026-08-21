// stores/posPlans.ts
//
// Live Nexstack POS plans for the marketing site's pricing display
// (components/sections/PriceSection.vue) — display-only, same convention as
// stores/studioPlans.ts. Registration itself happens on the real admin app
// (see config/productTrials.ts), not on this site.
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getSmartStorePlansApi } from '~/api/plans'
import type { PosPlan } from '~/types'

export const usePosPlansStore = defineStore('posPlans', () => {
  const plans = ref<PosPlan[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchPlans() {
    if (plans.value.length) return
    loading.value = true
    error.value = null
    try {
      const { data } = await getSmartStorePlansApi()
      plans.value = data.data ?? []
    } catch (err: any) {
      error.value = err?.response?.data?.message ?? 'Failed to load plans'
    } finally {
      loading.value = false
    }
  }

  return { plans, loading, error, fetchPlans }
})
