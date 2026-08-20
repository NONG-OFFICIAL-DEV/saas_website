// app/composables/useNotif.ts
export function useNotif() {
  const { proxy } = getCurrentInstance()!
  return (proxy as any).$notif
}
