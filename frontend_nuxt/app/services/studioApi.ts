import axios from 'axios'
import { useLoadingStore } from '~/stores/loadingStore'
import type { ApiError } from '~/types/api'

// Studio Management System has its own live backend, entirely separate
// from both the CMS (services/cmsApi.ts) and the legacy Nexstack POS API
// (api/api.ts) — same pattern as those, its own dedicated client.
//
// baseURL resolved inside the interceptor for the same reason as
// cmsApi.ts — see that file's comment.
const studioApi = axios.create({
  headers: {
    'Content-Type': 'application/json'
  }
})

studioApi.interceptors.request.use(requestConfig => {
  const runtimeConfig = useRuntimeConfig()
  requestConfig.baseURL = runtimeConfig.public.studioApiUrl || 'https://photo-studio.nexstacktech.com/api/v1'

  const loaderType = requestConfig.meta?.loader ?? 'skip'
  if (loaderType !== 'skip') {
    useLoadingStore().start(loaderType)
    requestConfig.meta = { ...requestConfig.meta, __loadingStarted: true }
  }
  return requestConfig
})

studioApi.interceptors.response.use(
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

export default studioApi
