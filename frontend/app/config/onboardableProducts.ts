// Products with a guided in-house onboarding wizard (`pages/onboarding/[slug].vue`)
// skip straight to it; anything else falls back to that product's own
// external signup/marketing page instead (see config/productTrials.ts).
// Single source of truth — previously duplicated as a local literal in
// both get-started.vue and products/[slug].vue.
export const ONBOARDABLE_SLUGS = ['nexstack-pos', 'studio-management']
