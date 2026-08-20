import http from './api'

export const getAllPlansApi = () => http.get('/v1/public/plans')
