import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getSolutions, getSolutionBySlug } from '~/services/solutions'
import type { Solution } from '~/types'

export const useSolutionsStore = defineStore('solutions', () => {
  const solutions = ref<Solution[]>([])
  const currentSolution = ref<Solution | null>(null)
  const loading = ref(false)
  const loadingSolution = ref(false)
  const error = ref<string | null>(null)

  async function fetchSolutions() {
    if (solutions.value.length) return
    loading.value = true
    error.value = null
    try {
      solutions.value = await getSolutions()
    } catch (err: any) {
      error.value = err?.message ?? 'Failed to load solutions'
    } finally {
      loading.value = false
    }
  }

  // Deliberately does NOT null out currentSolution before fetching — see
  // the equivalent note in stores/products.ts's fetchProductBySlug.
  async function fetchSolutionBySlug(slug: string) {
    loadingSolution.value = true
    error.value = null
    try {
      currentSolution.value = await getSolutionBySlug(slug)
    } catch (err: any) {
      error.value = err?.message ?? 'Failed to load solution'
      currentSolution.value = null
    } finally {
      loadingSolution.value = false
    }
  }

  return {
    solutions,
    currentSolution,
    loading,
    loadingSolution,
    error,
    fetchSolutions,
    fetchSolutionBySlug
  }
})
