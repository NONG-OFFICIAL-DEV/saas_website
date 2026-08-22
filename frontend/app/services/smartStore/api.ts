import axios from 'axios'
import { useLoadingStore } from '~/stores/loadingStore'

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
