import { Node, mergeAttributes } from '@tiptap/core'

declare module '@tiptap/core' {
  interface Commands<ReturnType> {
    callout: {
      setCallout: (variant: string) => ReturnType
      unsetCallout: () => ReturnType
    }
  }
}

// Tip / Important / Note callout blocks — renders as
// <div data-type="callout" data-variant="tip|important|note">...</div>
// so both the admin editor and the public article renderer parse the same
// shape (see pages/documentation/[slug].vue's content styling).
export const Callout = Node.create({
  name: 'callout',
  group: 'block',
  content: 'block+',
  defining: true,

  addAttributes() {
    return {
      variant: {
        default: 'note',
        parseHTML: (element: HTMLElement) => element.getAttribute('data-variant'),
        renderHTML: (attributes: { variant: string }) => ({ 'data-variant': attributes.variant })
      }
    }
  },

  parseHTML() {
    return [{ tag: 'div[data-type="callout"]' }]
  },

  renderHTML({ HTMLAttributes }) {
    return ['div', mergeAttributes(HTMLAttributes, { 'data-type': 'callout' }), 0]
  },

  addCommands() {
    return {
      // Idempotent: if the selection is already inside a callout, switch its
      // variant in place instead of wrapping it again — otherwise clicking
      // the same (or a different variant's) toolbar button twice nests
      // callouts inside each other instead of just changing the variant.
      setCallout:
        (variant: string) =>
        ({ commands, editor }: any) =>
          editor.isActive(this.name)
            ? commands.updateAttributes(this.name, { variant })
            : commands.wrapIn(this.name, { variant }),
      unsetCallout:
        () =>
        ({ commands }: any) =>
          commands.lift(this.name)
    } as any
  }
})

export default Callout
