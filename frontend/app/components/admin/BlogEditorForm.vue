<template>
  <div>
    <div class="editor-header">
      <div>
        <NuxtLink to="/admin/blog" class="back-link">
          <Icon name="mdi-arrow-left" size="16" /> Back to blog
        </NuxtLink>
        <h1 class="editor-title">{{ isNew ? 'New post' : form.title || 'Edit post' }}</h1>
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
              <p class="field-hint">e.g. qr-ordering-worth-it</p>
            </div>
          </Col>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="title">Title</Label>
              <Input id="title" v-model="form.title" required />
            </div>
          </Col>

          <Col cols="12" sm="6">
            <div class="field">
              <Label for="author_name">Author name</Label>
              <Input id="author_name" v-model="form.author_name" />
            </div>
          </Col>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="published_at">Published date</Label>
              <Input id="published_at" v-model="form.published_at" type="date" />
            </div>
          </Col>

          <Col cols="12" sm="6">
            <div class="field">
              <Label for="cover_image_url">Cover image URL</Label>
              <Input id="cover_image_url" v-model="form.cover_image_url" />
            </div>
          </Col>
          <Col cols="12" sm="6">
            <FileInput
              label="Upload cover image"
              accept="image/*"
              :loading="uploadingCover"
              @change="handleCoverUpload"
            />
          </Col>

          <Col cols="12">
            <div class="field">
              <Label for="excerpt">Excerpt (blog list card summary)</Label>
              <Input id="excerpt" v-model="form.excerpt" />
            </div>
          </Col>
          <Col cols="12">
            <div class="field">
              <Label for="content">Content</Label>
              <Textarea id="content" v-model="form.content" rows="10" required />
            </div>
          </Col>

          <Col cols="12" sm="6">
            <div class="field">
              <Label for="seo_title">SEO title</Label>
              <Input id="seo_title" v-model="form.seo_title" />
            </div>
          </Col>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="seo_description">SEO description</Label>
              <Input id="seo_description" v-model="form.seo_description" />
            </div>
          </Col>

          <Col cols="12">
            <div class="switch-row">
              <Switch id="is_published" :model-value="form.is_published" @update:model-value="(v) => (form.is_published = v)" />
              <Label for="is_published">Published (visible on the public site)</Label>
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
  import { Switch } from '~/components/ui/switch'
  import { Textarea } from '~/components/ui/textarea'
  // Explicit import — Vuetify also exports its own `useDate` (date-adapter
  // composable) which Nuxt's auto-import would otherwise resolve instead.
  import { useDate } from '~/composables/useDate'
  import { getBlogPostForEdit, createBlogPost, updateBlogPost } from '~/services/cms/adminBlog'
  import { uploadProductMedia } from '~/services/cms/adminProducts'

  const notify = useNotif()
  const route = useRoute()
  const { formatLocalDate } = useDate()

  const postId = ref<string | null>((route.params.id as string) || null)
  const isNew = computed(() => !postId.value)

  const form = reactive({
    slug: '',
    title: '',
    author_name: '',
    published_at: '',
    cover_image_url: '',
    excerpt: '',
    content: '',
    seo_title: '',
    seo_description: '',
    is_published: false
  })

  const loading = ref(false)
  const saving = ref(false)
  const error = ref<string | null>(null)
  const uploadingCover = ref(false)

  async function load() {
    if (!postId.value) return
    loading.value = true
    error.value = null
    try {
      const data = await getBlogPostForEdit(postId.value)
      if (!data) {
        error.value = 'Post not found.'
        return
      }
      Object.assign(form, {
        slug: data.slug,
        title: data.title,
        author_name: data.author_name ?? '',
        published_at: data.published_at ? formatLocalDate(data.published_at) : '',
        cover_image_url: data.cover_image_url ?? '',
        excerpt: data.excerpt ?? '',
        content: data.content ?? '',
        seo_title: data.seo_title ?? '',
        seo_description: data.seo_description ?? '',
        is_published: data.is_published
      })
    } catch (err: any) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }
  onMounted(load)

  async function handleCoverUpload(e: Event) {
    const file = (e.target as HTMLInputElement)?.files?.[0]
    if (!file) return
    uploadingCover.value = true
    try {
      form.cover_image_url = await uploadProductMedia(file)
    } catch (err: any) {
      notify(err.message || 'Failed to upload cover image', { type: 'error' })
    } finally {
      uploadingCover.value = false
    }
  }

  async function handleSave() {
    saving.value = true
    error.value = null
    try {
      const payload = { ...form, published_at: form.published_at || null }
      if (isNew.value) {
        const created = await createBlogPost(payload)
        postId.value = created.id
        navigateTo(`/admin/blog/${created.id}/edit`, { replace: true })
        await load()
      } else {
        await updateBlogPost(postId.value!, payload)
      }
      notify('Blog post saved successfully', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to save blog post', { type: 'error' })
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
  .switch-row {
    display: flex;
    align-items: center;
    gap: 10px;
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
