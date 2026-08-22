import axios from 'axios'
import { useLoadingStore } from '~/stores/loadingStore'
import type { ApiError } from '~/types/api'

const cmsApi = axios.create({
  headers: {
    'Content-Type': 'application/json'
  }
})

cmsApi.interceptors.request.use(requestConfig => {
  const runtimeConfig = useRuntimeConfig()
  requestConfig.baseURL = runtimeConfig.public.cmsApiUrl || 'http://127.0.0.1:8000/api/v1'

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
