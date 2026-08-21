<template>
  <!--
    default layout wraps all public/marketing pages.
    AppNavbar is sticky via CSS (no v-app-bar dependency).
    AppDrawer is position:fixed — lives outside normal flow.
    <slot /> renders the current page.
  -->
  <div class="landing-layout">
    <AppNavbar />

    <main class="landing-main">
      <slot />
    </main>

    <AppFooter />
    <FloatingCtaDock />
  </div>
</template>

<script setup lang="ts">
  import AOS from 'aos'
  import 'aos/dist/aos.css'

  const route = useRoute()

  onMounted(() => {
    AOS.init({ duration: 700, easing: 'ease-out-cubic', once: true })
  })

  // The layout itself never remounts between child pages (only the page
  // content inside <slot /> changes), so AOS needs an explicit refresh
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
    background: color-mix(in srgb, var(--muted) 5%, transparent);
  }

  /*
   * ── Section backgrounds ──────────────────────────────────────────────
   * Every section is flat, uniform white (the page background) — no
   * per-section color washes. These classes are kept as a no-op so
   * existing section components don't need their markup changed.
   */
  .section-tint-lavender,
  .section-tint-sky,
  .section-tint-mint,
  .section-tint-peach,
  .section-tint-neutral {
    background: transparent;
  }

  /*
   * ── Claymorphism surface ────────────────────────────────────────────
   * Soft rounded elevation for cards. Shadow color rides on on-surface
   * (near-black in light mode, near-white in dark mode) so one rule
   * works correctly in both themes without duplication.
   */
  .clay-surface {
    border-radius: 22px;
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 6%, transparent);
    box-shadow:
      0 14px 32px color-mix(in srgb, var(--foreground) 8%, transparent),
      0 2px 8px color-mix(in srgb, var(--foreground) 4%, transparent);
    transition:
      transform 0.25s ease,
      box-shadow 0.25s ease;
  }
  .clay-surface--interactive:hover {
    transform: translateY(-4px);
    box-shadow:
      0 22px 46px color-mix(in srgb, var(--foreground) 12%, transparent),
      0 4px 14px color-mix(in srgb, var(--foreground) 6%, transparent);
  }

  /*
   * ── Buttons ──────────────────────────────────────────────────────────
   * Scoped to .landing-layout only — the admin panel is a separate root
   * (the admin layout's own shell), so this never touches it.
   * Default (filled/primary) buttons get a soft brand-colored lift;
   * outline/secondary buttons (secondary actions) get a soft tinted
   * background + brand-colored text/border, unless a component already
   * forces its own color (e.g. CtaSection's white-on-gradient buttons
   * keep their own rule). Matched via shadcn Button's `data-slot`/
   * `data-variant` attributes rather than a class, since those survive
   * regardless of whether Button renders as a <button> or (via `as`) a
   * NuxtLink <a>.
   */
  .landing-layout [data-slot='button'][data-variant='default']:not(.cta-btn-solid) {
    box-shadow: 0 10px 26px color-mix(in srgb, var(--primary) 28%, transparent);
  }
  .landing-layout [data-slot='button'][data-variant='outline']:not(.cta-btn-outline),
  .landing-layout [data-slot='button'][data-variant='secondary'] {
    background: color-mix(in srgb, var(--primary) 6%, transparent);
    color: var(--primary) !important;
    border-color: color-mix(in srgb, var(--primary) 35%, transparent) !important;
  }
  .landing-layout [data-slot='button']:active {
    transform: scale(0.97);
  }

  .section-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.7rem;
    font-weight: 800;
    color: var(--primary);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 6px 16px 6px 12px;
    border: 1px solid color-mix(in srgb, var(--primary) 30%, transparent);
    border-radius: 999px;
    background: color-mix(in srgb, var(--primary) 6%, transparent);
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
    .landing-main {
      padding-bottom: 76px;
    }
  }
</style>
