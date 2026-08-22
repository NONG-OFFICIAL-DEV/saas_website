<template>
  <div>
    <div class="editor-header">
      <div>
        <NuxtLink to="/admin/documentation/articles" class="back-link">
          <Icon name="mdi-arrow-left" size="16" /> Back to articles
        </NuxtLink>
        <h1 class="editor-title">{{ isNew ? 'New article' : form.title || 'Edit article' }}</h1>
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
          <Col cols="12" sm="8">
            <div class="field">
              <Label for="title">Title *</Label>
              <Input id="title" v-model="form.title" />
            </div>
          </Col>
          <Col cols="12" sm="4">
            <div class="field">
              <Label for="slug">Slug</Label>
              <Input id="slug" v-model="form.slug" />
              <p class="field-hint">Lowercase, hyphenated</p>
            </div>
          </Col>

          <Col cols="12">
            <div class="field">
              <Label for="excerpt">Excerpt</Label>
              <Textarea id="excerpt" v-model="form.excerpt" rows="2" />
              <p class="field-hint">Short summary shown in search results and cards</p>
            </div>
          </Col>

          <Col cols="12" sm="4">
            <div class="field">
              <Label>Category *</Label>
              <Select v-model="form.category_id" @update:model-value="onCategoryChange">
                <SelectTrigger class="w-full">
                  <SelectValue placeholder="Category" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="c in allCategories" :key="c.id" :value="c.id">{{ c.name }}</SelectItem>
                </SelectContent>
              </Select>
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
            </div>
          </Col>
          <Col cols="12" sm="4">
            <div class="field">
              <Label>Status</Label>
              <Select v-model="form.status">
                <SelectTrigger class="w-full">
                  <SelectValue placeholder="Status" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="s in STATUS_OPTIONS" :key="s" :value="s">{{ s }}</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </Col>

          <Col cols="12" sm="6">
            <div class="field">
              <Label for="sort_order">Sort order</Label>
              <Input id="sort_order" v-model.number="form.sort_order" type="number" />
              <p class="field-hint">Controls order within the category, and previous/next navigation</p>
            </div>
          </Col>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="cover_image_url">Cover image URL (optional)</Label>
              <Input id="cover_image_url" v-model="form.cover_image_url" />
            </div>
          </Col>
        </Row>
      </section>

      <section class="editor-section">
        <h2 class="section-heading">Content</h2>
        <RichTextEditor v-model="form.content" placeholder="Write the step-by-step guide…" />
      </section>

      <section class="editor-section">
        <h2 class="section-heading">SEO</h2>
        <Row dense>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="seo_title">SEO title</Label>
              <Input id="seo_title" v-model="form.seo_title" :placeholder="form.title" />
            </div>
          </Col>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="seo_description">SEO description</Label>
              <Input id="seo_description" v-model="form.seo_description" :placeholder="form.excerpt" />
            </div>
          </Col>
        </Row>
      </section>
    </template>
  </div>
</template>

<script setup lang="ts">
  import { Alert, AlertDescription } from '~/components/ui/alert'
  import { Button } from '~/components/ui/button'
  import { Input } from '~/components/ui/input'
  import { Label } from '~/components/ui/label'
  import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '~/components/ui/select'
  import { Textarea } from '~/components/ui/textarea'
  import {
    getDocArticleForEdit,
    createDocArticle,
    updateDocArticle,
    listAllDocCategories
  } from '~/services/cms/adminDocumentation'
  import { listAllProducts } from '~/services/cms/adminProducts'
  import type { Product, DocumentationCategory } from '~/types'

  const STATUS_OPTIONS = ['draft', 'published', 'archived']

  const notify = useNotif()
  const route = useRoute()

  const articleId = ref<string | null>((route.params.id as string) || null)
  const isNew = computed(() => !articleId.value)

  const form = reactive({
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    category_id: null as string | null,
    product_id: null as string | null,
    status: 'draft',
    sort_order: 0,
    cover_image_url: '',
    seo_title: '',
    seo_description: ''
  })

  const allProducts = ref<Product[]>([])
  const allCategories = ref<DocumentationCategory[]>([])

  // Reka UI's Select has no built-in "clear" affordance like Vuetify's
  // `clearable` — a sentinel "__none__" item plays that role, mapped back
  // to null (Vuetify's actual cleared value) through this computed.
  const productIdModel = computed({
    get: () => form.product_id ?? '__none__',
    set: (v: string) => { form.product_id = v === '__none__' ? null : v }
  })

  const loading = ref(false)
  const saving = ref(false)
  const error = ref<string | null>(null)

  // Picking a category defaults the product to that category's own
  // product (most articles belong entirely to one product) — still
  // overridable via the Product select right after.
  function onCategoryChange(categoryId: unknown) {
    if (form.product_id || typeof categoryId !== 'string') return
    const category = allCategories.value.find((c) => c.id === categoryId)
    if (category?.product_id) form.product_id = category.product_id
  }

  async function load() {
    loading.value = true
    error.value = null
    try {
      ;[allProducts.value, allCategories.value] = await Promise.all([listAllProducts(), listAllDocCategories()])

      if (articleId.value) {
        const data = await getDocArticleForEdit(articleId.value)
        if (!data) {
          error.value = 'Article not found.'
          return
        }
        Object.assign(form, {
          title: data.title,
          slug: data.slug,
          excerpt: data.excerpt ?? '',
          content: data.content ?? '',
          category_id: data.category_id,
          product_id: data.product_id ?? null,
          status: data.status,
          sort_order: data.sort_order ?? 0,
          cover_image_url: data.cover_image_url ?? '',
          seo_title: data.seo_title ?? '',
          seo_description: data.seo_description ?? ''
        })
      } else if (allCategories.value.length) {
        form.category_id = allCategories.value[0]!.id
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
      if (!payload.slug) payload.slug = slugify(payload.title)

      if (isNew.value) {
        const created = await createDocArticle(payload)
        articleId.value = created.id
        navigateTo(`/admin/documentation/articles/${created.id}/edit`, { replace: true })
        await load()
      } else {
        await updateDocArticle(articleId.value!, payload)
      }
      notify('Article saved successfully', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to save article', { type: 'error' })
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
</style>
