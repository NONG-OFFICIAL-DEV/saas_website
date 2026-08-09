<template>
  <div class="plan-picker">
    <div class="text-center mb-2">
      <div class="text-h6 font-weight-black">
        {{ t('auth.register.pick_plan_title') }}
      </div>
      <div class="text-body-2 text-medium-emphasis mt-1">
        {{ t('auth.register.pick_plan_sub') }}
      </div>
    </div>

    <div v-if="!visiblePlans.length" class="plan-grid cols-3">
      <v-skeleton-loader
        v-for="i in 3"
        :key="i"
        type="article"
        rounded="lg"
        height="320"
      />
    </div>
    <!-- ── Billing cycle toggle ── -->
    <div v-if="unifiedCycles.length > 1" class="d-flex justify-center">
      <v-btn-toggle
        v-model="selectedMonths"
        mandatory
        rounded="lg"
        density="compact"
        color="primary"
        border
      >
        <v-btn
          v-for="c in unifiedCycles"
          :key="c.months"
          :value="c.months"
          size="small"
          class="px-4"
        >
          {{ c.label }}
          <v-chip
            v-if="Number(c.discount_percent) > 0"
            class="ms-2"
            color="success"
            size="x-small"
            variant="flat"
          >
            -{{ Number(c.discount_percent).toFixed(0) }}%
          </v-chip>
        </v-btn>
      </v-btn-toggle>
    </div>

    <!-- ── Plan cards ── -->
    <div class="plan-grid" :class="`cols-${visiblePlans.length}`">
      <v-card
        v-for="plan in visiblePlans"
        :key="plan.id"
        rounded="lg"
        :variant="modelPlan === plan.id ? 'tonal' : 'outlined'"
        :color="
          modelPlan === plan.id
            ? PLAN_UI[plan.code]?.color ?? 'primary'
            : undefined
        "
        :disabled="!isPlanAvailableForCycle(plan)"
        class="plan-card pa-4 cursor-pointer overflow-visible"
        :class="{
          'plan-selected': modelPlan === plan.id,
          'plan-featured': plan.popular
        }"
        @click="onSelectPlan(plan)"
      >
        <!-- Popular badge -->
        <v-chip
          v-if="plan.popular"
          class="pop-badge"
          color="primary"
          size="x-small"
          variant="flat"
          prepend-icon="mdi-star"
        >
          {{ t('pricing.popular') }}
        </v-chip>

        <!-- Check indicator -->
        <v-icon
          class="sel-check"
          :icon="
            modelPlan === plan.id ? 'mdi-check-circle' : 'mdi-circle-outline'
          "
          :color="
            modelPlan === plan.id
              ? PLAN_UI[plan.code]?.color ?? 'primary'
              : 'grey-lighten-2'
          "
          size="20"
        />

        <!-- Plan head -->
        <div class="d-flex align-center ga-3 mb-3">
          <v-avatar
            :color="PLAN_UI[plan.code]?.color ?? 'grey'"
            variant="tonal"
            size="38"
            rounded="lg"
          >
            <v-icon
              :icon="PLAN_UI[plan.code]?.icon ?? 'mdi-help-circle-outline'"
              size="19"
            />
          </v-avatar>
          <div>
            <div class="text-body-2 font-weight-black">{{ plan.name }}</div>
            <div class="text-caption text-medium-emphasis">
              {{ plan.tagline ?? PLAN_UI[plan.code]?.tagline }}
            </div>
          </div>
        </div>

        <!-- Price block -->
        <div class="mb-3">
          <template v-if="!isPlanAvailableForCycle(plan)">
            <div
              class="text-caption text-medium-emphasis font-italic d-flex align-center ga-1"
            >
              <v-icon icon="mdi-information-outline" size="13" />
              {{ t('pricing.free_monthly_only') }}
            </div>
          </template>

          <template v-else-if="isFree(plan)">
            <div class="d-flex align-baseline ga-1">
              <span class="text-h5 font-weight-black text-success">$0</span>
              <span class="text-caption text-medium-emphasis">
                / {{ t('pricing.per_month') }}
              </span>
            </div>
            <v-chip
              size="x-small"
              color="primary"
              variant="tonal"
              prepend-icon="mdi-gift-outline"
              class="mt-1"
            >
              14-day trial
            </v-chip>
          </template>

          <template v-else>
            <div class="d-flex align-baseline ga-1">
              <span
                class="text-h5 font-weight-black"
                :class="`text-${PLAN_UI[plan.code]?.color ?? 'primary'}`"
              >
                ${{ getMonthlyPrice(plan) }}
              </span>
              <span class="text-caption text-medium-emphasis">
                / {{ t('pricing.per_month') }}
              </span>
            </div>
            <div
              v-if="selectedMonths > 1"
              class="text-caption text-medium-emphasis mt-1"
            >
              {{
                t('pricing.billed_every_months', {
                  price: getCycleTotal(plan),
                  months: selectedMonths
                })
              }}
            </div>
            <v-chip
              v-if="getSavingsPct(plan) > 0"
              size="x-small"
              color="success"
              variant="tonal"
              prepend-icon="mdi-tag-outline"
              class="mt-1"
            >
              {{ t('pricing.save_pct', { pct: getSavingsPct(plan) }) }}
            </v-chip>
          </template>
        </div>

        <v-divider class="mb-3" />

        <!-- Limits -->
        <div class="d-flex flex-wrap ga-2 mb-3">
          <v-chip
            size="x-small"
            variant="tonal"
            :color="PLAN_UI[plan.code]?.color ?? 'grey'"
          >
            <v-icon start size="11">mdi-account-group-outline</v-icon>
            {{ plan.seats }} {{ plan.seats === 1 ? 'seat' : 'seats' }}
          </v-chip>
          <v-chip
            size="x-small"
            variant="tonal"
            :color="PLAN_UI[plan.code]?.color ?? 'grey'"
          >
            <v-icon start size="11">mdi-database-outline</v-icon>
            {{ plan.storage_gb }}GB
          </v-chip>
          <v-chip
            size="x-small"
            variant="tonal"
            :color="PLAN_UI[plan.code]?.color ?? 'grey'"
          >
            <v-icon start size="11">mdi-api</v-icon>
            {{
              plan.api_limit > 0
                ? plan.api_limit.toLocaleString() + ' API'
                : 'Unlimited'
            }}
          </v-chip>
        </div>

        <!-- Features -->
        <div v-if="plan.features?.length" class="d-flex flex-column ga-2">
          <div
            v-for="f in plan.features"
            :key="f.id ?? f.key"
            class="d-flex align-center ga-2"
          >
            <v-icon icon="mdi-check-circle-outline" size="14" color="primary" />
            <span class="text-caption">{{ f[locale] ?? f.en ?? f.key }}</span>
          </div>
        </div>
      </v-card>
    </div>

    <!-- ── Footer ── -->
    <div class="picker-footer">
      <div class="sub-actions">
        <v-btn
          variant="outlined"
          rounded="lg"
          
          prepend-icon="mdi-arrow-left"
          to="/"
        >
          {{ t('auth.register.back') }}
        </v-btn>
        <v-btn
          color="primary"
          
          rounded="lg"
          :disabled="!modelPlan"
          append-icon="mdi-arrow-right"
          @click="emit('next')"
        >
          {{ t('auth.register.next') }}
        </v-btn>
      </div>
      <p class="text-caption text-medium-emphasis">
        {{ t('auth.register.already') }}
        <a
          :href="adminAppUrl"
          class="text-primary font-weight-bold text-decoration-none"
        >
          {{ t('auth.register.login_link') }}
        </a>
      </p>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, watch } from 'vue'
  import { useI18n } from 'vue-i18n'

  const { t, locale } = useI18n()

  const props = defineProps({
    modelPlan: { type: String, default: null },
    plans: { type: [Array, Object], default: () => [] },
    hidePlanId: { type: String, default: null }
  })

  const emit = defineEmits(['update:modelPlan', 'update:selection', 'next'])

  // No customer-login page exists on this marketing site — the real
  // login lives on the admin app, same host used after successful signup.
  const adminAppUrl =
    import.meta.env.VITE_APP_MODE === 'Production'
      ? 'https://admin.nexstacktech.com'
      : 'http://localhost:5173'

  const PLAN_UI = {
    free: {
      icon: 'mdi-gift-outline',
      color: 'grey',
      tagline: 'Get started for free'
    },
    starter: {
      icon: 'mdi-star-half-full',
      color: 'blue',
      tagline: 'For small teams'
    },
    pro: { icon: 'mdi-star', color: 'primary', tagline: 'Most popular choice' },
    enterprise: {
      icon: 'mdi-crown',
      color: 'warning',
      tagline: 'For large organisations'
    }
  }

  // ── Normalise ──────────────────────────────────────────────────────────────────
  const normalizedPlans = computed(() => {
    const raw = props.plans
    if (!raw) return []
    if (Array.isArray(raw)) return raw
    if (Array.isArray(raw.data)) return raw.data
    return []
  })

  const visiblePlans = computed(() =>
    normalizedPlans.value
      .filter(p => p.is_active && p.id !== props.hidePlanId)
      .map(p => ({ ...p, popular: p.code === 'pro' }))
  )

  // ── Unified cycles ─────────────────────────────────────────────────────────────
  const unifiedCycles = computed(() => {
    const seen = new Map()
    normalizedPlans.value.forEach(plan => {
      ;(plan.billing_cycles ?? [])
        .filter(c => c.is_active)
        .forEach(c => {
          if (!seen.has(c.months)) seen.set(c.months, c)
        })
    })
    return [...seen.values()].sort((a, b) => a.months - b.months)
  })

  // v-btn-toggle binds directly to selectedMonths (integer)
  const selectedMonths = ref(1)

  watch(
    unifiedCycles,
    cycles => {
      if (
        cycles.length &&
        !cycles.find(c => c.months === selectedMonths.value)
      ) {
        selectedMonths.value = cycles[0].months
      }
    },
    { immediate: true }
  )

  // When cycle tab changes → re-emit with updated cycle id
  watch(selectedMonths, () => {
    if (!props.modelPlan) return
    const plan = visiblePlans.value.find(p => p.id === props.modelPlan)
    if (!plan) return
    if (!isPlanAvailableForCycle(plan)) {
      emit('update:modelPlan', null) // deselect if no longer valid
      emit('update:selection', null)
      return
    }
    emitSelection(plan) // re-emit with new billing_cycle_id
  })

  // ── Helpers ────────────────────────────────────────────────────────────────────
  const isFree = plan => parseFloat(plan.price_usd ?? 0) === 0

  function planCycleForSelected(plan) {
    return (
      (plan.billing_cycles ?? []).find(
        c => c.is_active && c.months === selectedMonths.value
      ) ?? null
    )
  }

  function isPlanAvailableForCycle(plan) {
    if (isFree(plan)) return selectedMonths.value === 1
    return planCycleForSelected(plan) !== null
  }

  function getMonthlyPrice(plan) {
    const base = Number(plan.price_usd ?? 0)
    const discount =
      Number(planCycleForSelected(plan)?.discount_percent ?? 0) / 100
    return (base * (1 - discount)).toFixed(2)
  }

  function getCycleTotal(plan) {
    return (parseFloat(getMonthlyPrice(plan)) * selectedMonths.value).toFixed(2)
  }

  function getSavingsPct(plan) {
    return Number(planCycleForSelected(plan)?.discount_percent ?? 0)
  }

  // ── Emit ───────────────────────────────────────────────────────────────────────
  function emitSelection(plan) {
    // For free plan, take the monthly cycle from unified list (months=1)
    // For paid plans, take the cycle record that belongs to that specific plan
    const cycle = isFree(plan)
      ? unifiedCycles.value.find(c => c.months === selectedMonths.value)
      : planCycleForSelected(plan)

    emit('update:selection', {
      plan_id: plan.id,
      billing_cycle_id: cycle?.id ?? null,
      billing_months: selectedMonths.value,
      discount_percent: Number(cycle?.discount_percent ?? 0),
      cycle_label: cycle?.label ?? ''
    })
  }

  function onSelectPlan(plan) {
    if (!isPlanAvailableForCycle(plan)) return
    emit('update:modelPlan', plan.id)
    emitSelection(plan)
  }
</script>

<style scoped>
  .plan-picker {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 24px;
    width: 100%;
  }

  .plan-grid {
    display: grid;
    gap: 16px;
    width: 100%;
  }
  .plan-grid.cols-1 {
    grid-template-columns: minmax(0, 360px);
    justify-content: center;
  }
  .plan-grid.cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .plan-grid.cols-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
  .plan-grid.cols-4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
  @media (max-width: 760px) {
    .plan-grid {
      grid-template-columns: 1fr !important;
      max-width: 400px;
    }
  }

  .plan-card {
    position: relative;
    display: flex;
    flex-direction: column;
    transition:
      transform 0.18s,
      box-shadow 0.18s;
    height: 100%;
  }
  .plan-card:hover:not(.v-card--disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07) !important;
  }
  .plan-selected {
    box-shadow: 0 0 0 2px rgb(var(--v-theme-primary)) !important;
  }
  .plan-featured {
    border-color: rgba(var(--v-theme-primary), 0.25) !important;
  }

  .picker-footer {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    width: 100%;
  }
  .sub-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
  }

  .pop-badge {
    position: absolute;
    top: -11px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1;
  }
  .sel-check {
    position: absolute;
    top: 12px;
    right: 12px;
  }
  .cursor-pointer {
    cursor: pointer;
  }
</style>
