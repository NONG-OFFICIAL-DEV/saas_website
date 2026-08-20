import DOMPurify from 'isomorphic-dompurify'
import { isAllowedEmbedSrc } from './videoEmbed'

// DOMPurify's ADD_TAGS/ADD_ATTR below allow <iframe> through structurally
// (stripped of any event handlers / javascript: URIs like anything else),
// but that alone doesn't restrict *which* src it can point to. This hook
// removes any iframe whose src isn't our own generated YouTube/Vimeo embed
// URL — belt-and-suspenders against a compromised/malicious src ever
// reaching a real <iframe> on the page, even though today's only writer is
// the trusted admin editor.
//
// Registered once at module load (not per-render, and not via a second
// DOMParser pass over the sanitized output) — DOMParser doesn't exist in
// Node, so doing this as a DOMPurify hook inside the single sanitize() call
// keeps the whole thing SSR-safe.
DOMPurify.addHook('uponSanitizeElement', (node, data) => {
  if (data.tagName !== 'iframe') return
  const el = node as Element
  const src = el.getAttribute?.('src')
  if (isAllowedEmbedSrc(src)) return
  const wrapper = el.closest?.('div[data-type="video-embed"]')
  ;(wrapper ?? el).remove()
})

export function sanitizeArticleHtml(html: string): string {
  return DOMPurify.sanitize(html, {
    ADD_TAGS: ['iframe'],
    ADD_ATTR: ['allow', 'allowfullscreen', 'frameborder', 'target']
  })
}
