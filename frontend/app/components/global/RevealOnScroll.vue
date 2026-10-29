<script setup lang="ts">
import type { HTMLAttributes } from 'vue'

// AOS replacement — see useRevealOnScroll.ts. `delay` is a plain prop
// (rather than a CSS custom property baked into a `data-aos-delay`
// attribute) so staggered lists can compute it inline, e.g.
// `:delay="i * 80"`, same pattern the old `data-aos-delay` calls used.
const props = withDefaults(defineProps<{
  as?: string
  direction?: 'up' | 'down' | 'none'
  distance?: number
  delay?: number
  once?: boolean
  threshold?: number
  class?: HTMLAttributes['class']
}>(), {
  as: 'div',
  direction: 'up',
  distance: 28,
  delay: 0,
  once: true,
  threshold: 0.15,
})

const { target, visible } = useRevealOnScroll({ once: props.once, threshold: props.threshold })

const offsetY = props.direction === 'none' ? 0 : props.distance * (props.direction === 'up' ? 1 : -1)
</script>

<template>
  <component
    :is="as"
    ref="target"
    class="reveal-on-scroll"
    :class="props.class"
    :data-visible="visible"
    :style="{ transitionDelay: visible ? `${delay}ms` : '0ms' }"
  >
    <slot />
  </component>
</template>

<style scoped>
.reveal-on-scroll {
  opacity: 0;
  transform: translateY(v-bind('`${offsetY}px`'));
  transition:
    opacity var(--duration-slow) var(--ease-out-expo),
    transform var(--duration-slow) var(--ease-out-expo);
  will-change: opacity, transform;
}
.reveal-on-scroll[data-visible='true'] {
  opacity: 1;
  transform: translateY(0);
}
</style>
