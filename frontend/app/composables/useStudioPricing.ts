import type { StudioPlan } from '~/types'

// Shared by StudioPriceSection.vue (product detail page) and pricing.vue
// (pricing hub) — previously each hand-rolled an identical copy of this
// cycle/price math. Pure display-logic extraction: the formulas, API
// calls, and billing-cycle codes sent to Studio's own backend are
// byte-for-byte unchanged from both prior copies.
export function useStudioPricing() {
  const { locale } = useI18n()
  const store = useStudioPlansStore()

  // `code` matches Studio's own BillingCycle enum values exactly
  // (backend/app/Enums/BillingCycle.php) — this is what actually gets sent
  // through onboarding to Studio's /register endpoint, not just a display label.
  const CYCLES = [
    { months: 1, code: 'monthly', labelKey: 'studio_pricing.monthly' },
    { months: 3, code: 'quarterly', labelKey: 'studio_pricing.quarterly' },
    { months: 12, code: 'yearly', labelKey: 'studio_pricing.yearly' }
  ]

  const selectedMonths = ref(1)
  const cycleCode = computed(() => CYCLES.find((c) => c.months === selectedMonths.value)?.code ?? 'monthly')
  const hasPaidPlans = computed(() => store.plans.some((p) => Number(p.price_monthly) > 0))

  const isFree = (plan: StudioPlan) => Number(plan.price_monthly) === 0

  // The API gives price_monthly/price_quarterly/price_yearly as flat
  // totals for that billing period — normalize to an effective
  // per-month price so cards stay comparable across cycles.
  function totalForCycle(plan: StudioPlan, months: number) {
    if (months === 1) return Number(plan.price_monthly)
    if (months === 3) return Number(plan.price_quarterly)
    return Number(plan.price_yearly)
  }
  function monthlyPrice(plan: StudioPlan) {
    return (totalForCycle(plan, selectedMonths.value) / selectedMonths.value).toFixed(2)
  }
  function savingsPct(months: number) {
    const referencePlan = store.plans.find((p) => Number(p.price_monthly) > 0)
    if (!referencePlan) return 0
    const monthly = Number(referencePlan.price_monthly)
    if (!monthly) return 0
    const perMonthAtCycle = totalForCycle(referencePlan, months) / months
    return Math.round((1 - perMonthAtCycle / monthly) * 100)
  }

  // Returns the full bullet list — callers that need a truncated list
  // (pricing.vue shows only the top 7 alongside a full comparison table)
  // slice it themselves; this composable doesn't bake in a display cap.
  function planFeatures(plan: StudioPlan): string[] {
    return studioPlanBullets(plan, locale.value)
  }

  return { store, CYCLES, selectedMonths, cycleCode, hasPaidPlans, isFree, totalForCycle, monthlyPrice, savingsPct, planFeatures }
}
