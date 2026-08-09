// stores/register.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import {
  getBusinessTypes,
  businessRegister,
  getAllPlansApi
} from '@/api/register'

export const useRegisterStore = defineStore('register', () => {
  // ── State ─────────────────────────────────────────────────────────────
  const businessTypes = ref([]) // [{ value, label, icon }]
  const plans = ref([]) // [{ value, label, icon }]
  const loading = ref(false)
  const loadingTypes = ref(false)
  const error = ref(null) // null | string | Object (422 field errors)

  // ── Actions ───────────────────────────────────────────────────────────

  /**
   * Load business types once on mount.
   * Falls back to hardcoded list if API unavailable.
   */
  async function fetchBusinessTypes() {
    loadingTypes.value = true
    error.value = null
    try {
      const { data } = await getBusinessTypes()
      businessTypes.value = data.data ?? data
    } catch (err) {
      // Fallback so the UI never breaks
      businessTypes.value = FALLBACK_BIZ_TYPES
      error.value =
        err?.response?.data?.message ?? 'Failed to load business types'
    } finally {
      loadingTypes.value = false
    }
  }

  /**
   * Submit registration.
   * Maps camelCase form fields → snake_case API fields.
   *
   * @param {{ plan, cycle, business, account }} formState
   * @returns {{ success: boolean, data?, error? }}
   */
  async function register({ plan, billing_cycle_id, business, account }) {
    loading.value = true
    error.value = null

    // Use FormData so the logo is sent as a real file upload
    const formData = new FormData()

    // Owner account
    formData.append('owner_first_name', account.firstName)
    formData.append('owner_last_name', account.lastName)
    formData.append('owner_email', account.email)
    formData.append('owner_password', account.password)
    if (business.phone) formData.append('owner_phone', business.phone.replace(/[\s\-().]/g, ''))

    // Tenant
    formData.append('name', business.storeName)
    formData.append('plan', plan)
    formData.append('billing_cycle_id', billing_cycle_id)
    if (business.bizType) formData.append('business_type_id', business.bizType)
    formData.append('currency', business.currency || 'USD')

    // Logo — append raw File object, not base64
    if (business.logoFile) formData.append('logo', business.logoFile)

    try {
      const { data } = await businessRegister(formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      if (data.token) localStorage.setItem('token', data.token)
      return { success: true, data }
    } catch (err) {
      const res = err?.response
      if (res?.status === 422) {
        error.value = res.data.errors ?? res.data.message
      } else {
        error.value =
          res?.data?.message ?? 'Registration failed. Please try again.'
      }
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  // ── Helpers ───────────────────────────────────────────────────────────

  /** Extract first message for a field from Laravel 422 errors object */
  function fieldError(field) {
    if (!error.value || typeof error.value !== 'object') return null
    const msgs = error.value[field]
    return Array.isArray(msgs) ? msgs[0] : null
  }

  async function fetchPlans() {
    const res = await getAllPlansApi()
    plans.value = res.data.data
  }

  return {
    businessTypes,
    plans,
    loading,
    loadingTypes,
    error,
    fetchBusinessTypes,
    fetchPlans,
    register,
    fieldError
  }
})

// ── Fallback business types (used if API is down) ──────────────────────
const FALLBACK_BIZ_TYPES = [
  {
    id: 'restaurant',
    name: 'Restaurant',
    icon: 'mdi-silverware-fork-knife'
  },
  { id: 'cafe', name: 'Café', icon: 'mdi-coffee' },
  { id: 'minimart', name: 'Mini Mart', icon: 'mdi-storefront-outline' },
  { id: 'retail', name: 'Retail Shop', icon: 'mdi-shopping-outline' },
  { id: 'bakery', name: 'Bakery', icon: 'mdi-bread-slice-outline' },
  { id: 'food_truck', name: 'Food Truck', icon: 'mdi-truck-outline' },
  { id: 'bar', name: 'Bar / Lounge', icon: 'mdi-glass-cocktail' }
]
