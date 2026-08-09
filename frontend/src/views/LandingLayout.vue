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
    font-family: 'Google Sans', serif;
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
