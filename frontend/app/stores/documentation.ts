import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getDocumentationCategories, getDocumentationArticle, searchDocumentation } from '~/services/cms/documentation'
import type { DocumentationCategory, DocumentationArticle, DocumentationSearchResult } from '~/types'

export const useDocumentationStore = defineStore('documentation', () => {
  const categories = ref<DocumentationCategory[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const currentArticle = ref<DocumentationArticle | null>(null)
  const loadingArticle = ref(false)

  const searchResults = ref<DocumentationSearchResult[]>([])
  const searching = ref(false)

  async function fetchCategories() {
    if (categories.value.length) return
    loading.value = true
    error.value = null
    try {
      categories.value = await getDocumentationCategories()
    } catch (err: any) {
      error.value = err?.message ?? 'Failed to load documentation'
    } finally {
      loading.value = false
    }
  }

  // Deliberately does NOT null out currentArticle before fetching — see the
  // equivalent note in stores/products.ts's fetchProductBySlug. A genuine
  // fetch error still propagates (no catch here, matching the original
  // behavior) so the caller's own error handling still clears stale state.
  async function fetchArticleBySlug(slug: string) {
    loadingArticle.value = true
    try {
      currentArticle.value = await getDocumentationArticle(slug)
    } catch (err) {
      currentArticle.value = null
      throw err
    } finally {
      loadingArticle.value = false
    }
  }

  async function search(query: string) {
    if (!query || query.trim().length < 2) {
      searchResults.value = []
      return
    }
    searching.value = true
    try {
      searchResults.value = await searchDocumentation(query.trim())
    } finally {
      searching.value = false
    }
  }

  function clearSearch() {
    searchResults.value = []
  }

  return {
    categories,
    loading,
    error,
    currentArticle,
    loadingArticle,
    searchResults,
    searching,
    fetchCategories,
    fetchArticleBySlug,
    search,
    clearSearch
  }
})
