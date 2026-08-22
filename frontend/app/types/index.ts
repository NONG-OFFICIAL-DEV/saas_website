// Shared shapes returned by the CMS backend (SaaS_Website/backend). Kept
// pragmatic, not exhaustive — matches what each Resource actually returns
// (see backend/app/Http/Resources/*.php), not a full generated schema.

export interface ProductFeature {
  id: string
  icon: string | null
  title: string
  description: string | null
  sort_order: number
}

export interface ProductScreenshot {
  id: string
  url: string
  alt_text: string | null
  caption: string | null
  sort_order: number
}

export interface ProductFaq {
  id: string
  question: string
  answer: string
  sort_order: number
}

export interface Product {
  id: string
  slug: string
  status: 'live' | 'beta' | 'coming_soon'
  cta_type: 'register' | 'external_link'
  cta_url: string | null
  accent_color: string
  logo_url: string | null
  hero_image_url: string | null
  demo_video_url: string | null
  sort_order: number
  is_published: boolean
  name: string
  tagline: string | null
  summary: string | null
  description: string | null
  cta_label: string | null
  seo_title: string | null
  seo_description: string | null
  product_features?: ProductFeature[]
  product_screenshots?: ProductScreenshot[]
  faqs?: ProductFaq[]
  translations?: unknown[]
}

export interface Solution {
  id: string
  slug: string
  icon: string | null
  sort_order: number
  is_published: boolean
  name: string
  tagline: string | null
  description: string | null
  products?: Product[]
}

export interface BlogPost {
  id: string
  slug: string
  author_name: string | null
  cover_image_url: string | null
  published_at: string | null
  is_published: boolean
  title: string
  excerpt: string | null
  content: string | null
  seo_title: string | null
  seo_description: string | null
}

export interface Testimonial {
  id: string
  author_name: string
  author_title: string | null
  author_avatar_url: string | null
  product_id: string | null
  product?: Product | null
  rating: number | null
  sort_order: number
  is_published: boolean
  quote: string
}

export interface DocumentationArticleSummary {
  id: string
  slug: string
  category_id: string
  product_id: string | null
  status: 'draft' | 'published' | 'archived'
  sort_order: number
  title: string
  excerpt: string | null
}

export interface DocumentationCategory {
  id: string
  slug: string
  icon: string | null
  product_id: string | null
  product?: Product | null
  parent_id: string | null
  parent?: DocumentationCategory | null
  sort_order: number
  is_active: boolean
  name: string
  description: string | null
  children?: DocumentationCategory[]
  articles?: DocumentationArticleSummary[]
}

export interface DocumentationArticleLink {
  slug: string
  title: string
  excerpt?: string
}

export interface DocumentationArticle {
  id: string
  slug: string
  category_id: string
  category?: DocumentationCategory
  product_id: string | null
  product?: Product | null
  cover_image_url: string | null
  status: 'draft' | 'published' | 'archived'
  sort_order: number
  published_at: string | null
  title: string
  excerpt: string | null
  content: string | null
  seo_title: string | null
  seo_description: string | null
  prev?: DocumentationArticleLink | null
  next?: DocumentationArticleLink | null
  related?: DocumentationArticleLink[]
}

export interface DocumentationSearchResult {
  slug: string
  title: string
  excerpt: string | null
  category: string | null
  product: string | null
}

export interface OnboardingSubmission {
  id: string
  product_slug: string
  business_name: string
  owner_first_name: string
  owner_last_name: string
  email: string
  phone: string | null
  plan_code: string | null
  status: 'success' | 'failed'
  error_message: string | null
  created_at: string
}

export interface AdminUser {
  id: string
  name: string
  email: string
}

export interface SocialLink {
  name: string
  href: string
}

export interface NavItem {
  label: string
  description?: string | null
  icon?: string | null
  to: string
}

export interface NavSection {
  key: string
  type: 'dropdown' | 'link'
  label: string
  to?: string
  items?: NavItem[]
  viewAllLabel?: string
  viewAllTo?: string
}

// Site content blocks are genuinely dynamic CMS blobs (see
// SiteContentSeeder.php / services/siteContent.ts's flatten()/split()) —
// loosely typed on purpose rather than modeling every optional field. The
// handful of fields templates actually iterate/index directly (socials,
// contact info) are called out so `v-for`/property access type-checks
// without widening to `unknown`.
export interface HeroContent {
  id?: string
  badge_text?: string
  headline?: string
  subheadline?: string
  description?: string
  trust_line?: string
  cta_primary_label?: string
  cta_secondary_label?: string
  cta_secondary_url?: string
  stats?: { num: string; label: string }[]
  [key: string]: unknown
}

export interface AboutApproachCard {
  icon: string
  title: string
  description: string
}

export interface AboutAudienceExample {
  icon: string
  label: string
  image_url: string
}

export interface AboutContent {
  id?: string
  hero_tag?: string
  hero_heading?: string
  hero_description?: string
  hero_cta_primary_label?: string
  hero_cta_secondary_label?: string
  story_title?: string
  story_content?: string
  approach_tag?: string
  approach_title?: string
  approach_cards?: AboutApproachCard[]
  audience_title?: string
  audience_description?: string
  audience_examples?: AboutAudienceExample[]
  products_tag?: string
  products_title?: string
  products_description?: string
  email?: string
  profile_photo_url?: string
  profile_greeting?: string
  profile_name?: string
  profile_bio?: string
  profile_skills?: string[]
  socials?: SocialLink[]
  cta_title?: string
  cta_description?: string
  cta_primary_label?: string
  cta_secondary_label?: string
  [key: string]: unknown
}

export interface FooterContent {
  id?: string
  email?: string
  phone?: string
  address?: string
  socials?: SocialLink[]
  [key: string]: unknown
}

export interface BillingCycle {
  months: number
  discount_percent: string | number
  is_active: boolean
}

export interface PosPlan {
  id: string
  code: string
  name: string
  price_usd: string | number
  is_active: boolean
  billing_cycles: BillingCycle[]
  features: Array<{ id?: string; key?: string; en: string; km?: string }>
}

// Fetched live from Studio's own product API (services/studioPlans.ts) —
// a different shape from PosPlan, which comes from this site's CMS. Kept
// pragmatic/loose since it's an external API this repo doesn't own.
export interface StudioPlan {
  id: string
  code: string
  name: string
  description?: string | null
  price_monthly: string | number
  price_quarterly: string | number
  price_yearly: string | number
  trial_days: number
  is_active: boolean
  max_users?: number | null
  storage_limit_gb?: number | null
  monthly_order_limit?: number | null
  has_online_gallery?: boolean
  has_reports?: boolean
  has_telegram?: boolean
  has_api_access?: boolean
}
