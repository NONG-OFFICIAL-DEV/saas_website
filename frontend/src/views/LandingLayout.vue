<template>
  <!--
    LandingLayout wraps all public/marketing pages.
    AppNavbar is sticky via CSS (no v-app-bar dependency).
    AppDrawer is position:fixed — lives outside normal flow.
    <router-view> renders the current child page (e.g. LandingPage).
  -->
  <div class="landing-layout">
    <AppNavbar />

    <main class="landing-main">
      <router-view />
    </main>

    <AppFooter />
    <FloatingCtaDock />
  </div>
</template>

<script setup>
  import { nextTick, onMounted, watch } from 'vue'
  import { useRoute } from 'vue-router'
  import AOS from 'aos'
  import 'aos/dist/aos.css'

  import AppNavbar from '@/components/layout/AppNavbar.vue'
  import AppFooter from '@/components/layout/AppFooter.vue'
  import FloatingCtaDock from '@/components/layout/FloatingCtaDock.vue'

  const route = useRoute()

  onMounted(() => {
    AOS.init({ duration: 700, easing: 'ease-out-cubic', once: true })
  })

  // LandingLayout itself never remounts between child routes (only
  // <router-view>'s content changes), so AOS needs an explicit refresh
  // after each navigation to pick up the new page's data-aos elements.
  watch(
    () => route.fullPath,
    async () => {
      await nextTick()
      AOS.refreshHard()
    }
  )
</script>

<style>
  .landing-layout {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  .landing-main {
    flex: 1;
    padding-top: 68px;
  }

  .landing-layout,
  .landing-layout * {
    /* Hanuman is the Khmer fallback — Google Sans has no Khmer glyphs at
       all, so without this Khmer text silently fell back to whatever
       generic serif the browser picked. */
    font-family: 'Google Sans', 'Hanuman', sans-serif;
    font-optical-sizing: auto;
    font-style: normal;
    box-sizing: border-box;
  }

  .landing-main {
    overflow-x: hidden;
  }

  /* ── Shared utilities — available to all child section components ── */
  .section-pad {
    padding: 88px 0;
  }
  .bg-soft {
    background: rgba(var(--v-theme-surface-variant), 0.05);
  }

  /*
   * ── Soft section-tint palette ──────────────────────────────────────
   * Very low-opacity washes derived from the existing theme tokens
   * (not new hues) so each major section reads as a distinct "zone"
   * while the page stays mostly neutral. rgba() driven off the theme's
   * own RGB-channel custom properties means these adapt automatically
   * between light/dark — no separate dark-mode overrides needed.
   */
  .section-tint-lavender {
    background: rgba(var(--v-theme-primary), 0.05);
  }
  .section-tint-sky {
    background: rgba(var(--v-theme-info), 0.06);
  }
  .section-tint-mint {
    background: rgba(var(--v-theme-success), 0.055);
  }
  .section-tint-peach {
    background: rgba(var(--v-theme-warning), 0.055);
  }
  .section-tint-neutral {
    background: rgba(var(--v-theme-on-surface), 0.03);
  }

  /*
   * ── Claymorphism surface ────────────────────────────────────────────
   * Soft rounded elevation for cards. Shadow color rides on on-surface
   * (near-black in light mode, near-white in dark mode) so one rule
   * works correctly in both themes without duplication.
   */
  .clay-surface {
    border-radius: 22px;
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-theme-on-surface), 0.06);
    box-shadow:
      0 14px 32px rgba(var(--v-theme-on-surface), 0.08),
      0 2px 8px rgba(var(--v-theme-on-surface), 0.04);
    transition:
      transform 0.25s ease,
      box-shadow 0.25s ease;
  }
  .clay-surface--interactive:hover {
    transform: translateY(-4px);
    box-shadow:
      0 22px 46px rgba(var(--v-theme-on-surface), 0.12),
      0 4px 14px rgba(var(--v-theme-on-surface), 0.06);
  }

  /*
   * ── Buttons ──────────────────────────────────────────────────────────
   * Scoped to .landing-layout only — the admin panel is a separate root
   * (AdminLayout's v-navigation-drawer), so this never touches it.
   * Flat buttons get a soft brand-colored lift; outlined/tonal buttons
   * (secondary actions) get a soft tinted background + brand-colored
   * text/border, unless a component already forces its own color
   * (e.g. CtaSection's white-on-gradient buttons keep their own rule).
   */
  .landing-layout .v-btn.v-btn--variant-flat:not(.cta-btn-solid) {
    box-shadow: 0 10px 26px rgba(var(--v-theme-primary), 0.28);
  }
  .landing-layout .v-btn.v-btn--variant-outlined:not(.cta-btn-outline),
  .landing-layout .v-btn.v-btn--variant-tonal {
    background: rgba(var(--v-theme-primary), 0.06);
    color: rgb(var(--v-theme-primary)) !important;
    border-color: rgba(var(--v-theme-primary), 0.35) !important;
  }
  .landing-layout .v-btn:active {
    transform: scale(0.97);
  }

  .section-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.7rem;
    font-weight: 800;
    color: rgb(var(--v-theme-primary));
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 6px 16px 6px 12px;
    border: 1px solid rgba(var(--v-theme-primary), 0.3);
    border-radius: 999px;
    background: rgba(var(--v-theme-primary), 0.06);
    margin-bottom: 14px;
  }
  .section-tag::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
  }

  .section-title {
    font-size: clamp(1.4rem, 4vw, 2.6rem);
    font-weight: 900;
    letter-spacing: -1px;
    line-height: 1.12;
    margin-bottom: 14px;
  }

  .section-sub {
    font-size: clamp(0.82rem, 2.2vw, 0.95rem);
    opacity: 0.6;
    line-height: 1.75;
    max-width: 480px;
  }

  /* Gap helpers */
  .gap-7 {
    gap: 28px;
  }
  .gap-4 {
    gap: 16px;
  }
  .gap-3 {
    gap: 12px;
  }
  .gap-2 {
    gap: 8px;
  }

  @media (max-width: 600px) {
    .section-pad {
      padding: 52px 0;
    }
    .section-title {
      letter-spacing: -0.5px;
    }
    .landing-layout .v-container {
      padding-left: 16px !important;
      padding-right: 16px !important;
    }
    .landing-main {
      padding-bottom: 76px;
    }
  }
</style>
