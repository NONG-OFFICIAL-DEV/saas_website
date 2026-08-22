import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getTestimonials } from '~/services/cms/testimonials'
import type { Testimonial } from '~/types'

export const useTestimonialsStore = defineStore('testimonials', () => {
  const testimonials = ref<Testimonial[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchTestimonials() {
    if (testimonials.value.length) return
    loading.value = true
    error.value = null
    try {
      testimonials.value = await getTestimonials()
    } catch (err: any) {
      error.value = err?.message ?? 'Failed to load testimonials'
    } finally {
      loading.value = false
    }
  }

  return { testimonials, loading, error, fetchTestimonials }
})
