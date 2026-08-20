<template>
  <div>
    <div class="dash-header">
      <div>
        <h1 class="dash-title">Testimonials</h1>
        <p class="dash-sub">Manage the business-owner quotes shown on the homepage.</p>
      </div>
      <v-btn
        color="primary"
        variant="flat"
        rounded="lg"
        prepend-icon="mdi-plus"
        :to="{ name: 'admin-testimonial-new' }"
      >
        New testimonial
      </v-btn>
    </div>

    <v-alert v-if="error" type="error" variant="tonal" rounded="lg" class="mb-4">
      {{ error }}
    </v-alert>

    <div v-if="loading" class="dash-loading">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <div v-else-if="!testimonials.length" class="dash-empty">
      <v-icon icon="mdi-comment-quote-outline" size="36" />
      <p>No testimonials yet.</p>
    </div>

    <div v-else class="dash-table">
      <div class="dash-row dash-row--head">
        <span>Author</span>
        <span>Product</span>
        <span>Rating</span>
        <span>Published</span>
        <span class="dash-actions-head">Actions</span>
      </div>

      <div v-for="item in testimonials" :key="item.id" class="dash-row">
        <span class="dash-name">{{ item.author_name }}</span>
        <span>{{ item.product?.name ?? '—' }}</span>
        <span>{{ item.rating ? `${item.rating} / 5` : '—' }}</span>
        <span>
          <v-chip
            size="x-small"
            variant="flat"
            :color="item.is_published ? 'success' : 'default'"
          >
            {{ item.is_published ? 'Published' : 'Draft' }}
          </v-chip>
        </span>
        <span class="dash-actions">
          <v-btn
            size="small"
            variant="text"
            icon="mdi-pencil-outline"
            :to="{ name: 'admin-testimonial-edit', params: { id: item.id } }"
          />
          <v-btn
            size="small"
            variant="text"
            icon="mdi-delete-outline"
            color="error"
            @click="confirmDelete(item)"
          />
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { onMounted, ref } from 'vue'
  import { listAllTestimonials, deleteTestimonial } from '@/services/adminTestimonials'
  import { useNotif } from '@/composables/useNotif'

  const notify = useNotif()

  const testimonials = ref([])
  const loading = ref(true)
  const error = ref(null)

  async function load() {
    loading.value = true
    error.value = null
    try {
      testimonials.value = await listAllTestimonials()
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  async function confirmDelete(item) {
    if (!window.confirm(`Delete testimonial from "${item.author_name}"?`)) return
    try {
      await deleteTestimonial(item.id)
      testimonials.value = testimonials.value.filter(t => t.id !== item.id)
      notify('Testimonial deleted', { type: 'success' })
    } catch (err) {
      notify(err.message || 'Failed to delete testimonial', { type: 'error' })
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
    grid-template-columns: 1.2fr 1fr 0.8fr 0.9fr 0.8fr;
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
