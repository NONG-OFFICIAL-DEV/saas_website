import http from './api'

export function submitLead(data: { name: string; email: string; source?: string | null }) {
  return http.post('/v1/public/leads', data)
}
