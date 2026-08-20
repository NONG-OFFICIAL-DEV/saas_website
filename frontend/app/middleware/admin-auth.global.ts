let adminAuthReady: Promise<void> | null = null

export default defineNuxtRouteMiddleware(async (to) => {
  if (!to.path.startsWith('/admin')) return

  const authStore = useAdminAuthStore()
  adminAuthReady ??= authStore.init()
  await adminAuthReady

  const isLoggedIn = !!authStore.session
  if (to.path === '/admin/login') {
    if (isLoggedIn) return navigateTo('/admin')
    return
  }
  if (!isLoggedIn) return navigateTo('/admin/login')
})
