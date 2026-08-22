import http from './api'

export const getSmartStorePlansApi = () => http.get('/v1/public/plans')
