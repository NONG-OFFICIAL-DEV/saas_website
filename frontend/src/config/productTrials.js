// Per-product resolution for "Start Free Trial" / plan-selection CTAs.
//
// Nexstack POS is the only product with a signup flow inside this app
// (src/views/Register.vue) — everything else is a real, separate SaaS app
// with its own accounts, so those CTAs hand off to that app's own
// register page instead of pretending it's part of this site.
//
// { to } → internal Vue route. { href } → external app, opened in a new
// tab (same convention already used for cta_type === 'external_link' in
// ProductDetail.vue), since it's a full handoff to a different product.
const STUDIO_APP_URL = import.meta.env.VITE_APP_STUDIO_APP_URL || 'https://photo-studio.nexstacktech.com'

const PRODUCT_TRIAL_LINKS = {
  'nexstack-pos': () => ({ to: '/auth/register?intent=trial' }),
  'studio-management': (planCode = 'free_trial') => ({
    href: `${STUDIO_APP_URL}/register?plan=${planCode}`,
    external: true
  })
}

const DEFAULT_SLUG = 'nexstack-pos'

/**
 * @param {string|null} slug - the product currently in view, or last viewed
 * @param {string} [planCode] - a specific plan chosen (e.g. from a pricing card)
 */
export function getTrialLink(slug, planCode) {
  const resolve = PRODUCT_TRIAL_LINKS[slug] ?? PRODUCT_TRIAL_LINKS[DEFAULT_SLUG]
  return resolve(planCode)
}
