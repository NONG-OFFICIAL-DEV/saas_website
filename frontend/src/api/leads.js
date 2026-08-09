import http from './api'

export function submitLead(data) {
  return http.post('/v1/public/leads', data)
}
