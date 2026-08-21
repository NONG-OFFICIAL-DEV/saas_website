<template>
  <div>
    <div class="dash-header">
      <div>
        <h1 class="dash-title">Onboarding activity</h1>
        <p class="dash-sub">Every signup submitted through the onboarding wizard, across every product.</p>
      </div>
    </div>

    <Alert v-if="error" variant="destructive" class="mb-4">
      <AlertDescription>{{ error }}</AlertDescription>
    </Alert>

    <InlineLoader v-if="loading" min-height="80px" />

    <div v-else-if="!submissions.length" class="dash-empty">
      <Icon name="mdi-account-arrow-right-outline" size="36" />
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
          <Badge :class="item.status === 'success' ? 'bg-success text-success-foreground' : 'bg-destructive text-destructive-foreground'">
            {{ item.status === 'success' ? 'Success' : 'Failed' }}
          </Badge>
          <div v-if="item.status === 'failed' && item.error_message" class="dash-error">
            {{ item.error_message }}
          </div>
        </span>
        <span class="dash-actions">
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
  import { useDate } from '~/composables/useDate'
  import { listOnboardingSubmissions, deleteOnboardingSubmission } from '~/services/adminOnboarding'
  import type { OnboardingSubmission } from '~/types'

  const notify = useNotif()

  const productsStore = useProductsStore()
  const { formatDate } = useDate()

  const submissions = ref<OnboardingSubmission[]>([])
  const loading = ref(true)
  const error = ref<string | null>(null)

  function productName(slug: string) {
    return productsStore.products.find((p) => p.slug === slug)?.name ?? slug
  }

  async function load() {
    loading.value = true
    error.value = null
    try {
      await productsStore.fetchProducts()
      submissions.value = await listOnboardingSubmissions()
    } catch (err: any) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  async function confirmDelete(item: OnboardingSubmission) {
    if (!window.confirm(`Remove the submission from "${item.business_name}"?`)) return
    try {
      await deleteOnboardingSubmission(item.id)
      submissions.value = submissions.value.filter((s) => s.id !== item.id)
      notify('Submission removed', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to remove submission', { type: 'error' })
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
    grid-template-columns: 0.9fr 0.9fr 1.1fr 1.3fr 1fr 0.6fr;
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
  .dash-email {
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
    font-size: 0.78rem;
  }
  .dash-error {
    margin-top: 4px;
    font-size: 0.72rem;
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
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
