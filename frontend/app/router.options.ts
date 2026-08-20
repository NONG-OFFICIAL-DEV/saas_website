import type { RouterConfig } from '@nuxt/schema'

export default <RouterConfig>{
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      // Browser back/forward — restore where the user was.
      return savedPosition
    }
    if (to.hash) {
      // In-page anchor links, e.g. /#contact.
      return { el: to.hash, top: 0, behavior: 'smooth' }
    }
    // Any other navigation (nav links, product cards, etc.) — start at the top.
    return { top: 0 }
  }
}
