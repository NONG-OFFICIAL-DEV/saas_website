declare module 'aos' {
  interface AosOptions {
    duration?: number
    easing?: string
    once?: boolean
    offset?: number
    delay?: number
    disable?: boolean | 'mobile' | 'phone' | 'tablet' | (() => boolean)
    startEvent?: string
    throttleDelay?: number
    debounceDelay?: number
    disableMutationObserver?: boolean
  }

  interface Aos {
    init(options?: AosOptions): void
    refresh(): void
    refreshHard(): void
  }

  const AOS: Aos
  export default AOS
}
