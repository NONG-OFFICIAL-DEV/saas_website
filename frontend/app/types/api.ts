import type { AxiosRequestConfig } from 'axios'

// Every api client's request interceptor reads config.meta.loader to decide
// whether to trigger the global 'bar' loader (see stores/loadingStore.ts) —
// axios has no such field by default, so it's declared here once.
declare module 'axios' {
  interface AxiosRequestConfig {
    meta?: {
      loader?: 'skip' | 'bar'
      __loadingStarted?: boolean
    }
  }
}

export interface ApiError extends Error {
  status?: number
  errors?: Record<string, string[]>
}

export const NO_OVERLAY: AxiosRequestConfig = { meta: { loader: 'skip' } }
