<template>
  <section class="section-pad onboarding">
    <Container>
      <div v-if="!productMeta" class="unknown-state text-center" data-aos="fade-up">
        <Icon name="mdi-compass-off-outline" size="44" />
        <p class="section-sub">{{ t('onboarding.unknown_product') }}</p>
        <Button as="NuxtLink" to="/get-started">{{ t('onboarding.start_over') }}</Button>
      </div>

      <template v-else>
        <div v-if="step !== 'success'" class="header text-center" data-aos="fade-up">
          <h1 class="section-title">{{ t('onboarding.title', { name: productMeta.name }) }}</h1>
          <p class="section-sub header-sub">{{ t('onboarding.sub', { name: productMeta.name }) }}</p>
        </div>

        <!-- Success screen -->
        <div v-if="step === 'success'" class="success-card text-center" data-aos="fade-up">
          <Icon name="mdi-check-circle" size="56" color="success" />
          <h2 class="success-title">{{ t('onboarding.success_title', { name: productMeta.name }) }}</h2>
          <p class="section-sub success-sub">{{ t('onboarding.success_sub') }}</p>
          <Button as="a" size="lg" :href="loginUrl ?? undefined">
            {{ t('onboarding.go_to_login', { name: productMeta.name }) }}
          </Button>
        </div>

        <!-- Wizard -->
        <form v-else class="wizard-card" data-aos="fade-up" @submit.prevent="handleSubmit">
          <div class="step-tabs">
            <span :class="['step-tab', { active: step === 'business' }]">1. {{ t('onboarding.step_business') }}</span>
            <span :class="['step-tab', { active: step === 'owner' }]">2. {{ t('onboarding.step_owner') }}</span>
          </div>

          <Alert v-if="error" variant="destructive" class="mb-4">
            <AlertDescription>{{ error }}</AlertDescription>
          </Alert>

          <div v-if="step === 'business'" class="step-fields">
            <div class="field">
              <Label for="business_name">{{ t('onboarding.business_name') }}</Label>
              <Input
                id="business_name"
                v-model="form.business_name"
                :placeholder="t('onboarding.business_name_placeholder')"
                :class="fieldError('name') ? 'border-destructive' : ''"
                required
              />
              <p v-if="fieldError('name')" class="field-error">{{ fieldError('name') }}</p>
            </div>

            <div v-if="productMeta.needsBusinessType" class="field">
              <Label>{{ t('onboarding.business_type') }}</Label>
              <Select v-model="form.business_type_id">
                <SelectTrigger class="w-full" :disabled="loadingBusinessTypes" :class="fieldError('business_type_id') ? 'border-destructive' : ''">
                  <SelectValue :placeholder="t('onboarding.business_type')" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="bt in businessTypes" :key="bt.id" :value="bt.id">{{ bt.name }}</SelectItem>
                </SelectContent>
              </Select>
              <p v-if="fieldError('business_type_id')" class="field-error">{{ fieldError('business_type_id') }}</p>
            </div>

            <div class="field">
              <Label for="phone">{{ t('auth.register.phone') }}</Label>
              <Input id="phone" v-model="form.phone" :placeholder="t('auth.register.phone_placeholder')" />
            </div>
          </div>

          <div v-else class="step-fields">
            <div class="two-col">
              <div class="field">
                <Label for="owner_first_name">{{ t('auth.register.first_name') }}</Label>
                <Input
                  id="owner_first_name"
                  v-model="form.owner_first_name"
                  :placeholder="t('auth.register.first_name_placeholder')"
                  required
                />
              </div>
              <div class="field">
                <Label for="owner_last_name">{{ t('auth.register.last_name') }}</Label>
                <Input
                  id="owner_last_name"
                  v-model="form.owner_last_name"
                  :placeholder="t('auth.register.last_name_placeholder')"
                  required
                />
              </div>
            </div>

            <div class="field">
              <Label for="email">{{ t('auth.register.email') }}</Label>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                :placeholder="t('auth.register.email_placeholder')"
                :class="(fieldError('email') || fieldError('owner_email')) ? 'border-destructive' : ''"
                required
              />
              <p v-if="fieldError('email') || fieldError('owner_email')" class="field-error">
                {{ fieldError('email') || fieldError('owner_email') }}
              </p>
            </div>

            <div class="field">
              <Label for="password">{{ t('auth.register.password') }}</Label>
              <Input
                id="password"
                v-model="form.password"
                type="password"
                :placeholder="t('auth.register.password_placeholder')"
                :class="(fieldError('password') || fieldError('owner_password')) ? 'border-destructive' : ''"
                required
              />
              <p v-if="fieldError('password') || fieldError('owner_password')" class="field-error">
                {{ fieldError('password') || fieldError('owner_password') }}
              </p>
            </div>

            <div class="field">
              <Label for="password_confirmation">{{ t('auth.register.confirm_password') }}</Label>
              <Input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                :placeholder="t('auth.register.confirm_password_placeholder')"
                required
              />
            </div>
          </div>

          <div class="step-actions">
            <Button v-if="step === 'owner'" type="button" variant="ghost" @click="step = 'business'">
              {{ t('onboarding.back') }}
            </Button>
            <div class="flex-1" />
            <Button v-if="step === 'business'" type="button" @click="goToOwnerStep">
              {{ t('onboarding.next') }}
            </Button>
            <Button v-else type="submit" :disabled="submitting">
              <Icon v-if="submitting" name="mdi-loading" size="16" class="animate-spin" />
              {{ submitting ? t('onboarding.submitting') : t('onboarding.submit') }}
            </Button>
          </div>
        </form>
      </template>
    </Container>
  </section>
</template>

<script setup lang="ts">
  import { Alert, AlertDescription } from '~/components/ui/alert'
  import { Button } from '~/components/ui/button'
  import { Input } from '~/components/ui/input'
  import { Label } from '~/components/ui/label'
  import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '~/components/ui/select'
  import { getOnboardingBusinessTypes, provisionOnboarding } from '~/services/cms/onboarding'

  const { t } = useI18n()
  const route = useRoute()
  const productsStore = useProductsStore()

  const ONBOARDABLE: Record<string, { needsBusinessType: boolean }> = {
    'nexstack-pos': { needsBusinessType: true },
    'studio-management': { needsBusinessType: false }
  }

  const slug = computed(() => String(route.params.slug))
  const storageKey = computed(() => `onboarding_draft_${slug.value}`)

  const productMeta = computed(() => {
    const config = ONBOARDABLE[slug.value]
    if (!config) return null
    const product = productsStore.products.find((p) => p.slug === slug.value)
    return { ...config, name: product?.name || slug.value }
  })

  const step = ref<'business' | 'owner' | 'success'>('business')
  const submitting = ref(false)
  const error = ref<string | null>(null)
  const fieldErrors = ref<Record<string, string[]>>({})
  const loginUrl = ref<string | null>(null)
  const businessTypes = ref<any[]>([])
  const loadingBusinessTypes = ref(false)

  const form = reactive({
    business_name: '',
    business_type_id: null as string | null,
    phone: '',
    owner_first_name: '',
    owner_last_name: '',
    email: '',
    password: '',
    password_confirmation: ''
  })

  function fieldError(key: string) {
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
    } catch (err: any) {
      fieldErrors.value = err.errors || {}
      error.value = err.message || t('onboarding.generic_error')
    } finally {
      submitting.value = false
    }
  }

  // Awaited (not onMounted) so the page's heading (product name) is correct
  // in the server-rendered HTML. The draft/localStorage restore and
  // business-types fetch stay client-only (onMounted) — they're inherently
  // browser-only (localStorage) or only needed once the form is interactive.
  await useAsyncData(
    () => `onboarding-products-${slug.value}`,
    async () => {
      await productsStore.fetchProducts()
      return true
    }
  )

  onMounted(async () => {
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
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
    box-shadow: 0 14px 32px color-mix(in srgb, var(--foreground) 7%, transparent);
  }

  .step-tabs {
    display: flex;
    gap: 20px;
    margin-bottom: 24px;
    font-size: 0.82rem;
    font-weight: 700;
    color: color-mix(in srgb, var(--foreground) 40%, transparent);
  }
  .step-tab.active {
    color: var(--primary);
  }

  .step-fields {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  .field {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .field-error {
    font-size: 0.75rem;
    color: var(--destructive);
    margin: 0;
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
    color: color-mix(in srgb, var(--foreground) 60%, transparent);
  }
</style>
