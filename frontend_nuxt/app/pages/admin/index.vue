<template>
  <div>
    <div class="dash-header">
      <div>
        <h1 class="dash-title">Products</h1>
        <p class="dash-sub">Manage what shows up on the public Products hub.</p>
      </div>
      <v-btn
        color="primary"
        variant="flat"
        rounded="lg"
        prepend-icon="mdi-plus"
        to="/admin/products/new"
      >
        New product
      </v-btn>
    </div>

    <v-alert v-if="error" type="error" variant="tonal" rounded="lg" class="mb-4">
      {{ error }}
    </v-alert>

    <div v-if="loading" class="dash-loading">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <div v-else-if="!products.length" class="dash-empty">
      <v-icon icon="mdi-package-variant" size="36" />
      <p>No products yet.</p>
    </div>

    <div v-else class="dash-table">
      <div class="dash-row dash-row--head">
        <span>Name</span>
        <span>Slug</span>
        <span>Status</span>
        <span>Published</span>
        <span class="dash-actions-head">Actions</span>
      </div>

      <div v-for="p in products" :key="p.id" class="dash-row">
        <span class="dash-name">{{ p.name }}</span>
        <span class="dash-slug">/{{ p.slug }}</span>
        <span>
          <v-chip size="x-small" variant="tonal">{{ p.status }}</v-chip>
        </span>
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
            :to="`/admin/products/${p.id}/edit`"
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

<script setup lang="ts">
  definePageMeta({ layout: 'admin' })

  import { listAllProducts, deleteProduct } from '~/services/adminProducts'
  import type { Product } from '~/types'

  const notify = useNotif()
  const products = ref<Product[]>([])
  const loading = ref(true)
  const error = ref<string | null>(null)

  async function load() {
    loading.value = true
    error.value = null
    try {
      products.value = await listAllProducts()
    } catch (err: any) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  async function confirmDelete(product: Product) {
    if (!window.confirm(`Delete "${product.name}"? This also deletes its features, pricing tiers, and screenshots.`)) {
      return
    }
    try {
      await deleteProduct(product.id)
      products.value = products.value.filter((p) => p.id !== product.id)
      notify('Product deleted', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to delete product', { type: 'error' })
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
    grid-template-columns: 1.6fr 1.2fr 0.9fr 0.9fr 0.8fr;
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
