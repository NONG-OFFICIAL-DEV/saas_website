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
   * Soft rounded elevation for cards, built on the --shadow- and --radius-
   * tokens in tailwind.css (shared with the shadcn Card primitive) plus
   * --surface-sheen, a thin top rim-light that reads as a genuine
   * highlight in dark mode and is a no-op on white light-mode cards.
   */
  .clay-surface {
    border-radius: var(--radius-2xl);
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 6%, transparent);
    box-shadow: var(--shadow-md), var(--surface-sheen);
    transition:
      transform var(--duration-base) var(--ease-out-quart),
      box-shadow var(--duration-base) var(--ease-out-quart);
  }
  .clay-surface--interactive:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg), var(--surface-sheen);
  }

  /*
   * ── Sunken surface ───────────────────────────────────────────────────
   * The recessed counterpart to .clay-surface — inset shadow instead of a
   * raised one, for tracks/wells (pricing-toggle backgrounds, stat strips)
   * that should read as "pressed in" rather than "floating".
   */
  .surface-sunken {
    background: var(--muted);
    border-radius: var(--radius-lg);
    box-shadow: var(--inset-shadow-sm);
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
    box-shadow:
      0 10px 28px color-mix(in srgb, var(--primary) 30%, transparent),
      var(--shadow-xs);
    transition:
      box-shadow var(--duration-base) var(--ease-out-quart),
      transform var(--duration-base) var(--ease-out-quart);
  }
  .landing-layout [data-slot='button'][data-variant='default']:not(.cta-btn-solid):hover {
    box-shadow:
      0 16px 38px color-mix(in srgb, var(--primary) 36%, transparent),
      var(--shadow-sm);
  }
  .landing-layout [data-slot='button'][data-variant='outline']:not(.cta-btn-outline),
  .landing-layout [data-slot='button'][data-variant='secondary'] {
    background: color-mix(in srgb, var(--primary) 6%, transparent);
    color: var(--primary) !important;
    border-color: color-mix(in srgb, var(--primary) 35%, transparent) !important;
  }
  .landing-layout [data-slot='button']:active {
    transform: scale(0.97) translateY(0);
  }

  .section-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: var(--text-caption);
    letter-spacing: var(--text-caption--letter-spacing);
    font-weight: 800;
    color: var(--primary);
    text-transform: uppercase;
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
    font-size: var(--text-h2);
    font-weight: var(--text-h2--font-weight);
    letter-spacing: var(--text-h2--letter-spacing);
    line-height: var(--text-h2--line-height);
    margin-bottom: 14px;
  }

  .section-sub {
    font-size: var(--text-lead);
    line-height: var(--text-lead--line-height);
    opacity: 0.65;
    max-width: 520px;
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
