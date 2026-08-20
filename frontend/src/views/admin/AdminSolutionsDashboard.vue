<template>
  <div>
    <div class="dash-header">
      <div>
        <h1 class="dash-title">Solutions</h1>
        <p class="dash-sub">Manage the business-type groupings shown on the public Solutions hub.</p>
      </div>
      <v-btn
        color="primary"
        variant="flat"
        rounded="lg"
        prepend-icon="mdi-plus"
        :to="{ name: 'admin-solution-new' }"
      >
        New solution
      </v-btn>
    </div>

    <v-alert v-if="error" type="error" variant="tonal" rounded="lg" class="mb-4">
      {{ error }}
    </v-alert>

    <div v-if="loading" class="dash-loading">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <div v-else-if="!solutions.length" class="dash-empty">
      <v-icon icon="mdi-lightbulb-outline" size="36" />
      <p>No solutions yet.</p>
    </div>

    <div v-else class="dash-table">
      <div class="dash-row dash-row--head">
        <span>Name</span>
        <span>Slug</span>
        <span>Products</span>
        <span>Published</span>
        <span class="dash-actions-head">Actions</span>
      </div>

      <div v-for="s in solutions" :key="s.id" class="dash-row">
        <span class="dash-name">{{ s.name }}</span>
        <span class="dash-slug">/{{ s.slug }}</span>
        <span>{{ (s.products ?? []).map(p => p.name).join(', ') || '—' }}</span>
        <span>
          <v-chip
            size="x-small"
            variant="flat"
            :color="s.is_published ? 'success' : 'default'"
          >
            {{ s.is_published ? 'Published' : 'Draft' }}
          </v-chip>
        </span>
        <span class="dash-actions">
          <v-btn
            size="small"
            variant="text"
            icon="mdi-pencil-outline"
            :to="{ name: 'admin-solution-edit', params: { id: s.id } }"
          />
          <v-btn
            size="small"
            variant="text"
            icon="mdi-delete-outline"
            color="error"
            @click="confirmDelete(s)"
          />
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { onMounted, ref } from 'vue'
  import { listAllSolutions, deleteSolution } from '@/services/adminSolutions'
  import { useNotif } from '@/composables/useNotif'

  const notify = useNotif()

  const solutions = ref([])
  const loading = ref(true)
  const error = ref(null)

  async function load() {
    loading.value = true
    error.value = null
    try {
      solutions.value = await listAllSolutions()
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  async function confirmDelete(solution) {
    if (!window.confirm(`Delete "${solution.name}"?`)) return
    try {
      await deleteSolution(solution.id)
      solutions.value = solutions.value.filter(s => s.id !== solution.id)
      notify('Solution deleted', { type: 'success' })
    } catch (err) {
      notify(err.message || 'Failed to delete solution', { type: 'error' })
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
    grid-template-columns: 1.2fr 1fr 1.4fr 0.9fr 0.8fr;
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
