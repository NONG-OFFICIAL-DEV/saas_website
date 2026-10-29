import axios from 'axios'
import { useLoadingStore } from '~/stores/loadingStore'
import type { ApiError } from '~/types/api'

// `Access-Control-Allow-Origin` is a response header set by the server, not
// something a client request header can meaningfully influence — removed
// (was a no-op). No endpoint under this client is auth-gated today (see
// services/smartStore/plans.ts — the only consumer, a public endpoint), so
// no Authorization header is attached here, matching that.
const api = axios.create({
  headers: {
    'Content-Type': 'application/json'
  }
})

api.interceptors.request.use(requestConfig => {
  const runtimeConfig = useRuntimeConfig()
  requestConfig.baseURL = runtimeConfig.public.smartStoreApiUrl || 'https://admin.nexstacktech.com/api/'

  const loaderType = requestConfig.meta?.loader ?? 'skip'
  if (loaderType !== 'skip') {
    useLoadingStore().start(loaderType)
    requestConfig.meta = { ...requestConfig.meta, __loadingStarted: true }
  }
  return requestConfig
})

// Unwrapped into a plain ApiError (message + status) the same way
// services/cms/api.ts and services/studio/api.ts already do, so callers can
// use `err.message` consistently instead of reaching into `err.response`.
api.interceptors.response.use(
  response => {
    if (response.config.meta?.__loadingStarted) useLoadingStore().stop()
    return response
  },
  error => {
    if (error.config?.meta?.__loadingStarted) useLoadingStore().stop()
    const message = error.response?.data?.message || error.message
    const wrapped = new Error(message) as ApiError
    wrapped.status = error.response?.status
    return Promise.reject(wrapped)
  }
)

export default api
