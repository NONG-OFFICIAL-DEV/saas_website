<template>
  <div>
    <div class="dash-header">
      <div>
        <h1 class="dash-title">Testimonials</h1>
        <p class="dash-sub">Manage the business-owner quotes shown on the homepage.</p>
      </div>
      <Button as="NuxtLink" to="/admin/testimonials/new">
        <Icon name="mdi-plus" size="18" />
        New testimonial
      </Button>
    </div>

    <Alert v-if="error" variant="destructive" class="mb-4">
      <AlertDescription>{{ error }}</AlertDescription>
    </Alert>

    <InlineLoader v-if="loading" min-height="80px" />

    <div v-else-if="!testimonials.length" class="dash-empty">
      <Icon name="mdi-comment-quote-outline" size="36" />
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
          <Badge :class="item.is_published ? 'bg-success text-success-foreground' : ''" :variant="item.is_published ? undefined : 'secondary'">
            {{ item.is_published ? 'Published' : 'Draft' }}
          </Badge>
        </span>
        <span class="dash-actions">
          <Button as="NuxtLink" size="icon-sm" variant="ghost" :to="`/admin/testimonials/${item.id}/edit`">
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
  import { listAllTestimonials, deleteTestimonial } from '~/services/adminTestimonials'
  import type { Testimonial } from '~/types'

  const notify = useNotif()

  const testimonials = ref<Testimonial[]>([])
  const loading = ref(true)
  const error = ref<string | null>(null)

  async function load() {
    loading.value = true
    error.value = null
    try {
      testimonials.value = await listAllTestimonials()
    } catch (err: any) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  async function confirmDelete(item: Testimonial) {
    if (!window.confirm(`Delete testimonial from "${item.author_name}"?`)) return
    try {
      await deleteTestimonial(item.id)
      testimonials.value = testimonials.value.filter((t) => t.id !== item.id)
      notify('Testimonial deleted', { type: 'success' })
    } catch (err: any) {
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
    grid-template-columns: 1.2fr 1fr 0.8fr 0.9fr 0.8fr;
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
