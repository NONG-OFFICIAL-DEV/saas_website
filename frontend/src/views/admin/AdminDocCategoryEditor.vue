<template>
  <div>
    <div class="editor-header">
      <div>
        <router-link to="/admin/documentation/categories" class="back-link">
          <v-icon icon="mdi-arrow-left" size="16" /> Back to categories
        </router-link>
        <h1 class="editor-title">{{ isNew ? 'New category' : form.name || 'Edit category' }}</h1>
      </div>
      <v-btn color="primary" variant="flat" rounded="lg" :loading="saving" @click="handleSave">Save</v-btn>
    </div>

    <v-alert v-if="error" type="error" variant="tonal" rounded="lg" class="mb-4">{{ error }}</v-alert>
    <v-alert v-if="savedNotice" type="success" variant="tonal" rounded="lg" class="mb-4">Saved.</v-alert>

    <div v-if="loading" class="editor-loading">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <section v-else class="editor-section">
      <v-row dense>
        <v-col cols="12" sm="6">
          <v-text-field v-model="form.name" label="Name" required />
        </v-col>
        <v-col cols="12" sm="6">
          <v-text-field v-model="form.slug" label="Slug" hint="Lowercase, hyphenated" persistent-hint />
        </v-col>

        <v-col cols="12">
          <v-textarea v-model="form.description" label="Description" rows="2" auto-grow />
        </v-col>

        <v-col cols="12" sm="4">
          <v-text-field v-model="form.icon" label="Icon (mdi-...)" />
        </v-col>
        <v-col cols="12" sm="4">
          <v-select
            v-model="form.product_id"
            label="Product (optional)"
            :items="allProducts"
            item-title="name"
            item-value="id"
            clearable
            hint="Leave empty for a general category"
            persistent-hint
          />
        </v-col>
        <v-col cols="12" sm="4">
          <v-select
            v-model="form.parent_id"
            label="Parent category (optional)"
            :items="parentOptions"
            item-title="name"
            item-value="id"
            clearable
          />
        </v-col>

        <v-col cols="12" sm="6">
          <v-text-field v-model.number="form.sort_order" type="number" label="Sort order" />
        </v-col>
        <v-col cols="12" sm="6" class="d-flex align-center">
          <v-switch v-model="form.is_active" color="primary" label="Active (visible on the public site)" hide-details />
        </v-col>
      </v-row>
    </section>
  </div>
</template>

<script setup>
  import { computed, onMounted, reactive, ref } from 'vue'
  import { useRoute, useRouter } from 'vue-router'
  import { getDocCategoryForEdit, createDocCategory, updateDocCategory, listAllDocCategories } from '@/services/adminDocumentation'
  import { listAllProducts } from '@/services/adminProducts'

  const route = useRoute()
  const router = useRouter()

  const isNew = computed(() => !categoryId.value)
  const categoryId = ref(route.params.id || null)

  const form = reactive({
    name: '',
    slug: '',
    description: '',
    icon: '',
    product_id: null,
    parent_id: null,
    sort_order: 0,
    is_active: true
  })

  const allProducts = ref([])
  const allCategories = ref([])
  const parentOptions = computed(() => allCategories.value.filter(c => c.id !== categoryId.value))

  const loading = ref(false)
  const saving = ref(false)
  const error = ref(null)
  const savedNotice = ref(false)

  function flashSaved() {
    savedNotice.value = true
    setTimeout(() => (savedNotice.value = false), 2000)
  }

  async function load() {
    loading.value = true
    error.value = null
    try {
      ;[allProducts.value, allCategories.value] = await Promise.all([listAllProducts(), listAllDocCategories()])

      if (categoryId.value) {
        const data = await getDocCategoryForEdit(categoryId.value)
        if (!data) {
          error.value = 'Category not found.'
          return
        }
        Object.assign(form, {
          name: data.name,
          slug: data.slug,
          description: data.description ?? '',
          icon: data.icon ?? '',
          product_id: data.product_id ?? null,
          parent_id: data.parent_id ?? null,
          sort_order: data.sort_order ?? 0,
          is_active: data.is_active
        })
      }
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }
  onMounted(load)

  function slugify(text) {
    return text
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/(^-|-$)/g, '')
  }

  async function handleSave() {
    saving.value = true
    error.value = null
    try {
      const payload = { ...form }
      if (!payload.slug) payload.slug = slugify(payload.name)

      if (isNew.value) {
        const created = await createDocCategory(payload)
        categoryId.value = created.id
        router.replace({ name: 'admin-doc-category-edit', params: { id: created.id } })
        await load()
      } else {
        await updateDocCategory(categoryId.value, payload)
      }
      flashSaved()
    } catch (err) {
      error.value = err.message
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
</style>
