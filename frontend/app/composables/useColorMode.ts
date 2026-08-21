// Replaces Vuetify's useTheme() for the 5 call sites that only ever read
// `theme.global.name.value === 'dark'` / toggle it. Persisted via a cookie
// (not localStorage) so the correct theme class can be set during SSR
// itself — localStorage isn't readable on the server, and reading it at
// module scope (the original pre-Nuxt app's approach) was already flagged
// as an SSR hazard once during the Nuxt migration; a cookie avoids
// repeating that mistake here for the Tailwind dark-mode class strategy.
//
// No system-preference (prefers-color-scheme) detection — matches the
// original app's manual-toggle-only behavior, defaulting to dark.
export function useColorMode() {
  const theme = useCookie<'light' | 'dark'>('theme', {
    default: () => 'dark',
    maxAge: 60 * 60 * 24 * 365,
    sameSite: 'lax'
  })

  const isDark = computed(() => theme.value === 'dark')

  function toggle() {
    theme.value = isDark.value ? 'light' : 'dark'
  }

  return { theme, isDark, toggle }
}
