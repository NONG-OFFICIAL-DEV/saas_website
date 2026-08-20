import { Node, mergeAttributes } from '@tiptap/core'
import { toEmbedUrl } from '@/utils/videoEmbed'

// YouTube/Vimeo video embed — renders as
// <div data-type="video-embed"><iframe data-src="..." src="..." ...></iframe></div>
// `src` on the parsed-back element is what actually plays; `data-src` is
// kept so re-parsing (loading existing content back into the editor)
// round-trips without re-deriving anything.
export const VideoEmbed = Node.create({
  name: 'videoEmbed',
  group: 'block',
  atom: true,
  draggable: true,

  addAttributes() {
    return {
      src: {
        default: null,
        parseHTML: element => element.querySelector('iframe')?.getAttribute('data-src') ?? null,
        renderHTML: () => ({})
      }
    }
  },

  parseHTML() {
    return [{ tag: 'div[data-type="video-embed"]' }]
  },

  renderHTML({ node, HTMLAttributes }) {
    return [
      'div',
      mergeAttributes(HTMLAttributes, { 'data-type': 'video-embed' }),
      [
        'iframe',
        {
          src: node.attrs.src,
          'data-src': node.attrs.src,
          frameborder: '0',
          allow: 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture',
          allowfullscreen: 'true'
        }
      ]
    ]
  },

  addCommands() {
    return {
      // Returns false (command fails, toolbar can react) when the URL
      // isn't a recognized YouTube/Vimeo link — inserting a broken embed
      // silently would be worse than refusing.
      setVideoEmbed:
        rawUrl =>
        ({ commands }) => {
          const embedSrc = toEmbedUrl(rawUrl)
          if (!embedSrc) return false
          return commands.insertContent({ type: this.name, attrs: { src: embedSrc } })
        }
    }
  }
})

export default VideoEmbed
