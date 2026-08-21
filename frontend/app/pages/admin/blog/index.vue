<template>
  <div>
    <div class="dash-header">
      <div>
        <h1 class="dash-title">Blog</h1>
        <p class="dash-sub">Manage posts shown on the public blog.</p>
      </div>
      <Button as="NuxtLink" to="/admin/blog/new">
        <Icon name="mdi-plus" size="18" />
        New post
      </Button>
    </div>

    <Alert v-if="error" variant="destructive" class="mb-4">
      <AlertDescription>{{ error }}</AlertDescription>
    </Alert>

    <InlineLoader v-if="loading" min-height="80px" />

    <div v-else-if="!posts.length" class="dash-empty">
      <Icon name="mdi-newspaper-variant-outline" size="36" />
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
          <Badge :class="p.is_published ? 'bg-success text-success-foreground' : ''" :variant="p.is_published ? undefined : 'secondary'">
            {{ p.is_published ? 'Published' : 'Draft' }}
          </Badge>
        </span>
        <span class="dash-actions">
          <Button as="NuxtLink" size="icon-sm" variant="ghost" :to="`/admin/blog/${p.id}/edit`">
            <Icon name="mdi-pencil-outline" size="16" />
          </Button>
          <Button size="icon-sm" variant="ghost" class="text-destructive hover:text-destructive" @click="confirmDelete(p)">
            <Icon name="mdi-delete-outline" size="16" />
          </Button>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  definePageMeta({ layout: 'admin' })

  import { Alert, AlertDescription } from '~/components/ui/alert'
  import { Badge } from '~/components/ui/badge'
  import { Button } from '~/components/ui/button'
  // Explicit import — Vuetify also exports its own `useDate` (date-adapter
  // composable) which Nuxt's auto-import would otherwise resolve instead.
  import { useDate } from '~/composables/useDate'
  import { listAllBlogPosts, deleteBlogPost } from '~/services/adminBlog'
  import type { BlogPost } from '~/types'

  const notify = useNotif()

  const posts = ref<BlogPost[]>([])
  const loading = ref(true)
  const error = ref<string | null>(null)
  const { formatDate } = useDate()

  async function load() {
    loading.value = true
    error.value = null
    try {
      posts.value = await listAllBlogPosts()
    } catch (err: any) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  async function confirmDelete(post: BlogPost) {
    if (!window.confirm(`Delete "${post.title}"?`)) return
    try {
      await deleteBlogPost(post.id)
      posts.value = posts.value.filter((p) => p.id !== post.id)
      notify('Blog post deleted', { type: 'success' })
    } catch (err: any) {
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
    color: color-mix(in srgb, var(--foreground) 60%, transparent);
    margin: 0;
  }

  .dash-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 60px 0;
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
  }

  .dash-table {
    border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
    border-radius: 14px;
    overflow: hidden;
  }
  .dash-row {
    display: grid;
    grid-template-columns: 1.6fr 1.2fr 1fr 0.8fr 0.8fr;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    border-bottom: 1px solid color-mix(in srgb, var(--foreground) 6%, transparent);
    font-size: 0.86rem;
  }
  .dash-row:last-child {
    border-bottom: none;
  }
  .dash-row--head {
    background: color-mix(in srgb, var(--foreground) 3%, transparent);
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
  }
  .dash-name {
    font-weight: 700;
  }
  .dash-slug {
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
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
