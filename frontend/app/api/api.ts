import axios from 'axios'
import { useLoadingStore } from '~/stores/loadingStore'

// Legacy REST API — Nexstack POS's auth, lead capture, live plans/billing.
// baseURL resolved inside the interceptor for the same reason as
// services/cmsApi.ts — see that file's comment.
const api = axios.create({
  headers: {
    'Access-Control-Allow-Origin': '*',
    'Content-Type': 'application/json'
  }
})

// Every current caller of this client is a customer-facing flow (plans,
// registration steps, waitlist leads) and already renders its own local
// loading state (button spinner, inline indicator) — a global blocking
// overlay stacked on top of those is a worse experience, not a better one.
// So the default here is 'skip'; pass { meta: { loader: 'bar' } } to opt a
// specific call back in if a future call genuinely has no local loading UI
// of its own.
api.interceptors.request.use(requestConfig => {
  const runtimeConfig = useRuntimeConfig()
  requestConfig.baseURL = runtimeConfig.public.apiBaseUrl

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
