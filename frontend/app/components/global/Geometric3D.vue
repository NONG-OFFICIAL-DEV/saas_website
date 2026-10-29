<script setup lang="ts">
import { useMediaQuery } from '@vueuse/core'

// Layered CSS-3D composition replacing the old static isometric-cube SVG —
// real perspective/translateZ depth plus pointer parallax (CSS-only, no
// WebGL/Three.js per the brief). Same external API as before (`accent`
// prop, no required slots) so existing call sites (HeroSection, about.vue,
// products/[slug].vue) need no changes.
const props = defineProps({
  accent: {
    type: String,
    default: '#6366F1'
  }
})

function hexToHsl(hex: string) {
  const clean = hex.replace('#', '')
  const r = parseInt(clean.substring(0, 2), 16) / 255
  const g = parseInt(clean.substring(2, 4), 16) / 255
  const b = parseInt(clean.substring(4, 6), 16) / 255
  const max = Math.max(r, g, b)
  const min = Math.min(r, g, b)
  let h = 0
  let s = 0
  const l = (max + min) / 2
  const d = max - min
  if (d !== 0) {
    s = d / (1 - Math.abs(2 * l - 1))
    switch (max) {
      case r: h = ((g - b) / d) % 6; break
      case g: h = (b - r) / d + 2; break
      default: h = (r - g) / d + 4
    }
    h *= 60
    if (h < 0) h += 360
  }
  return { h, s, l }
}

function hslToHex(h: number, s: number, l: number) {
  const c = (1 - Math.abs(2 * l - 1)) * s
  const x = c * (1 - Math.abs(((h / 60) % 2) - 1))
  const m = l - c / 2
  let r = 0, g = 0, b = 0
  if (h < 60) [r, g, b] = [c, x, 0]
  else if (h < 120) [r, g, b] = [x, c, 0]
  else if (h < 180) [r, g, b] = [0, c, x]
  else if (h < 240) [r, g, b] = [0, x, c]
  else if (h < 300) [r, g, b] = [x, 0, c]
  else [r, g, b] = [c, 0, x]
  const toHex = (v: number) =>
    Math.round((v + m) * 255)
      .toString(16)
      .padStart(2, '0')
  return `#${toHex(r)}${toHex(g)}${toHex(b)}`
}

function adjustLightness(hex: string, amount: number) {
  const { h, s, l } = hexToHsl(hex)
  const newL = Math.min(1, Math.max(0, l + amount))
  return hslToHex(h, s, newL)
}

const shades = computed(() => ({
  light: adjustLightness(props.accent, 0.22),
  midLight: adjustLightness(props.accent, 0.1),
  mid: props.accent,
  dark: adjustLightness(props.accent, -0.18)
}))

// Pointer parallax — a window-level listener (not a local pointermove
// handler) because this component always sits inside a `pointer-events:
// none` decorative wrapper at call sites; tracking the pointer globally
// and re-deriving this element's own bounding rect each time keeps it
// purely decorative (never intercepts clicks) while still reacting to the
// cursor anywhere on the page.
const root = ref<HTMLElement | null>(null)
const parallaxX = ref(0)
const parallaxY = ref(0)

const prefersReducedMotion = useMediaQuery('(prefers-reduced-motion: reduce)')
const supportsParallax = useMediaQuery('(pointer: fine)')
const parallaxEnabled = computed(() => supportsParallax.value && !prefersReducedMotion.value)

let frame: number | null = null

function handlePointerMove(e: PointerEvent) {
  if (!parallaxEnabled.value || !root.value) return
  if (frame) cancelAnimationFrame(frame)
  frame = requestAnimationFrame(() => {
    const rect = root.value!.getBoundingClientRect()
    const cx = rect.left + rect.width / 2
    const cy = rect.top + rect.height / 2
    // Normalized to roughly [-1, 1] across a generous capture radius so
    // the effect stays subtle even when the cursor is far from the shape.
    parallaxX.value = Math.max(-1, Math.min(1, (e.clientX - cx) / 420))
    parallaxY.value = Math.max(-1, Math.min(1, (e.clientY - cy) / 420))
  })
}

onMounted(() => {
  if (import.meta.client) window.addEventListener('pointermove', handlePointerMove, { passive: true })
})
onBeforeUnmount(() => {
  if (import.meta.client) window.removeEventListener('pointermove', handlePointerMove)
  if (frame) cancelAnimationFrame(frame)
})
</script>

<template>
  <div ref="root" class="geo3d-scene" :aria-hidden="true">
    <!-- Back layer: soft ambient glow, furthest depth, slowest drift -->
    <div
      class="geo3d-layer geo3d-layer--back geo3d-float geo3d-float--slow"
      :style="{
        background: `radial-gradient(circle at 35% 30%, ${shades.light}, transparent 70%)`,
        transform: `translate3d(${parallaxX * 10}px, ${parallaxY * 10}px, -140px)`,
      }"
    />
    <!-- Mid layer: the "floating dashboard" card — base depth plane -->
    <div
      class="geo3d-layer geo3d-layer--card geo3d-float geo3d-float--med"
      :style="{
        background: `linear-gradient(135deg, ${shades.midLight}, ${shades.mid})`,
        boxShadow: `0 28px 64px color-mix(in srgb, ${shades.dark} 35%, transparent)`,
        transform: `translate3d(${parallaxX * 22}px, ${parallaxY * 22}px, 0px) rotateX(${-parallaxY * 6}deg) rotateY(${parallaxX * 6}deg)`,
      }"
    >
      <span class="geo3d-card-line" :style="{ background: shades.light }" />
      <span class="geo3d-card-line geo3d-card-line--short" :style="{ background: shades.light }" />
    </div>
    <!-- Front layer: closest depth, fastest parallax + drift -->
    <div
      class="geo3d-layer geo3d-layer--orb geo3d-float geo3d-float--fast"
      :style="{
        background: `radial-gradient(circle at 35% 30%, ${shades.light}, ${shades.dark})`,
        boxShadow: `0 18px 36px color-mix(in srgb, ${shades.dark} 40%, transparent)`,
        transform: `translate3d(${parallaxX * 36}px, ${parallaxY * 36}px, 90px)`,
      }"
    />
    <div
      class="geo3d-layer geo3d-layer--chip geo3d-float geo3d-float--fast"
      :style="{
        background: shades.midLight,
        transform: `translate3d(${parallaxX * 30}px, ${parallaxY * 30}px, 60px)`,
      }"
    />
  </div>
</template>

<style scoped>
.geo3d-scene {
  position: relative;
  width: 100%;
  height: 100%;
  perspective: var(--perspective-tilt);
  transform-style: preserve-3d;
}

.geo3d-layer {
  position: absolute;
  border-radius: 28px;
  transition: transform var(--duration-base) var(--ease-out-quart);
  will-change: transform;
}

.geo3d-layer--back {
  inset: -20%;
  border-radius: 50%;
  opacity: 0.55;
  filter: blur(2px);
}

.geo3d-layer--card {
  left: 18%;
  top: 22%;
  width: 56%;
  height: 46%;
  padding: 14% 12%;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  gap: 8px;
}

.geo3d-card-line {
  display: block;
  height: 8px;
  width: 70%;
  border-radius: 999px;
  opacity: 0.85;
}
.geo3d-card-line--short {
  width: 42%;
  opacity: 0.6;
}

.geo3d-layer--orb {
  top: 4%;
  right: 6%;
  width: 26%;
  height: 26%;
  border-radius: 50%;
}

.geo3d-layer--chip {
  bottom: 8%;
  left: 6%;
  width: 16%;
  height: 16%;
  border-radius: 40%;
  opacity: 0.8;
}

.geo3d-float--slow {
  animation: geo3d-bob 8s ease-in-out infinite;
}
.geo3d-float--med {
  animation: geo3d-bob 6s ease-in-out infinite;
  animation-delay: 0.3s;
}
.geo3d-float--fast {
  animation: geo3d-bob 4.5s ease-in-out infinite;
  animation-delay: 0.6s;
}

@keyframes geo3d-bob {
  0%, 100% { translate: 0 0; }
  50% { translate: 0 -10px; }
}

@media (prefers-reduced-motion: reduce) {
  .geo3d-layer {
    animation: none !important;
    transition: none;
  }
}
</style>
