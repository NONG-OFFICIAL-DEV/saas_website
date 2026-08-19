<template>
  <section class="section-pad onboarding">
    <v-container>
      <div v-if="!productMeta" class="unknown-state text-center" data-aos="fade-up">
        <v-icon icon="mdi-compass-off-outline" size="44" />
        <p class="section-sub">{{ t('onboarding.unknown_product') }}</p>
        <v-btn color="primary" rounded="lg" to="/get-started">{{ t('onboarding.start_over') }}</v-btn>
      </div>

      <template v-else>
        <div v-if="step !== 'success'" class="header text-center" data-aos="fade-up">
          <h1 class="section-title">{{ t('onboarding.title', { name: productMeta.name }) }}</h1>
          <p class="section-sub header-sub">{{ t('onboarding.sub', { name: productMeta.name }) }}</p>
        </div>

        <!-- Success screen -->
        <div v-if="step === 'success'" class="success-card text-center" data-aos="fade-up">
          <v-icon icon="mdi-check-circle" size="56" color="success" />
          <h2 class="success-title">{{ t('onboarding.success_title', { name: productMeta.name }) }}</h2>
          <p class="section-sub success-sub">{{ t('onboarding.success_sub') }}</p>
          <v-btn color="primary" size="large" rounded="lg" :href="loginUrl">
            {{ t('onboarding.go_to_login', { name: productMeta.name }) }}
          </v-btn>
        </div>

        <!-- Wizard -->
        <form v-else class="wizard-card" data-aos="fade-up" @submit.prevent="handleSubmit">
          <div class="step-tabs">
            <span :class="['step-tab', { active: step === 'business' }]">1. {{ t('onboarding.step_business') }}</span>
            <span :class="['step-tab', { active: step === 'owner' }]">2. {{ t('onboarding.step_owner') }}</span>
          </div>

          <v-alert v-if="error" type="error" variant="tonal" rounded="lg" class="mb-4">{{ error }}</v-alert>

          <div v-if="step === 'business'" class="step-fields">
            <v-text-field
              v-model="form.business_name"
              :label="t('onboarding.business_name')"
              :placeholder="t('onboarding.business_name_placeholder')"
              :error-messages="fieldError('name')"
              required
            />
            <v-select
              v-if="productMeta.needsBusinessType"
              v-model="form.business_type_id"
              :label="t('onboarding.business_type')"
              :items="businessTypes"
              item-title="name"
              item-value="id"
              :loading="loadingBusinessTypes"
              :error-messages="fieldError('business_type_id')"
              required
            />
            <v-text-field
              v-model="form.phone"
              :label="t('auth.register.phone')"
              :placeholder="t('auth.register.phone_placeholder')"
            />
          </div>

          <div v-else class="step-fields">
            <div class="two-col">
              <v-text-field
                v-model="form.owner_first_name"
                :label="t('auth.register.first_name')"
                :placeholder="t('auth.register.first_name_placeholder')"
                required
              />
              <v-text-field
                v-model="form.owner_last_name"
                :label="t('auth.register.last_name')"
                :placeholder="t('auth.register.last_name_placeholder')"
                required
              />
            </div>
            <v-text-field
              v-model="form.email"
              type="email"
              :label="t('auth.register.email')"
              :placeholder="t('auth.register.email_placeholder')"
              :error-messages="fieldError('email') || fieldError('owner_email')"
              required
            />
            <v-text-field
              v-model="form.password"
              type="password"
              :label="t('auth.register.password')"
              :placeholder="t('auth.register.password_placeholder')"
              :error-messages="fieldError('password') || fieldError('owner_password')"
              required
            />
            <v-text-field
              v-model="form.password_confirmation"
              type="password"
              :label="t('auth.register.confirm_password')"
              :placeholder="t('auth.register.confirm_password_placeholder')"
              required
            />
          </div>

          <div class="step-actions">
            <v-btn v-if="step === 'owner'" variant="text" rounded="lg" @click="step = 'business'">
              {{ t('onboarding.back') }}
            </v-btn>
            <v-spacer />
            <v-btn v-if="step === 'business'" color="primary" rounded="lg" @click="goToOwnerStep">
              {{ t('onboarding.next') }}
            </v-btn>
            <v-btn v-else type="submit" color="primary" rounded="lg" :loading="submitting">
              {{ submitting ? t('onboarding.submitting') : t('onboarding.submit') }}
            </v-btn>
          </div>
        </form>
      </template>
    </v-container>
  </section>
</template>

<script setup>
  import { computed, onMounted, reactive, ref, watch } from 'vue'
  import { useRoute } from 'vue-router'
  import { useI18n } from 'vue-i18n'
  import { useProductsStore } from '@/stores/products'
  import { getOnboardingBusinessTypes, provisionOnboarding } from '@/services/onboarding'

  const { t } = useI18n()
  const route = useRoute()
  const productsStore = useProductsStore()

  const ONBOARDABLE = {
    'nexstack-pos': { needsBusinessType: true },
    'studio-management': { needsBusinessType: false }
  }

  const slug = computed(() => route.params.slug)
  const storageKey = computed(() => `onboarding_draft_${slug.value}`)

  const productMeta = computed(() => {
    const config = ONBOARDABLE[slug.value]
    if (!config) return null
    const product = productsStore.products.find(p => p.slug === slug.value)
    return { ...config, name: product?.name || slug.value }
  })

  const step = ref('business')
  const submitting = ref(false)
  const error = ref(null)
  const fieldErrors = ref({})
  const loginUrl = ref(null)
  const businessTypes = ref([])
  const loadingBusinessTypes = ref(false)

  const form = reactive({
    business_name: '',
    business_type_id: null,
    phone: '',
    owner_first_name: '',
    owner_last_name: '',
    email: '',
    password: '',
    password_confirmation: ''
  })

  function fieldError(key) {
    return fieldErrors.value[key]?.[0]
  }

  function loadDraft() {
    try {
      const saved = JSON.parse(localStorage.getItem(storageKey.value) || 'null')
      if (saved) Object.assign(form, saved)
    } catch {
      // corrupt/old draft — ignore and start fresh
    }
  }

  function saveDraft() {
    const { password, password_confirmation, ...rest } = form
    localStorage.setItem(storageKey.value, JSON.stringify(rest))
  }

  watch(form, saveDraft, { deep: true })

  function goToOwnerStep() {
    error.value = null
    step.value = 'owner'
  }

  async function handleSubmit() {
    submitting.value = true
    error.value = null
    fieldErrors.value = {}
    try {
      const result = await provisionOnboarding({
        product_slug: slug.value,
        plan_code: route.query.plan || null,
        business_name: form.business_name,
        business_type_id: form.business_type_id,
        owner_first_name: form.owner_first_name,
        owner_last_name: form.owner_last_name,
        email: form.email,
        phone: form.phone || null,
        password: form.password,
        password_confirmation: form.password_confirmation
      })
      loginUrl.value = result.login_url
      localStorage.removeItem(storageKey.value)
      step.value = 'success'
    } catch (err) {
      fieldErrors.value = err.errors || {}
      error.value = err.message || t('onboarding.generic_error')
    } finally {
      submitting.value = false
    }
  }

  onMounted(async () => {
    await productsStore.fetchProducts()
    loadDraft()
    if (productMeta.value?.needsBusinessType) {
      loadingBusinessTypes.value = true
      try {
        businessTypes.value = await getOnboardingBusinessTypes()
      } catch {
        businessTypes.value = []
      } finally {
        loadingBusinessTypes.value = false
      }
    }
  })
</script>

<style scoped>
  .onboarding {
    padding-top: 120px;
    min-height: 70vh;
  }
  .header {
    max-width: 620px;
    margin: 0 auto 40px;
  }
  .header-sub {
    max-width: 480px;
    margin: 0 auto;
  }

  .wizard-card,
  .success-card {
    max-width: 520px;
    margin: 0 auto;
    padding: 36px;
    border-radius: 22px;
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-theme-on-surface), 0.07);
    box-shadow: 0 14px 32px rgba(var(--v-theme-on-surface), 0.07);
  }

  .step-tabs {
    display: flex;
    gap: 20px;
    margin-bottom: 24px;
    font-size: 0.82rem;
    font-weight: 700;
    color: rgba(var(--v-theme-on-surface), 0.4);
  }
  .step-tab.active {
    color: rgb(var(--v-theme-primary));
  }

  .step-fields {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .two-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }
  @media (max-width: 500px) {
    .two-col {
      grid-template-columns: 1fr;
      gap: 0;
    }
  }

  .step-actions {
    display: flex;
    align-items: center;
    margin-top: 12px;
  }

  .success-title {
    font-size: 1.4rem;
    font-weight: 800;
    margin: 16px 0 8px;
  }
  .success-sub {
    max-width: 420px;
    margin: 0 auto 28px;
  }

  .unknown-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
    padding: 60px 0;
    color: rgba(var(--v-theme-on-surface), 0.6);
  }
</style>
