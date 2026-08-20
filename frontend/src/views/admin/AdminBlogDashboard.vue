<template>
  <div>
    <div class="dash-header">
      <div>
        <h1 class="dash-title">Blog</h1>
        <p class="dash-sub">Manage posts shown on the public blog.</p>
      </div>
      <v-btn
        color="primary"
        variant="flat"
        rounded="lg"
        prepend-icon="mdi-plus"
        :to="{ name: 'admin-blog-new' }"
      >
        New post
      </v-btn>
    </div>

    <v-alert v-if="error" type="error" variant="tonal" rounded="lg" class="mb-4">
      {{ error }}
    </v-alert>

    <div v-if="loading" class="dash-loading">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <div v-else-if="!posts.length" class="dash-empty">
      <v-icon icon="mdi-newspaper-variant-outline" size="36" />
      <p>No posts yet.</p>
    </div>

    <div v-else class="dash-table">
      <div class="dash-row dash-row--head">
        <span>Title</span>
        <span>Slug</span>
        <span>Published at</span>
        <span>Status</span>
        <span class="dash-actions-head">Actions</span>
      </div>

      <div v-for="p in posts" :key="p.id" class="dash-row">
        <span class="dash-name">{{ p.title }}</span>
        <span class="dash-slug">/{{ p.slug }}</span>
        <span>{{ p.published_at ? formatDate(p.published_at) : '—' }}</span>
        <span>
          <v-chip
            size="x-small"
            variant="flat"
            :color="p.is_published ? 'success' : 'default'"
          >
            {{ p.is_published ? 'Published' : 'Draft' }}
          </v-chip>
        </span>
        <span class="dash-actions">
          <v-btn
            size="small"
            variant="text"
            icon="mdi-pencil-outline"
            :to="{ name: 'admin-blog-edit', params: { id: p.id } }"
          />
          <v-btn
            size="small"
            variant="text"
            icon="mdi-delete-outline"
            color="error"
            @click="confirmDelete(p)"
          />
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { onMounted, ref } from 'vue'
  import { listAllBlogPosts, deleteBlogPost } from '@/services/adminBlog'
  import { useDate } from '@/composables/useDate'
  import { useNotif } from '@/composables/useNotif'

  const notify = useNotif()

  const posts = ref([])
  const loading = ref(true)
  const error = ref(null)
  const { formatDate } = useDate()

  async function load() {
    loading.value = true
    error.value = null
    try {
      posts.value = await listAllBlogPosts()
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  async function confirmDelete(post) {
    if (!window.confirm(`Delete "${post.title}"?`)) return
    try {
      await deleteBlogPost(post.id)
      posts.value = posts.value.filter(p => p.id !== post.id)
      notify('Blog post deleted', { type: 'success' })
    } catch (err) {
      notify(err.message || 'Failed to delete blog post', { type: 'error' })
    }
  }

  onMounted(load)
</script>

<style scoped>
  .dash-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 28px;
    flex-wrap: wrap;
  }
  .dash-title {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0 0 4px;
  }
  .dash-sub {
    font-size: 0.86rem;
    color: rgba(var(--v-theme-on-surface), 0.6);
    margin: 0;
  }

  .dash-loading,
  .dash-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 60px 0;
    color: rgba(var(--v-theme-on-surface), 0.5);
  }

  .dash-table {
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
    border-radius: 14px;
    overflow: hidden;
  }
  .dash-row {
    display: grid;
    grid-template-columns: 1.6fr 1.2fr 1fr 0.8fr 0.8fr;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.06);
    font-size: 0.86rem;
  }
  .dash-row:last-child {
    border-bottom: none;
  }
  .dash-row--head {
    background: rgba(var(--v-theme-on-surface), 0.03);
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: rgba(var(--v-theme-on-surface), 0.5);
  }
  .dash-name {
    font-weight: 700;
  }
  .dash-slug {
    color: rgba(var(--v-theme-on-surface), 0.55);
    font-family: monospace;
  }
  .dash-actions,
  .dash-actions-head {
    display: flex;
    gap: 4px;
  }

  @media (max-width: 700px) {
    .dash-row {
      grid-template-columns: 1fr;
      gap: 6px;
    }
    .dash-row--head {
      display: none;
    }
    .dash-row {
      padding: 16px 18px;
      position: relative;
    }
  }
</style>
