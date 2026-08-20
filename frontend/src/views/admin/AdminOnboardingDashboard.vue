<template>
  <div>
    <div class="dash-header">
      <div>
        <h1 class="dash-title">Onboarding activity</h1>
        <p class="dash-sub">Every signup submitted through the onboarding wizard, across every product.</p>
      </div>
    </div>

    <v-alert v-if="error" type="error" variant="tonal" rounded="lg" class="mb-4">
      {{ error }}
    </v-alert>

    <div v-if="loading" class="dash-loading">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <div v-else-if="!submissions.length" class="dash-empty">
      <v-icon icon="mdi-account-arrow-right-outline" size="36" />
      <p>No onboarding submissions yet.</p>
    </div>

    <div v-else class="dash-table">
      <div class="dash-row dash-row--head">
        <span>Date</span>
        <span>Product</span>
        <span>Business</span>
        <span>Owner</span>
        <span>Status</span>
        <span class="dash-actions-head">Actions</span>
      </div>

      <div v-for="item in submissions" :key="item.id" class="dash-row">
        <span>{{ formatDate(item.created_at) }}</span>
        <span>{{ productName(item.product_slug) }}</span>
        <span class="dash-name">{{ item.business_name }}</span>
        <span>
          {{ item.owner_first_name }} {{ item.owner_last_name }}
          <br />
          <span class="dash-email">{{ item.email }}</span>
        </span>
        <span>
          <v-chip size="x-small" variant="flat" :color="item.status === 'success' ? 'success' : 'error'">
            {{ item.status === 'success' ? 'Success' : 'Failed' }}
          </v-chip>
          <div v-if="item.status === 'failed' && item.error_message" class="dash-error">
            {{ item.error_message }}
          </div>
        </span>
        <span class="dash-actions">
          <v-btn size="small" variant="text" icon="mdi-delete-outline" color="error" @click="confirmDelete(item)" />
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { onMounted, ref } from 'vue'
  import { useProductsStore } from '@/stores/products'
  import { useDate } from '@/composables/useDate'
  import { listOnboardingSubmissions, deleteOnboardingSubmission } from '@/services/adminOnboarding'

  const productsStore = useProductsStore()
  const { formatDate } = useDate()

  const submissions = ref([])
  const loading = ref(true)
  const error = ref(null)

  function productName(slug) {
    return productsStore.products.find(p => p.slug === slug)?.name ?? slug
  }

  async function load() {
    loading.value = true
    error.value = null
    try {
      await productsStore.fetchProducts()
      submissions.value = await listOnboardingSubmissions()
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  async function confirmDelete(item) {
    if (!window.confirm(`Remove the submission from "${item.business_name}"?`)) return
    try {
      await deleteOnboardingSubmission(item.id)
      submissions.value = submissions.value.filter(s => s.id !== item.id)
    } catch (err) {
      error.value = err.message
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
    grid-template-columns: 0.9fr 0.9fr 1.1fr 1.3fr 1fr 0.6fr;
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
  .dash-email {
    color: rgba(var(--v-theme-on-surface), 0.55);
    font-size: 0.78rem;
  }
  .dash-error {
    margin-top: 4px;
    font-size: 0.72rem;
    color: rgba(var(--v-theme-on-surface), 0.5);
    max-width: 200px;
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
    .dash-row {
      padding: 16px 18px;
    }
  }
</style>
