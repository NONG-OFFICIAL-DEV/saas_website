# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
npm run dev       # start Nuxt dev server
npm run build     # production build (Nitro node-server, real SSR)
npm run preview   # preview a production build locally
npx nuxi typecheck  # type-check the whole app
```

There is no lint or test script configured.

### Environment

Copy `.env.example` to `.env`. These are Nuxt **runtime config** values (see `nuxt.config.ts`'s `runtimeConfig.public`), read at container/process start rather than baked in at build time — the same built image works across environments without a rebuild:
- `NUXT_PUBLIC_CMS_API_URL` — base URL of this repo's own CMS backend, e.g. `http://127.0.0.1:8000/api/v1`
- `NUXT_PUBLIC_SMART_STORE_API_URL` / `NUXT_PUBLIC_SMART_STORE_APP_URL` — Nexstack POS's own live API and app
- `NUXT_PUBLIC_STUDIO_API_URL` / `NUXT_PUBLIC_STUDIO_APP_URL` — Studio Management's own live API and app

(see "Three backends, three folders" below)

## Architecture

This is a **Nuxt 4 + Vue 3 + Vuetify 3 + TypeScript** marketing/SaaS site ("Nexstack") for a solo freelance developer showcasing multiple products (currently Nexstack POS and Studio Management System). It has two independent backends and a self-service CMS + admin panel. It was migrated from a Vue 3 + Vite SPA to Nuxt specifically for **real server-side rendering** — public marketing/CMS/documentation pages are genuinely rendered on the server (verified via `curl`, not just checked in a browser after hydration), not just client-rendered after a blank shell.

Nuxt 4's `app/` source-directory convention is used throughout: `app/pages` (file-based routing), `app/components` (auto-imported, **`pathPrefix: false`** configured in `nuxt.config.ts` so components keep their original flat names — e.g. `AppNavbar`, `HeroSection` — instead of Nuxt's default directory-prefixed names), `app/composables`, `app/stores`, `app/services` (see below), `app/middleware`, `app/layouts`, `app/utils`, `app/types`. `app/services/*` is **not** auto-imported (only `composables/`/`utils`/`stores` are) — import explicitly, e.g. `import { getProducts } from '~/services/cms/products'`.

### SSR-relevant conventions — read this before touching data fetching

- **Every page that's the actual point of a route awaits its data fetch via `useAsyncData()`** at the top of `<script setup>`, not a bare `onMounted(() => store.fetchX())`. `onMounted` never runs during SSR, so a naive port of the old fetch-on-mount pattern would silently produce empty server-rendered HTML — this was a real bug caught and fixed during the migration (see `app/pages/index.vue`, `app/pages/products/[slug].vue`, etc.). Shared layout chrome that repeats on every page (nav dropdown items, footer's product list) is the deliberate exception — those stay `onMounted`-fetched (client-only) to avoid blocking every single page load on decorative, low-SEO-value data; only genuinely primary page content is awaited.
- **`experimental.asyncContext: true`** is enabled in `nuxt.config.ts` — without it, only the *first* `await` in a component's setup keeps Nuxt's request context; every awaited composable call after that would throw `NUXT_E1001` ("called outside setup"). This was hit for real during the migration (any page with two sequential `await useAsyncData(...)` calls) and is why this flag exists — don't remove it.
- Dynamic-route pages (`products/[slug].vue`, `documentation/[slug].vue`, etc.) use `useAsyncData(() => \`key-${slug.value}\`, fetcher, { watch: [slug] })` — the `watch` option re-runs the fetch on client-side navigation between two pages of the same route (Vue Router reuses the component instance across param-only changes, so a plain top-level call wouldn't refire on its own).
- `useSeoMeta()` calls are placed **before** the awaited `useAsyncData()` in the same file, not after — a composable call after an awaited call can lose Nuxt's request context even with `asyncContext` enabled in some cases; keeping them ordered this way avoids the whole class of issue.
- The admin CMS (`/admin/**`) is forced fully client-rendered via `routeRules: { '/admin/**': { ssr: false } } ` in `nuxt.config.ts` — it's auth-gated, has zero SEO value, and depends on `localStorage` + the Tiptap rich-text editor (a real DOM dependency). This one route rule eliminates that whole class of SSR hazard instead of guarding dozens of individual call sites. Admin pages keep the simpler `onMounted`-fetch pattern from the original app — there's no SSR benefit to awaiting them.
- `~/services/cms/api.ts`, `~/services/studio/api.ts`, `~/services/smartStore/api.ts` each call `useRuntimeConfig()` **inside** their axios request interceptor (not at module top level) and guard `localStorage` reads with `import.meta.client` — `useRuntimeConfig()` needs an active Nuxt context, which module-scope code doesn't have at cold-import time.
- `~/config/productTrials.ts`'s `getTrialLink()`/`getLoginLink()` likewise call `useRuntimeConfig()` inside the function body, not at module scope, for the same reason.

### Three backends, three folders — don't conflate them

`app/services/` is organized **by which backend a file talks to**, not by feature. Each backend gets its own subfolder with an `api.ts` axios client; everything else in that folder calls it.

- **`app/services/cms/`** — this repo's own Laravel + PostgreSQL + Sanctum backend (`../backend`), base URL `NUXT_PUBLIC_CMS_API_URL`. `api.ts` is the client; `products.ts`, `solutions.ts`, `blog.ts`, `testimonials.ts`, `siteContent.ts`, `documentation.ts`, `onboarding.ts`, and the `admin*.ts` files all call it. Powers marketing content, site-wide copy, and Sanctum auth for the admin panel (`stores/adminAuth.ts`). No real payments happen here.
- **`app/services/smartStore/`** — Nexstack POS's own separate live backend (`smart-store-admin`/`smart_store_db`, not part of this repo — never conflate the two). `api.ts` is the client (base URL `NUXT_PUBLIC_SMART_STORE_API_URL`), `plans.ts` fetches its live pricing plans (`stores/posPlans.ts`, rendered in `PriceSection.vue` — the only source of truth for actual money/checkout). `config/productTrials.ts` builds register/login links to its app via `NUXT_PUBLIC_SMART_STORE_APP_URL`.
- **`app/services/studio/`** — Studio Management's own separate live backend, same shape (`api.ts` + `plans.ts`, `NUXT_PUBLIC_STUDIO_API_URL` / `NUXT_PUBLIC_STUDIO_APP_URL`).

New CMS-backed content types go in `services/cms/`. A new external product gets its own `services/<name>/` folder with the same `api.ts` (+ `plans.ts` if it has pricing) shape — don't mix an external product's client into `services/cms/`, and don't add a new top-level `app/api/` folder (that used to exist and was folded into `services/smartStore/`).

**Pricing is never CMS-authored.** Every product's pricing is owned and controlled entirely within that product's own SaaS backend — this site only ever displays it via that product's own live API, one bespoke pricing component per product, slug-keyed in `pages/products/[slug].vue`: `nexstack-pos` → `PriceSection.vue` (fetches via `stores/posPlans.ts` + `services/smartStore/plans.ts`), `studio-management` → `StudioPriceSection.vue` (fetches from Studio's own live production API via `stores/studioPlans.ts` + `services/studio/plans.ts` — **not** CMS data, despite living in this repo). A product with no live pricing component yet simply shows no pricing section — there is no CMS-authored placeholder/fallback.

`Product::faqs()` (`product_faqs` + `product_faq_translations`) backs a generic `ProductFaqSection.vue` rendered on every product page — plain CMS content, unrelated to pricing, since there's no billing-system analog for FAQs.

### CMS data model (Laravel backend)

Schema lives in the `../backend` Laravel project as normal migrations (`database/migrations/`), not hand-run SQL. Each translatable entity is a base table + `_translations` table pair (locale + translatable fields), so English/Khmer content can be added per-row without schema changes:
- `products` + `product_translations`, `product_features` + `..._translations`, `product_screenshots` + `..._translations`, `product_faqs` + `..._translations` — normalized child tables (not jsonb), mirroring the old Supabase shape so the admin UI stays simple form fields, not raw JSON editing.
- `solutions` + `solution_translations` + `solution_product` (pivot, custom `SolutionProduct` model with `HasUuids` since `attach()`/`sync()` don't auto-generate pivot UUIDs) — business-type groupings pointing at one or more products.
- `testimonials` + `testimonial_translations` — business-owner quotes shown on the homepage (`TestimonialsSection.vue`), optionally tied to one `product_id`.
- `site_content_blocks` + `site_content_block_translations` — singleton rows keyed by `hero`/`about`/`footer`. Each block splits into non-translatable `data` (emails, phones, social URLs — jsonb) and translatable `content` (headlines, bios, labels — jsonb on the translations table). `services/siteContent.ts` flattens these two into one flat object for components and splits them back apart on save — see `DATA_KEYS` in that file for which fields go in which half per block.
- `media` — uploaded file records (admin logo/hero-image/screenshot uploads), served from Laravel's `storage/app/public` disk.

The public API only returns `is_published = true` products; the admin API (Sanctum-protected) returns everything including drafts, plus every locale's raw translation rows for the editor.

**CMS content is English-only by design today** — none of it goes through vue-i18n, even though the rest of the site's chrome (nav labels, buttons, static UI strings) is bilingual (`i18n/locales/en.json` / `km.json`). The backend's translation-table design supports adding Khmer CMS content later (just send `locale: 'km'` on writes), but don't wire that up unless explicitly asked.

Every CMS-backed Pinia store (`stores/products.ts`, `stores/siteContent.ts`) follows the same shape: seed the ref with a hardcoded fallback (matching the backend's seed data) *before* fetching, so a down API, empty table, or network error degrades to the fallback copy instead of a blank/broken page. Preserve this pattern when touching these stores. (Note: `products`/`solutions`/`blog`/`testimonials`/`documentation`/`posPlans`/`studioPlans` do **not** have fallback seed data — this is why their consuming pages must await the fetch via `useAsyncData` for SSR, per the section above.)

`app/services/cms/api.ts` is the axios client for this backend: attaches a Bearer token (from `localStorage['cms_admin_token']`, client-only) to every request, and unwraps Laravel's `{message, errors}` error payloads into a plain `Error` with `.message`/`.status` so calling code can keep using `err.message` like before.

### Routing (`app/pages/**`, file-based)

Nuxt file-based routing replaces the old `vue-router` config file — directory structure *is* the route table (`pages/products/index.vue` → `/products`, `pages/products/[slug].vue` → `/products/:slug`). Three route groups with different layouts:
- **Public marketing site**: uses `app/layouts/default.vue` (shares `AppNavbar`, `AppFooter`, `FloatingCtaDock`) — this is Nuxt's default layout, applied automatically to any page that doesn't override it.
- **Standalone pages**: `pages/terms.vue`, `pages/privacy.vue` — `definePageMeta({ layout: false })`, no shared layout, self-contained.
- **Admin**: `app/layouts/admin.vue` (Vuetify `v-navigation-drawer` sidebar layout), applied via `definePageMeta({ layout: 'admin' })` on every `pages/admin/**` page. Separate from the public nav entirely, and force-client-only via the `/admin/**` route rule (see SSR section above).

Custom scroll behavior (`app/router.options.ts`) resets scroll to top on normal navigation, smooth-scrolls to hash targets (`/#contact`), and restores position on browser back/forward — matches the original app's behavior deliberately, don't remove it.

`app/middleware/admin-auth.global.ts` (using `stores/adminAuth.ts`) is a global route middleware guarding every `/admin/*` route — unauthenticated visitors are redirected to `/admin/login`, and an already-logged-in visitor hitting `/admin/login` is redirected to the dashboard instead. It exits immediately for non-admin paths.

### AOS (scroll animations) across nested routes

`app/layouts/default.vue` calls `AOS.init()` once in `onMounted` — but since it wraps all public-site child pages, it never remounts when navigating between them (only the page content inside `<slot />` changes). It has a `watch(() => route.fullPath, ...)` that calls `AOS.refreshHard()` after each navigation to pick up the new page's `data-aos` elements. Keep this in mind if scroll-reveal animations seem "stuck" after adding new sections.

### Documentation system (`pages/documentation/**`, `components/documentation/*`, `components/admin/RichTextEditor.vue`)

Full CMS-driven docs. Two backend entities, both base+translation pairs like every other CMS content type: `documentation_categories` (self-referencing `parent_id` for one level of nesting, optional `product_id` — general categories like Getting Started/Troubleshooting have none) and `documentation_articles` (`draft`/`published`/`archived`, optional `product_id` independent of the category's own — the admin can override it per-article).

- **`/documentation`** (`pages/documentation/index.vue`) — hero + live search (debounced, ≥2 chars) + one card per product-linked category (with a "Watch demo" link if that product has a `demo_video_url`) + the Getting Started category's articles rendered as numbered steps + a flat chip list of the remaining general categories. Categories are awaited via `useAsyncData` for SSR; the search itself stays client-only (`watch(query, ...)` with a debounce timer — inherently interactive, no SEO value in transient search results).
- **`/documentation/:slug`** (`pages/documentation/[slug].vue`) — 3-column layout: `DocsCategoryNav.vue` (full tree, current article highlighted) on the left, article content in the middle (breadcrumb → header → sanitized HTML content → was-this-helpful feedback (client-side only, no persistence) → prev/next via `sort_order` within the category → related articles), and an auto-generated `DocsTableOfContents.vue` on the right built by regexing the content for `h2`/`h3` tags, injecting `id`s, and scrollspying via `IntersectionObserver` (guarded with `if (import.meta.server) return` at the top of `setupScrollSpy()` — `IntersectionObserver`/`document` don't exist server-side). Both sidebars collapse into native `<details>` elements below `md` — deliberately zero-JS for the collapse behavior itself.
- Content is rendered via `v-html` after sanitizing — **`isomorphic-dompurify`**, not plain `dompurify` (this page genuinely server-renders, and plain DOMPurify throws in Node with no DOM). The iframe-src allowlist (only our own generated YouTube/Vimeo embed URLs get through) is implemented as a DOMPurify `uponSanitizeElement` hook in `app/utils/sanitizeArticleHtml.ts`, registered **once at module load**, not via a second `DOMParser`-based pass over the sanitized output the way the original Vite app did it — `DOMParser` doesn't exist in Node, so that approach couldn't survive SSR; the hook runs inside DOMPurify's own single sanitize() call instead, which works identically client- and server-side.
- **Rich text editor** (`components/admin/RichTextEditor.vue`): Tiptap v3 (`@tiptap/vue-3` + `starter-kit` + `extension-image` + `extension-table`'s `TableKit` + `extension-placeholder`). Tiptap v3's `StarterKit` bundles `Link` by default — don't add a separate `@tiptap/extension-link`, configure it via `StarterKit.configure({ link: {...} })` instead. Callout blocks (Tip/Important/Note, rendered as `<div data-type="callout" data-variant="tip|important|note">`) are a custom node in `components/admin/CalloutExtension.ts` — its `setCallout` command checks `editor.isActive('callout')` first and updates the attribute in place rather than always `wrapIn`-ing, since blindly wrapping nests callouts inside each other when the toolbar button is clicked while already inside one.
- **Video embeds** (`components/admin/VideoEmbedExtension.ts`, a custom atom node rendered as `<div data-type="video-embed"><iframe data-src="..." src="...">`): the toolbar's YouTube icon prompts for a URL and converts it via `app/utils/videoEmbed.ts`'s `toEmbedUrl()` (shared — also used by `pages/documentation/index.vue`'s per-product "Watch demo" link), refusing to insert anything it can't recognize as a YouTube/Vimeo link. In the node's `renderHTML`, read the real attribute value from `node.attrs.src`, not `HTMLAttributes.src` — the `src` attribute's own `renderHTML` hook deliberately returns `{}` (it belongs on the inner `<iframe>`, not the outer wrapper div).
- Admin CRUD: `pages/admin/documentation/categories/**` + `components/admin/DocCategoryEditorForm.vue`, `pages/admin/documentation/articles/**` + `components/admin/DocArticleEditorForm.vue` (uses `RichTextEditor`), `services/cms/adminDocumentation.ts`. Picking a category in the article editor defaults the product field to that category's own product (still overridable).
- `/help` (Help Center, `pages/help.vue`) remains separate and simpler — CMS-backed FAQs per product plus contact channels from `site_content_blocks`' `footer` key — it isn't part of the Documentation tree.

### Product detail pages: generic CMS + bespoke "deep dive" extras

`pages/products/[slug].vue` always renders the generic CMS-driven blocks (features grid, screenshots, pricing) for any product. Additionally, it has a hardcoded `DEEP_DIVE_EXTRAS` map keyed by product `slug` that injects extra hand-built Vue components after the generic content — currently only `nexstack-pos` has entries (`RestaurantPosSection`, `InventorySection`, `MobileQrSection`, all POS-specific, non-i18n'd mockup components under `components/sections/`). Use this pattern for any product that needs bespoke marketing sections beyond what the generic CMS fields support, rather than hardcoding product-specific logic into the generic rendering path.

### Onboarding — no platform accounts, a stateless provisioning wizard instead

There is deliberately no platform-level user/account system (login, sessions, password reset) on this site. Studio and Smart Store already each have their own complete, independent auth/tenant systems — building a third, redundant one here was considered and explicitly rejected. Instead, `pages/onboarding/[slug].vue` (route `/onboarding/:slug`, driven from `/get-started` cards, `PriceSection.vue`'s plan CTAs, and `pages/products/[slug].vue`'s final CTA/Studio pricing CTA for `nexstack-pos`/`studio-management` only) is a two-step form (business info → owner info) that POSTs straight to `../backend`'s `OnboardingController` (`v1/public/onboarding/provision`), which server-side calls that specific product's own already-existing public registration endpoint (Studio's `/api/v1/auth/register`, Smart Store's `/api/v1/public/business-register` — see `app/Services/Onboarding/*ProvisioningAdapter.php` in the backend) via `Http::post`. On success the wizard shows a "your workspace is ready" screen linking straight to that product's own login page — the user logs in there with the email/password they just typed; no token handoff, no shared session. In-progress form state is saved to `localStorage` (keyed per product slug, cleared on success, guarded implicitly by only ever running inside `onMounted`/`watch` — inherently client-only) since there's no server-side account to attach a resumable session to. Smart Store's registration additionally requires a real `business_type_id` UUID, fetched live through `OnboardingController::businessTypes` (a read-only proxy to Smart Store's own `/api/v1/public/business-types`, kept server-side to avoid a CORS dependency on that product). A product not in `ONBOARDABLE_SLUGS` (defined redundantly in each of the three call sites above) has no guided wizard yet and falls back to `config/productTrials.ts`'s external-link behavior instead.

Every provisioning attempt (success or failure) is logged to the backend's `onboarding_submissions` table — product slug, business name, owner name/email/phone, plan code, status, and (on failure) the error message. This is a visibility log only, not an account system: it exists so the solo builder has one place (`/admin/onboarding`) to see signups across both products instead of checking each product's own admin panel separately. The password is deliberately never stored. Both provisioning adapters and `businessTypes()` catch `Illuminate\Http\Client\ConnectionException` around their `Http::` calls and return a graceful `{success:false}` response instead of a raw 500 — don't remove those try/catches, and add the same one to any new adapter.

### Loading UX — no global blocking, everything is localized

`services/cms/api.ts`, `services/studio/api.ts`, and `services/smartStore/api.ts` all default their interceptor's loader mode to `'skip'` — no request triggers a global blocking indicator unless a call explicitly opts in via `{ meta: { loader: 'bar' } }`. Every component owns its own scoped loading state (`loading`, `saving`, `uploadingImage`, etc. — never one shared `isLoading`) and renders its own small, localized indicator: a button's `:loading`, a section's `InlineLoader` (`components/global/InlineLoader.vue` — a small centered `v-progress-circular` with a `min-height` to reserve space and avoid layout shift), or a `v-alert` for errors. `components/global/Loading.vue` (mounted once in `app.vue`) is a thin fixed top progress bar for the rare `loader: 'bar'` opt-in — never a full-screen overlay/backdrop.

**Don't reintroduce `v-skeleton-loader` or `v-overlay` for loading.** Both were fully removed from the codebase in favor of `InlineLoader` — large skeleton grids and full-page blocking overlays make the app feel frozen even when only one section is fetching; a small spinner that doesn't hide already-rendered UI (nav, footer, page chrome) is the standard here. `stores/loadingStore.ts` is reference-counted (not a boolean) so concurrent `'bar'`-mode calls don't hide it prematurely.

**Page-transition loading is Nuxt's own `<NuxtLoadingIndicator>`** (mounted in `app.vue`), replacing the old hand-rolled `router.beforeEach`/`afterEach` + `loadingStore` top-bar mechanism — Nuxt drives it directly off real navigation lifecycle, so it's structurally immune to the double-count-on-redirect bug class the old manual version needed a `navigationInFlight` flag to work around. `loadingStore.ts`'s reference-counted `'bar'` mode still exists and is still used by the rare non-navigation call that opts into the top bar via `{ meta: { loader: 'bar' } }`.

**Completed CRUD actions use the existing toast system, not inline banners.** `components/global/Notification.vue` (`$notif()` / `composables/useNotif.ts`'s `useNotif()`, mounted once in `app.vue`, wired up via `app.config.globalProperties` in `app.vue`'s `onMounted`) — every admin editor/dashboard calls `notify(message, { type: 'success' | 'error' })` on save/delete instead of a `savedNotice` ref + inline "Saved." `v-alert`. The one exception: `error` + an inline `v-alert` is still used for a *persistent* load failure (e.g. "Product not found." when the record itself can't be fetched) — that's ongoing page state, not a fleeting action result, so it stays visible rather than auto-dismissing like a toast.

### Vuetify theme (`nuxt.config.ts`'s `vuetify.vuetifyOptions`)

Configured via `vuetify-nuxt-module` instead of a hand-written plugin (`src/plugins/vuetify.js` in the old app) — the module handles SSR style injection and hydration correctly out of the box. Centralizes component defaults instead of scattering `variant`/`density`/`rounded` props per instance. Notably: **`VBtn` defaults to `rounded: 'lg'` globally** — this is an intentional, explicit design decision (buttons across the whole site were normalized to one rounding value). Don't add `rounded="xl"`/`"pill"` to a new button; use the default. `defaultTheme` is hardcoded to `'dark'` (light is opt-in via the theme toggle) — the original app additionally persisted the user's choice via `localStorage.getItem('theme')` read at module top-level; that specific persistence pattern is an open TODO here (deferred because reading `localStorage` at module scope is an SSR hazard) — light/dark color tokens are both already defined and `ThemeToggle.vue`/`MobileNavDrawer.vue` already write `localStorage.setItem('theme', ...)` on toggle, just nothing yet restores it via a client-safe pattern on load.

### Path alias

`~/` → `app/` (Nuxt's own convention, configured automatically — no manual `vite.config.js` alias needed the way the old app required `@` → `src/`).
