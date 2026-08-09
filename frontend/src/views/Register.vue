<template>
  <div class="reg-layout">

    <main class="reg-page">
    <!-- ══════ One persistent shell for all 3 steps ══════ -->
    <div class="form-card" :style="{ maxWidth: cardMaxWidth }">
      <!-- Persistent 3-step header -->
      <div class="stepper" role="tablist">
        <template v-for="(s, i) in steps" :key="s.key">
          <button
            class="step-btn"
            :class="{ active: pageStep === s.key, done: stepIndex(pageStep) > i }"
            role="tab"
            :aria-selected="pageStep === s.key"
            :disabled="stepIndex(pageStep) <= i"
            @click="goToStep(s.key)"
          >
            <span class="step-circle">
              <v-icon v-if="stepIndex(pageStep) > i" icon="mdi-check" size="11" />
              <span v-else>{{ i + 1 }}</span>
            </span>
            {{ s.label }}
          </button>
          <div
            v-if="i < steps.length - 1"
            class="step-connector"
            :class="{ done: stepIndex(pageStep) > i }"
          />
        </template>
      </div>

      <!-- Plan summary row — shown once a plan is in context -->
      <div v-if="pageStep !== 'plan'" class="plan-row">
        <div class="plan-row-left">
          <div class="plan-icon" :class="`icon-${chosenPlan?.code}`">
            <v-icon
              :icon="
                PLAN_UI[chosenPlan?.code]?.icon ?? 'mdi-help-circle-outline'
              "
              size="18"
            />
          </div>
          <div>
            <div class="plan-name">
              {{ chosenPlan?.name }}
              <span class="plan-cycle">· {{ selectionSummary.cycleLabel }}</span>
            </div>
            <div class="plan-price">
              {{
                selectionSummary.total > 0
                  ? `$${selectionSummary.total}`
                  : t('auth.register.free')
              }}
            </div>
          </div>
        </div>
        <button class="change-btn" @click="handleChangePlan">
          <v-icon icon="mdi-pencil-outline" size="12" />
          {{
            cameFromPricing
              ? t('auth.register.back_to_pricing')
              : t('auth.register.change_plan')
          }}
        </button>
      </div>

      <!-- Step 1: Plan picker -->
      <div v-if="pageStep === 'plan'" class="plan-step-wrap">
        <PlanPicker
          v-model:model-plan="selectedPlanId"
          :plans="store.plans"
          @update:selection="onPlanSelection"
          @next="pageStep = 'business'"
        />
      </div>

      <!-- Step 2: Business info -->
      <StepBusiness
        v-if="pageStep === 'business'"
        :form="businessForm"
        @next="pageStep = 'account'"
        @back="handleChangePlan"
      />

      <!-- Step 3: Account info -->
      <StepAccount
        v-if="pageStep === 'account'"
        :form="accountForm"
        :loading="store.loading"
        :api-error="store.error"
        @back="pageStep = 'business'"
        @submit="handleSubmit"
      />
    </div>
    </main>
  </div>
</template>

<script setup>
  import { ref, reactive, computed, onMounted } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useRouter, useRoute } from 'vue-router'

  import { useRegisterStore } from '@/stores/register'
  import PlanPicker from '@/components/register/PlanPicker.vue'
  import StepBusiness from '@/components/register/StepBusiness.vue'
  import StepAccount from '@/components/register/StepAccount.vue'

  const { t } = useI18n()
  const router = useRouter()
  const route = useRoute()
  const store = useRegisterStore()

  // ── Plan UI metadata (static, keyed by plan code) ─────────────────────────────
  const PLAN_UI = {
    free: { icon: 'mdi-gift-outline', color: 'grey' },
    starter: { icon: 'mdi-star-half-full', color: 'blue' },
    pro: { icon: 'mdi-star', color: 'primary' },
    enterprise: { icon: 'mdi-crown', color: 'warning' }
  }

  // ── Entry: came from pricing page (/pricing?plan=pro&cycle=3) ─────────────────
  const cameFromPricing = route.query.from === 'pricing'

  // ── Page step ─────────────────────────────────────────────────────────────────
  const pageStep = ref(cameFromPricing ? 'business' : 'plan')

  // ── 3-step header (persistent across the whole flow) ──────────────────────────
  const STEP_ORDER = ['plan', 'business', 'account']
  const steps = computed(() => [
    { key: 'plan', label: t('auth.register.step1_label') },
    { key: 'business', label: t('auth.register.tab_business') },
    { key: 'account', label: t('auth.register.tab_account') }
  ])
  const stepIndex = key => STEP_ORDER.indexOf(key)

  function goToStep(key) {
    if (stepIndex(key) >= stepIndex(pageStep.value)) return // no forward-jump
    if (key === 'plan') {
      handleChangePlan()
      return
    }
    pageStep.value = key
  }

  // ── Card width — wider while picking a plan, narrower for the forms ───────────
  const cardMaxWidth = computed(() => (pageStep.value === 'plan' ? '1080px' : '640px'))

  // ── Selection state — driven by PlanPicker's update:selection emit ─────────────
  const selectedPlanId = ref(route.query.plan ?? null)

  const selection = ref({
    plan_id: selectedPlanId.value,
    billing_cycle_id: null,
    billing_months: Number(route.query.months ?? 1),
    discount_percent: 0,
    cycle_label: 'Monthly'
  })

  function onPlanSelection(payload) {
    if (!payload) return
    selection.value = payload
    selectedPlanId.value = payload.plan_id
  }

  // ── Derived: chosen plan object ───────────────────────────────────────────────
  const chosenPlan = computed(
    () => store.plans?.find?.(p => p.id === selectedPlanId.value) ?? null
  )

  // ── Header summary ────────────────────────────────────────────────────────────
  const selectionSummary = computed(() => {
    const base = parseFloat(chosenPlan.value?.price_usd ?? 0)
    const months = selection.value.billing_months
    const discount = selection.value.discount_percent / 100
    const total = (base * months * (1 - discount)).toFixed(2)

    return {
      cycleLabel: selection.value.cycle_label || 'Monthly',
      total: parseFloat(total)
    }
  })

  // ── Form state ────────────────────────────────────────────────────────────────
  const businessForm = reactive({
    storeName: '',
    bizType: null,
    phone: '',
    address: '',
    currency: 'KHR',
    logoFile: null,
    logoUrl: null
  })

  const accountForm = reactive({
    firstName: '',
    lastName: '',
    email: '',
    password: '',
    confirmPassword: '',
    agreed: false
  })

  // ── Init ──────────────────────────────────────────────────────────────────────
  onMounted(() => {
    store.fetchBusinessTypes()
    store.fetchPlans()
  })

  // ── Navigation ────────────────────────────────────────────────────────────────
  function handleChangePlan() {
    if (cameFromPricing) {
      router.push({ path: '/', hash: '#pricing' })
    } else {
      pageStep.value = 'plan'
    }
  }

  // ── Submit ────────────────────────────────────────────────────────────────────
  async function handleSubmit() {
    const result = await store.register({
      // Plan & cycle — mapped to what the API expects
      plan: selection.value.plan_id,
      billing_months: selection.value.billing_months, // 1 | 3 | 6 | 12
      billing_cycle_id: selection.value.billing_cycle_id, // ← add this

      business: businessForm,
      account: accountForm
    })

    if (result.success) {
      const isProd = import.meta.env.VITE_APP_MODE === 'Production'
      const tab = window.open('', '_blank')
      tab.location.href = isProd
        ? 'https://admin.nexstacktech.com'
        : 'http://localhost:5173'
      window.location.href = '/'
    }
  }
</script>

<style scoped>
  /* ── Page wrapper ── */
  .reg-layout {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }
  .reg-page {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    padding: calc(68px + 32px) 16px 60px;
    width: 100%;
  }

  /* ── Form card ── */
  .form-card {
    width: 100%;
    max-width: 640px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
    border-radius: 18px;
    background: rgb(var(--v-theme-surface));
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: max-width 0.2s ease;
  }

  /* ── Plan summary row (top of card) ── */
  .plan-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 18px 24px;
    border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.07);
    background: rgba(var(--v-theme-on-surface), 0.02);
  }
  .plan-row-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }
  .plan-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .icon-free {
    background: rgba(var(--v-theme-on-surface), 0.06);
    color: rgba(var(--v-theme-on-surface), 0.45);
  }
  .icon-starter {
    background: rgba(55, 138, 221, 0.1);
    color: #378add;
  }
  .icon-pro {
    background: rgba(var(--v-theme-primary), 0.1);
    color: rgb(var(--v-theme-primary));
  }
  .icon-enterprise {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
  }

  .plan-name {
    font-size: 0.92rem;
    font-weight: 800;
    letter-spacing: -0.2px;
  }
  .plan-cycle {
    font-weight: 500;
    opacity: 0.5;
  }
  .plan-price {
    font-size: 0.78rem;
    font-weight: 700;
    color: rgb(var(--v-theme-primary));
    margin-top: 1px;
  }

  .change-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    font-size: 0.72rem;
    font-weight: 700;
    color: rgb(var(--v-theme-primary));
    white-space: nowrap;
  }
  .change-btn:hover {
    text-decoration: underline;
  }

  /* ── Plan step content ── */
  .plan-step-wrap {
    padding: 32px 28px;
  }

  /* ── Stepper ── */
  .stepper {
    display: flex;
    align-items: center;
    padding: 14px 24px;
    border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.07);
  }
  .step-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: none;
    border: none;
    padding: 0;
    cursor: default;
    font-size: 0.78rem;
    font-weight: 600;
    color: rgba(var(--v-theme-on-surface), 0.35);
    transition: color 0.15s;
    white-space: nowrap;
  }
  .step-btn.active {
    color: rgba(var(--v-theme-on-surface), 1);
  }
  .step-btn.done {
    color: #16a34a;
    cursor: pointer;
  }
  .step-btn.done:hover {
    opacity: 0.8;
  }
  .step-circle {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 1.5px solid currentColor;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 700;
    flex-shrink: 0;
    transition:
      background 0.2s,
      border-color 0.2s;
  }
  .step-btn.active .step-circle {
    background: rgb(var(--v-theme-primary));
    border-color: rgb(var(--v-theme-primary));
    color: #fff;
  }
  .step-btn.done .step-circle {
    background: #16a34a;
    border-color: #16a34a;
    color: #fff;
  }
  .step-connector {
    flex: 1;
    height: 1.5px;
    background: rgba(var(--v-theme-on-surface), 0.1);
    margin: 0 12px;
    transition: background 0.3s;
  }
  .step-connector.done {
    background: #16a34a;
  }

  @media (max-width: 500px) {
    .form-card {
      border-radius: 14px;
    }
    .stepper {
      padding: 12px 16px;
      overflow-x: auto;
      /* Hint that the step row scrolls when all 3 steps don't fit. */
      -webkit-mask-image: linear-gradient(
        to right,
        transparent 0%,
        black 4%,
        black 88%,
        transparent 100%
      );
      mask-image: linear-gradient(
        to right,
        transparent 0%,
        black 4%,
        black 88%,
        transparent 100%
      );
    }
    .plan-step-wrap {
      padding: 20px 14px;
    }
  }
</style>
