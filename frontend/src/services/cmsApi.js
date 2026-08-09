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

cmsApi.interceptors.request.use(config => {
  const token = localStorage.getItem('cms_admin_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Laravel error payloads look like { message, errors }. Unwrap so callers
// can use `err.message` directly.
cmsApi.interceptors.response.use(
  response => response,
  error => {
    const message = error.response?.data?.message || error.message
    const wrapped = new Error(message)
    wrapped.status = error.response?.status
    return Promise.reject(wrapped)
  }
)

export default cmsApi
