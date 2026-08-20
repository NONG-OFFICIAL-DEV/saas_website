# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
npm run dev       # start Vite dev server
npm run build     # production build to dist/
npm run preview   # preview a production build locally
```

There is no lint or test script. `vitest.config.js` exists but `vitest` is not installed and there are no test files — do not assume a test suite exists. `prettier` is a dependency with a repo `.prettierrc.json` (no semicolons, single quotes, 2-space indent) but there's no `format` script; run `npx prettier --write .` if asked to format.

### Environment

Copy `.env.example` to `.env.development` (see `README.md`). Required vars:
- `VITE_APP_API_BASE_URL` — base URL of the legacy REST backend (see "Two backends" below)
- `VITE_APP_I18N_LOCALE` / `VITE_APP_I18N_FALLBACK_LOCALE` — usually `en`
- `VITE_APP_CMS_API_URL` — base URL of the CMS backend (see below), e.g. `http://127.0.0.1:8000/api/v1`

The Docker build (`Dockerfile`, `.github/workflows/ci-cd.yml`) only injects `VITE_APP_MODE`, `VITE_APP_API_BASE_URL`, and the i18n vars as build args — it does **not** currently pass `VITE_APP_CMS_API_URL`, so a container build will ship without a working CMS unless that's fixed.

## Architecture

This is a Vue 3 + Vuetify 3 + Vite marketing/SaaS site ("Nexstack") for a solo freelance developer showcasing multiple products (currently Nexstack POS and Studio Management System). It has two independent backends and a self-service CMS + admin panel.

### Two backends — don't conflate them

1. **Legacy REST API** (`src/api/*.js`, axios client in `src/api/api.js`, base URL from `VITE_APP_API_BASE_URL`): powers the *real* product — Nexstack POS's auth, lead capture (`api/leads.js`), and live subscription plans/billing (`stores/posPlans.js`, rendered in `components/sections/PriceSection.vue`). This is the only source of truth for actual money/checkout.
2. **CMS backend** (`src/services/cmsApi.js`, a standalone Laravel + PostgreSQL + Sanctum API, base URL from `VITE_APP_CMS_API_URL`, lives in the sibling `../backend` directory of this `SaaS_Website` folder): powers the CMS — marketing content for the products hub/detail pages and site-wide copy (hero, about, footer) — plus Sanctum token auth for the admin panel (`stores/adminAuth.js`). No real payments happen here. This backend is entirely separate from `smart-store-admin`/`smart_store_db` (the real POS backend) — never conflate the two.

**Pricing is never CMS-authored.** Every product's pricing is owned and controlled entirely within that product's own SaaS backend — this site only ever displays it via that product's own live API, one bespoke pricing component per product, slug-keyed in `ProductDetail.vue`: `nexstack-pos` → `PriceSection.vue` (fetches from backend #1 via `stores/posPlans.js`), `studio-management` → `StudioPriceSection.vue` (fetches from Studio's own live production API via `stores/studioPlans.js`/`services/studioPlans.js` — **not** CMS data, despite living in this repo). A product with no live pricing component yet simply shows no pricing section — there is no CMS-authored placeholder/fallback. A `pricing_mode` column + `pricing_plans` CMS table existed briefly for this and was fully removed; don't reintroduce it.

`Product::faqs()` (`product_faqs` + `product_faq_translations`) backs a generic `ProductFaqSection.vue` rendered on every product page — plain CMS content, unrelated to pricing, since there's no billing-system analog for FAQs.

### CMS data model (Laravel backend)

Schema lives in the `../backend` Laravel project as normal migrations (`database/migrations/`), not hand-run SQL. Each translatable entity is a base table + `_translations` table pair (locale + translatable fields), so English/Khmer content can be added per-row without schema changes:
- `products` + `product_translations`, `product_features` + `..._translations`, `product_screenshots` + `..._translations`, `product_faqs` + `..._translations` — normalized child tables (not jsonb), mirroring the old Supabase shape so the admin UI stays simple form fields, not raw JSON editing. (`product_pricing_tiers` existed briefly for this same shape and was removed — see the pricing note above; don't reintroduce it.)
- `solutions` + `solution_translations` + `solution_product` (pivot, custom `SolutionProduct` model with `HasUuids` since `attach()`/`sync()` don't auto-generate pivot UUIDs) — business-type groupings pointing at one or more products.
- `testimonials` + `testimonial_translations` — business-owner quotes shown on the homepage (`TestimonialsSection.vue`), optionally tied to one `product_id`.
- `site_content_blocks` + `site_content_block_translations` — singleton rows keyed by `hero`/`about`/`footer`. Each block splits into non-translatable `data` (emails, phones, social URLs — jsonb) and translatable `content` (headlines, bios, labels — jsonb on the translations table). `src/services/siteContent.js` flattens these two into one flat object for components and splits them back apart on save — see `DATA_KEYS` in that file for which fields go in which half per block.
- `media` — uploaded file records (admin logo/hero-image/screenshot uploads), served from Laravel's `storage/app/public` disk.

The public API only returns `is_published = true` products; the admin API (Sanctum-protected) returns everything including drafts, plus every locale's raw translation rows for the editor.

**CMS content is English-only by design today** — none of it goes through vue-i18n, even though the rest of the site's chrome (nav labels, buttons, static UI strings) is bilingual (`src/locales/en.json` / `km.json`). The backend's translation-table design supports adding Khmer CMS content later (just send `locale: 'km'` on writes), but don't wire that up unless explicitly asked.

Every CMS-backed Pinia store (`stores/products.js`, `stores/siteContent.js`) follows the same shape: seed the ref with a hardcoded fallback (matching the backend's seed data) *before* fetching, so a down API, empty table, or network error degrades to the fallback copy instead of a blank/broken page. Preserve this pattern when touching these stores.

`src/services/cmsApi.js` is the axios client for this backend: attaches a Bearer token (from `localStorage['cms_admin_token']`) to every request, and unwraps Laravel's `{message, errors}` error payloads into a plain `Error` with `.message`/`.status` so calling code can keep using `err.message` like before.

### Routing (`src/router/index.js`)

Three route groups with different layouts:
- **Public marketing site**: nested under `LandingLayout.vue` (`/`, `/products`, `/products/:slug`, `/about`) — shares `AppNavbar`, `AppFooter`, `FloatingCtaDock`.
- **Standalone pages**: `/auth/register`, `/terms`, `/privacy`, — no shared layout, self-contained.
- **Admin**: nested under `views/admin/AdminLayout.vue` (`/admin`, `/admin/products/new`, `/admin/products/:id/edit`, `/admin/site-content`) — Vuetify `v-navigation-drawer` sidebar layout, separate from the public nav entirely.

`scrollBehavior` resets scroll to top on normal navigation, smooth-scrolls to hash targets (`/#contact`), and restores position on browser back/forward — this is deliberate, don't remove it.

`router.beforeEach` (using `stores/adminAuth.js`) is live and guards every `/admin/*` route — unauthenticated visitors are redirected to `/admin/login`, and an already-logged-in visitor hitting `/admin/login` is redirected to the dashboard instead.

### AOS (scroll animations) across nested routes

`LandingLayout.vue` calls `AOS.init()` once in `onMounted` — but since it wraps all public-site child routes, it never remounts when navigating between them (only `<router-view>`'s content changes). It has a `watch(() => route.fullPath, ...)` that calls `AOS.refreshHard()` after each navigation to pick up the new page's `data-aos` elements. Keep this in mind if scroll-reveal animations seem "stuck" after adding new sections.

### Documentation system (`views/documentation/*`, `components/documentation/*`, `components/admin/RichTextEditor.vue`)

Full CMS-driven docs, replacing the earlier video-only `/docs` stub (`/docs` now just redirects to `/documentation`; `DocsPage.vue` was deleted). Two new backend entities, both base+translation pairs like every other CMS content type: `documentation_categories` (self-referencing `parent_id` for one level of nesting, optional `product_id` — general categories like Getting Started/Troubleshooting have none) and `documentation_articles` (`draft`/`published`/`archived`, optional `product_id` independent of the category's own — the admin can override it per-article). Seeded with the full category tree and ~27 real, genuinely-written articles (generic-actionable style — "Open Bookings from the sidebar, click New Booking" — not fabricated pixel-perfect UI claims, since I don't have screenshot-level access to either live product).

- **`/documentation`** (`DocumentationHome.vue`) — hero + live search (debounced, ≥2 chars) + one card per product-linked category (with a "Watch demo" link if that product has a `demo_video_url`) + the Getting Started category's articles rendered as numbered steps + a flat chip list of the remaining general categories.
- **`/documentation/:slug`** (`DocumentationArticle.vue`) — 3-column layout: `DocsCategoryNav.vue` (full tree, current article highlighted) on the left, article content in the middle (breadcrumb → header → sanitized HTML content → was-this-helpful feedback (client-side only, no persistence) → prev/next via `sort_order` within the category → related articles), and an auto-generated `DocsTableOfContents.vue` on the right built by regexing the content for `h2`/`h3` tags, injecting `id`s, and scrollspying via `IntersectionObserver`. Both sidebars collapse into native `<details>` elements below `md` — deliberately zero-JS for the collapse behavior itself.
- Content is rendered via `v-html` after `DOMPurify.sanitize()` — never trust raw admin HTML on the public page even though only the trusted admin can author it.
- **Rich text editor** (`components/admin/RichTextEditor.vue`): Tiptap v3 (`@tiptap/vue-3` + `starter-kit` + `extension-image` + `extension-table`'s `TableKit` + `extension-placeholder`). Tiptap v3's `StarterKit` bundles `Link` by default now — don't add a separate `@tiptap/extension-link`, configure it via `StarterKit.configure({ link: {...} })` instead, or you'll get silent duplicate-extension warnings. Callout blocks (Tip/Important/Note, rendered as `<div data-type="callout" data-variant="tip|important|note">`) are a custom node in `components/admin/CalloutExtension.js` — its `setCallout` command checks `editor.isActive('callout')` first and updates the attribute in place rather than always `wrapIn`-ing, since blindly wrapping nests callouts inside each other when the toolbar button is clicked while already inside one (a real bug found and fixed during this build).
- **Video embeds** (`components/admin/VideoEmbedExtension.js`, a custom atom node rendered as `<div data-type="video-embed"><iframe data-src="..." src="...">`): the toolbar's YouTube icon prompts for a URL and converts it via `utils/videoEmbed.js`'s `toEmbedUrl()` (shared — also used by `DocumentationHome.vue`'s per-product "Watch demo" link), refusing to insert anything it can't recognize as a YouTube/Vimeo link rather than silently inserting a broken embed. In the node's `renderHTML`, read the real attribute value from `node.attrs.src`, not `HTMLAttributes.src` — the `src` attribute's own `renderHTML` hook deliberately returns `{}` (it belongs on the inner `<iframe>`, not the outer wrapper div), which means `HTMLAttributes.src` is always undefined; this was a real bug caught during testing (iframe rendered with no `src` at all). `DocumentationArticle.vue` allows `<iframe>` through DOMPurify via `ADD_TAGS`/`ADD_ATTR`, then runs a second pass (`stripDisallowedIframes`) that removes any iframe whose `src` isn't `isAllowedEmbedSrc()` (youtube.com/embed or player.vimeo.com only) — verified by injecting a fake malicious `src` directly via the API and confirming it gets stripped on render while surrounding content survives.
- Admin CRUD: `AdminDocCategoriesDashboard/Editor.vue`, `AdminDocArticlesDashboard/Editor.vue` (uses `RichTextEditor`), `services/adminDocumentation.js`. Picking a category in the article editor defaults the product field to that category's own product (still overridable).
- `/help` (Help Center) remains separate and simpler — CMS-backed FAQs per product plus contact channels from `site_content_blocks`' `footer` key — it isn't part of the Documentation tree.

### Product detail pages: generic CMS + bespoke "deep dive" extras

`views/products/ProductDetail.vue` always renders the generic CMS-driven blocks (features grid, screenshots, pricing) for any product. Additionally, it has a hardcoded `DEEP_DIVE_EXTRAS` map keyed by product `slug` that injects extra hand-built Vue components after the generic content — currently only `nexstack-pos` has entries (`RestaurantPosSection`, `InventorySection`, `MobileQrSection`, all POS-specific, non-i18n'd mockup components under `components/sections/`). `BizTypesSection`/`FeatureCardsSection` used to be in this list but were removed (and deleted from disk) for duplicating content shown elsewhere on the page; `TargetAudienceSection`/`FaqSection` never existed. Use this pattern for any product that needs bespoke marketing sections beyond what the generic CMS fields support, rather than hardcoding product-specific logic into the generic rendering path.

### Onboarding — no platform accounts, a stateless provisioning wizard instead

There is deliberately no platform-level user/account system (login, sessions, password reset) on this site. Studio and Smart Store already each have their own complete, independent auth/tenant systems — building a third, redundant one here was considered and explicitly rejected. Instead, `views/OnboardingWizard.vue` (route `/onboarding/:slug`, driven from `/get-started` cards, `PriceSection.vue`'s plan CTAs, and `ProductDetail.vue`'s final CTA/Studio pricing CTA for `nexstack-pos`/`studio-management` only) is a two-step form (business info → owner info) that POSTs straight to `../backend`'s `OnboardingController` (`v1/public/onboarding/provision`), which server-side calls that specific product's own already-existing public registration endpoint (Studio's `/api/v1/auth/register`, Smart Store's `/api/v1/public/business-register` — see `app/Services/Onboarding/*ProvisioningAdapter.php` in the backend) via `Http::post`. On success the wizard shows a "your workspace is ready" screen linking straight to that product's own login page — the user logs in there with the email/password they just typed; no token handoff, no shared session. In-progress form state is saved to `localStorage` (keyed per product slug, cleared on success) since there's no server-side account to attach a resumable session to. Smart Store's registration additionally requires a real `business_type_id` UUID, fetched live through `OnboardingController::businessTypes` (a read-only proxy to Smart Store's own `/api/v1/public/business-types`, kept server-side to avoid a CORS dependency on that product). A product not in `ONBOARDABLE_SLUGS` (defined redundantly in each of the three call sites above) has no guided wizard yet and falls back to `config/productTrials.js`'s external-link behavior instead.

Every provisioning attempt (success or failure) is logged to the backend's `onboarding_submissions` table — product slug, business name, owner name/email/phone, plan code, status, and (on failure) the error message. This is a visibility log only, not an account system: it exists so the solo builder has one place (`/admin/onboarding`) to see signups across both products instead of checking each product's own admin panel separately. The password is deliberately never stored. Both provisioning adapters and `businessTypes()` catch `Illuminate\Http\Client\ConnectionException` around their `Http::` calls (Studio/Smart Store being unreachable is a real, expected failure mode, not an edge case) and return a graceful `{success:false}` response instead of a raw 500 — don't remove those try/catches, and add the same one to any new adapter.

### Loading UX — no global blocking, everything is localized

`cmsApi.js`, `studioApi.js`, and `api/api.js` all default their interceptor's loader mode to `'skip'` — no request triggers a global blocking indicator unless a call explicitly opts in via `{ meta: { loader: 'bar' } }`. Every component owns its own scoped loading state (`loading`, `saving`, `uploadingImage`, etc. — never one shared `isLoading`) and renders its own small, localized indicator: a button's `:loading`, a section's `InlineLoader` (`components/global/InlineLoader.vue` — a small centered `v-progress-circular` with a `min-height` to reserve space and avoid layout shift), or a `v-alert` for errors. `components/global/Loading.vue` (mounted once in `App.vue`) is a thin fixed top progress bar for the rare `loader: 'bar'` opt-in — never a full-screen overlay/backdrop; nothing calls it today.

**Don't reintroduce `v-skeleton-loader` or `v-overlay` for loading.** Both were fully removed from the codebase (14 files) in favor of `InlineLoader` — large skeleton grids and full-page blocking overlays make the app feel frozen even when only one section is fetching; a small spinner that doesn't hide already-rendered UI (nav, footer, page chrome) is the standard here. `stores/loadingStore.js` is reference-counted (not a boolean) so concurrent `'bar'`-mode calls don't hide it prematurely.

`router/index.js` starts/stops the `'bar'` loader around every navigation (a lazy route chunk is a real network fetch). Guarded by a module-level `navigationInFlight` flag, not a bare start/stop pair — a redirect (e.g. the admin-auth guard bouncing an unauthenticated visitor from `/admin` to `/admin/login`) makes Vue Router re-run the whole `beforeEach` chain for the new target, which would call `start()` twice for one logical navigation while `afterEach` only fires once on the navigation that actually completes; without the flag the bar gets permanently stuck showing after any redirect.

**Completed CRUD actions use the existing toast system, not inline banners.** `components/global/Notification.vue` (`$notif()` / `composables/useNotif.js`'s `useNotif()`, mounted once in `App.vue`) was fully built but unused until this pass — every admin editor/dashboard now calls `notify(message, { type: 'success' | 'error' })` on save/delete instead of a `savedNotice` ref + inline "Saved." `v-alert`. The one exception: `error` + an inline `v-alert` is still used for a *persistent* load failure (e.g. "Product not found." when the record itself can't be fetched) — that's ongoing page state, not a fleeting action result, so it stays visible rather than auto-dismissing like a toast.

### Vuetify theme (`src/plugins/vuetify.js`)

Centralizes component defaults instead of scattering `variant`/`density`/`rounded` props per instance. Notably: **`VBtn` defaults to `rounded: 'lg'` globally** — this is an intentional, explicit design decision (buttons across the whole site were normalized to one rounding value). Don't add `rounded="xl"`/`"pill"` to a new button; use the default. `defaultTheme` reads `localStorage.getItem('theme')` and defaults to **dark** (light is opt-in via the theme toggle), light/dark color tokens are both defined.

### Path alias

`@` → `src/` (configured in `vite.config.js`, shared by `vitest.config.js`).
