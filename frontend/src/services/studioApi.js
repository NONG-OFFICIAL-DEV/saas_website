import { useLoadingStore } from '@/stores/loadingStore'
import axios from 'axios'

// Studio Management System has its own live backend, entirely separate
// from both the CMS (services/cmsApi.js) and the legacy Nexstack POS API
// (api/api.js) — same pattern as those, its own dedicated client.
const studioApi = axios.create({
  baseURL: import.meta.env.VITE_APP_STUDIO_API_URL || 'https://photo-studio.nexstacktech.com/api/v1',
  headers: {
    'Content-Type': 'application/json'
  }
})

// No global blocking loader by default — same convention as cmsApi.js /
// api.js. Every caller owns its own local loading state; pass
// { meta: { loader: 'bar' } } only for a call with no local UI of its own.
// useLoadingStore() is called inside the interceptor (not at module scope)
// for the same reason as cmsApi.js — this can be reached before main.js
// calls app.use(pinia).
studioApi.interceptors.request.use(config => {
  const loaderType = config.meta?.loader ?? 'skip'
  if (loaderType !== 'skip') {
    useLoadingStore().start(loaderType)
    config.meta = { ...config.meta, __loadingStarted: true }
  }
  return config
})

studioApi.interceptors.response.use(
  response => {
    if (response.config.meta?.__loadingStarted) useLoadingStore().stop()
    return response
  },
  error => {
    if (error.config?.meta?.__loadingStarted) useLoadingStore().stop()
    const message = error.response?.data?.message || error.message
    const wrapped = new Error(message)
    wrapped.status = error.response?.status
    return Promise.reject(wrapped)
  }
)

export default studioApi
