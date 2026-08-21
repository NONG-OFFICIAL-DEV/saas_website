<template>
  <div>
    <div class="dash-header">
      <div>
        <h1 class="dash-title">Documentation articles</h1>
        <p class="dash-sub">Every article across every category.</p>
      </div>
      <Button as="NuxtLink" to="/admin/documentation/articles/new">
        <Icon name="mdi-plus" size="18" />
        New article
      </Button>
    </div>

    <Alert v-if="error" variant="destructive" class="mb-4">
      <AlertDescription>{{ error }}</AlertDescription>
    </Alert>

    <InlineLoader v-if="loading" min-height="80px" />

    <div v-else-if="!articles.length" class="dash-empty">
      <Icon name="mdi-file-document-outline" size="36" />
      <p>No articles yet.</p>
    </div>

    <div v-else class="dash-table">
      <div class="dash-row dash-row--head">
        <span>Title</span>
        <span>Category</span>
        <span>Product</span>
        <span>Status</span>
        <span>Sort</span>
        <span class="dash-actions-head">Actions</span>
      </div>

      <div v-for="item in articles" :key="item.id" class="dash-row">
        <span class="dash-name">{{ item.title }}</span>
        <span>{{ item.category?.name ?? '—' }}</span>
        <span>{{ item.product?.name ?? '—' }}</span>
        <span>
          <Badge :class="statusClass(item.status)" :variant="statusVariant(item.status)">{{ item.status }}</Badge>
        </span>
        <span>{{ item.sort_order }}</span>
        <span class="dash-actions">
          <Button as="NuxtLink" size="icon-sm" variant="ghost" :to="`/admin/documentation/articles/${item.id}/edit`">
            <Icon name="mdi-pencil-outline" size="16" />
          </Button>
          <Button size="icon-sm" variant="ghost" class="text-destructive hover:text-destructive" @click="confirmDelete(item)">
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
  import { listAllDocArticles, deleteDocArticle } from '~/services/adminDocumentation'
  import type { DocumentationArticle } from '~/types'

  const notify = useNotif()

  const articles = ref<DocumentationArticle[]>([])
  const loading = ref(true)
  const error = ref<string | null>(null)

  function statusClass(status: string) {
    if (status === 'published') return 'bg-success text-success-foreground'
    if (status === 'archived') return ''
    return 'bg-warning text-warning-foreground'
  }
  function statusVariant(status: string) {
    return status === 'archived' ? 'secondary' : undefined
  }

  async function load() {
    loading.value = true
    error.value = null
    try {
      articles.value = await listAllDocArticles()
    } catch (err: any) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  async function confirmDelete(item: DocumentationArticle) {
    if (!window.confirm(`Delete article "${item.title}"?`)) return
    try {
      await deleteDocArticle(item.id)
      articles.value = articles.value.filter((a) => a.id !== item.id)
      notify('Article deleted', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to delete article', { type: 'error' })
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
    grid-template-columns: 1.6fr 1fr 1fr 0.8fr 0.6fr 0.8fr;
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
  .dash-actions,
  .dash-actions-head {
    display: flex;
    gap: 4px;
  }
  @media (max-width: 900px) {
    .dash-row {
      grid-template-columns: 1fr;
      gap: 6px;
    }
    .dash-row--head {
      display: none;
    }
  }
</style>
