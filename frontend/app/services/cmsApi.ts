import axios from 'axios'
import { useLoadingStore } from '~/stores/loadingStore'
import type { ApiError } from '~/types/api'

// Dedicated client for the CMS backend (Laravel + Sanctum) — separate from
// app/api/api.ts, which talks to the legacy POS backend and shares nothing
// with this one (different base URL, different token).
//
// baseURL is resolved inside the request interceptor, not passed to
// axios.create() here — useRuntimeConfig() must run within an active Nuxt
// context, which is only guaranteed once a real request is being handled
// (component setup, a lifecycle hook, a plugin). Calling it at raw module
// scope would run at cold-import time, before that context necessarily
// exists, and could throw or (worse) silently misbehave under SSR.
const cmsApi = axios.create({
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
// localStorage only exists in the browser — this client's calls made
// during SSR (public pages, via useAsyncData) hit this interceptor
// server-side too, so the token lookup must be guarded.
cmsApi.interceptors.request.use(requestConfig => {
  const runtimeConfig = useRuntimeConfig()
  requestConfig.baseURL = runtimeConfig.public.cmsApiUrl || 'http://127.0.0.1:8000/api/v1' || 'https://www.nexstacktech.com/api/v1'

  const token = import.meta.client ? localStorage.getItem('cms_admin_token') : null
  if (token) {
    requestConfig.headers.Authorization = `Bearer ${token}`
  }

  const loaderType = requestConfig.meta?.loader ?? 'skip'
  if (loaderType !== 'skip') {
    useLoadingStore().start(loaderType)
    requestConfig.meta = { ...requestConfig.meta, __loadingStarted: true }
  }

  return requestConfig
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
    const wrapped = new Error(message) as ApiError
    wrapped.status = error.response?.status
    wrapped.errors = error.response?.data?.errors
    return Promise.reject(wrapped)
  }
)

export default cmsApi
