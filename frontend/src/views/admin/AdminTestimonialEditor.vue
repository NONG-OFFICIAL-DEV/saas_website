<template>
  <div>
    <div class="editor-header">
      <div>
        <router-link to="/admin/testimonials" class="back-link">
          <v-icon icon="mdi-arrow-left" size="16" /> Back to testimonials
        </router-link>
        <h1 class="editor-title">{{ isNew ? 'New testimonial' : form.author_name || 'Edit testimonial' }}</h1>
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
            <v-text-field v-model="form.author_name" label="Author name" required />
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.author_title" label="Author title" hint="e.g. Owner, Golden Spoon Restaurant" persistent-hint />
          </v-col>

          <v-col cols="12" sm="6">
            <v-text-field v-model="form.author_avatar_url" label="Avatar URL" />
          </v-col>
          <v-col cols="12" sm="6">
            <v-file-input
              label="Upload avatar"
              accept="image/*"
              prepend-icon=""
              :loading="uploadingAvatar"
              @change="handleAvatarUpload"
            />
          </v-col>

          <v-col cols="12" sm="6">
            <v-select
              v-model="form.product_id"
              label="About which product? (optional)"
              :items="allProducts"
              item-title="name"
              item-value="id"
              clearable
            />
          </v-col>
          <v-col cols="12" sm="6">
            <v-select
              v-model="form.rating"
              label="Rating (optional)"
              :items="[1, 2, 3, 4, 5]"
              clearable
            />
          </v-col>

          <v-col cols="12">
            <v-textarea v-model="form.quote" label="Quote" rows="4" auto-grow required />
          </v-col>

          <v-col cols="12" sm="6">
            <v-text-field v-model.number="form.sort_order" type="number" label="Sort order" />
          </v-col>
          <v-col cols="12" sm="6" class="d-flex align-center">
            <v-switch v-model="form.is_published" color="primary" label="Published (visible on the public site)" hide-details />
          </v-col>
        </v-row>
      </section>
    </template>
  </div>
</template>

<script setup>
  import { computed, onMounted, reactive, ref } from 'vue'
  import { useRoute, useRouter } from 'vue-router'
  import { getTestimonialForEdit, createTestimonial, updateTestimonial } from '@/services/adminTestimonials'
  import { listAllProducts, uploadProductMedia } from '@/services/adminProducts'
  import { useNotif } from '@/composables/useNotif'

  const notify = useNotif()

  const route = useRoute()
  const router = useRouter()

  const isNew = computed(() => !testimonialId.value)
  const testimonialId = ref(route.params.id || null)

  const form = reactive({
    author_name: '',
    author_title: '',
    author_avatar_url: '',
    product_id: null,
    rating: null,
    quote: '',
    sort_order: 0,
    is_published: false
  })

  const allProducts = ref([])
  const loading = ref(false)
  const saving = ref(false)
  const error = ref(null)
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
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }
  onMounted(load)

  async function handleAvatarUpload(e) {
    const file = e?.target?.files?.[0] ?? e?.[0]
    if (!file) return
    uploadingAvatar.value = true
    try {
      form.author_avatar_url = await uploadProductMedia(file)
    } catch (err) {
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
        router.replace({ name: 'admin-testimonial-edit', params: { id: created.id } })
        await load()
      } else {
        await updateTestimonial(testimonialId.value, payload)
      }
      notify('Testimonial saved successfully', { type: 'success' })
    } catch (err) {
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
