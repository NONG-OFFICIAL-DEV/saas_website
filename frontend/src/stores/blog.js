// stores/blog.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getBlogPosts, getBlogPostBySlug } from '@/services/blog'

export const useBlogStore = defineStore('blog', () => {
  const posts = ref([])
  const currentPost = ref(null)
  const loading = ref(false)
  const loadingPost = ref(false)
  const error = ref(null)

  async function fetchPosts() {
    if (posts.value.length) return
    loading.value = true
    error.value = null
    try {
      posts.value = await getBlogPosts()
    } catch (err) {
      error.value = err?.message ?? 'Failed to load posts'
    } finally {
      loading.value = false
    }
  }

  async function fetchPostBySlug(slug) {
    loadingPost.value = true
    error.value = null
    currentPost.value = null
    try {
      currentPost.value = await getBlogPostBySlug(slug)
    } catch (err) {
      error.value = err?.message ?? 'Failed to load post'
    } finally {
      loadingPost.value = false
    }
  }

  return { posts, currentPost, loading, loadingPost, error, fetchPosts, fetchPostBySlug }
})
