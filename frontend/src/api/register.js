import http from './api'

export function getBusinessTypes() {
  return http.get('/v1/public/business-types')
}

export function businessRegister(data) {
  return http.post('/v1/public/business-register', data, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
}

export const getAllPlansApi = () => http.get('/v1/public/plans')
