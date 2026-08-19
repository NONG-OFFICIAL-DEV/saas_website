import { useLoadingStore } from '@/stores/loadingStore'
import axios from 'axios'

// Dedicated client for the CMS backend (Laravel + Sanctum) — separate from
// src/api/api.js, which talks to the legacy POS backend and shares nothing
// with this one (different base URL, different token).
const cmsApi = axios.create({
  baseURL: import.meta.env.VITE_APP_CMS_API_URL || 'http://127.0.0.1:8001/api/v1',
  headers: {
    'Content-Type': 'application/json'
  }
})

// Same convention as src/api/api.js: pass { meta: { loader: 'skip' } } in a
// call's axios config to opt out — used by requests whose caller already
// renders its own skeleton/spinner (a second full-screen overlay on top of
// those would just be visual noise), for both the public site and the
// admin panel. Anything else defaults to the global overlay.
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

  const loaderType = config.meta?.loader ?? 'overlay'
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
