// stores/solutions.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getSolutions, getSolutionBySlug } from '@/services/solutions'

export const useSolutionsStore = defineStore('solutions', () => {
  const solutions = ref([])
  const currentSolution = ref(null)
  const loading = ref(false)
  const loadingSolution = ref(false)
  const error = ref(null)

  async function fetchSolutions() {
    if (solutions.value.length) return
    loading.value = true
    error.value = null
    try {
      solutions.value = await getSolutions()
    } catch (err) {
      error.value = err?.message ?? 'Failed to load solutions'
    } finally {
      loading.value = false
    }
  }

  async function fetchSolutionBySlug(slug) {
    loadingSolution.value = true
    error.value = null
    currentSolution.value = null
    try {
      currentSolution.value = await getSolutionBySlug(slug)
    } catch (err) {
      error.value = err?.message ?? 'Failed to load solution'
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
