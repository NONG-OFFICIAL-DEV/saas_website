// stores/productContext.js
import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'

const STORAGE_KEY = 'last_viewed_product'

export const useProductContextStore = defineStore('productContext', () => {
  const route = useRoute()
  const lastViewedSlug = ref(localStorage.getItem(STORAGE_KEY) || null)

  function setLastViewed(slug) {
    if (!slug) return
    lastViewedSlug.value = slug
    localStorage.setItem(STORAGE_KEY, slug)
  }

  // The product page currently on screen wins over whatever was viewed
  // last (e.g. browsing Studio's page overrides an earlier POS visit),
  // falling back to the last one actually viewed anywhere else on the site.
  const activeSlug = computed(
    () => (route.name === 'product-detail' ? route.params.slug : null) ?? lastViewedSlug.value
  )

  return { lastViewedSlug, activeSlug, setLastViewed }
})
