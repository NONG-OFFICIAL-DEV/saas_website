// Vuetify's useDisplay().mobile replacement, matching its `md` breakpoint
// threshold (960px). /admin/** is force-client-only (routeRules ssr:false
// in nuxt.config.ts), so this never renders during SSR — window is always
// available, no hydration-mismatch guard needed.
export function useAdminBreakpoint() {
  const mobile = ref(window.innerWidth < 960)

  function update() {
    mobile.value = window.innerWidth < 960
  }

  onMounted(() => window.addEventListener('resize', update))
  onUnmounted(() => window.removeEventListener('resize', update))

  return { mobile }
}
