// Shared by the admin rich text editor's video node (components/admin/
// VideoEmbedExtension.js) and the public article renderer's DOMPurify
// allowlist (views/documentation/DocumentationArticle.vue) — one place that
// defines what counts as a real, embeddable YouTube/Vimeo URL.

const YOUTUBE_EMBED_HOST = 'https://www.youtube.com/embed/'
const VIMEO_EMBED_HOST = 'https://player.vimeo.com/video/'

/**
 * Converts a plain YouTube/Vimeo watch/share link into its embeddable
 * iframe src. Returns null if the URL isn't a recognized format — callers
 * should treat that as "can't embed this," not silently degrade.
 */
export function toEmbedUrl(url) {
  if (!url) return null

  const youtubeWatch = url.match(/[?&]v=([\w-]{6,})/)
  const youtubeShort = url.match(/youtu\.be\/([\w-]{6,})/)
  const youtubeId = youtubeWatch?.[1] ?? youtubeShort?.[1]
  if (youtubeId) return `${YOUTUBE_EMBED_HOST}${youtubeId}`
  if (url.startsWith(YOUTUBE_EMBED_HOST)) return url

  const vimeoMatch = url.match(/vimeo\.com\/(\d+)/)
  if (vimeoMatch) return `${VIMEO_EMBED_HOST}${vimeoMatch[1]}`
  if (url.startsWith(VIMEO_EMBED_HOST)) return url

  return null
}

/** True only for our own generated embed src values — used to allowlist iframe src when sanitizing admin-authored article content before v-html. */
export function isAllowedEmbedSrc(src) {
  return typeof src === 'string' && (src.startsWith(YOUTUBE_EMBED_HOST) || src.startsWith(VIMEO_EMBED_HOST))
}
