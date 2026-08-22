<template>
  <div>
    <div class="editor-header">
      <div>
        <NuxtLink to="/admin/testimonials" class="back-link">
          <Icon name="mdi-arrow-left" size="16" /> Back to testimonials
        </NuxtLink>
        <h1 class="editor-title">{{ isNew ? 'New testimonial' : form.author_name || 'Edit testimonial' }}</h1>
      </div>
      <Button :disabled="saving" @click="handleSave">
        <Icon v-if="saving" name="mdi-loading" size="16" class="animate-spin" />
        Save
      </Button>
    </div>

    <Alert v-if="error" variant="destructive" class="mb-4">
      <AlertDescription>{{ error }}</AlertDescription>
    </Alert>

    <InlineLoader v-if="loading" min-height="80px" />

    <template v-else>
      <section class="editor-section">
        <h2 class="section-heading">Details</h2>
        <Row dense>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="author_name">Author name</Label>
              <Input id="author_name" v-model="form.author_name" required />
            </div>
          </Col>
          <Col cols="12" sm="6">
            <div class="field">
              <Label for="author_title">Author title</Label>
              <Input id="author_title" v-model="form.author_title" />
              <p class="field-hint">e.g. Owner, Golden Spoon Restaurant</p>
            </div>
          </Col>

          <Col cols="12" sm="6">
            <div class="field">
              <Label for="author_avatar_url">Avatar URL</Label>
              <Input id="author_avatar_url" v-model="form.author_avatar_url" />
            </div>
          </Col>
          <Col cols="12" sm="6">
            <FileInput
              label="Upload avatar"
              accept="image/*"
              :loading="uploadingAvatar"
              @change="handleAvatarUpload"
            />
          </Col>

          <Col cols="12" sm="6">
            <div class="field">
              <Label>About which product? (optional)</Label>
              <Select v-model="productSelectValue">
                <SelectTrigger class="w-full">
                  <SelectValue placeholder="None" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="__none__">— None —</SelectItem>
                  <SelectItem v-for="p in allProducts" :key="p.id" :value="p.id">{{ p.name }}</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </Col>
          <Col cols="12" sm="6">
            <div class="field">
              <Label>Rating (optional)</Label>
              <Select v-model="ratingSelectValue">
                <SelectTrigger class="w-full">
                  <SelectValue placeholder="None" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="__none__">— None —</SelectItem>
                  <SelectItem v-for="n in [1, 2, 3, 4, 5]" :key="n" :value="String(n)">{{ n }}</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </Col>

          <Col cols="12">
            <div class="field">
              <Label for="quote">Quote</Label>
              <Textarea id="quote" v-model="form.quote" rows="4" required />
            </div>
          </Col>

          <Col cols="12" sm="6">
            <div class="field">
              <Label for="sort_order">Sort order</Label>
              <Input id="sort_order" v-model.number="form.sort_order" type="number" />
            </div>
          </Col>
          <Col cols="12" sm="6" class="flex items-center">
            <div class="flex items-center gap-2">
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
  import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '~/components/ui/select'
  import { Switch } from '~/components/ui/switch'
  import { Textarea } from '~/components/ui/textarea'
  import { getTestimonialForEdit, createTestimonial, updateTestimonial } from '~/services/cms/adminTestimonials'
  import { listAllProducts, uploadProductMedia } from '~/services/cms/adminProducts'
  import type { Product } from '~/types'

  const notify = useNotif()
  const route = useRoute()

  const testimonialId = ref<string | null>((route.params.id as string) || null)
  const isNew = computed(() => !testimonialId.value)

  const form = reactive({
    author_name: '',
    author_title: '',
    author_avatar_url: '',
    product_id: null as string | null,
    rating: null as number | null,
    quote: '',
    sort_order: 0,
    is_published: false
  })

  const allProducts = ref<Product[]>([])

  // Select has no native "clearable" concept — a sentinel value stands in
  // for "no selection" and is translated back to null on write.
  const productSelectValue = computed({
    get: () => form.product_id ?? '__none__',
    set: (v: string) => {
      form.product_id = v === '__none__' ? null : v
    }
  })
  const ratingSelectValue = computed({
    get: () => (form.rating != null ? String(form.rating) : '__none__'),
    set: (v: string) => {
      form.rating = v === '__none__' ? null : Number(v)
    }
  })
  const loading = ref(false)
  const saving = ref(false)
  const error = ref<string | null>(null)
  const uploadingAvatar = ref(false)

  async function load() {
    loading.value = true
    error.value = null
    try {
      allProducts.value = await listAllProducts()

      if (testimonialId.value) {
        const data = await getTestimonialForEdit(testimonialId.value)
        if (!data) {
          error.value = 'Testimonial not found.'
          return
        }
        Object.assign(form, {
          author_name: data.author_name,
          author_title: data.author_title ?? '',
          author_avatar_url: data.author_avatar_url ?? '',
          product_id: data.product_id ?? null,
          rating: data.rating ?? null,
          quote: data.quote ?? '',
          sort_order: data.sort_order ?? 0,
          is_published: data.is_published
        })
      }
    } catch (err: any) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }
  onMounted(load)

  async function handleAvatarUpload(e: Event) {
    const file = (e.target as HTMLInputElement)?.files?.[0]
    if (!file) return
    uploadingAvatar.value = true
    try {
      form.author_avatar_url = await uploadProductMedia(file)
    } catch (err: any) {
      notify(err.message || 'Failed to upload avatar', { type: 'error' })
    } finally {
      uploadingAvatar.value = false
    }
  }

  async function handleSave() {
    saving.value = true
    error.value = null
    try {
      const payload = { ...form }
      if (isNew.value) {
        const created = await createTestimonial(payload)
        testimonialId.value = created.id
        navigateTo(`/admin/testimonials/${created.id}/edit`, { replace: true })
        await load()
      } else {
        await updateTestimonial(testimonialId.value!, payload)
      }
      notify('Testimonial saved successfully', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to save testimonial', { type: 'error' })
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
