<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { useMediaQuery } from '@vueuse/core'

// Pointer-responsive CSS 3D tilt — no WebGL/Three.js. Gated on `(pointer:
// fine)` (mouse/trackpad only — touch has no hover, tilting on tap reads
// as broken rather than premium) and prefers-reduced-motion, per the "CSS
// effects over WebGL, respect reduced-motion, mind mobile" brief.
const props = withDefaults(defineProps<{
  maxTilt?: number
  scale?: number
  glare?: boolean
  class?: HTMLAttributes['class']
}>(), {
  maxTilt: 8,
  scale: 1.02,
  glare: true,
})

const el = ref<HTMLElement | null>(null)
const rotateX = ref(0)
const rotateY = ref(0)
const hovering = ref(false)
const glareX = ref(50)
const glareY = ref(50)

const prefersReducedMotion = useMediaQuery('(prefers-reduced-motion: reduce)')
const supportsHoverTilt = useMediaQuery('(pointer: fine)')
// useMediaQuery reads the real matchMedia result synchronously on the
// client, which would otherwise differ from the SSR default and cause a
// hydration mismatch on the v-if'd glare div below — gate on mounted state
// so both SSR and the client's pre-hydration render agree (tilt disabled),
// then enable for real once mounted, same pattern as useRevealOnScroll.
const isMounted = ref(false)
onMounted(() => { isMounted.value = true })
const tiltEnabled = computed(() => isMounted.value && supportsHoverTilt.value && !prefersReducedMotion.value)

let frame: number | null = null

function handlePointerMove(e: PointerEvent) {
  if (!tiltEnabled.value || !el.value) return
  if (frame) cancelAnimationFrame(frame)
  frame = requestAnimationFrame(() => {
    const rect = el.value!.getBoundingClientRect()
    const px = (e.clientX - rect.left) / rect.width
    const py = (e.clientY - rect.top) / rect.height
    rotateY.value = (px - 0.5) * 2 * props.maxTilt
    rotateX.value = (0.5 - py) * 2 * props.maxTilt
    glareX.value = px * 100
    glareY.value = py * 100
  })
}

function handlePointerEnter() {
  if (tiltEnabled.value) hovering.value = true
}

function handlePointerLeave() {
  hovering.value = false
  rotateX.value = 0
  rotateY.value = 0
}

onBeforeUnmount(() => {
  if (frame) cancelAnimationFrame(frame)
})
</script>

<template>
  <div
    ref="el"
    class="tilt-card"
    :class="props.class"
    :style="{
      transform: hovering
        ? `perspective(var(--perspective-tilt)) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(${scale})`
        : 'perspective(var(--perspective-tilt)) rotateX(0deg) rotateY(0deg) scale(1)',
    }"
    @pointermove="handlePointerMove"
    @pointerenter="handlePointerEnter"
    @pointerleave="handlePointerLeave"
  >
    <slot />
    <div
      v-if="glare && tiltEnabled"
      class="tilt-card-glare"
      :style="{
        opacity: hovering ? 1 : 0,
        background: `radial-gradient(circle at ${glareX}% ${glareY}%, color-mix(in srgb, white 25%, transparent), transparent 60%)`,
      }"
    />
  </div>
</template>

<style scoped>
.tilt-card {
  position: relative;
  transform-style: preserve-3d;
  transition: transform var(--duration-base) var(--ease-out-quart);
  will-change: transform;
}
.tilt-card-glare {
  position: absolute;
  inset: 0;
  pointer-events: none;
  border-radius: inherit;
  transition: opacity var(--duration-base) var(--ease-out-quart);
  mix-blend-mode: overlay;
}
</style>
