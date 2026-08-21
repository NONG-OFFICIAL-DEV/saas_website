<template>
  <div>
    <div class="editor-header">
      <div>
        <NuxtLink to="/admin/documentation/categories" class="back-link">
          <Icon name="mdi-arrow-left" size="16" /> Back to categories
        </NuxtLink>
        <h1 class="editor-title">{{ isNew ? 'New category' : form.name || 'Edit category' }}</h1>
      </div>
      <Button :disabled="saving" @click="handleSave">
        <Icon v-if="saving" name="mdi-loading" size="16" class="animate-spin" />
        Save
      </Button>
    </div>

    <Alert v-if="error" variant="destructive" class="mb-4">
      <AlertDescription>{{ error }}</AlertDescription>
    </Alert>

    <InlineLoader v-if="loading" min-height="120px" />

    <section v-else class="editor-section">
      <Row dense>
        <Col cols="12" sm="6">
          <div class="field">
            <Label for="name">Name *</Label>
            <Input id="name" v-model="form.name" />
          </div>
        </Col>
        <Col cols="12" sm="6">
          <div class="field">
            <Label for="slug">Slug</Label>
            <Input id="slug" v-model="form.slug" />
            <p class="field-hint">Lowercase, hyphenated</p>
          </div>
        </Col>

        <Col cols="12">
          <div class="field">
            <Label for="description">Description</Label>
            <Textarea id="description" v-model="form.description" rows="2" />
          </div>
        </Col>

        <Col cols="12" sm="4">
          <div class="field">
            <Label for="icon">Icon (mdi-...)</Label>
            <Input id="icon" v-model="form.icon" />
          </div>
        </Col>
        <Col cols="12" sm="4">
          <div class="field">
            <Label>Product (optional)</Label>
            <Select v-model="productIdModel">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Product (optional)" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="__none__">— None —</SelectItem>
                <SelectItem v-for="p in allProducts" :key="p.id" :value="p.id">{{ p.name }}</SelectItem>
              </SelectContent>
            </Select>
            <p class="field-hint">Leave empty for a general category</p>
          </div>
        </Col>
        <Col cols="12" sm="4">
          <div class="field">
            <Label>Parent category (optional)</Label>
            <Select v-model="parentIdModel">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Parent category (optional)" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="__none__">— None —</SelectItem>
                <SelectItem v-for="c in parentOptions" :key="c.id" :value="c.id">{{ c.name }}</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </Col>

        <Col cols="12" sm="6">
          <div class="field">
            <Label for="sort_order">Sort order</Label>
            <Input id="sort_order" v-model.number="form.sort_order" type="number" />
          </div>
        </Col>
        <Col cols="12" sm="6" class="flex items-center">
          <div class="switch-row">
            <Switch v-model="form.is_active" />
            <Label>Active (visible on the public site)</Label>
          </div>
        </Col>
      </Row>
    </section>
  </div>
</template>

<script setup lang="ts">
  import { Alert, AlertDescription } from '~/components/ui/alert'
  import { Button } from '~/components/ui/button'
  import { Input } from '~/components/ui/input'
  import { Label } from '~/components/ui/label'
  import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '~/components/ui/select'
  import { Switch } from '~/components/ui/switch'
  import { Textarea } from '~/components/ui/textarea'
  import { getDocCategoryForEdit, createDocCategory, updateDocCategory, listAllDocCategories } from '~/services/adminDocumentation'
  import { listAllProducts } from '~/services/adminProducts'
  import type { Product, DocumentationCategory } from '~/types'

  const notify = useNotif()
  const route = useRoute()

  const categoryId = ref<string | null>((route.params.id as string) || null)
  const isNew = computed(() => !categoryId.value)

  const form = reactive({
    name: '',
    slug: '',
    description: '',
    icon: '',
    product_id: null as string | null,
    parent_id: null as string | null,
    sort_order: 0,
    is_active: true
  })

  const allProducts = ref<Product[]>([])
  const allCategories = ref<DocumentationCategory[]>([])
  const parentOptions = computed(() => allCategories.value.filter((c) => c.id !== categoryId.value))

  // Reka UI's Select has no built-in "clear" affordance like Vuetify's
  // `clearable` — a sentinel "__none__" item plays that role, mapped back
  // to null (Vuetify's actual cleared value) through this computed.
  const productIdModel = computed({
    get: () => form.product_id ?? '__none__',
    set: (v: string) => { form.product_id = v === '__none__' ? null : v }
  })
  const parentIdModel = computed({
    get: () => form.parent_id ?? '__none__',
    set: (v: string) => { form.parent_id = v === '__none__' ? null : v }
  })

  const loading = ref(false)
  const saving = ref(false)
  const error = ref<string | null>(null)

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
    } catch (err: any) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }
  onMounted(load)

  function slugify(text: string) {
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
        navigateTo(`/admin/documentation/categories/${created.id}/edit`, { replace: true })
        await load()
      } else {
        await updateDocCategory(categoryId.value!, payload)
      }
      notify('Category saved successfully', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to save category', { type: 'error' })
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
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
    text-decoration: none;
    margin-bottom: 6px;
  }
  .editor-title {
    font-size: 1.4rem;
    font-weight: 800;
    margin: 0;
  }
  .editor-section {
    padding: 24px;
    margin-bottom: 24px;
    border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
    border-radius: 14px;
    background: color-mix(in srgb, var(--card) 60%, transparent);
  }
  .field {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .field-hint {
    font-size: 0.75rem;
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
    margin: 0;
  }
  .switch-row {
    display: flex;
    align-items: center;
    gap: 10px;
  }
</style>
