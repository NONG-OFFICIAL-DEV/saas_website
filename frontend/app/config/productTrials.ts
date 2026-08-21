// Per-product resolution for "Start Free Trial" / plan-selection CTAs.
//
// Every product here is a real, separate SaaS app with its own accounts —
// this site does not run its own signup form for any of them. Each CTA
// hands off to that product's own register page in a new tab instead of
// pretending signup is part of this marketing site.
//
// { to } → internal Vue route (only used for the no-live-signup-yet
// fallback below). { href } → external app, opened in a new tab (same
// convention already used for cta_type === 'external_link' in
// ProductDetail.vue), since it's a full handoff to a different product.
//
// App URLs are read from runtime config *inside* each exported function,
// not at module scope — useRuntimeConfig() needs an active Nuxt context,
// which only exists once these are actually called (from a component/page),
// never at cold-import time.

export type TrialLink = { href: string; external: true } | { to: string }

export function getTrialLink(slug: string, planCode?: string): TrialLink {
  const config = useRuntimeConfig()
  const smartStoreAppUrl = config.public.smartStoreAppUrl || 'https://admin.nexstacktech.com'
  const studioAppUrl = config.public.studioAppUrl || 'https://photo-studio.nexstacktech.com'

  if (slug === 'nexstack-pos') {
    return {
      href: `${smartStoreAppUrl}/register${planCode ? `?plan=${planCode}` : ''}`,
      external: true
    }
  }
  if (slug === 'studio-management') {
    return {
      href: `${studioAppUrl}/register?plan=${planCode ?? 'free_trial'}`,
      external: true
    }
  }
  // No live signup flow wired up for this product yet — send them to its
  // own marketing page rather than pretending it's a different product.
  return { to: slug ? `/products/${slug}` : '/products' }
}

/**
 * @returns that product's own login URL, or null if it doesn't have one
 *   wired up yet (caller should fall back to its own marketing page
 *   rather than guessing).
 */
export function getLoginLink(slug: string): string | null {
  const config = useRuntimeConfig()
  const smartStoreAppUrl = config.public.smartStoreAppUrl || 'https://admin.nexstacktech.com'
  const studioAppUrl = config.public.studioAppUrl || 'https://photo-studio.nexstacktech.com'

  if (slug === 'nexstack-pos') return `${smartStoreAppUrl}/login`
  if (slug === 'studio-management') return `${studioAppUrl}/login`
  return null
}
