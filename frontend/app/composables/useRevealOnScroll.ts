import { useIntersectionObserver, useMediaQuery } from '@vueuse/core'

interface RevealOnScrollOptions {
  /** Keep visible once revealed instead of toggling back out on scroll-away. */
  once?: boolean
  /** Fraction of the target that must be visible before it reveals. */
  threshold?: number
}

/**
 * AOS replacement. `visible` starts `true` on both server AND client —
 * matching the server-rendered markup exactly avoids a Vue hydration
 * mismatch (content must never differ between SSR output and the
 * client's pre-hydration render). Only after mount does it flip to
 * hidden and arm the intersection observer, so a crawler or a client
 * whose JS never runs always sees real content, and prefers-reduced-
 * motion skips the hide/reveal cycle outright.
 */
export function useRevealOnScroll(options: RevealOnScrollOptions = {}) {
  const { once = true, threshold = 0.15 } = options

  const target = ref<HTMLElement | null>(null)
  const visible = ref(true)
  const prefersReducedMotion = useMediaQuery('(prefers-reduced-motion: reduce)')

  onMounted(() => {
    if (prefersReducedMotion.value) return

    visible.value = false

    const { stop } = useIntersectionObserver(
      target,
      ([entry]) => {
        if (entry?.isIntersecting) {
          visible.value = true
          if (once) stop()
        } else if (!once) {
          visible.value = false
        }
      },
      { threshold }
    )
  })

  return { target, visible }
}
