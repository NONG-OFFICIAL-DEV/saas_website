<template>
  <div class="step-business pa-4">
    <v-form ref="formRef" @submit.prevent>
      <v-row dense>
        <v-col col="12">
          <!-- Logo upload -->
          <div class="form-group">
            <label class="field-label">
              {{ t('auth.register.logo') || 'Store Logo' }}
            </label>
            <div
              class="logo-upload"
              @click="fileInput.click()"
              @dragover.prevent
              @drop.prevent="handleDrop"
            >
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                class="d-none"
                @change="handleFileChange"
              />
              <template v-if="logoPreview">
                <img :src="logoPreview" class="logo-img" />
                <button
                  type="button"
                  class="logo-remove"
                  @click.stop="removeLogo"
                >
                  <v-icon size="14">mdi-close</v-icon>
                </button>
              </template>
              <template v-else>
                <v-icon size="24" color="primary">
                  mdi-image-plus-outline
                </v-icon>
                <span class="logo-hint">
                  {{ t('auth.register.logo_upload') || 'Tap to upload' }}
                </span>
              </template>
            </div>
            <p v-if="logoError" class="field-error">{{ logoError }}</p>
          </div>
        </v-col>
      </v-row>
      <v-row dense>
        <v-col col="12">
          <!-- Store name -->
          <label class="field-label">
            {{ t('auth.register.store_name') }}
          </label>
          <v-text-field
            v-model="form.storeName"
            :rules="[rules.required]"
            :error-messages="serverError('name')"
            :placeholder="t('auth.register.store_name_placeholder')"
            prepend-inner-icon="mdi-store-outline"
            variant="outlined"
            density="comfortable"
            rounded="lg"
          />
        </v-col>
      </v-row>
      <v-row dense>
        <v-col col="12">
          <!-- Business type -->
          <label class="field-label">{{ t('auth.register.business') }}</label>
          <div v-if="loadingTypes" class="biz-loading">
            <v-progress-circular
              indeterminate
              size="20"
              width="2"
              color="primary"
            />
          </div>
          <div v-else class="biz-grid">
            <button
              v-for="biz in businessTypes"
              :key="biz.id"
              type="button"
              class="biz-tile"
              :class="{ selected: form.bizType === biz.id }"
              @click="selectBizType(biz.id)"
            >
              <span>{{ biz.name }}</span>
            </button>
          </div>
          <p v-if="showBizError" class="field-error">
            {{ t('auth.validation.required') }}
          </p>
        </v-col>
      </v-row>
      <v-row dense>
        <v-col cols="12" sm="6">
          <!-- Phone + Currency row -->
          <label class="field-label">{{ t('auth.register.phone') }}</label>
          <v-text-field
            v-model="form.phone"
            :rules="[rules.required, rules.maxPhone, rules.phone]"
            :error-messages="serverError('owner_phone')"
            :placeholder="t('auth.register.phone_placeholder')"
            prepend-inner-icon="mdi-phone-outline"
            variant="outlined"
            density="comfortable"
            rounded="lg"
            @input="sanitizePhone"
          />
        </v-col>
        <v-col cols="12" sm="6">
          <label class="field-label">
            {{ t('auth.register.currency') || 'Currency' }}({{
              t('auth.register.currency_hint')
            }})
          </label>
          <div class="currency-toggle">
            <button
              v-for="c in currencies"
              :key="c.value"
              type="button"
              class="currency-btn"
              :class="{ selected: form.currency === c.value }"
              @click="form.currency = c.value"
            >
              {{ c.label }}
            </button>
          </div>
        </v-col>
      </v-row>
      <v-row dense>
        <v-col col="12">
          <!-- Address -->
          <label class="field-label">{{ t('auth.register.address') }}</label>
          <v-text-field
            v-model="form.address"
            :placeholder="t('auth.register.address_placeholder')"
            prepend-inner-icon="mdi-map-marker-outline"
            variant="outlined"
            density="comfortable"
            rounded="lg"
          />
        </v-col>
      </v-row>

      <!-- Navigation -->
      <div class="sub-actions">
        <v-btn
          variant="outlined"
          rounded="lg"
          
          prepend-icon="mdi-arrow-left"
          @click="emit('back')"
        >
          {{ t('auth.register.back') }}
        </v-btn>
        <v-btn
          color="primary"
          
          rounded="lg"
          append-icon="mdi-arrow-right"
          class="next-btn"
          @click="handleNext"
        >
          {{ t('auth.register.next') }}
        </v-btn>
      </div>
    </v-form>
  </div>
</template>

<script setup>
  import { ref, computed } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useRegisterStore } from '@/stores/register'

  const { t } = useI18n()
  const store = useRegisterStore()
  const props = defineProps({
    form: { type: Object, required: true }
  })
  const emit = defineEmits(['next', 'back'])

  const formRef = ref(null) // ← add this
  const showBizError = ref(false) // ← add this
  // Defaults
  if (!props.form.currency) props.form.currency = 'KHR'
  if (props.form.logoUrl === undefined) props.form.logoUrl = null

  const currencies = [
    { value: 'USD', label: 'USD ($)' },
    { value: 'KHR', label: 'KHR (៛)' }
  ]

  // ── Logo ──────────────────────────────────────────────────────
  const fileInput = ref(null)
  const logoPreview = ref(props.form.logoUrl || null)
  const logoError = ref('')

  function handleFileChange(e) {
    processFile(e.target.files?.[0])
  }
  function handleDrop(e) {
    processFile(e.dataTransfer.files?.[0])
  }

  function processFile(file) {
    logoError.value = ''
    if (!file) return
    if (!file.type.startsWith('image/')) {
      logoError.value = 'Please upload a valid image.'
      return
    }
    if (file.size > 2 * 1024 * 1024) {
      logoError.value = 'Max size is 2MB.'
      return
    }

    // ✅ Store raw File for upload
    props.form.logoFile = file

    // ✅ Keep base64 only for preview
    const reader = new FileReader()
    reader.onload = e => {
      logoPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }

  function removeLogo() {
    logoPreview.value = null
    props.form.logoUrl = null
    if (fileInput.value) fileInput.value.value = ''
  }

  // ── Business types ────────────────────────────────────────────
  const businessTypes = computed(() => store.businessTypes)
  const loadingTypes = computed(() => store.loadingTypes)

  // ── Validation ────────────────────────────────────────────────
  const rules = {
    required: v => !!v || t('auth.validation.required'),
    phone: v => {
      const phoneRegex = /^(\+?[1-9]\d{6,14}|0\d{7,14})$/
      return (
        phoneRegex.test(v?.replace(/[\s\-().]/g, '')) ||
        t('auth.validation.phone_invalid')
      )
    },
    maxPhone: v => {
      const digits = v?.replace(/[\s\-().+]/g, '') || ''
      return digits.length <= 10 || t('auth.validation.phone_max') // E.164 standard max
    }
  }
  const sanitizePhone = () => {
    let cleaned = props.form.phone.replace(/[^\d\s\-().+]/g, '')

    // Hard cap: count digits only, stop at 15
    const digitsOnly = cleaned.replace(/[\s\-().+]/g, '')
    if (digitsOnly.length > 15) {
      // Trim the raw value to enforce the limit
      let count = 0
      cleaned = cleaned
        .split('')
        .filter(char => {
          if (/\d/.test(char)) count++
          return count <= 15 || !/\d/.test(char)
        })
        .join('')
    }

    props.form.phone = cleaned
  }
  function serverError(field) {
    return store.fieldError(field) ?? undefined
  }
  function selectBizType(id) {
    props.form.bizType = id
    showBizError.value = false // ✅ clear error on selection
  }
  // StepBusiness.vue
  async function handleNext() {
    showBizError.value = !props.form.bizType

    const { valid } = await formRef.value.validate() // triggers all v-text-field rules

    if (!valid || !props.form.bizType) return

    emit('next')
  }
</script>

<style scoped>
  .step-business {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  /* Logo */
  .logo-upload {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 5px;
    height: 84px;
    border: 1.5px dashed rgba(var(--v-theme-primary), 0.4);
    border-radius: 12px;
    cursor: pointer;
    transition:
      background 0.15s,
      border-color 0.15s;
  }
  .logo-upload:hover {
    background: rgba(var(--v-theme-primary), 0.04);
    border-color: rgba(var(--v-theme-primary), 0.7);
  }
  .logo-hint {
    font-size: 0.72rem;
    opacity: 0.55;
    font-weight: 600;
  }
  .logo-img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 1.5px solid rgba(var(--v-theme-primary), 0.25);
  }
  .logo-remove {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: rgb(var(--v-theme-error));
    color: #fff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Currency toggle */
  .currency-toggle {
    display: flex;
    border: 1.5px solid rgba(var(--v-theme-on-surface), 0.15);
    border-radius: 10px;
    overflow: hidden;
    height: 44px;
  }
  .currency-btn {
    flex: 1;
    border: none;
    background: transparent;
    font-size: 0.78rem;
    font-weight: 700;
    cursor: pointer;
    color: rgba(var(--v-theme-on-surface), 0.55);
    transition: all 0.15s;
  }
  .currency-btn:not(:last-child) {
    border-right: 1.5px solid rgba(var(--v-theme-on-surface), 0.15);
  }
  .currency-btn.selected {
    background: rgba(var(--v-theme-primary), 0.1);
    color: rgb(var(--v-theme-primary));
  }

  /* Biz grid */
  .biz-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: 8px;
    margin-top: 4px;
  }
  .biz-tile {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 6px;
    border-radius: 12px;
    border: 1.5px solid rgba(var(--v-theme-on-surface), 0.1);
    background: transparent;
    cursor: pointer;
    font-size: 0.72rem;
    font-weight: 600;
    color: rgba(var(--v-theme-on-surface), 0.6);
    transition: all 0.15s;
  }
  .biz-tile:hover {
    border-color: rgba(var(--v-theme-primary), 0.4);
    color: rgb(var(--v-theme-primary));
    background: rgba(var(--v-theme-primary), 0.04);
  }
  .biz-tile.selected {
    border-color: rgb(var(--v-theme-primary));
    border-width: 2px;
    color: rgb(var(--v-theme-primary));
    background: rgba(var(--v-theme-primary), 0.07);
    box-shadow: 0 0 0 3px rgba(var(--v-theme-primary), 0.1);
  }
  .biz-loading {
    display: flex;
    align-items: center;
    gap: 8px;
    opacity: 0.55;
    padding: 10px 0;
  }

  /* Layout */
  .form-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }
  @media (max-width: 500px) {
    .form-row {
      grid-template-columns: 1fr;
    }
    .biz-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  .field-label {
    font-size: 0.75rem;
    font-weight: 700;
    opacity: 0.55;
  }
  .field-error {
    font-size: 0.72rem;
    color: rgb(var(--v-theme-error));
    margin-top: 2px;
  }

  .sub-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 4px;
  }
  .next-btn {
    box-shadow: 0 6px 20px rgba(var(--v-theme-primary), 0.28) !important;
  }
</style>
