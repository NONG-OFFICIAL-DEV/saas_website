import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getBlogPosts, getBlogPostBySlug } from '~/services/blog'
import type { BlogPost } from '~/types'

export const useBlogStore = defineStore('blog', () => {
  const posts = ref<BlogPost[]>([])
  const currentPost = ref<BlogPost | null>(null)
  const loading = ref(false)
  const loadingPost = ref(false)
  const error = ref<string | null>(null)

  async function fetchPosts() {
    if (posts.value.length) return
    loading.value = true
    error.value = null
    try {
      posts.value = await getBlogPosts()
    } catch (err: any) {
      error.value = err?.message ?? 'Failed to load posts'
    } finally {
      loading.value = false
    }
  }

  // Deliberately does NOT null out currentPost before fetching — see the
  // equivalent note in stores/products.ts's fetchProductBySlug.
  async function fetchPostBySlug(slug: string) {
    loadingPost.value = true
    error.value = null
    try {
      currentPost.value = await getBlogPostBySlug(slug)
    } catch (err: any) {
      error.value = err?.message ?? 'Failed to load post'
      currentPost.value = null
    } finally {
      loadingPost.value = false
    }
  }

  return { posts, currentPost, loading, loadingPost, error, fetchPosts, fetchPostBySlug }
})
