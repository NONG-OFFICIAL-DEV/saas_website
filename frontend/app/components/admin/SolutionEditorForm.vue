<template>
  <div>
    <div class="editor-header">
      <div>
        <NuxtLink to="/admin/solutions" class="back-link">
          <Icon name="mdi-arrow-left" size="16" /> Back to solutions
        </NuxtLink>
        <h1 class="editor-title">{{ isNew ? 'New solution' : form.name || 'Edit solution' }}</h1>
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

    <template v-else>
      <section class="editor-section">
        <h2 class="section-heading">Details</h2>
        <Row dense>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="slug">Slug</Label>
              <Input id="slug" v-model="form.slug" required />
              <p class="field-hint">e.g. coffee-shop</p>
            </div>
          </Col>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="name">Name</Label>
              <Input id="name" v-model="form.name" required />
            </div>
          </Col>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="icon">Icon (mdi-...)</Label>
              <Input id="icon" v-model="form.icon" />
            </div>
          </Col>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="sort_order">Sort order</Label>
              <Input id="sort_order" v-model.number="form.sort_order" type="number" />
            </div>
          </Col>
          <Col cols="12">
            <div class="field">
              <Label for="tagline">Tagline</Label>
              <Input id="tagline" v-model="form.tagline" />
            </div>
          </Col>
          <Col cols="12">
            <div class="field">
              <Label for="description">Description</Label>
              <Textarea id="description" v-model="form.description" rows="3" />
            </div>
          </Col>

          <Col cols="12">
            <div class="field">
              <Label>Linked products</Label>
              <div v-if="form.product_ids.length" class="chips-row">
                <Badge v-for="pid in form.product_ids" :key="pid" variant="secondary">
                  {{ productName(pid) }}
                  <Icon name="mdi-close" size="12" class="cursor-pointer ml-1" @click="toggleProduct(pid)" />
                </Badge>
              </div>
              <div class="checklist">
                <label v-for="p in allProducts" :key="p.id" class="checklist-item">
                  <input
                    type="checkbox"
                    class="accent-primary size-4 rounded border-input"
                    :checked="form.product_ids.includes(p.id)"
                    @change="toggleProduct(p.id)"
                  />
                  {{ p.name }}
                </label>
              </div>
              <p class="field-hint">Which product(s) does this solution recommend?</p>
            </div>
          </Col>

          <Col cols="12">
            <div class="switch-row">
              <Switch :model-value="form.is_published" @update:model-value="(v: boolean) => (form.is_published = v)" />
              <Label>Published (visible on the public site)</Label>
            </div>
          </Col>
        </Row>
      </section>
    </template>
  </div>
</template>

<script setup lang="ts">
  import { Alert, AlertDescription } from '~/components/ui/alert'
  import { Badge } from '~/components/ui/badge'
  import { Button } from '~/components/ui/button'
  import { Input } from '~/components/ui/input'
  import { Label } from '~/components/ui/label'
  import { Switch } from '~/components/ui/switch'
  import { Textarea } from '~/components/ui/textarea'
  import { getSolutionForEdit, createSolution, updateSolution } from '~/services/cms/adminSolutions'
  import { listAllProducts } from '~/services/cms/adminProducts'
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

  function productName(id: string) {
    return allProducts.value.find((p) => p.id === id)?.name ?? id
  }

  function toggleProduct(id: string) {
    const idx = form.product_ids.indexOf(id)
    if (idx === -1) form.product_ids.push(id)
    else form.product_ids.splice(idx, 1)
  }

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
  .section-heading {
    font-size: 1rem;
    font-weight: 800;
    margin: 0 0 16px;
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
  .chips-row {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 8px;
  }
  .checklist {
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-height: 200px;
    overflow-y: auto;
    padding: 10px 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
  }
  .checklist-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    cursor: pointer;
  }
  .switch-row {
    display: flex;
    align-items: center;
    gap: 10px;
  }
</style>
