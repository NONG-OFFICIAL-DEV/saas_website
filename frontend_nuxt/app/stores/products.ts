import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getProducts, getProductBySlug } from '~/services/products'
import type { Product } from '~/types'

export const useProductsStore = defineStore('products', () => {
  const products = ref<Product[]>([]) // hub-grid rows
  const currentProduct = ref<Product | null>(null) // full detail row, or null if not found
  const loading = ref(false)
  const loadingProduct = ref(false)
  const error = ref<string | null>(null)

  /**
   * Load the published products list once; cached across hub/detail navigation.
   */
  async function fetchProducts() {
    if (products.value.length) return
    loading.value = true
    error.value = null
    try {
      products.value = await getProducts()
    } catch (err: any) {
      error.value = err?.message ?? 'Failed to load products'
    } finally {
      loading.value = false
    }
  }

  /**
   * Load a single product with its features/pricing/screenshots by slug.
   * Sets currentProduct to null if the slug doesn't exist or isn't published.
   */
  async function fetchProductBySlug(slug: string) {
    loadingProduct.value = true
    error.value = null
    currentProduct.value = null
    try {
      currentProduct.value = await getProductBySlug(slug)
    } catch (err: any) {
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
