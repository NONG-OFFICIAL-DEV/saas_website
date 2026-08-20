import { useLoadingStore } from '@/stores/loadingStore'
import axios from 'axios'

// Dedicated client for the CMS backend (Laravel + Sanctum) — separate from
// src/api/api.js, which talks to the legacy POS backend and shares nothing
// with this one (different base URL, different token).
const cmsApi = axios.create({
  baseURL: import.meta.env.VITE_APP_CMS_API_URL || 'http://127.0.0.1:8000/api/v1',
  headers: {
    'Content-Type': 'application/json'
  }
})

// No global blocking loader by default — every caller owns its own
// local loading state (a button's :loading, a section's small spinner,
// a table's "Refreshing…" indicator). Pass { meta: { loader: 'bar' } }
// only for the rare call that genuinely has no local UI of its own and
// needs the thin top progress bar (components/global/Loading.vue).
//
// useLoadingStore() is called inside the interceptors (not at module scope)
// because this module is reachable through router/index.js's static import
// of the adminAuth store, which loads before main.js calls app.use(pinia) —
// calling it too early throws "no active Pinia".
cmsApi.interceptors.request.use(config => {
  const token = localStorage.getItem('cms_admin_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  const loaderType = config.meta?.loader ?? 'skip'
  if (loaderType !== 'skip') {
    useLoadingStore().start(loaderType)
    config.meta = { ...config.meta, __loadingStarted: true }
  }

  return config
})

// Laravel error payloads look like { message, errors }. Unwrap so callers
// can use `err.message` directly.
cmsApi.interceptors.response.use(
  response => {
    if (response.config.meta?.__loadingStarted) useLoadingStore().stop()
    return response
  },
  error => {
    if (error.config?.meta?.__loadingStarted) useLoadingStore().stop()

    const message = error.response?.data?.message || error.message
    const wrapped = new Error(message)
    wrapped.status = error.response?.status
    wrapped.errors = error.response?.data?.errors
    return Promise.reject(wrapped)
  }
)

export default cmsApi
