import axios from 'axios'
import { useLoadingStore } from '~/stores/loadingStore'

// Smart Store (Nexstack POS) — the real product's own live backend, entirely
// separate from this repo's CMS (services/cmsApi.ts) and Studio
// (services/studioApi.ts). Auth, live subscription plans/billing (plans.ts)
// only — the only source of truth for actual money/checkout.
// baseURL resolved inside the interceptor for the same reason as
// services/cmsApi.ts — see that file's comment.
const api = axios.create({
  headers: {
    'Access-Control-Allow-Origin': '*',
    'Content-Type': 'application/json'
  }
})

api.interceptors.request.use(requestConfig => {
  const runtimeConfig = useRuntimeConfig()
  requestConfig.baseURL = runtimeConfig.public.smartStoreApiUrl || 'https://admin.nexstacktech.com/api/'

  const loaderType = requestConfig.meta?.loader ?? 'skip'
  const token = import.meta.client ? localStorage.getItem('token') : null
  if (token) {
    requestConfig.headers.Authorization = `Bearer ${token}`
  }
  if (loaderType !== 'skip') {
    useLoadingStore().start(loaderType)
    requestConfig.meta = { ...requestConfig.meta, __loadingStarted: true }
  }
  return requestConfig
})

api.interceptors.response.use(
  response => {
    if (response.config.meta?.__loadingStarted) useLoadingStore().stop()
    return response
  },
  error => {
    if (error.config?.meta?.__loadingStarted) useLoadingStore().stop()
    return Promise.reject(error)
  }
)

export default api
