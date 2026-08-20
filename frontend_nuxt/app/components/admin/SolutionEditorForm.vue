<template>
  <div>
    <div class="editor-header">
      <div>
        <NuxtLink to="/admin/solutions" class="back-link">
          <v-icon icon="mdi-arrow-left" size="16" /> Back to solutions
        </NuxtLink>
        <h1 class="editor-title">{{ isNew ? 'New solution' : form.name || 'Edit solution' }}</h1>
      </div>
      <v-btn
        color="primary"
        variant="flat"
        rounded="lg"
        :loading="saving"
        @click="handleSave"
      >
        Save
      </v-btn>
    </div>

    <v-alert v-if="error" type="error" variant="tonal" rounded="lg" class="mb-4">
      {{ error }}
    </v-alert>

    <div v-if="loading" class="editor-loading">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <template v-else>
      <section class="editor-section">
        <h2 class="section-heading">Details</h2>
        <v-row dense>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.slug" label="Slug" hint="e.g. coffee-shop" persistent-hint required />
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.name" label="Name" required />
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.icon" label="Icon (mdi-...)" />
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field v-model.number="form.sort_order" type="number" label="Sort order" />
          </v-col>
          <v-col cols="12">
            <v-text-field v-model="form.tagline" label="Tagline" />
          </v-col>
          <v-col cols="12">
            <v-textarea v-model="form.description" label="Description" rows="3" auto-grow />
          </v-col>

          <v-col cols="12">
            <v-select
              v-model="form.product_ids"
              label="Linked products"
              :items="allProducts"
              item-title="name"
              item-value="id"
              multiple
              chips
              closable-chips
              hint="Which product(s) does this solution recommend?"
              persistent-hint
            />
          </v-col>

          <v-col cols="12">
            <v-switch v-model="form.is_published" color="primary" label="Published (visible on the public site)" hide-details />
          </v-col>
        </v-row>
      </section>
    </template>
  </div>
</template>

<script setup lang="ts">
  import { getSolutionForEdit, createSolution, updateSolution } from '~/services/adminSolutions'
  import { listAllProducts } from '~/services/adminProducts'
  import type { Product } from '~/types'

  const notify = useNotif()
  const route = useRoute()

  const solutionId = ref<string | null>((route.params.id as string) || null)
  const isNew = computed(() => !solutionId.value)

  const form = reactive({
    slug: '',
    name: '',
    icon: '',
    tagline: '',
    description: '',
    sort_order: 0,
    is_published: false,
    product_ids: [] as string[]
  })

  const allProducts = ref<Product[]>([])
  const loading = ref(false)
  const saving = ref(false)
  const error = ref<string | null>(null)

  async function load() {
    loading.value = true
    error.value = null
    try {
      allProducts.value = await listAllProducts()

      if (solutionId.value) {
        const data = await getSolutionForEdit(solutionId.value)
        if (!data) {
          error.value = 'Solution not found.'
          return
        }
        Object.assign(form, {
          slug: data.slug,
          name: data.name,
          icon: data.icon ?? '',
          tagline: data.tagline ?? '',
          description: data.description ?? '',
          sort_order: data.sort_order ?? 0,
          is_published: data.is_published,
          product_ids: (data.products ?? []).map((p) => p.id)
        })
      }
    } catch (err: any) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }
  onMounted(load)

  async function handleSave() {
    saving.value = true
    error.value = null
    try {
      const payload = { ...form }
      if (isNew.value) {
        const created = await createSolution(payload)
        solutionId.value = created.id
        navigateTo(`/admin/solutions/${created.id}/edit`, { replace: true })
        await load()
      } else {
        await updateSolution(solutionId.value!, payload)
      }
      notify('Solution saved successfully', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to save solution', { type: 'error' })
    } finally {
      saving.value = false
    }
  }
</script>

<style scoped>
  .editor-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.8rem;
    color: rgba(var(--v-theme-on-surface), 0.55);
    text-decoration: none;
    margin-bottom: 6px;
  }
  .editor-title {
    font-size: 1.4rem;
    font-weight: 800;
    margin: 0;
  }

  .editor-loading {
    display: flex;
    justify-content: center;
    padding: 60px 0;
  }

  .editor-section {
    padding: 24px;
    margin-bottom: 24px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
    border-radius: 14px;
    background: rgba(var(--v-theme-surface), 0.6);
  }
  .section-heading {
    font-size: 1rem;
    font-weight: 800;
    margin: 0 0 16px;
  }
</style>
