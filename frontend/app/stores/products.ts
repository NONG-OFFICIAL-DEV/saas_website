import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getProducts, getProductBySlug } from '~/services/cms/products'
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
  // Deliberately does NOT null out currentProduct before fetching — this
  // runs again on every slug change (see the [slug] page's useAsyncData
  // `watch`), and Vue Router reuses the same page instance for param-only
  // changes, so nulling it first would flash a loading/not-found state over
  // the still-valid previous product instead of swapping directly to the
  // new one once it arrives.
  async function fetchProductBySlug(slug: string) {
    loadingProduct.value = true
    error.value = null
    try {
      currentProduct.value = await getProductBySlug(slug)
    } catch (err: any) {
      error.value = err?.message ?? 'Failed to load product'
      currentProduct.value = null
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
