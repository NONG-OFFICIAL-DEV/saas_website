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
- `VITE_APP_CMS_API_URL` — base URL of the CMS backend (see below), e.g. `http://127.0.0.1:8001/api/v1`

The Docker build (`Dockerfile`, `.github/workflows/ci-cd.yml`) only injects `VITE_APP_MODE`, `VITE_APP_API_BASE_URL`, and the i18n vars as build args — it does **not** currently pass `VITE_APP_CMS_API_URL`, so a container build will ship without a working CMS unless that's fixed.

## Architecture

This is a Vue 3 + Vuetify 3 + Vite marketing/SaaS site ("Nexstack") for a solo freelance developer showcasing multiple products (currently Nexstack POS and Studio Management System). It has two independent backends and a self-service CMS + admin panel.

### Two backends — don't conflate them

1. **Legacy REST API** (`src/api/*.js`, axios client in `src/api/api.js`, base URL from `VITE_APP_API_BASE_URL`): powers the *real* product — Nexstack POS's auth, registration (`Register.vue` + `components/register/*`), lead capture (`api/leads.js`), and live subscription plans/billing (`stores/register.js`, rendered in `components/sections/PriceSection.vue`). This is the only source of truth for actual money/checkout.
2. **CMS backend** (`src/services/cmsApi.js`, a standalone Laravel + PostgreSQL + Sanctum API, base URL from `VITE_APP_CMS_API_URL`, lives in the sibling `../backend` directory of this `SaaS_Website` folder): powers the CMS — marketing content for the products hub/detail pages and site-wide copy (hero, about, footer) — plus Sanctum token auth for the admin panel (`stores/adminAuth.js`). No real payments happen here. This backend is entirely separate from `smart-store-admin`/`smart_store_db` (the real POS backend) — never conflate the two.

Because of this split, `ProductDetail.vue` treats pricing specially: for `nexstack-pos` it renders the real `PriceSection.vue` (live billing via backend #1); every other product renders CMS-authored `product_pricing_tiers` rows instead (marketing-only "starting at" cards, no checkout).

### CMS data model (Laravel backend)

Schema lives in the `../backend` Laravel project as normal migrations (`database/migrations/`), not hand-run SQL. Each translatable entity is a base table + `_translations` table pair (locale + translatable fields), so English/Khmer content can be added per-row without schema changes:
- `products` + `product_translations`, `product_features` + `..._translations`, `product_pricing_tiers` + `..._translations`, `product_screenshots` + `..._translations` — normalized child tables (not jsonb), mirroring the old Supabase shape so the admin UI stays simple form fields, not raw JSON editing.
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

There is a commented-out `router.beforeEach` guard (using `stores/adminAuth.js`) that's meant to redirect unauthenticated users away from `/admin/*`. It was disabled mid-debugging a Supabase Auth login issue and **needs to be re-enabled** before `/admin` is exposed anywhere real — check its current state before assuming `/admin` is protected.

### AOS (scroll animations) across nested routes

`LandingLayout.vue` calls `AOS.init()` once in `onMounted` — but since it wraps all public-site child routes, it never remounts when navigating between them (only `<router-view>`'s content changes). It has a `watch(() => route.fullPath, ...)` that calls `AOS.refreshHard()` after each navigation to pick up the new page's `data-aos` elements. Keep this in mind if scroll-reveal animations seem "stuck" after adding new sections.

### Product detail pages: generic CMS + bespoke "deep dive" extras

`views/products/ProductDetail.vue` always renders the generic CMS-driven blocks (features grid, screenshots, pricing) for any product. Additionally, it has a hardcoded `DEEP_DIVE_EXTRAS` map keyed by product `slug` that injects extra hand-built Vue components after the generic content — currently only `nexstack-pos` has entries (`BizTypesSection`, `TargetAudienceSection`, `FeatureCardsSection`, `RestaurantPosSection`, `InventorySection`, `MobileQrSection`, `FaqSection` — all pre-existing, POS-specific, non-i18n'd mockup components under `components/sections/`). Use this pattern for any product that needs bespoke marketing sections beyond what the generic CMS fields support, rather than hardcoding product-specific logic into the generic rendering path.

### Vuetify theme (`src/plugins/vuetify.js`)

Centralizes component defaults instead of scattering `variant`/`density`/`rounded` props per instance. Notably: **`VBtn` defaults to `rounded: 'lg'` globally** — this is an intentional, explicit design decision (buttons across the whole site were normalized to one rounding value). Don't add `rounded="xl"`/`"pill"` to a new button; use the default. `defaultTheme` reads `localStorage.getItem('theme')` and defaults to **dark** (light is opt-in via the theme toggle), light/dark color tokens are both defined.

### Path alias

`@` → `src/` (configured in `vite.config.js`, shared by `vitest.config.js`).
