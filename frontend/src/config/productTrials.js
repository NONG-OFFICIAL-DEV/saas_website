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
const ADMIN_APP_URL = import.meta.env.VITE_APP_ADMIN_APP_URL || 'https://admin.nexstacktech.com'
const STUDIO_APP_URL = import.meta.env.VITE_APP_STUDIO_APP_URL || 'https://photo-studio.nexstacktech.com'

const PRODUCT_TRIAL_LINKS = {
  'nexstack-pos': planCode => ({
    href: `${ADMIN_APP_URL}/register${planCode ? `?plan=${planCode}` : ''}`,
    external: true
  }),
  'studio-management': (planCode = 'free_trial') => ({
    href: `${STUDIO_APP_URL}/register?plan=${planCode}`,
    external: true
  })
}

/**
 * @param {string} slug - the specific product the visitor picked (never a
 *   guess/fallback — callers must know which product they mean; there is no
 *   cross-product default here, since silently substituting a different
 *   product's signup flow is exactly the bug this used to have)
 * @param {string} [planCode] - a specific plan chosen (e.g. from a pricing card)
 */
export function getTrialLink(slug, planCode) {
  const resolve = PRODUCT_TRIAL_LINKS[slug]
  if (resolve) return resolve(planCode)
  // No live signup flow wired up for this product yet (e.g. a new product
  // still in pricing_mode 'cms') — send them to its own marketing page
  // rather than pretending it's a different product.
  return { to: slug ? `/products/${slug}` : '/products' }
}
