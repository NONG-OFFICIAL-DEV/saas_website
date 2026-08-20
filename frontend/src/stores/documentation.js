import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getDocumentationCategories, getDocumentationArticle, searchDocumentation } from '@/services/documentation'

export const useDocumentationStore = defineStore('documentation', () => {
  const categories = ref([])
  const loading = ref(false)
  const error = ref(null)

  const currentArticle = ref(null)
  const loadingArticle = ref(false)

  const searchResults = ref([])
  const searching = ref(false)

  async function fetchCategories() {
    if (categories.value.length) return
    loading.value = true
    error.value = null
    try {
      categories.value = await getDocumentationCategories()
    } catch (err) {
      error.value = err?.message ?? 'Failed to load documentation'
    } finally {
      loading.value = false
    }
  }

  async function fetchArticleBySlug(slug) {
    loadingArticle.value = true
    currentArticle.value = null
    try {
      currentArticle.value = await getDocumentationArticle(slug)
    } finally {
      loadingArticle.value = false
    }
  }

  async function search(query) {
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
