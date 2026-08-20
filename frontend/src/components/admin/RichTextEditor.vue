<template>
  <div class="rte">
    <div v-if="editor" class="rte-toolbar">
      <button type="button" class="rte-btn" :class="{ active: editor.isActive('heading', { level: 2 }) }" title="Heading 2" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()">H2</button>
      <button type="button" class="rte-btn" :class="{ active: editor.isActive('heading', { level: 3 }) }" title="Heading 3" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()">H3</button>
      <span class="rte-sep" />
      <button type="button" class="rte-btn" :class="{ active: editor.isActive('bold') }" title="Bold" @click="editor.chain().focus().toggleBold().run()"><v-icon icon="mdi-format-bold" size="16" /></button>
      <button type="button" class="rte-btn" :class="{ active: editor.isActive('italic') }" title="Italic" @click="editor.chain().focus().toggleItalic().run()"><v-icon icon="mdi-format-italic" size="16" /></button>
      <button type="button" class="rte-btn" :class="{ active: editor.isActive('code') }" title="Inline code" @click="editor.chain().focus().toggleCode().run()"><v-icon icon="mdi-code-tags" size="16" /></button>
      <span class="rte-sep" />
      <button type="button" class="rte-btn" :class="{ active: editor.isActive('bulletList') }" title="Bullet list" @click="editor.chain().focus().toggleBulletList().run()"><v-icon icon="mdi-format-list-bulleted" size="16" /></button>
      <button type="button" class="rte-btn" :class="{ active: editor.isActive('orderedList') }" title="Numbered list" @click="editor.chain().focus().toggleOrderedList().run()"><v-icon icon="mdi-format-list-numbered" size="16" /></button>
      <button type="button" class="rte-btn" :class="{ active: editor.isActive('blockquote') }" title="Quote" @click="editor.chain().focus().toggleBlockquote().run()"><v-icon icon="mdi-format-quote-close" size="16" /></button>
      <button type="button" class="rte-btn" :class="{ active: editor.isActive('codeBlock') }" title="Code block" @click="editor.chain().focus().toggleCodeBlock().run()"><v-icon icon="mdi-code-braces" size="16" /></button>
      <span class="rte-sep" />
      <button type="button" class="rte-btn" :class="{ active: editor.isActive('link') }" title="Link" @click="handleSetLink"><v-icon icon="mdi-link-variant" size="16" /></button>
      <button type="button" class="rte-btn" title="Image" :disabled="uploadingImage" @click="triggerImagePick"><v-icon icon="mdi-image-outline" size="16" /></button>
      <button type="button" class="rte-btn" title="Table" @click="editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()"><v-icon icon="mdi-table" size="16" /></button>
      <button type="button" class="rte-btn" title="Video (YouTube/Vimeo)" @click="handleInsertVideo"><v-icon icon="mdi-youtube" size="16" /></button>
      <span class="rte-sep" />
      <button type="button" class="rte-btn rte-btn--tip" :class="{ active: editor.isActive('callout', { variant: 'tip' }) }" title="Tip callout" @click="editor.chain().focus().setCallout('tip').run()">💡</button>
      <button type="button" class="rte-btn rte-btn--important" :class="{ active: editor.isActive('callout', { variant: 'important' }) }" title="Important callout" @click="editor.chain().focus().setCallout('important').run()">⚠️</button>
      <button type="button" class="rte-btn rte-btn--note" :class="{ active: editor.isActive('callout', { variant: 'note' }) }" title="Note callout" @click="editor.chain().focus().setCallout('note').run()">ℹ️</button>
      <button v-if="editor.isActive('callout')" type="button" class="rte-btn" title="Remove callout" @click="editor.chain().focus().unsetCallout().run()"><v-icon icon="mdi-close" size="16" /></button>
    </div>

    <input ref="fileInput" type="file" accept="image/*" class="d-none" @change="handleImagePicked" />

    <editor-content :editor="editor" class="rte-content" />
  </div>
</template>

<script setup>
  import { onBeforeUnmount, ref, watch } from 'vue'
  import { Editor, EditorContent } from '@tiptap/vue-3'
  import StarterKit from '@tiptap/starter-kit'
  import Image from '@tiptap/extension-image'
  import { TableKit } from '@tiptap/extension-table'
  import Placeholder from '@tiptap/extension-placeholder'
  import { Callout } from './CalloutExtension'
  import { VideoEmbed } from './VideoEmbedExtension'
  import { uploadProductMedia } from '@/services/adminProducts'

  const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Write the article content…' }
  })
  const emit = defineEmits(['update:modelValue'])

  const fileInput = ref(null)
  const uploadingImage = ref(false)

  const editor = new Editor({
    content: props.modelValue || '',
    extensions: [
      StarterKit.configure({ link: { openOnClick: false, autolink: true } }),
      Image,
      TableKit.configure({ table: { resizable: false } }),
      Placeholder.configure({ placeholder: props.placeholder }),
      Callout,
      VideoEmbed
    ],
    onUpdate: ({ editor }) => emit('update:modelValue', editor.getHTML())
  })

  // The article's real content usually arrives asynchronously (after the
  // parent's fetch resolves), well after this editor instance is created —
  // push it in once it shows up rather than only reading the initial prop.
  watch(
    () => props.modelValue,
    value => {
      if (value !== editor.getHTML()) editor.commands.setContent(value || '', false)
    }
  )

  function handleSetLink() {
    const previousUrl = editor.getAttributes('link').href
    const url = window.prompt('Link URL', previousUrl || 'https://')
    if (url === null) return
    if (url === '') {
      editor.chain().focus().extendMarkRange('link').unsetLink().run()
      return
    }
    editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
  }

  function handleInsertVideo() {
    const url = window.prompt('YouTube or Vimeo video URL')
    if (!url) return
    const inserted = editor.chain().focus().setVideoEmbed(url).run()
    if (!inserted) window.alert("That doesn't look like a YouTube or Vimeo link — double-check the URL and try again.")
  }

  function triggerImagePick() {
    fileInput.value?.click()
  }

  async function handleImagePicked(e) {
    const file = e.target.files?.[0]
    e.target.value = ''
    if (!file) return
    uploadingImage.value = true
    try {
      const url = await uploadProductMedia(file)
      editor.chain().focus().setImage({ src: url }).run()
    } finally {
      uploadingImage.value = false
    }
  }

  onBeforeUnmount(() => editor.destroy())
</script>

<style scoped>
  .rte {
    border: 1px solid rgba(var(--v-theme-on-surface), 0.15);
    border-radius: 10px;
    overflow: hidden;
  }

  .rte-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 2px;
    padding: 6px 8px;
    border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.1);
    background: rgba(var(--v-theme-on-surface), 0.03);
  }
  .rte-sep {
    width: 1px;
    height: 20px;
    background: rgba(var(--v-theme-on-surface), 0.12);
    margin: 0 4px;
  }
  .rte-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    padding: 0 6px;
    border: none;
    border-radius: 6px;
    background: transparent;
    color: rgba(var(--v-theme-on-surface), 0.7);
    cursor: pointer;
    font-size: 0.78rem;
    font-weight: 700;
  }
  .rte-btn:hover {
    background: rgba(var(--v-theme-on-surface), 0.08);
  }
  .rte-btn.active {
    background: rgba(var(--v-theme-primary), 0.14);
    color: rgb(var(--v-theme-primary));
  }
  .rte-btn:disabled {
    opacity: 0.5;
    cursor: default;
  }

  .rte-content {
    padding: 14px 16px;
    min-height: 220px;
    max-height: 520px;
    overflow-y: auto;
  }
  .rte-content :deep(.tiptap) {
    outline: none;
    min-height: 200px;
  }
  .rte-content :deep(p.is-editor-empty:first-child::before) {
    content: attr(data-placeholder);
    float: left;
    height: 0;
    color: rgba(var(--v-theme-on-surface), 0.4);
    pointer-events: none;
  }
  .rte-content :deep(h2) {
    font-size: 1.2rem;
    font-weight: 800;
    margin: 18px 0 8px;
  }
  .rte-content :deep(h3) {
    font-size: 1.05rem;
    font-weight: 800;
    margin: 16px 0 6px;
  }
  .rte-content :deep(p) {
    margin: 0 0 10px;
    line-height: 1.6;
  }
  .rte-content :deep(ul),
  .rte-content :deep(ol) {
    margin: 0 0 10px;
    padding-left: 24px;
  }
  .rte-content :deep(blockquote) {
    margin: 0 0 10px;
    padding-left: 14px;
    border-left: 3px solid rgba(var(--v-theme-primary), 0.4);
    color: rgba(var(--v-theme-on-surface), 0.65);
  }
  .rte-content :deep(pre) {
    background: rgba(var(--v-theme-on-surface), 0.06);
    border-radius: 8px;
    padding: 12px 14px;
    overflow-x: auto;
    margin: 0 0 10px;
  }
  .rte-content :deep(img) {
    max-width: 100%;
    border-radius: 8px;
  }
  .rte-content :deep(table) {
    border-collapse: collapse;
    width: 100%;
    margin: 0 0 10px;
  }
  .rte-content :deep(td),
  .rte-content :deep(th) {
    border: 1px solid rgba(var(--v-theme-on-surface), 0.15);
    padding: 6px 10px;
  }
  .rte-content :deep(th) {
    background: rgba(var(--v-theme-on-surface), 0.04);
  }
  .rte-content :deep(div[data-type='callout']) {
    padding: 12px 16px;
    border-radius: 10px;
    margin: 0 0 12px;
  }
  .rte-content :deep(div[data-type='callout'][data-variant='tip']) {
    background: rgba(99, 102, 241, 0.08);
    border-left: 3px solid rgb(var(--v-theme-primary));
  }
  .rte-content :deep(div[data-type='callout'][data-variant='important']) {
    background: rgba(245, 158, 11, 0.1);
    border-left: 3px solid #f59e0b;
  }
  .rte-content :deep(div[data-type='callout'][data-variant='note']) {
    background: rgba(var(--v-theme-on-surface), 0.05);
    border-left: 3px solid rgba(var(--v-theme-on-surface), 0.3);
  }
  .rte-content :deep(div[data-type='callout'] p) {
    margin: 0 0 4px;
  }

  .rte-content :deep(div[data-type='video-embed']) {
    position: relative;
    width: 100%;
    max-width: 480px;
    padding-top: 56.25%; /* 16:9, relative to max-width above */
    margin: 0 0 12px;
    border-radius: 8px;
    overflow: hidden;
    background: rgba(var(--v-theme-on-surface), 0.06);
  }
  .rte-content :deep(div[data-type='video-embed'] iframe) {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: none;
  }
</style>
