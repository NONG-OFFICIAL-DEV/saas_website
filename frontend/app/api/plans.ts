import http from './smartStoreApi'

export const getSmartStorePlansApi = () => http.get('/v1/public/plans')
