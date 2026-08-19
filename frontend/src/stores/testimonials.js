// stores/testimonials.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getTestimonials } from '@/services/testimonials'

export const useTestimonialsStore = defineStore('testimonials', () => {
  const testimonials = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchTestimonials() {
    if (testimonials.value.length) return
    loading.value = true
    error.value = null
    try {
      testimonials.value = await getTestimonials()
    } catch (err) {
      error.value = err?.message ?? 'Failed to load testimonials'
    } finally {
      loading.value = false
    }
  }

  return { testimonials, loading, error, fetchTestimonials }
})
