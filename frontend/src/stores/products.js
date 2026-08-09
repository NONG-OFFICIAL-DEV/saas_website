// stores/products.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getProducts, getProductBySlug } from '@/services/products'

export const useProductsStore = defineStore('products', () => {
  // ── State ─────────────────────────────────────────────────────────────
  const products = ref([]) // hub-grid rows
  const currentProduct = ref(null) // full detail row, or null if not found
  const loading = ref(false)
  const loadingProduct = ref(false)
  const error = ref(null)

  // ── Actions ───────────────────────────────────────────────────────────

  /**
   * Load the published products list once; cached across hub/detail navigation.
   */
  async function fetchProducts() {
    if (products.value.length) return
    loading.value = true
    error.value = null
    try {
      products.value = await getProducts()
    } catch (err) {
      error.value = err?.message ?? 'Failed to load products'
    } finally {
      loading.value = false
    }
  }

  /**
   * Load a single product with its features/pricing/screenshots by slug.
   * Sets currentProduct to null if the slug doesn't exist or isn't published.
   */
  async function fetchProductBySlug(slug) {
    loadingProduct.value = true
    error.value = null
    currentProduct.value = null
    try {
      currentProduct.value = await getProductBySlug(slug)
    } catch (err) {
      error.value = err?.message ?? 'Failed to load product'
    } finally {
      loadingProduct.value = false
    }
  }

  return {
    products,
    currentProduct,
    loading,
    loadingProduct,
    error,
    fetchProducts,
    fetchProductBySlug
  }
})
