<template>
  <div>
    <div class="editor-header">
      <div>
        <NuxtLink to="/admin" class="back-link">
          <Icon name="mdi-arrow-left" size="16" /> Back to products
        </NuxtLink>
        <h1 class="editor-title">{{ isNew ? 'New product' : form.name || 'Edit product' }}</h1>
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
      <!-- ── Basic info ── -->
      <section class="editor-section">
        <h2 class="section-heading">Details</h2>
        <Row dense>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="slug">Slug</Label>
              <Input id="slug" v-model="form.slug" required />
              <p class="field-hint">e.g. nexstack-pos</p>
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
              <Label for="tagline">Tagline</Label>
              <Input id="tagline" v-model="form.tagline" />
            </div>
          </Col>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="summary">Summary (hub card blurb)</Label>
              <Input id="summary" v-model="form.summary" />
            </div>
          </Col>
          <Col cols="12">
            <div class="field">
              <Label for="description">Description</Label>
              <Textarea id="description" v-model="form.description" rows="3" />
            </div>
          </Col>

          <Col cols="12" sm="6">
            <div class="field">
              <Label>Status</Label>
              <Select v-model="form.status">
                <SelectTrigger class="w-full">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="live">live</SelectItem>
                  <SelectItem value="beta">beta</SelectItem>
                  <SelectItem value="coming_soon">coming_soon</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </Col>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="accent_color">Accent color</Label>
              <div class="color-field">
                <input v-model="form.accent_color" type="color" class="color-swatch" />
                <Input id="accent_color" v-model="form.accent_color" />
              </div>
            </div>
          </Col>

          <Col cols="12" sm="6">
            <div class="field">
              <Label for="logo_url">Logo URL</Label>
              <Input id="logo_url" v-model="form.logo_url" />
            </div>
          </Col>
          <Col cols="12" sm="6">
            <FileInput
              label="Upload logo"
              accept="image/*"
              :loading="uploading.logo"
              @change="(e: Event) => handleFieldUpload(e, 'logo_url')"
            />
          </Col>

          <Col cols="12" sm="6">
            <div class="field">
              <Label for="hero_image_url">Hero image URL</Label>
              <Input id="hero_image_url" v-model="form.hero_image_url" />
            </div>
          </Col>
          <Col cols="12" sm="6">
            <FileInput
              label="Upload hero image"
              accept="image/*"
              :loading="uploading.hero"
              @change="(e: Event) => handleFieldUpload(e, 'hero_image_url')"
            />
          </Col>

          <Col cols="12" sm="6">
            <div class="field">
              <Label for="demo_video_url">Demo video URL</Label>
              <Input id="demo_video_url" v-model="form.demo_video_url" />
              <p class="field-hint">YouTube or Vimeo link — shown on the /docs page</p>
            </div>
          </Col>

          <Col cols="12" sm="4">
            <div class="field">
              <Label>CTA type</Label>
              <Select v-model="form.cta_type">
                <SelectTrigger class="w-full">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="register">register</SelectItem>
                  <SelectItem value="external_link">external_link</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </Col>
          <Col cols="12" sm="4">
            <div class="field">
              <Label for="cta_label">CTA label</Label>
              <Input id="cta_label" v-model="form.cta_label" />
            </div>
          </Col>
          <Col cols="12" sm="4">
            <div class="field">
              <Label for="cta_url">CTA URL</Label>
              <Input id="cta_url" v-model="form.cta_url" :disabled="form.cta_type !== 'external_link'" />
            </div>
          </Col>

          <Col cols="12" sm="6">
            <div class="field">
              <Label for="sort_order">Sort order</Label>
              <Input id="sort_order" v-model.number="form.sort_order" type="number" />
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
              <Switch id="is_published" :model-value="form.is_published" @update:model-value="(v: boolean) => (form.is_published = v)" />
              <Label for="is_published">Published (visible on the public site)</Label>
            </div>
          </Col>
        </Row>
      </section>

      <template v-if="isNew">
        <Alert>
          <AlertDescription>Save this product first to add features and screenshots.</AlertDescription>
        </Alert>
      </template>

      <template v-else>
        <!-- ── Features ── -->
        <section class="editor-section">
          <div class="section-row">
            <h2 class="section-heading">Features</h2>
            <Button type="button" size="sm" variant="secondary" @click="addFeature">
              <Icon name="mdi-plus" size="16" />
              Add feature
            </Button>
          </div>

          <div v-for="f in features" :key="f.id" class="nested-card">
            <Row dense>
              <Col cols="12" sm="3">
                <div class="field">
                  <Label>Icon (mdi-...)</Label>
                  <Input
                    :model-value="f.icon ?? ''"
                    @update:model-value="(v: string | number) => (f.icon = String(v))"
                    @blur="saveFeature(f)"
                  />
                </div>
              </Col>
              <Col cols="12" sm="3">
                <div class="field">
                  <Label>Title</Label>
                  <Input v-model="f.title" @blur="saveFeature(f)" />
                </div>
              </Col>
              <Col cols="12" sm="4">
                <div class="field">
                  <Label>Description</Label>
                  <Input
                    :model-value="f.description ?? ''"
                    @update:model-value="(v: string | number) => (f.description = String(v))"
                    @blur="saveFeature(f)"
                  />
                </div>
              </Col>
              <Col cols="12" sm="1">
                <div class="field">
                  <Label>Order</Label>
                  <Input v-model.number="f.sort_order" type="number" @blur="saveFeature(f)" />
                </div>
              </Col>
              <Col cols="12" sm="1" class="flex items-end justify-end">
                <Button type="button" size="icon-sm" variant="ghost" class="text-destructive hover:text-destructive" @click="removeFeature(f)">
                  <Icon name="mdi-delete-outline" size="16" />
                </Button>
              </Col>
            </Row>
          </div>
          <p v-if="!features.length" class="nested-empty">No features yet.</p>
        </section>

        <!-- ── Screenshots ── -->
        <section class="editor-section">
          <div class="section-row">
            <h2 class="section-heading">Screenshots</h2>
            <FileInput
              label="Upload screenshot"
              accept="image/*"
              class="max-w-[260px]"
              :loading="uploadingScreenshot"
              @change="handleScreenshotUpload"
            />
          </div>

          <div v-for="s in screenshots" :key="s.id" class="nested-card nested-card--shot">
            <img v-if="s.url" :src="s.url" class="shot-preview" :alt="s.alt_text ?? ''" />
            <Row dense class="grow">
              <Col cols="12" sm="5">
                <div class="field">
                  <Label>Alt text</Label>
                  <Input
                    :model-value="s.alt_text ?? ''"
                    @update:model-value="(v: string | number) => (s.alt_text = String(v))"
                    @blur="saveScreenshot(s)"
                  />
                </div>
              </Col>
              <Col cols="12" sm="5">
                <div class="field">
                  <Label>Caption</Label>
                  <Input
                    :model-value="s.caption ?? ''"
                    @update:model-value="(v: string | number) => (s.caption = String(v))"
                    @blur="saveScreenshot(s)"
                  />
                </div>
              </Col>
              <Col cols="12" sm="1">
                <div class="field">
                  <Label>Order</Label>
                  <Input v-model.number="s.sort_order" type="number" @blur="saveScreenshot(s)" />
                </div>
              </Col>
              <Col cols="12" sm="1" class="flex items-end justify-end">
                <Button type="button" size="icon-sm" variant="ghost" class="text-destructive hover:text-destructive" @click="removeScreenshot(s)">
                  <Icon name="mdi-delete-outline" size="16" />
                </Button>
              </Col>
            </Row>
          </div>
          <p v-if="!screenshots.length" class="nested-empty">No screenshots yet.</p>
        </section>

        <!-- ── FAQs ── -->
        <section class="editor-section">
          <div class="section-row">
            <h2 class="section-heading">FAQs</h2>
            <Button type="button" size="sm" variant="secondary" @click="addFaq">
              <Icon name="mdi-plus" size="16" />
              Add FAQ
            </Button>
          </div>

          <div v-for="f in faqs" :key="f.id" class="nested-card">
            <Row dense>
              <Col cols="12" sm="5">
                <div class="field">
                  <Label>Question</Label>
                  <Input v-model="f.question" @blur="saveFaq(f)" />
                </div>
              </Col>
              <Col cols="12" sm="5">
                <div class="field">
                  <Label>Answer</Label>
                  <Input v-model="f.answer" @blur="saveFaq(f)" />
                </div>
              </Col>
              <Col cols="12" sm="1">
                <div class="field">
                  <Label>Order</Label>
                  <Input v-model.number="f.sort_order" type="number" @blur="saveFaq(f)" />
                </div>
              </Col>
              <Col cols="12" sm="1" class="flex items-end justify-end">
                <Button type="button" size="icon-sm" variant="ghost" class="text-destructive hover:text-destructive" @click="removeFaq(f)">
                  <Icon name="mdi-delete-outline" size="16" />
                </Button>
              </Col>
            </Row>
          </div>
          <p v-if="!faqs.length" class="nested-empty">No FAQs yet.</p>
        </section>
      </template>
    </template>
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
  import {
    getProductForEdit,
    createProduct,
    updateProduct,
    createFeature,
    updateFeature,
    deleteFeature,
    createScreenshot,
    updateScreenshot,
    deleteScreenshot,
    createFaq,
    updateFaq,
    deleteFaq,
    uploadProductMedia
  } from '~/services/cms/adminProducts'
  import type { ProductFeature, ProductScreenshot, ProductFaq } from '~/types'

  const notify = useNotif()
  const route = useRoute()

  const productId = ref<string | null>((route.params.id as string) || null)
  const isNew = computed(() => !productId.value)

  const form = reactive({
    slug: '',
    name: '',
    tagline: '',
    summary: '',
    description: '',
    logo_url: '',
    hero_image_url: '',
    demo_video_url: '',
    accent_color: '#6366F1',
    status: 'coming_soon',
    cta_type: 'register',
    cta_label: '',
    cta_url: '',
    seo_title: '',
    seo_description: '',
    sort_order: 0,
    is_published: false
  })

  const features = ref<ProductFeature[]>([])
  const screenshots = ref<ProductScreenshot[]>([])
  const faqs = ref<ProductFaq[]>([])

  const loading = ref(false)
  const saving = ref(false)
  const error = ref<string | null>(null)
  const uploading = reactive({ logo: false, hero: false })
  const uploadingScreenshot = ref(false)

  async function load() {
    if (!productId.value) return
    loading.value = true
    error.value = null
    try {
      const data = await getProductForEdit(productId.value)
      if (!data) {
        error.value = 'Product not found.'
        return
      }
      Object.assign(form, {
        slug: data.slug,
        name: data.name,
        tagline: data.tagline ?? '',
        summary: data.summary ?? '',
        description: data.description ?? '',
        logo_url: data.logo_url ?? '',
        hero_image_url: data.hero_image_url ?? '',
        demo_video_url: data.demo_video_url ?? '',
        accent_color: data.accent_color ?? '#6366F1',
        status: data.status,
        cta_type: data.cta_type,
        cta_label: data.cta_label ?? '',
        cta_url: data.cta_url ?? '',
        seo_title: data.seo_title ?? '',
        seo_description: data.seo_description ?? '',
        sort_order: data.sort_order ?? 0,
        is_published: data.is_published
      })
      features.value = data.product_features ?? []
      screenshots.value = data.product_screenshots ?? []
      faqs.value = data.faqs ?? []
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
        const created = (await createProduct(payload))!
        productId.value = created.id
        navigateTo(`/admin/products/${created.id}/edit`, { replace: true })
        await load()
      } else {
        await updateProduct(productId.value!, payload)
      }
      notify('Product saved successfully', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to save product', { type: 'error' })
    } finally {
      saving.value = false
    }
  }

  function fileFromEvent(e: Event): File | undefined {
    return (e.target as HTMLInputElement)?.files?.[0]
  }

  async function handleFieldUpload(e: Event, field: 'logo_url' | 'hero_image_url') {
    const file = fileFromEvent(e)
    if (!file) return
    const key = field === 'logo_url' ? 'logo' : 'hero'
    uploading[key] = true
    try {
      form[field] = await uploadProductMedia(file)
    } catch (err: any) {
      notify(err.message || 'Failed to upload image', { type: 'error' })
    } finally {
      uploading[key] = false
    }
  }

  // ── Features ──
  async function addFeature() {
    try {
      const created = await createFeature(productId.value!, {
        icon: 'mdi-check-circle-outline',
        title: 'New feature',
        description: '',
        sort_order: features.value.length + 1
      })
      features.value.push(created)
    } catch (err: any) {
      notify(err.message || 'Failed to add feature', { type: 'error' })
    }
  }
  async function saveFeature(f: ProductFeature) {
    try {
      await updateFeature(f.id, {
        icon: f.icon,
        title: f.title,
        description: f.description,
        sort_order: f.sort_order
      })
    } catch (err: any) {
      notify(err.message || 'Failed to save feature', { type: 'error' })
    }
  }
  async function removeFeature(f: ProductFeature) {
    if (!window.confirm('Delete this feature?')) return
    try {
      await deleteFeature(f.id)
      features.value = features.value.filter((x) => x.id !== f.id)
      notify('Feature deleted', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to delete feature', { type: 'error' })
    }
  }

  // ── Screenshots ──
  async function handleScreenshotUpload(e: Event) {
    const file = fileFromEvent(e)
    if (!file) return
    uploadingScreenshot.value = true
    try {
      const url = await uploadProductMedia(file)
      const created = await createScreenshot(productId.value!, {
        url,
        alt_text: form.name,
        sort_order: screenshots.value.length + 1
      })
      screenshots.value.push(created)
    } catch (err: any) {
      notify(err.message || 'Failed to upload screenshot', { type: 'error' })
    } finally {
      uploadingScreenshot.value = false
    }
  }
  async function saveScreenshot(s: ProductScreenshot) {
    try {
      await updateScreenshot(s.id, {
        alt_text: s.alt_text,
        caption: s.caption,
        sort_order: s.sort_order
      })
    } catch (err: any) {
      notify(err.message || 'Failed to save screenshot', { type: 'error' })
    }
  }
  async function removeScreenshot(s: ProductScreenshot) {
    if (!window.confirm('Delete this screenshot?')) return
    try {
      await deleteScreenshot(s.id)
      screenshots.value = screenshots.value.filter((x) => x.id !== s.id)
      notify('Screenshot deleted', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to delete screenshot', { type: 'error' })
    }
  }

  // ── FAQs ──
  async function addFaq() {
    try {
      const created = await createFaq(productId.value!, {
        question: 'New question',
        answer: 'Answer here...',
        sort_order: faqs.value.length + 1
      })
      faqs.value.push(created)
    } catch (err: any) {
      notify(err.message || 'Failed to add FAQ', { type: 'error' })
    }
  }
  async function saveFaq(f: ProductFaq) {
    try {
      await updateFaq(f.id, {
        question: f.question,
        answer: f.answer,
        sort_order: f.sort_order
      })
    } catch (err: any) {
      notify(err.message || 'Failed to save FAQ', { type: 'error' })
    }
  }
  async function removeFaq(f: ProductFaq) {
    if (!window.confirm('Delete this FAQ?')) return
    try {
      await deleteFaq(f.id)
      faqs.value = faqs.value.filter((x) => x.id !== f.id)
      notify('FAQ deleted', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to delete FAQ', { type: 'error' })
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
  .section-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
  }
  .section-row .section-heading {
    margin-bottom: 0;
  }

  .field {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .field-hint {
    font-size: 0.72rem;
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
    margin: 0;
  }
  .switch-row {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .color-field {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .color-swatch {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 1px solid color-mix(in srgb, var(--foreground) 15%, transparent);
    padding: 2px;
    flex-shrink: 0;
    cursor: pointer;
  }

  .nested-card {
    padding: 14px 16px;
    border: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
    border-radius: 10px;
    margin-bottom: 10px;
  }
  .nested-card--shot {
    display: flex;
    gap: 14px;
    align-items: flex-start;
  }
  .shot-preview {
    width: 96px;
    height: 64px;
    object-fit: cover;
    border-radius: 8px;
    flex-shrink: 0;
  }
  .nested-empty {
    font-size: 0.85rem;
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
    margin: 0;
  }
</style>
