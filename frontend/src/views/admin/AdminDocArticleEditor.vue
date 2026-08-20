<template>
  <div>
    <div class="editor-header">
      <div>
        <router-link to="/admin/documentation/articles" class="back-link">
          <v-icon icon="mdi-arrow-left" size="16" /> Back to articles
        </router-link>
        <h1 class="editor-title">{{ isNew ? 'New article' : form.title || 'Edit article' }}</h1>
      </div>
      <v-btn color="primary" variant="flat" rounded="lg" :loading="saving" @click="handleSave">Save</v-btn>
    </div>

    <v-alert v-if="error" type="error" variant="tonal" rounded="lg" class="mb-4">{{ error }}</v-alert>
    <v-alert v-if="savedNotice" type="success" variant="tonal" rounded="lg" class="mb-4">Saved.</v-alert>

    <div v-if="loading" class="editor-loading">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <template v-else>
      <section class="editor-section">
        <h2 class="section-heading">Details</h2>
        <v-row dense>
          <v-col cols="12" sm="8">
            <v-text-field v-model="form.title" label="Title" required />
          </v-col>
          <v-col cols="12" sm="4">
            <v-text-field v-model="form.slug" label="Slug" hint="Lowercase, hyphenated" persistent-hint />
          </v-col>

          <v-col cols="12">
            <v-textarea v-model="form.excerpt" label="Excerpt" rows="2" auto-grow hint="Short summary shown in search results and cards" persistent-hint />
          </v-col>

          <v-col cols="12" sm="4">
            <v-select
              v-model="form.category_id"
              label="Category"
              :items="allCategories"
              item-title="name"
              item-value="id"
              required
              @update:model-value="onCategoryChange"
            />
          </v-col>
          <v-col cols="12" sm="4">
            <v-select
              v-model="form.product_id"
              label="Product (optional)"
              :items="allProducts"
              item-title="name"
              item-value="id"
              clearable
            />
          </v-col>
          <v-col cols="12" sm="4">
            <v-select v-model="form.status" label="Status" :items="['draft', 'published', 'archived']" />
          </v-col>

          <v-col cols="12" sm="6">
            <v-text-field v-model.number="form.sort_order" type="number" label="Sort order" hint="Controls order within the category, and previous/next navigation" persistent-hint />
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.cover_image_url" label="Cover image URL (optional)" />
          </v-col>
        </v-row>
      </section>

      <section class="editor-section">
        <h2 class="section-heading">Content</h2>
        <RichTextEditor v-model="form.content" placeholder="Write the step-by-step guide…" />
      </section>

      <section class="editor-section">
        <h2 class="section-heading">SEO</h2>
        <v-row dense>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.seo_title" label="SEO title" :placeholder="form.title" />
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.seo_description" label="SEO description" :placeholder="form.excerpt" />
          </v-col>
        </v-row>
      </section>
    </template>
  </div>
</template>

<script setup>
  import { computed, onMounted, reactive, ref } from 'vue'
  import { useRoute, useRouter } from 'vue-router'
  import {
    getDocArticleForEdit,
    createDocArticle,
    updateDocArticle,
    listAllDocCategories
  } from '@/services/adminDocumentation'
  import { listAllProducts } from '@/services/adminProducts'
  import RichTextEditor from '@/components/admin/RichTextEditor.vue'

  const route = useRoute()
  const router = useRouter()

  const isNew = computed(() => !articleId.value)
  const articleId = ref(route.params.id || null)

  const form = reactive({
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    category_id: null,
    product_id: null,
    status: 'draft',
    sort_order: 0,
    cover_image_url: '',
    seo_title: '',
    seo_description: ''
  })

  const allProducts = ref([])
  const allCategories = ref([])

  const loading = ref(false)
  const saving = ref(false)
  const error = ref(null)
  const savedNotice = ref(false)

  function flashSaved() {
    savedNotice.value = true
    setTimeout(() => (savedNotice.value = false), 2000)
  }

  // Picking a category defaults the product to that category's own
  // product (most articles belong entirely to one product) — still
  // overridable via the Product select right after.
  function onCategoryChange(categoryId) {
    if (form.product_id) return
    const category = allCategories.value.find(c => c.id === categoryId)
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
        form.category_id = allCategories.value[0].id
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
      if (!payload.slug) payload.slug = slugify(payload.title)

      if (isNew.value) {
        const created = await createDocArticle(payload)
        articleId.value = created.id
        router.replace({ name: 'admin-doc-article-edit', params: { id: created.id } })
        await load()
      } else {
        await updateDocArticle(articleId.value, payload)
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
  .section-heading {
    font-size: 1rem;
    font-weight: 800;
    margin: 0 0 16px;
  }
</style>
