<template>
  <div>
    <div class="editor-header">
      <div>
        <NuxtLink to="/admin/blog" class="back-link">
          <v-icon icon="mdi-arrow-left" size="16" /> Back to blog
        </NuxtLink>
        <h1 class="editor-title">{{ isNew ? 'New post' : form.title || 'Edit post' }}</h1>
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
            <v-text-field v-model="form.slug" label="Slug" hint="e.g. qr-ordering-worth-it" persistent-hint required />
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.title" label="Title" required />
          </v-col>

          <v-col cols="12" sm="6">
            <v-text-field v-model="form.author_name" label="Author name" />
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.published_at" type="date" label="Published date" />
          </v-col>

          <v-col cols="12" sm="6">
            <v-text-field v-model="form.cover_image_url" label="Cover image URL" />
          </v-col>
          <v-col cols="12" sm="6">
            <v-file-input
              label="Upload cover image"
              accept="image/*"
              prepend-icon=""
              :loading="uploadingCover"
              @change="handleCoverUpload"
            />
          </v-col>

          <v-col cols="12">
            <v-text-field v-model="form.excerpt" label="Excerpt (blog list card summary)" />
          </v-col>
          <v-col cols="12">
            <v-textarea v-model="form.content" label="Content" rows="10" auto-grow required />
          </v-col>

          <v-col cols="12" sm="6">
            <v-text-field v-model="form.seo_title" label="SEO title" />
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.seo_description" label="SEO description" />
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
  // Explicit import — Vuetify also exports its own `useDate` (date-adapter
  // composable) which Nuxt's auto-import would otherwise resolve instead.
  import { useDate } from '~/composables/useDate'
  import { getBlogPostForEdit, createBlogPost, updateBlogPost } from '~/services/adminBlog'
  import { uploadProductMedia } from '~/services/adminProducts'

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
