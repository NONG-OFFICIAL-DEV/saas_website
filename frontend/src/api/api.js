import { useLoadingStore } from '@/stores/loadingStore'
import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_APP_API_BASE_URL,
  headers: {
    'Access-Control-Allow-Origin': '*',
    'Content-type': 'application/json'
  }
})

// useLoadingStore() is called inside the interceptors, not at module scope —
// this module can be reached before main.js calls app.use(pinia), and
// calling it too early throws "no active Pinia".
//
// Every current caller of this client is a customer-facing flow (plans,
// registration steps, waitlist leads) and already renders its own
// skeleton/button spinner — a full-screen overlay stacked on top of those
// is a worse experience, not a better one. So the default here is 'skip';
// pass { meta: { loader: 'overlay' } } to opt a specific call back in if a
// future call genuinely has no local loading UI of its own.
api.interceptors.request.use(async config => {
  const loaderType = config.meta?.loader ?? 'skip'
  try {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    if (loaderType !== 'skip') {
      useLoadingStore().start(loaderType)
      config.meta = { ...config.meta, __loadingStarted: true }
    }
    return config
  } catch (error) {
    if (config.meta?.__loadingStarted) useLoadingStore().stop()
    return Promise.reject(error)
  }
})

// Response Interceptor
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
