<template>
  <div>
    <div class="dash-header">
      <div>
        <h1 class="dash-title">Documentation categories</h1>
        <p class="dash-sub">The sections articles are organized into.</p>
      </div>
      <v-btn color="primary" variant="flat" rounded="lg" prepend-icon="mdi-plus" to="/admin/documentation/categories/new">
        New category
      </v-btn>
    </div>

    <v-alert v-if="error" type="error" variant="tonal" rounded="lg" class="mb-4">{{ error }}</v-alert>

    <div v-if="loading" class="dash-loading">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <div v-else-if="!categories.length" class="dash-empty">
      <v-icon icon="mdi-book-open-outline" size="36" />
      <p>No categories yet.</p>
    </div>

    <div v-else class="dash-table">
      <div class="dash-row dash-row--head">
        <span>Name</span>
        <span>Product</span>
        <span>Parent</span>
        <span>Sort</span>
        <span>Active</span>
        <span class="dash-actions-head">Actions</span>
      </div>

      <div v-for="item in categories" :key="item.id" class="dash-row">
        <span class="dash-name"><v-icon :icon="item.icon || 'mdi-folder-outline'" size="16" class="mr-1" /> {{ item.name }}</span>
        <span>{{ item.product?.name ?? '—' }}</span>
        <span>{{ item.parent?.name ?? '—' }}</span>
        <span>{{ item.sort_order }}</span>
        <span>
          <v-chip size="x-small" variant="flat" :color="item.is_active ? 'success' : 'default'">
            {{ item.is_active ? 'Active' : 'Inactive' }}
          </v-chip>
        </span>
        <span class="dash-actions">
          <v-btn size="small" variant="text" icon="mdi-pencil-outline" :to="`/admin/documentation/categories/${item.id}/edit`" />
          <v-btn size="small" variant="text" icon="mdi-delete-outline" color="error" @click="confirmDelete(item)" />
        </span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  definePageMeta({ layout: 'admin' })

  import { listAllDocCategories, deleteDocCategory } from '~/services/adminDocumentation'
  import type { DocumentationCategory } from '~/types'

  const notify = useNotif()

  const categories = ref<DocumentationCategory[]>([])
  const loading = ref(true)
  const error = ref<string | null>(null)

  async function load() {
    loading.value = true
    error.value = null
    try {
      categories.value = await listAllDocCategories()
    } catch (err: any) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  async function confirmDelete(item: DocumentationCategory) {
    if (!window.confirm(`Delete category "${item.name}"? Its articles will need reassigning first.`)) return
    try {
      await deleteDocCategory(item.id)
      categories.value = categories.value.filter((c) => c.id !== item.id)
      notify('Category deleted', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to delete category', { type: 'error' })
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
    grid-template-columns: 1.3fr 1fr 1fr 0.6fr 0.8fr 0.8fr;
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
    display: flex;
    align-items: center;
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
