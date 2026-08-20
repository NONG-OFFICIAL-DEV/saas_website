import { defineStore } from 'pinia'
import { ref } from 'vue'
import cmsApi from '~/services/cmsApi'
import { NO_OVERLAY } from '~/types/api'
import type { AdminUser } from '~/types'

const TOKEN_KEY = 'cms_admin_token'

interface AdminSession {
  token: string
  user: AdminUser
}

// This store already drives its own `loading` ref (the login button's
// spinner) and init()/signOut() are silent/fast redirects — skip the
// global overlay everywhere here so it never fights with those.
export const useAdminAuthStore = defineStore('adminAuth', () => {
  const session = ref<AdminSession | null>(null)
  // Starts false — this also backs the login button's spinner via
  // AdminLogin.vue, and defaulting it true made that button look stuck
  // loading from the moment the page rendered, not just during a real
  // request. init()/signIn() each set it true themselves while they run.
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function init() {
    loading.value = true
    try {
      const token = import.meta.client ? localStorage.getItem(TOKEN_KEY) : null
      if (!token) {
        session.value = null
        return
      }
      const { data } = await cmsApi.get('/admin/auth/me', NO_OVERLAY)
      session.value = { token, user: data.data }
    } catch {
      if (import.meta.client) localStorage.removeItem(TOKEN_KEY)
      session.value = null
    } finally {
      loading.value = false
    }
  }

  async function signIn(email: string, password: string) {
    error.value = null
    loading.value = true
    try {
      const { data } = await cmsApi.post('/auth/login', { email, password }, NO_OVERLAY)
      if (import.meta.client) localStorage.setItem(TOKEN_KEY, data.data.token)
      session.value = { token: data.data.token, user: data.data.user }
      return true
    } catch (err: any) {
      error.value = err.message
      return false
    } finally {
      loading.value = false
    }
  }

  async function signOut() {
    try {
      await cmsApi.post('/admin/auth/logout', null, NO_OVERLAY)
    } catch {
      // token may already be invalid/expired — clear local state regardless
    }
    if (import.meta.client) localStorage.removeItem(TOKEN_KEY)
    session.value = null
  }

  return { session, loading, error, init, signIn, signOut }
})
