// app/composables/useAppUtils.ts
export function useAppUtils() {
  const { proxy } = getCurrentInstance()!

  return {
    confirm: (proxy as any).$confirm,
    notif: (proxy as any).$notif
  }
}
