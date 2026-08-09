<template>
  <div>
    <div class="editor-header">
      <div>
        <router-link to="/admin" class="back-link">
          <v-icon icon="mdi-arrow-left" size="16" /> Back to products
        </router-link>
        <h1 class="editor-title">{{ isNew ? 'New product' : form.name || 'Edit product' }}</h1>
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
    <v-alert v-if="savedNotice" type="success" variant="tonal" rounded="lg" class="mb-4">
      Saved.
    </v-alert>

    <div v-if="loading" class="editor-loading">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <template v-else>
      <!-- ── Basic info ── -->
      <section class="editor-section">
        <h2 class="section-heading">Details</h2>
        <v-row dense>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.slug" label="Slug" hint="e.g. nexstack-pos" persistent-hint required />
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.name" label="Name" required />
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.tagline" label="Tagline" />
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.summary" label="Summary (hub card blurb)" />
          </v-col>
          <v-col cols="12">
            <v-textarea v-model="form.description" label="Description" rows="3" auto-grow />
          </v-col>

          <v-col cols="12" sm="6">
            <v-select
              v-model="form.status"
              label="Status"
              :items="['live', 'beta', 'coming_soon']"
            />
          </v-col>
          <v-col cols="12" sm="6">
            <div class="color-field">
              <input v-model="form.accent_color" type="color" class="color-swatch" />
              <v-text-field v-model="form.accent_color" label="Accent color" />
            </div>
          </v-col>

          <v-col cols="12" sm="6">
            <v-text-field v-model="form.logo_url" label="Logo URL" />
          </v-col>
          <v-col cols="12" sm="6">
            <v-file-input
              label="Upload logo"
              accept="image/*"
              prepend-icon=""
              :loading="uploading.logo"
              @change="e => handleFieldUpload(e, 'logo_url')"
            />
          </v-col>

          <v-col cols="12" sm="6">
            <v-text-field v-model="form.hero_image_url" label="Hero image URL" />
          </v-col>
          <v-col cols="12" sm="6">
            <v-file-input
              label="Upload hero image"
              accept="image/*"
              prepend-icon=""
              :loading="uploading.hero"
              @change="e => handleFieldUpload(e, 'hero_image_url')"
            />
          </v-col>

          <v-col cols="12" sm="4">
            <v-select
              v-model="form.cta_type"
              label="CTA type"
              :items="['register', 'external_link', 'waitlist']"
            />
          </v-col>
          <v-col cols="12" sm="4">
            <v-text-field v-model="form.cta_label" label="CTA label" />
          </v-col>
          <v-col cols="12" sm="4">
            <v-text-field
              v-model="form.cta_url"
              label="CTA URL"
              :disabled="form.cta_type !== 'external_link'"
            />
          </v-col>

          <v-col cols="12" sm="6">
            <v-text-field v-model="form.lead_source" label="Lead source tag" hint="Sent with waitlist submissions" persistent-hint />
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field v-model.number="form.sort_order" type="number" label="Sort order" />
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

      <template v-if="isNew">
        <v-alert type="info" variant="tonal" rounded="lg">
          Save this product first to add features and screenshots.
        </v-alert>
      </template>

      <template v-else>
        <!-- ── Features ── -->
        <section class="editor-section">
          <div class="section-row">
            <h2 class="section-heading">Features</h2>
            <v-btn size="small" variant="tonal" prepend-icon="mdi-plus" @click="addFeature">Add feature</v-btn>
          </div>

          <div v-for="f in features" :key="f.id" class="nested-card">
            <v-row dense>
              <v-col cols="12" sm="3">
                <v-text-field v-model="f.icon" label="Icon (mdi-...)" density="compact" @blur="saveFeature(f)" />
              </v-col>
              <v-col cols="12" sm="3">
                <v-text-field v-model="f.title" label="Title" density="compact" @blur="saveFeature(f)" />
              </v-col>
              <v-col cols="12" sm="4">
                <v-text-field v-model="f.description" label="Description" density="compact" @blur="saveFeature(f)" />
              </v-col>
              <v-col cols="12" sm="1">
                <v-text-field v-model.number="f.sort_order" type="number" label="Order" density="compact" @blur="saveFeature(f)" />
              </v-col>
              <v-col cols="12" sm="1" class="d-flex align-center justify-end">
                <v-btn icon="mdi-delete-outline" size="small" variant="text" color="error" @click="removeFeature(f)" />
              </v-col>
            </v-row>
          </div>
          <p v-if="!features.length" class="nested-empty">No features yet.</p>
        </section>

        <!-- ── Screenshots ── -->
        <section class="editor-section">
          <div class="section-row">
            <h2 class="section-heading">Screenshots</h2>
            <v-file-input
              label="Upload screenshot"
              accept="image/*"
              prepend-icon=""
              density="compact"
              hide-details
              style="max-width: 260px"
              :loading="uploadingScreenshot"
              @change="handleScreenshotUpload"
            />
          </div>

          <div v-for="s in screenshots" :key="s.id" class="nested-card nested-card--shot">
            <img v-if="s.url" :src="s.url" class="shot-preview" :alt="s.alt_text" />
            <v-row dense class="flex-grow-1">
              <v-col cols="12" sm="5">
                <v-text-field v-model="s.alt_text" label="Alt text" density="compact" @blur="saveScreenshot(s)" />
              </v-col>
              <v-col cols="12" sm="5">
                <v-text-field v-model="s.caption" label="Caption" density="compact" @blur="saveScreenshot(s)" />
              </v-col>
              <v-col cols="12" sm="1">
                <v-text-field v-model.number="s.sort_order" type="number" label="Order" density="compact" @blur="saveScreenshot(s)" />
              </v-col>
              <v-col cols="12" sm="1" class="d-flex align-center justify-end">
                <v-btn icon="mdi-delete-outline" size="small" variant="text" color="error" @click="removeScreenshot(s)" />
              </v-col>
            </v-row>
          </div>
          <p v-if="!screenshots.length" class="nested-empty">No screenshots yet.</p>
        </section>
      </template>
    </template>
  </div>
</template>

<script setup>
  import { computed, onMounted, reactive, ref } from 'vue'
  import { useRoute, useRouter } from 'vue-router'
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
    uploadProductMedia
  } from '@/services/adminProducts'

  const route = useRoute()
  const router = useRouter()

  const isNew = computed(() => !productId.value)
  const productId = ref(route.params.id || null)

  const form = reactive({
    slug: '',
    name: '',
    tagline: '',
    summary: '',
    description: '',
    logo_url: '',
    hero_image_url: '',
    accent_color: '#6366F1',
    status: 'coming_soon',
    cta_type: 'waitlist',
    cta_label: '',
    cta_url: '',
    lead_source: '',
    seo_title: '',
    seo_description: '',
    sort_order: 0,
    is_published: false
  })

  const features = ref([])
  const screenshots = ref([])

  const loading = ref(false)
  const saving = ref(false)
  const error = ref(null)
  const savedNotice = ref(false)
  const uploading = reactive({ logo: false, hero: false })
  const uploadingScreenshot = ref(false)

  function flashSaved() {
    savedNotice.value = true
    setTimeout(() => (savedNotice.value = false), 2000)
  }

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
        accent_color: data.accent_color ?? '#6366F1',
        status: data.status,
        cta_type: data.cta_type,
        cta_label: data.cta_label ?? '',
        cta_url: data.cta_url ?? '',
        lead_source: data.lead_source ?? '',
        seo_title: data.seo_title ?? '',
        seo_description: data.seo_description ?? '',
        sort_order: data.sort_order ?? 0,
        is_published: data.is_published
      })
      features.value = data.product_features ?? []
      screenshots.value = data.product_screenshots ?? []
    } catch (err) {
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
        const created = await createProduct(payload)
        productId.value = created.id
        router.replace({ name: 'admin-product-edit', params: { id: created.id } })
        await load()
      } else {
        await updateProduct(productId.value, payload)
      }
      flashSaved()
    } catch (err) {
      error.value = err.message
    } finally {
      saving.value = false
    }
  }

  async function handleFieldUpload(e, field) {
    const file = e?.target?.files?.[0] ?? e?.[0]
    if (!file) return
    const key = field === 'logo_url' ? 'logo' : 'hero'
    uploading[key] = true
    try {
      form[field] = await uploadProductMedia(file, form.slug || 'general')
    } catch (err) {
      error.value = err.message
    } finally {
      uploading[key] = false
    }
  }

  // ── Features ──
  async function addFeature() {
    try {
      const created = await createFeature(productId.value, {
        icon: 'mdi-check-circle-outline',
        title: 'New feature',
        description: '',
        sort_order: features.value.length + 1
      })
      features.value.push(created)
    } catch (err) {
      error.value = err.message
    }
  }
  async function saveFeature(f) {
    try {
      await updateFeature(f.id, {
        icon: f.icon,
        title: f.title,
        description: f.description,
        sort_order: f.sort_order
      })
    } catch (err) {
      error.value = err.message
    }
  }
  async function removeFeature(f) {
    if (!window.confirm('Delete this feature?')) return
    try {
      await deleteFeature(f.id)
      features.value = features.value.filter(x => x.id !== f.id)
    } catch (err) {
      error.value = err.message
    }
  }

  // ── Screenshots ──
  async function handleScreenshotUpload(e) {
    const file = e?.target?.files?.[0] ?? e?.[0]
    if (!file) return
    uploadingScreenshot.value = true
    try {
      const url = await uploadProductMedia(file, form.slug || 'general')
      const created = await createScreenshot(productId.value, {
        url,
        alt_text: form.name,
        sort_order: screenshots.value.length + 1
      })
      screenshots.value.push(created)
    } catch (err) {
      error.value = err.message
    } finally {
      uploadingScreenshot.value = false
    }
  }
  async function saveScreenshot(s) {
    try {
      await updateScreenshot(s.id, {
        alt_text: s.alt_text,
        caption: s.caption,
        sort_order: s.sort_order
      })
    } catch (err) {
      error.value = err.message
    }
  }
  async function removeScreenshot(s) {
    if (!window.confirm('Delete this screenshot?')) return
    try {
      await deleteScreenshot(s.id)
      screenshots.value = screenshots.value.filter(x => x.id !== s.id)
    } catch (err) {
      error.value = err.message
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

  .color-field {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .color-swatch {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.15);
    padding: 2px;
    flex-shrink: 0;
    cursor: pointer;
  }

  .nested-card {
    padding: 14px 16px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.07);
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
    color: rgba(var(--v-theme-on-surface), 0.5);
    margin: 0;
  }
</style>
